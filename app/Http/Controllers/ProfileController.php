<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function view(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;
        $shippingAddress = $customer->shippingAddress ?: new CustomerAddress(['type' => AddressType::Shipping]);
        $billingAddress = $customer->billingAddress ?: new CustomerAddress(['type' => AddressType::Billing]);
        $countries = Country::query()->orderBy('name')->get();
        $categories = RecipeCategory::All();

        return view('profile.view', compact('customer', 'user', 'shippingAddress', 'billingAddress', 'countries','categories'));
    }

    public function recipes(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;

        $categories = RecipeCategory::All();

        return view('profile.recipes', compact('customer', 'user', 'categories'));
    }

    public function store(ProfileRequest $request)
    {
        $customerData = $request->validated();
        $shippingData = $customerData['shipping'];
        $billingData = $customerData['billing'];

        /** @var \App\Models\User $user */
        $user = $request->user();
        /** @var \App\Models\Customer $customer */
        $customer = $user->customer;

        // Handle avatar upload if exists
        if ($request->hasFile('avatar')) {
            // Delete the old avatar if it exists
            if ($customer->avatar) {
                Storage::delete('public/' . $customer->avatar);
            }
            $avatar = $request->file('avatar');
            $filename = time() . '_' . $avatar->getClientOriginalName();

            // Store the file in 'public/avatars' directory and keep it in the 'public' disk
            $path = $avatar->storeAs('avatars', $filename, 'public');

            // Update the customer avatar
            $customer->avatar = $path;
            $customer->update(); // Save immediately to avoid issues if the transaction fails
        }

        // Unset the 'avatar' field so it is not overwritten during customer update
        unset($customerData['avatar']);

        // Start the database transaction for customer data, shipping, and billing
        DB::beginTransaction();
        try {
            // Update customer information
            $customer->update($customerData);

            // Update shipping address
            if ($customer->shippingAddress) {
                $customer->shippingAddress->update($shippingData);
            } else {
                $shippingData['customer_id'] = $customer->user_id;
                $shippingData['type'] = AddressType::Shipping->value;
                CustomerAddress::create($shippingData);
            }

            // Update billing address
            if ($customer->billingAddress) {
                $customer->billingAddress->update($billingData);
            } else {
                $billingData['customer_id'] = $customer->user_id;
                $billingData['type'] = AddressType::Billing->value;
                CustomerAddress::create($billingData);
            }

            // Commit the transaction
            DB::commit();

            // Flash success message
            $request->session()->flash('flash_message', 'Profile was successfully updated.');
            return redirect()->route('profile');
        } catch (\Exception $e) {
            // Rollback the transaction in case of failure
            DB::rollBack();

            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }
    }


    public function passwordUpdate(PasswordUpdateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $passwordData = $request->validated();

        $user->password = Hash::make($passwordData['new_password']);
        $user->save();

        $request->session()->flash('flash_message', 'Your password was successfully updated.');

        return redirect()->route('profile');
    }
}
