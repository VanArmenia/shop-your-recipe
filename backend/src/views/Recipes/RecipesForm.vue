<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <div class="relative">
    <div class="flex items-center justify-between mb-3">
      <h1 class="text-3xl font-semibold">{{
          recipe.id ? `Update recipe: "${recipe.name}"` : 'Create new Recipe'
        }}</h1>
    </div>
    <Spinner v-if="loading"
             class="p-5 z-[100] absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center"/>
    <form v-if="!loading" @submit.prevent="onSubmit">
      <div class="bg-white px-4 pt-5 pb-4 z-50 grid grid-cols-3 gap-3">
        <div class="col-span-1 sm:col-span-2 order-2 md:order-1 ">
          <CustomInput class="mb-2" v-model="recipe.name" placeholder="Recipe Title"/>
          <CustomInput type="richtext" class="mb-2" v-model="recipe.description" label="Description"/>
          <CustomInput type="text" class="mb-2" v-model="recipe.prep_time" label="Prep time"/>
          <div>
            <label class="text-gray-500 text-sm">Categories</label>
            <div class="mt-1 flex rounded-md">
              <select name="category_id"
                      class="inline-flex items-center px-3 rounded-md border border-gray-300 text-gray-500 text-sm mb2 h-10 w-full "
                      v-model="recipe.category_id">
                <option disabled value="">Select a Category:</option>
                <option v-for="option in categories" :key="option.id" :value="option.id">
                  {{ option.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-span-1 order-1 md:order-2">
          <image-preview v-model="recipe.images"
                         v-model:deleted-images="recipe.deleted_images"
                         :images="recipe.images"
          />
        </div>
      </div>
      <footer class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="submit"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                          text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save
        </button>
        <button type="button"
                @click="onSubmit($event, true)"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                          text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          Save and Close
        </button>
        <router-link :to="{name: 'app.recipes'}"
                     class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue'
import CustomInput from "../../components/core/CustomInput.vue";
import store from "../../store/index.js";
import Spinner from "../../components/core/Spinner.vue";
import {useRoute, useRouter} from "vue-router";
import ImagePreview from "../../components/core/ImagePreview.vue";
import axiosClient from "../../axios";

const recipe = ref({
  id: null,
  name: null,
  images: [],
  deleted_images: [],
  description: '',
  prep_time: null,
  category_id: null,
})

const loading = ref(false)

const router = useRouter();
const route = useRoute();
const categories = ref([]);
const error = ref(null);

onMounted(() => {
  fetchCategories(); // Fetching categories
  if (route.params.id) {
    loading.value = true
    store.dispatch('getRecipe', route.params.id)
      .then(({data}) => {
        recipe.value = data
        loading.value = false
      })
  }
})

function onSubmit(event, close = false) {
  loading.value = true
  if (recipe.value.id) {
    store.dispatch('updateRecipe', recipe.value)
      .then(response => {
        recipe.value = response.data
        loading.value = false;
        if (response.status === 200) {
          store.commit('showToast', 'Recipe was successfully updated')
          store.dispatch('getRecipes')
          if (close) {
            router.push({name: 'app.recipes'})
          }
        }
      })
  } else {
    store.dispatch('createRecipe', recipe.value)
      .then(response => {
        recipe.value = response.data
        loading.value = false;
        if (response.status === 201) {
          store.commit('showToast', 'Recipe was successfully created')
          store.dispatch('getRecipes')
          if (close) {
            router.push({name: 'app.recipes'})
          } else {
            recipe.value = response.data
            router.push({name: 'app.recipes.edit', params: {id: response.data.id}})
          }
        }
      })
      .catch(err => {
        loading.value = false;
      })
  }
}
  const fetchCategories = async () => {
    error.value = null;
    try {
      const response = await axiosClient.get('/recipe-categories');
      categories.value = response.data.data;
    } catch (err) {
      error.value = 'Failed to fetch categories';
      console.error(err);
    }
  };
</script>
<style scoped>

.ck-editor__editable_inline:not(.ck-comment__input *) {
  height: 50px !important;
  overflow-y: auto;
}
</style>
