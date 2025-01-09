<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">Recipes</h1>
    <router-link :to="{name: 'app.recipes.create'}"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      Add new Recipe
    </router-link>
  </div>
  <RecipesTable/>
  <RecipesModal v-model="showRecipeModal" :recipe="recipeModel" @close="onModalClose"/>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import store from "../../store";
import RecipesModal from "./RecipesModal.vue";
import RecipesTable from "./RecipesTable.vue";

const DEFAULT_RECIPE = {
  id: '',
  name: '',
  description: '',
  image: '',
  category_id: '',
  manufacturer_id: '',
}

const recipes = computed(() => store.state.recipes);

const recipeModel = ref({...DEFAULT_RECIPE})
const showRecipeModal = ref(false);

function showAddNewModal() {
  showRecipeModal.value = true
}

function editRecipe(p) {
  store.dispatch('getRecipe', p.id)
    .then(({data}) => {
      recipeModel.value = data
      showAddNewModal();
    })
}

function onModalClose() {
  recipeModel.value = {...DEFAULT_RECIPE}
}
</script>

<style scoped>

</style>
