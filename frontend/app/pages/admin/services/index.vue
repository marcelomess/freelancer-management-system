<template>
  <div>
    <NuxtLayout name="admin">
      <div>
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Serviços</h2>

        <!-- Tabela de Serviços -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Serviço
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Freelancer
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Categoria
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Preço
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="service in services" :key="service.id">
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ service.title }}</div>
                  <div class="text-sm text-gray-500">{{ service.description?.substring(0, 60) }}...</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ service.freelancer?.user?.name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ service.category?.name }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  R$ {{ service.price }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-green-100 text-green-800': service.is_active,
                      'bg-red-100 text-red-800': !service.is_active
                    }"
                  >
                    {{ service.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <NuxtLink
                    :to="`/services/${service.id}`"
                    target="_blank"
                    class="text-indigo-600 hover:text-indigo-900"
                  >
                    Ver
                  </NuxtLink>
                  <button
                    @click="confirmDelete(service)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Excluir
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="loading" class="text-center py-12">
            <p class="text-gray-600">Carregando serviços...</p>
          </div>

          <div v-else-if="services.length === 0" class="text-center py-12">
            <p class="text-gray-600">Nenhum serviço cadastrado.</p>
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
              Tem certeza que deseja excluir o serviço <strong>{{ serviceToDelete?.title }}</strong>?
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

const { getAllServices, deleteService } = useAdmin()
const { addNotification } = useNotifications()

const services = ref<any[]>([])
const loading = ref(true)
const showDeleteModal = ref(false)
const serviceToDelete = ref<any>(null)

const loadServices = async () => {
  loading.value = true
  try {
    const response = await getAllServices()
    console.log('Response from API:', response)
    if (response && response.success && response.data) {
      services.value = response.data
    } else if (Array.isArray(response)) {
      services.value = response
    }
  } catch (error: any) {
    console.error('Error loading services:', error)
    addNotification(error.message || 'Erro ao carregar serviços', 'error')
  } finally {
    loading.value = false
  }
}

const confirmDelete = (service: any) => {
  serviceToDelete.value = service
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!serviceToDelete.value) return

  try {
    const response = await deleteService(serviceToDelete.value.id)
    if (response.success) {
      addNotification('Serviço excluído com sucesso', 'success')
      services.value = services.value.filter(s => s.id !== serviceToDelete.value.id)
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao excluir serviço', 'error')
  } finally {
    showDeleteModal.value = false
    serviceToDelete.value = null
  }
}

onMounted(() => {
  loadServices()
})
</script>
