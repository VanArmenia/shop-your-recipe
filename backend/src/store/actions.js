import axiosClient from "../axios";

export function getCurrentUser({commit}, data) {
  return axiosClient.get('/user', data)
    .then(({data}) => {
      commit('setUser', data);
      return data;
    })
}

export function login({commit}, data) {
  return axiosClient.post('/login', data)
    .then(({data}) => {
      commit('setUser', data.user);
      commit('setToken', data.token)
      return data;
    })
}

export function logout({commit}) {
  return axiosClient.post('/logout')
    .then((response) => {
      commit('setToken', null)

      return response;
    })
}

export function getCountries({commit}) {
  return axiosClient.get('countries')
    .then(({data}) => {
      commit('setCountries', data)
    })
}

export function getOrders({commit, state}, {url = null, search = '', per_page, sort_field, sort_direction} = {}) {
  commit('setOrders', [true])
  url = url || '/orders'
  const params = {
    per_page: state.orders.limit,
  }
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction
    }
  })
    .then((response) => {
      commit('setOrders', [false, response.data])
    })
    .catch(() => {
      commit('setOrders', [false])
    })
}

export function getOrder({commit}, id) {
  return axiosClient.get(`/orders/${id}`)
}

export function getProducts({commit, state}, {url = null, search = '', per_page, sort_field, sort_direction} = {}) {
  commit('setProducts', [true])
  url = url || '/products'
  const params = {
    per_page: state.products.limit,
  }
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction
    }
  })
    .then((response) => {
      commit('setProducts', [false, response.data])
    })
    .catch(() => {
      commit('setProducts', [false])
    })
}

export function getManufacturers({commit}) {
  return axiosClient.get('manufacturers')
    .then(({data}) => {
      commit('setManufacturers', data)
    })
}

export function getRecipes({commit, state}, {url = null, search = '', per_page, sort_field, sort_direction} = {}) {
  commit('setRecipes', [true])
  url = url || '/recipes'
  const params = {
    per_page: state.recipes.limit,
  }
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction
    }
  })
    .then((response) => {
      commit('setRecipes', [false, response.data])
    })
    .catch(() => {
      commit('setRecipes', [false])
    })
}

export function getProduct({commit}, id) {
  return axiosClient.get(`/products/${id}`)
}

export function getRecipe({commit}, id) {
  return axiosClient.get(`/recipes/${id}`)
}

export function createProduct({commit}, product) {
  if (product.images && product.images.length) {
    const form = new FormData();
    form.append('title', product.title);
    product.images.forEach(im => form.append('images[]', im))
    form.append('description', product.description || '');
    form.append('manufacturer_id', product.manufacturer_id);
    form.append('allergens', product.allergens || '');
    form.append('composition', product.composition || '');
    form.append('storing', product.storing || '');
    form.append('nutritional', product.nutritional || '');
    form.append('published', product.published ? 1 : 0);
    form.append('price', product.price);
    form.append('category_id', product.category_id);
    product = form;
  }
  return axiosClient.post('/products', product)
}

export function createManufacturer({commit}, manufacturer) {
  const form = new FormData();
  form.append('name', manufacturer.name);
  form.append('description', manufacturer.description || '');
  manufacturer = form;

  return axiosClient.post('/manufacturers', manufacturer)
}

export function createCategory({commit}, category) {
  const form = new FormData();
  form.append('name', category.name);
  form.append('description', category.description || '');
  form.append('parent_id', category.parent_id || null);
  category = form;

  console.log(form.get('parent_id'))

  return axiosClient.post('/categories', category)
}

export function createRecipe({commit}, recipe) {
  if (recipe.images && recipe.images.length) {
    const form = new FormData();
    form.append('name', recipe.name);
    recipe.images.forEach(im => form.append('images[]', im))
    form.append('description', recipe.description || '');
    form.append('prep_time', recipe.prep_time);
    form.append('category_id', recipe.category_id);
    recipe = form;
    console.log(form.get('category'))
    console.log(form.get('prep_time'))
  }
  return axiosClient.post('/recipes', recipe)
}

