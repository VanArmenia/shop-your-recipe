<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select @change="getRecipes(null)" v-model="perPage"
                class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{recipes.total}} recipes</span>
      </div>
      <div>
        <input v-model="search" @change="getRecipes(null)"
               class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
               placeholder="Type to Search recipe">
      </div>
    </div>

    <div v-if="recipes.loading || !recipes.data.length">
      <Spinner v-if="recipes.loading"/>
      <p v-else class="text-center py-8 text-gray-700">
        There are no recipes
      </p>
    </div>
    <div v-else class="grid grid-cols-[70px,80px,1.5fr,1fr,3.5fr,80px,70px] gap-1 w-full border border-gray-200">
      <!-- Header Row -->
      <div class="font-bold p-2 border-b cursor-pointer" field="id" :sort-field="sortField" :sort-direction="sortDirection" @click="sortRecipes('id')">ID</div>
      <div class="font-bold p-2 border-b cursor-pointer">Image</div>
      <div class="font-bold p-2 border-b cursor-pointer" field="name" :sort-field="sortField" :sort-direction="sortDirection"
           @click="sortRecipes('name')">Name</div>
      <div class="font-bold p-2 border-b cursor-pointer" field="category" :sort-field="sortField" :sort-direction="sortDirection"
           @click="sortRecipes('category')">Category</div>
      <div class="font-bold p-2 border-b cursor-pointer">Description</div>
      <div class="font-bold p-2 border-b cursor-pointer" field="updated_at" :sort-field="sortField" :sort-direction="sortDirection"
           @click="sortRecipes('updated_at')">Updated</div>
      <div class="font-bold p-2 border-b cursor-pointer">Actions</div>

      <!-- Data Rows -->
      <div v-for="(recipe, index) in recipes.data" :key="index" class="contents">
        <!-- ID -->
        <div class="p-2 border-b max-w-[50px]">{{ recipe.id }}</div>

        <!-- Image -->
        <div class="p-2 border-b">
          <img v-if="recipe.image_url" class="w-16 h-16 object-cover" :src="recipe.image_url" :alt="recipe.name" />
          <img v-else class="w-16 h-16 object-cover" src="../../assets/noimage.jpg" />
        </div>

        <!-- Name -->
        <div class="p-2 border-b max-w-[200px] overflow-hidden text-ellipsis">
          {{ recipe.name }}
        </div>

        <!-- Category -->
        <div class="p-2 border-b max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">
          {{ recipe.category }}
        </div>

        <!-- Description -->
        <div class="p-2 border-b max-w-[400px] h-[100px] overflow-y-auto whitespace-normal break-words">
          {{ recipe.description }}
        </div>

        <!-- Last Updated At -->
        <div class="p-2 border-b text-sm font-bold">{{ formatDate(recipe.updated_at) }}</div>

        <!-- Actions -->
        <div class="p-2 border-b flex flex-col justify-between">
          <!-- Actions Menu -->
          <Menu as="div" class="relative inline-block text-left">
            <div>
              <MenuButton
                class="inline-flex items-center justify-center w-full justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
              >
                <DotsVerticalIcon
                  class="h-5 w-5 text-indigo-500"
                  aria-hidden="true"/>
              </MenuButton>
            </div>

            <transition
              enter-active-class="transition duration-100 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-75 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <MenuItems
                class="absolute pointer-events-auto z-50 right-12 -top-2 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <div class="px-1 py-1">
                  <MenuItem v-slot="{ active }">
                    <router-link
                      :to="{name: 'app.recipes.edit', params: {id: recipe.id}}"
                      :class="[
                        active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                    >
                      <PencilIcon
                        :active="active"
                        class="mr-2 h-5 w-5 text-indigo-400"
                        aria-hidden="true"
                      />
                      Edit
                    </router-link>
                  </MenuItem>
                  <MenuItem v-slot="{ active }">
                    <button
                      :class="[
                        active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                      @click="deleteRecipe(recipe)"
                    >
                      <TrashIcon
                        :active="active"
                        class="mr-2 h-5 w-5 text-indigo-400"
                        aria-hidden="true"
                      />
                      Delete
                    </button>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>


    <div v-if="!recipes.loading" class="flex justify-between items-center mt-5">
      <div v-if="recipes.data.length">
        Showing from {{ recipes.from }} to {{ recipes.to }}
      </div>
      <nav
        v-if="recipes.total > recipes.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
        <a
          v-for="(link, i) of recipes.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="getForPage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md' : '',
              i === recipes.links.length - 1 ? 'rounded-r-md' : '',
              !link.url ? ' bg-gray-100 text-gray-700': ''
            ]"
          v-html="link.label"
        >
        </a>
      </nav>
    </div>
  </div>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import store from "../../store";
import Spinner from "../../components/core/Spinner.vue";
import {RECIPES_PER_PAGE} from "../../constants";
import TableHeaderCell from "../../components/core/Table/TableHeaderCell.vue";
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {DotsVerticalIcon, PencilIcon, TrashIcon} from '@heroicons/vue/outline'
import RecipesModal from "./RecipesModal.vue";

const perPage = ref(RECIPES_PER_PAGE);
const search = ref('');
const recipes = computed(() => store.state.recipes);
const sortField = ref('updated_at');
const sortDirection = ref('desc')

const recipe = ref({})
const showRecipesModal = ref(false);

const emit = defineEmits(['clickEdit'])

onMounted(() => {
  getRecipes();
})

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getRecipes(link.url)
}

function getRecipes(url = null) {
  store.dispatch("getRecipes", {
    url,
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value
  });
}

function sortRecipes(field) {
  if (field === sortField.value) {
    if (sortDirection.value === 'desc') {
      sortDirection.value = 'asc'
    } else {
      sortDirection.value = 'desc'
    }
  } else {
    sortField.value = field;
    sortDirection.value = 'asc'
  }

  getRecipes()
}

function showAddNewModal() {
  showRecipesModal.value = true
}

function deleteRecipe(recipe) {
  if (!confirm(`Are you sure you want to delete the recipe?`)) {
    return
  }
  store.dispatch('deleteRecipe', recipe.id)
    .then(res => {
      store.commit('showToast', 'Recipe was successfully deleted');
      store.dispatch('getRecipes')
    })
}

function formatDate(date) {
  const d = new Date(date);
  const day = String(d.getDate()).padStart(2, '0');
  const month = String(d.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const year = String(d.getFullYear()).slice(-2);
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

</script>

<style scoped>

</style>
