<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard Cliente</h1>
      <p class="text-gray-600 mt-2">Bem-vindo, {{ user?.name }}</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Tickets Abertos</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.openTickets }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Em Andamento</h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats.inProgress }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Concluídos</h3>
        <p class="text-3xl font-bold text-gray-600">{{ stats.completed }}</p>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900">Meus Tickets</h2>
        <NuxtLink 
          to="/services"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm"
        >
          Explorar Serviços
        </NuxtLink>
      </div>

      <div v-if="tickets.length > 0" class="space-y-4">
        <div 
          v-for="ticket in tickets" 
          :key="ticket.id"
          class="border rounded-lg p-4 hover:shadow-md transition"
        >
          <div class="flex items-start justify-between mb-3">
            <div>
              <h3 class="font-semibold text-gray-900">{{ ticket.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ ticket.description }}</p>
            </div>
            <span 
              class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap"
              :class="{
                'bg-green-100 text-green-700': ticket.status === 'open',
                'bg-blue-100 text-blue-700': ticket.status === 'in_progress',
                'bg-gray-100 text-gray-700': ticket.status === 'completed'
              }"
            >
              {{ statusLabel(ticket.status) }}
            </span>
          </div>

          <div class="flex items-center justify-between text-sm">
            <div class="text-gray-600">
              <span v-if="ticket.service">Serviço: {{ ticket.service.title }}</span>
              <span v-if="ticket.freelancer" class="ml-4">
                Freelancer: {{ ticket.freelancer.name }}
              </span>
            </div>
            <NuxtLink 
              :to="`/tickets/${ticket.id}`"
              class="text-indigo-600 hover:text-indigo-700"
            >
              Ver Detalhes →
            </NuxtLink>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-600 mb-4">Você ainda não tem tickets</p>
        <NuxtLink 
          to="/services"
          class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700"
        >
          Explorar Serviços
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Ticket } from '@/types'

definePageMeta({
  layout: 'default',
  middleware: 'auth'
})

const { user } = useAuth()
const { fetchTickets } = useTickets()

const tickets = ref<Ticket[]>([])

const stats = computed(() => ({
  openTickets: tickets.value.filter(t => t.status === 'open').length,
  inProgress: tickets.value.filter(t => t.status === 'in_progress').length,
  completed: tickets.value.filter(t => t.status === 'completed').length
}))

const statusLabel = (status: string) => {
  const labels: Record<string, string> = {
    open: 'Aberto',
    in_progress: 'Em Andamento',
    completed: 'Concluído',
    cancelled: 'Cancelado'
  }
  return labels[status] || status
}

const loadTickets = async () => {
  try {
    const response = await fetchTickets()
    tickets.value = response.data
  } catch (error) {
    console.error('Erro ao carregar tickets:', error)
  }
}

onMounted(() => {
  loadTickets()
})
</script>
