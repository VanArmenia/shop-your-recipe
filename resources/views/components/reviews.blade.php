{{--Reviews--}}
<div class="border-t-2">
    <div x-data="reviewHandler()">
        <h3 class="p-4 text-lg">Reviews</h3>
        @if(auth()->check())
            <form @submit.prevent="submitReview">
                <label for="rating">Rating:</label>
                <select name="rating" x-model="rating" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>

                <label for="review_text">Review:</label>
                <textarea name="review_text" x-model="reviewText" rows="3"></textarea>

                <button
                    type="submit"
                    class="btn-primary py-4 text-lg flex justify-center min-w-0 w-48 m-6">
                    Submit Review
                </button>
            </form>
        @else
            <p>Please <a href="{{ route('login') }}">log in</a> to submit a review.</p>
        @endif
        <div>
            <template x-for="review in reviews" :key="review.id">
                <div class="review mb-4 p-2 border-b">
                    <div class="flex w-1/2 items-center mb-2">
                        <!-- Conditionally render the avatar -->
                        <template x-if="review.user.customer.avatar">
                            <img :src="'/storage/' + review.user.customer.avatar" alt="User Avatar" class="w-16 h-16 rounded-full">
                        </template>
                        <template x-if="!review.user.customer.avatar">
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-20 h-20 rounded-full">
                        </template>
                        <p class="font-bold p-4" x-text="review.user.name"></p>
                    </div>

                    <div class="flex items-center">
                        <template x-for="n in review.rating" :key="n">
                            <span class="start-icon text-l m-1">★</span> <!-- Star icon -->
                        </template>
                    </div>
                    <p x-text="review.review_text"></p>
                    <p class="text-sm text-gray-500" x-text="new Date(review.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })"></p>
                </div>
            </template>
        </div>
    </div>
</div>
