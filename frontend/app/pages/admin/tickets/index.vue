<template>
  <div>
    <NuxtLayout name="admin">
      <div>
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Tickets</h2>

        <!-- Tabela de Tickets -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Título
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Cliente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Serviço
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Orçamento
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Data
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="ticket in tickets" :key="ticket.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  #{{ ticket.id }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ ticket.title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ ticket.client?.user?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ ticket.service?.title }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="{
                      'bg-yellow-100 text-yellow-800': ticket.status === 'open',
                      'bg-blue-100 text-blue-800': ticket.status === 'in_progress',
                      'bg-green-100 text-green-800': ticket.status === 'completed',
                      'bg-red-100 text-red-800': ticket.status === 'cancelled'
                    }"
                  >
                    {{ statusLabel(ticket.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  R$ {{ ticket.budget }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(ticket.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>

          <div v-if="loading" class="text-center py-12">
            <p class="text-gray-600">Carregando tickets...</p>
          </div>

          <div v-else-if="tickets.length === 0" class="text-center py-12">
            <p class="text-gray-600">Nenhum ticket encontrado.</p>
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

const { getAllTickets } = useAdmin()
const { addNotification } = useNotifications()

const tickets = ref<any[]>([])
const loading = ref(true)

const loadTickets = async () => {
  loading.value = true
  try {
    const response = await getAllTickets()
    if (response && response.success && response.data) {
      tickets.value = response.data
    } else if (Array.isArray(response)) {
      tickets.value = response
    }
  } catch (error: any) {
    console.error('Error loading tickets:', error)
    addNotification(error.message || 'Erro ao carregar tickets', 'error')
  } finally {
    loading.value = false
  }
}

const statusLabel = (status: string) => {
  const labels: Record<string, string> = {
    open: 'Aberto',
    in_progress: 'Em Andamento',
    completed: 'Concluído',
    cancelled: 'Cancelado'
  }
  return labels[status] || status
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

onMounted(() => {
  loadTickets()
})
</script>
