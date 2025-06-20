<?php

namespace App\Http\Controllers;

use App\Helpers\Cart;
use App\Models\CartItem;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public function index()
    {
//        $request = \request();
//        $dbCartItems = CartItem::where(['user_id' => $request->user()->id])->get()->keyBy('product_id');
//        dd($dbCartItems);
        [$products, $cartItems] = Cart::getProductsAndCartItems();
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * $cartItems[$product->id]['quantity'];
        }

        return view('cart.index', compact('cartItems', 'products', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $quantity = $request->post('quantity', 1);
        $user = $request->user();

        $totalCartCount = 0;
        $totalQuantity = 0;
        $cartItem = null;
        // Validate quantity against product->quantity
        if ($user) {
            $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();
            if ($cartItem) {
                $totalQuantity = $cartItem->quantity + $quantity;
            } else {
                $totalQuantity = $quantity;
            }
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            $productFound = false;
            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $totalQuantity = $item['quantity'] + $quantity;
                    $productFound = true;
                    break;
                }
            }
            if (!$productFound) {
                $totalQuantity = $quantity;
            }
        }
        if ($product->quantity !== null && $product->quantity < $totalQuantity) {
            return response([
                'message' => match ( $product->quantity ) {
                    0 => 'The product is out of stock',
                    1 => 'There is only 1 item left',
                    default => 'There are only ' . $product->quantity . ' items left',
                }
            ], 422);
        }

        if ($user) {

            $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
                $cartItem->update();
            } else {
                $data = [
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];
                CartItem::create($data);
            }

            return response([
                'count' => Cart::getCartItemsCount()
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            $productFound = false;
            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] += $quantity;
                    $productFound = true;
                    break;
                }
            }
            if (!$productFound) {
                $cartItems[] = [
                    'user_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price
                ];
            }
            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response(['count' => Cart::getCountFromItems($cartItems)]);
        }
    }

    public function bulkAdd(Request $request)
    {
        $products = [];

        if (auth()->check()) {
            $userId = auth()->id();
        }

        $validated = $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required',
            'ingredients.*.quantity' => 'required|integer|min:1',
        ]);

//        return response()->json([
//            'ingredients' =>  $validated['ingredients']
//        ]);

        $totalQuantity = 0;

        foreach ($validated['ingredients'] as $item) {

            $product = Ingredient::where('id', $item['id'])->first()?->products()->first();

            // Validate quantity against product->quantity
            if (auth()->check()) {
                $cartItem = CartItem::where(['user_id' => $userId, 'product_id' => $product->id])->first();
                if ($cartItem) {
                    $totalQuantity = $cartItem->quantity + $item['quantity'];
                } else {
                    $totalQuantity = $item['quantity'];
                }
            }

            if ($product->quantity !== null && $product->quantity < $totalQuantity) {
                return response([
                    'message' => match ( $product->quantity ) {
                        0 => 'The product is out of stock',
                        1 => 'There is only 1 item left',
                        default => 'There are only ' . $product->quantity . ' items left',
                    }
                ], 422);
            }

            $products[] = $product;

            // Add to cart
            $cartItem = CartItem::where(['user_id' => $userId, 'product_id' => $product->id])->first();

            if ($cartItem) {
                $cartItem->quantity += $item['quantity'];
                $cartItem->update();
            } else {
                $data = [
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                ];
                CartItem::create($data);
            }
        }
        return response()->json([
                'success' => true,
                'count' => Cart::getCartItemsCount(),
//            'products' =>  $products,
        ]);
    }

    public function remove(Request $request, Product $product)
    {
        $user = $request->user();
        if ($user) {
            $cartItem = CartItem::query()->where(['user_id' => $user->id, 'product_id' => $product->id])->first();
            if ($cartItem) {
                $cartItem->delete();
            }

            return response([
                'count' => Cart::getCartItemsCount(),
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            foreach ($cartItems as $i => &$item) {
                if ($item['product_id'] === $product->id) {
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response(['count' => Cart::getCountFromItems($cartItems)]);
        }
    }

    public function updateQuantity(Request $request, Product $product)
    {
        $quantity = (int)$request->post('quantity');

        if ($product->quantity !== null && $product->quantity < $quantity) {
            return response([
                'message' => match ( $product->quantity ) {
                    0 => 'The product is out of stock',
                    1 => 'There is only 1 item left',
                    default => 'There are only ' . $product->quantity . ' items left',
                }
            ], 422);
        }

        $user = $request->user();
        if ($user) {
            CartItem::where(['user_id' => $request->user()->id, 'product_id' => $product->id])->update(['quantity' => $quantity]);

            return response([
                'count' => Cart::getCartItemsCount(),
            ]);
        } else {
            $cartItems = json_decode($request->cookie('cart_items', '[]'), true);
            foreach ($cartItems as &$item) {
                if ($item['product_id'] === $product->id) {
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30);

            return response(['count' => Cart::getCountFromItems($cartItems)]);
        }
    }
}
