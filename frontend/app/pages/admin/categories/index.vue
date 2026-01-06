<template>
  <div>
    <NuxtLayout name="admin">
      <div>
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold text-gray-900">Gerenciar Categorias</h2>
          <button
            @click="openCreateModal"
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Nova Categoria
          </button>
        </div>

        <!-- Lista de Categorias -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="category in categories"
            :key="category.id"
            class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ category.name }}</h3>
                <p class="text-sm text-gray-600">{{ category.description || 'Sem descrição' }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
              <span class="text-sm text-gray-500">
                {{ category.services_count || 0 }} serviços
              </span>
              <div class="flex space-x-2">
                <button
                  @click="openEditModal(category)"
                  class="text-indigo-600 hover:text-indigo-800"
                >
                  Editar
                </button>
                <button
                  @click="confirmDelete(category)"
                  class="text-red-600 hover:text-red-800"
                >
                  Excluir
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="loading" class="text-center py-12">
          <p class="text-gray-600">Carregando categorias...</p>
        </div>

        <div v-else-if="categories.length === 0" class="text-center py-12">
          <p class="text-gray-600">Nenhuma categoria cadastrada.</p>
        </div>

        <!-- Modal Criar/Editar -->
        <div
          v-if="showModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          @click="closeModal"
        >
          <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">
              {{ editingCategory ? 'Editar Categoria' : 'Nova Categoria' }}
            </h3>
            
            <form @submit.prevent="handleSubmit">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Nome *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  placeholder="Ex: Desenvolvimento Web"
                >
              </div>

              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Descrição
                </label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                  placeholder="Descrição da categoria..."
                ></textarea>
              </div>

              <div class="flex justify-end space-x-4">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                  :disabled="submitting"
                >
                  {{ submitting ? 'Salvando...' : 'Salvar' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Modal de Confirmação de Exclusão -->
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          @click="showDeleteModal = false"
        >
          <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmar Exclusão</h3>
            <p class="text-gray-600 mb-6">
              Tem certeza que deseja excluir a categoria <strong>{{ categoryToDelete?.name }}</strong>?
              Esta ação não pode ser desfeita.
            </p>
            <div class="flex justify-end space-x-4">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button
                @click="handleDelete"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
              >
                Excluir
              </button>
            </div>
          </div>
        </div>
      </div>
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'admin'
})

const { getCategories, createCategory, updateCategory, deleteCategory } = useAdmin()
const { addNotification } = useNotifications()

const categories = ref<any[]>([])
const loading = ref(true)
const showModal = ref(false)
const showDeleteModal = ref(false)
const editingCategory = ref<any>(null)
const categoryToDelete = ref<any>(null)
const submitting = ref(false)

const form = reactive({
  name: '',
  description: ''
})

const loadCategories = async () => {
  loading.value = true
  try {
    const response = await getCategories()
    if (response.success) {
      categories.value = response.data
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao carregar categorias', 'error')
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingCategory.value = null
  form.name = ''
  form.description = ''
  showModal.value = true
}

const openEditModal = (category: any) => {
  editingCategory.value = category
  form.name = category.name
  form.description = category.description || ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingCategory.value = null
  form.name = ''
  form.description = ''
}

const handleSubmit = async () => {
  submitting.value = true
  try {
    let response
    if (editingCategory.value) {
      response = await updateCategory(editingCategory.value.id, form)
    } else {
      response = await createCategory(form)
    }

    if (response.success) {
      addNotification(
        editingCategory.value ? 'Categoria atualizada com sucesso' : 'Categoria criada com sucesso',
        'success'
      )
      closeModal()
      loadCategories()
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao salvar categoria', 'error')
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (category: any) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!categoryToDelete.value) return

  try {
    const response = await deleteCategory(categoryToDelete.value.id)
    if (response.success) {
      addNotification('Categoria excluída com sucesso', 'success')
      categories.value = categories.value.filter(c => c.id !== categoryToDelete.value.id)
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao excluir categoria', 'error')
  } finally {
    showDeleteModal.value = false
    categoryToDelete.value = null
  }
}

onMounted(() => {
  loadCategories()
})
</script>
