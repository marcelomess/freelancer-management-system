<template>
  <div>
    <NuxtLayout name="admin">
      <div>
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold text-gray-900">Gerenciar Usuários</h2>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
              v-model="filters.search"
              type="text"
              placeholder="Buscar por nome ou email..."
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
            <select
              v-model="filters.role"
              class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">Todos os tipos</option>
              <option value="admin">Admin</option>
              <option value="freelancer">Freelancer</option>
              <option value="client">Cliente</option>
            </select>
            <button
              @click="loadUsers"
              class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Filtrar
            </button>
          </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usuário
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cadastro
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 font-medium text-sm">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ user.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-purple-100 text-purple-800': user.user_type === 'admin',
                      'bg-green-100 text-green-800': user.user_type === 'freelancer',
                      'bg-blue-100 text-blue-800': user.user_type === 'client'
                    }"
                  >
                    {{ user.user_type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button
                    @click="confirmDelete(user)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Excluir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="loading" class="text-center py-12">
            <p class="text-gray-600">Carregando usuários...</p>
          </div>

          <div v-else-if="users.length === 0" class="text-center py-12">
            <p class="text-gray-600">Nenhum usuário encontrado.</p>
          </div>
        </div>

        <!-- Modal de Confirmação -->
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
          @click="showDeleteModal = false"
        >
          <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmar Exclusão</h3>
            <p class="text-gray-600 mb-6">
              Tem certeza que deseja excluir o usuário <strong>{{ userToDelete?.name }}</strong>?
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

const { getUsers, deleteUser } = useAdmin()
const { addNotification } = useNotifications()

const users = ref<any[]>([])
const loading = ref(true)
const showDeleteModal = ref(false)
const userToDelete = ref<any>(null)

const filters = reactive({
  search: '',
  role: ''
})

const loadUsers = async () => {
  loading.value = true
  try {
    const response = await getUsers(filters)
    console.log('Response from API:', response)
    if (response && response.success && response.data) {
      users.value = response.data
    } else if (Array.isArray(response)) {
      users.value = response
    } else {
      console.error('Unexpected response format:', response)
    }
  } catch (error: any) {
    console.error('Error loading users:', error)
    addNotification(error.message || 'Erro ao carregar usuários', 'error')
  } finally {
    loading.value = false
  }
}

const confirmDelete = (user: any) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!userToDelete.value) return

  try {
    const response = await deleteUser(userToDelete.value.id)
    if (response.success) {
      addNotification('Usuário excluído com sucesso', 'success')
      users.value = users.value.filter(u => u.id !== userToDelete.value.id)
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao excluir usuário', 'error')
  } finally {
    showDeleteModal.value = false
    userToDelete.value = null
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

onMounted(() => {
  loadUsers()
})
</script>