export function updateProduct({commit}, product) {
  const id = product.id
  if (product.images && product.images.length) {
    const form = new FormData();
    form.append('id', product.id);
    form.append('title', product.title);
    product.images.forEach(im => form.append('images[]', im))
    if (product.deletedImages) {
      product.deletedImages.forEach(im => form.append('deleted_images[]', im))
    }
    form.append('description', product.description || '');
    form.append('manufacturer_id', product.manufacturer_id);
    form.append('allergens', product.allergens || '');
    form.append('composition', product.composition || '');
    form.append('storing', product.storing || '');
    form.append('nutritional', product.nutritional || '');
    form.append('published', product.published ? 1 : 0);
    form.append('price', product.price);
    form.append('category_id', product.category_id);
    form.append('_method', 'PUT');
    product = form;
  } else {
    product._method = 'PUT'
  }
  return axiosClient.post(`/products/${id}`, product)
}

export function updateRecipe({commit}, recipe) {
  const id = recipe.id
  if (recipe.images && recipe.images.length) {
    const form = new FormData();
    form.append('name', recipe.name);
    recipe.images.forEach(im => form.append('images[]', im))
    if (recipe.deletedImages) {
      recipe.deletedImages.forEach(im => form.append('deleted_images[]', im))
    }
    form.append('description', recipe.description || '');
    form.append('prep_time', recipe.prep_time);
    form.append('category_id', recipe.category_id); // Use category_id, not category name
    form.append('_method', 'PUT');
    recipe = form;

    // ✅ Append ingredients
    recipe.ingredients.forEach((ingredient, i) => {
      form.append(`ingredients[${i}][name]`, ingredient.name);
      if (ingredient.measurement) {
        form.append(`ingredients[${i}][measurement]`, ingredient.measurement);
      }
    });

    console.log(form.get('prep_time'))
    console.log(form.get('category_id'))
  } else {
    recipe._method = 'PUT'
  }
  return axiosClient.post(`/recipes/${id}`, recipe)
}

export function deleteProduct({commit}, id) {
  return axiosClient.delete(`/products/${id}`)
}

export function deleteRecipe({commit}, id) {
  return axiosClient.delete(`/recipes/${id}`)
}

export function getUsers({commit, state}, {url = null, search = '', per_page, sort_field, sort_direction} = {}) {
  commit('setUsers', [true])
  url = url || '/users'
  const params = {
    per_page: state.users.limit,
  }
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction
    }
  })
    .then((response) => {
      commit('setUsers', [false, response.data])
    })
    .catch(() => {
      commit('setUsers', [false])
    })
}

export function createUser({commit}, user) {
  return axiosClient.post('/users', user)
}

export function updateUser({commit}, user) {
  return axiosClient.put(`/users/${user.id}`, user)
}

export function getCustomers({commit, state}, {url = null, search = '', per_page, sort_field, sort_direction} = {}) {
  commit('setCustomers', [true])
  url = url || '/customers'
  const params = {
    per_page: state.customers.limit,
  }
  return axiosClient.get(url, {
    params: {
      ...params,
      search, per_page, sort_field, sort_direction
    }
  })
    .then((response) => {
      commit('setCustomers', [false, response.data])
    })
    .catch(() => {
      commit('setCustomers', [false])
    })
}

export function getCustomer({commit}, id) {
  return axiosClient.get(`/customers/${id}`)
}

export function createCustomer({commit}, customer) {
  return axiosClient.post('/customers', customer)
}

export function updateCustomer({commit}, customer) {
  return axiosClient.put(`/customers/${customer.id}`, customer)
}

export function deleteCustomer({commit}, customer) {
  return axiosClient.delete(`/customers/${customer.id}`)
}

