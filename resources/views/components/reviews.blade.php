{{--Reviews--}}
<div class="border-t-2">
    <div x-data="reviewHandler()">
        <h3 class="py-4 text-xl font-bold">Reviews</h3>
        @if(auth()->check())
            <div x-data="ratingComponent()" class="max-w-md mx-auto p-4 bg-white p-2">
                <form @submit.prevent="submitReview" class="space-y-4">

                    <!-- ⭐ Star Rating -->
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">My Rating:</label>
                        <div class="flex space-x-1">
                            <template x-for="i in 5">
                                <button type="button" @click="rating = i" @mouseenter="hoverRating = i" @mouseleave="hoverRating = 0">
                                    <svg
                                        :class="(hoverRating >= i || rating >= i) ? 'text-red-600' : 'text-gray-300'"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        class="w-8 h-8 cursor-pointer transition-colors duration-200"
                                    >
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.538 1.118l-3.39-2.46a1 1 0 00-1.175 0l-3.39 2.46c-.783.57-1.838-.196-1.538-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.39-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"
                                        />
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" :value="rating" required>
                    </div>

                    <!-- 💬 Review Text -->
                    <div>
                        <label class="block mb-1 font-medium text-gray-700">My Review:</label>
                        <textarea
                            name="review_text"
                            x-model="reviewText"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300"
                            placeholder="Write your review here..."
                            required
                        ></textarea>
                    </div>

                    <!-- ✅ Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
                    >
                        Submit Review
                    </button>

                </form>
            </div>

        @else
            <p>Please <a href="{{ route('login') }}">log in</a> to submit a review.</p>
        @endif
        <div class="mt-4">
            <template x-for="review in reviews" :key="review.id">
                <div class="review mb-4 p-2 border-b flex gap-3">
                    <div class="flex items-center mb-2 flex-col">
                        <!-- Conditionally render the avatar -->
                        <template x-if="review.user.customer.avatar">
                            <img :src="'/storage/' + review.user.customer.avatar" alt="User Avatar" class="w-14 h-14 rounded-full">
                        </template>
                        <template x-if="!review.user.customer.avatar">
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-14 h-14 rounded-full">
                        </template>
                        <p class="font-bold p-2" x-text="review.user.name"></p>
                    </div>

                    <div class="flex flex-col flex-1">
                        <div class="flex items-center">
                            <template x-for="n in review.rating" :key="n">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    class="w-4 h-4 cursor-pointer transition-colors duration-200 text-red-600"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.178c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.287 3.966c.3.922-.755 1.688-1.538 1.118l-3.39-2.46a1 1 0 00-1.175 0l-3.39 2.46c-.783.57-1.838-.196-1.538-1.118l1.287-3.966a1 1 0 00-.364-1.118l-3.39-2.46c-.783-.57-.38-1.81.588-1.81h4.178a1 1 0 00.95-.69l1.286-3.967z"
                                    />
                                </svg>
                            </template>
                        </div>
                        <div>
                            <p x-text="review.review_text" class="bg-white p-2 my-2 rounded-md"></p>
                            <p class="text-sm text-gray-500" x-text="new Date(review.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function ratingComponent() {
        return {
            rating: 0,
            hoverRating: 0,
            reviewText: '',
        };
    }
</script>

