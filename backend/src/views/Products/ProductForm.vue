<!-- This example requires Tailwind CSS v2.0+ -->
<template>
  <div class="relative">
    <div class="flex items-center justify-between mb-3">
      <h1 class="text-3xl font-semibold">{{
          product.id ? `Update product: "${product.title}"` : 'Create new Product'
        }}</h1>
    </div>
    <Spinner v-if="loading"
             class="p-5 z-[100] absolute left-0 top-0 bg-white right-0 bottom-0 flex items-center justify-center"/>
    <form v-if="!loading" @submit.prevent="onSubmit">
      <div class="bg-white px-4 pt-5 pb-4 z-50 grid grid-cols-3 gap-3">
        <div class="col-span-1 sm:col-span-2 order-2 md:order-1 ">
          <CustomInput class="mb-2" v-model="product.title" placeholder="Product Title"/>
          <CustomInput type="richtext" class="mb-2" v-model="product.description" label="Description"/>
          <CustomInput type="richtext" class="mb-2" v-model="product.allergens" label="Allergens"/>
          <CustomInput type="richtext" class="mb-2" v-model="product.composition" label="Composition"/>
          <CustomInput type="richtext" class="mb-2" v-model="product.storing" label="Storing"/>
          <CustomInput type="richtext" class="mb-2" v-model="product.nutritional" label="Nutritional"/>
          <CustomInput type="select" class="mb-2" v-model.number="product.manufacturer_id" :selectOptions="manufacturers" name="manufacturer_id" label="Manufacturer"/>
          <button type="button"
                  @click="showManufacturerModal = true"
                  class="my-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm
                          text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
            Add Manufacturer
          </button>
          <CustomInput type="select" class="mb-2" v-model.number="product.category_id" :selectOptions="categories" name="category_id" label="Category"/>
          <button type="button"
                  @click="showCategoryModal = true"
                  class="my-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm
                          text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
            Add Category
          </button>
          <CustomInput type="number" class="mb-2" v-model="product.price" placeholder="Price" prepend="$"/>
          <CustomInput type="number" class="mb-2" v-model="product.quantity" placeholder="Quantity"/>
          <CustomInput type="checkbox" class="mb-2" v-model="product.published" label="Published"/>
        </div>
        <div class="col-span-1 order-1 md:order-2">
          <image-preview v-model="product.images"
                         v-model:deleted-images="product.deleted_images"
                         :images="product.images"
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
        <router-link :to="{name: 'app.products'}"
                     class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
          Cancel
        </router-link>
      </footer>
    </form>
  </div>
  <ManufacturerModal v-model="showManufacturerModal" :manufacturer="manufacturerModel" @close="onManModalClose"/>
  <CategoryModal v-model="showCategoryModal" :category="categoryModel" @close="onCatModalClose"/>
</template>

<script setup>
import {onMounted, ref} from 'vue'
import CustomInput from "../../components/core/CustomInput.vue";
import store from "../../store/index.js";
import Spinner from "../../components/core/Spinner.vue";
import {useRoute, useRouter} from "vue-router";
import ImagePreview from "../../components/core/ImagePreview.vue";
import axiosClient from "../../axios";
import ManufacturerModal from "./ManufacturerModal.vue";
import CategoryModal from "./CategoryModal.vue";

const product = ref({
  id: null,
  title: null,
  images: [],
  deleted_images: [],
  description: '',
  manufacturer_id: null,
  allergens: '',
  composition: '',
  storing: '',
  nutritional: '',
  price: null,
  category_id: null,
  published: null
})

const DEFAULT_MANUFACTURER = {
  id: '',
  name: '',
  description: '',
}

const DEFAULT_CATEGORY = {
  id: '',
  name: '',
  description: '',
  parent_id: '',
}

const manufacturerModel = ref({...DEFAULT_MANUFACTURER})
const showManufacturerModal = ref(false);
const categoryModel = ref({...DEFAULT_CATEGORY})
const showCategoryModal = ref(false);

const loading = ref(false)

const router = useRouter();
const route = useRoute();
const categories = ref([]);
const manufacturers = ref([]);
const error = ref(null);

onMounted(() => {
  // console.log(product.value.category_id)
  fetchCategories(); // Fetching categories
  fetchManufacturers(); // Fetching manufacturers
  if (route.params.id) {
    loading.value = true
    store.dispatch('getProduct', route.params.id)
      .then(({data}) => {
        product.value = data
        loading.value = false
      })
  }
})

function onManModalClose() {
  manufacturerModel.value = {...DEFAULT_MANUFACTURER}
  fetchManufacturers();
}

function onCatModalClose() {
  categoryModel.value = {...DEFAULT_CATEGORY}
  fetchCategories();
}

const fetchCategories = async () => {
  error.value = null;
  try {
    const response = await axiosClient.get('/categories');
    categories.value = response.data.data;
  } catch (err) {
    error.value = 'Failed to fetch categories';
    console.error(err);
  }
};

const fetchManufacturers = async () => {
  error.value = null;
  try {
    const response = await axiosClient.get('/manufacturers');
    manufacturers.value = response.data.data;
  } catch (err) {
    error.value = 'Failed to fetch manufacturers';
    console.error(err);
  }
};

function onSubmit(event, close = false) {
  loading.value = true
  if (product.value.id) {
    store.dispatch('updateProduct', product.value)
      .then(response => {
        product.value = response.data
        loading.value = false;
        if (response.status === 200) {
          store.commit('showToast', 'Product was successfully updated')
          store.dispatch('getProducts')
          if (close) {
            router.push({name: 'app.products'})
          }
        }
      })
  } else {
    store.dispatch('createProduct', product.value)
      .then(response => {
        product.value = response.data
        loading.value = false;
        if (response.status === 201) {
          store.commit('showToast', 'Product was successfully created')
          store.dispatch('getProducts')
          if (close) {
            router.push({name: 'app.products'})
          } else {
            product.value = response.data
            router.push({name: 'app.products.edit', params: {id: response.data.id}})
          }
        }
      })
      .catch(err => {
        loading.value = false;
      })
  }
}
</script>
<style scoped>

.ck-editor__editable_inline:not(.ck-comment__input *) {
  height: 50px !important;
  overflow-y: auto;
}
</style>
