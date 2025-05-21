import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'
import {get, post} from "./http.js";

Alpine.plugin(collapse)

window.Alpine = Alpine;

document.addEventListener("alpine:init", async () => {

  Alpine.data("toast", () => ({
    visible: false,
    delay: 5000,
    percent: 0,
    interval: null,
    timeout: null,
    message: null,
    type: null,
    close() {
      this.visible = false;
      clearInterval(this.interval);
    },
    show(message, type = 'success') {
      this.visible = true;
      this.message = message;
      this.type = type;

      if (this.interval) {
        clearInterval(this.interval);
        this.interval = null;
      }
      if (this.timeout) {
        clearTimeout(this.timeout);
        this.timeout = null;
      }

      this.timeout = setTimeout(() => {
        this.visible = false;
        this.timeout = null;
      }, this.delay);
      const startDate = Date.now();
      const futureDate = Date.now() + this.delay;
      this.interval = setInterval(() => {
        const date = Date.now();
        this.percent = ((date - startDate) * 100) / (futureDate - startDate);
        if (this.percent >= 100) {
          clearInterval(this.interval);
          this.interval = null;
        }
      }, 30);
    },
  }));

  Alpine.data("productItem", (product) => {
    return {
      product,
      addToCart(quantity = 1) {
        post(this.product.addToCartUrl, {quantity})
          .then(result => {
            this.$dispatch('cart-change', {count: result.count})
            this.$dispatch("notify", {
              message: "The item was added into the cart",
            });
          })
          .catch(response => {
            console.log(response);
            this.$dispatch('notify', {
              message: response.message || 'Server Error. Please try again.',
              type: 'error'
            })
          })
      },
      removeItemFromCart() {
        post(this.product.removeUrl)
          .then(result => {
            this.$dispatch("notify", {
              message: "The item was removed from cart",
            });
            this.$dispatch('cart-change', {count: result.count})
            this.cartItems = this.cartItems.filter(p => p.id !== product.id)
          })
      },
      changeQuantity() {
        post(this.product.updateQuantityUrl, {quantity: product.quantity})
          .then(result => {
            this.$dispatch('cart-change', {count: result.count})
            this.$dispatch("notify", {
              message: "The item quantity was updated",
            });
          })
          .catch(response => {
            this.$dispatch('notify', {
              message: response.message || 'Server Error. Please try again.',
              type: 'error'
            })
          })
      },
      reviewHandler() {
      return {
        rating: 1,           // Default rating value
        reviewText: '',      // Default review text
        message: '',         // To display success or error messages
        reviews: [],         // Array to hold reviews dynamically

        init() {
          // Initialize reviews from the backend
          this.fetchReviews();
        },

        fetchReviews() {
          // Fetch the reviews initially (use a GET request)
          fetch(this.product.fetchReviews)
            .then(response => response.json())
            .then(data => {
              this.reviews = data.reviews; // Load initial reviews into Alpine
              this.reviews = this.reviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Descending
            })
            .catch(error => {
              console.error("Error fetching reviews:", error);
            });
        },

        // Method to handle the review submission
        submitReview() {
          // Post data to the server
          post(this.product.addReview, {
            rating: this.rating,
            review_text: this.reviewText
          })
            .then(result => {
              // Dispatch an event for any global updates if needed
              this.$dispatch("review-submitted", {
                message: "Review submitted successfully!",
              });

              // Add the new review to the reviews array to update the UI
              this.reviews.unshift({
                rating: this.rating,
                review_text: this.reviewText,
                created_at: new Date().toISOString(),  // Assuming you'll show the time
                user: { name: 'You' }  // Placeholder for the authenticated user
              });

              this.fetchReviews();

              // Optionally clear the form fields
              this.rating = 1;
              this.reviewText = '';
            })
            .catch(error => {
              // Handle error and show message
              console.log(error);
              this.message = error.message || 'Server Error. Please try again.';
            });
        }
      };
    }

  };
  });
  Alpine.data("recipeItem", (recipe) => {
    return {
      recipe,
      reviewHandler() {
        return {
          rating: 0,           // Default rating value
          reviewText: '',      // Default review text
          message: '',         // To display success or error messages
          reviews: [],         // Array to hold reviews dynamically
          hoverRating: 0,      // For controlling style on hover

          init() {
            // Initialize reviews from the backend
            this.fetchReviews();
          },

          fetchReviews() {
            // Fetch the reviews initially (use a GET request)
            fetch(this.recipe.fetchReviews)
              .then(response => response.json())
              .then(data => {
                this.reviews = data.reviews; // Load initial reviews into Alpine
                this.reviews = this.reviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Descending
              })
              .catch(error => {
                console.error("Error fetching reviews:", error);
              });
          },

          // Method to handle the review submission
          submitReview() {
            // Post data to the server
            post(this.recipe.addReview, {
              rating: this.rating,
              review_text: this.reviewText
            })
              .then(result => {
                // Dispatch an event for any global updates if needed
                this.$dispatch("review-submitted", {
                  message: "Review submitted successfully!",
                });

                // Add the new review to the reviews array to update the UI
                this.reviews.unshift({
                  rating: this.rating,
                  review_text: this.reviewText,
                  created_at: new Date().toISOString(),  // Assuming you'll show the time
                  user: { name: '' }  // Placeholder for the authenticated user
                });

                this.fetchReviews();

                // Optionally clear the form fields
                this.rating = 1;
                this.reviewText = '';
              })
              .catch(error => {
                // Handle error and show message
                console.log(error);
                this.message = error.message || 'Server Error. Please try again.';
              });
          }
        };
      }

    };
  });
});


Alpine.start();
