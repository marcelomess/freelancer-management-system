<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Dashboard Freelancer</h1>
      <p class="text-gray-600 mt-2">Bem-vindo, {{ user?.name }}</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Serviços Ativos</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ stats.services }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Tickets Abertos</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.tickets }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-600 text-sm font-medium mb-2">Avaliação Média</h3>
        <p class="text-3xl font-bold text-yellow-600">⭐ {{ stats.rating }}</p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Meus Serviços</h2>
          <button 
            @click="showCreateServiceModal = true"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm"
          >
            + Novo Serviço
          </button>
        </div>
        
        <div v-if="services.length > 0" class="space-y-3">
          <div 
            v-for="service in services" 
            :key="service.id"
            class="border-l-4 border-indigo-600 pl-4 py-2"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-gray-900">{{ service.title }}</p>
                <p class="text-sm text-gray-600">R$ {{ service.price }} • {{ service.category.name }}</p>
              </div>
              <NuxtLink 
                :to="`/services/${service.id}`"
                class="text-indigo-600 hover:text-indigo-700 text-sm"
              >
                Ver
              </NuxtLink>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-center py-4">Nenhum serviço cadastrado</p>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-900">Tickets Recentes</h2>
          <NuxtLink 
            to="/tickets"
            class="text-indigo-600 hover:text-indigo-700 text-sm"
          >
            Ver Todos
          </NuxtLink>
        </div>
        
        <div v-if="tickets.length > 0" class="space-y-3">
          <div 
            v-for="ticket in tickets.slice(0, 5)" 
            :key="ticket.id"
            class="border-l-4 pl-4 py-2"
            :class="{
              'border-green-600': ticket.status === 'open',
              'border-blue-600': ticket.status === 'in_progress',
              'border-gray-600': ticket.status === 'completed'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-gray-900">{{ ticket.title }}</p>
                <p class="text-sm text-gray-600">{{ ticket.client.name }}</p>
              </div>
              <span 
                class="px-2 py-1 rounded text-xs"
                :class="{
                  'bg-green-100 text-green-700': ticket.status === 'open',
                  'bg-blue-100 text-blue-700': ticket.status === 'in_progress',
                  'bg-gray-100 text-gray-700': ticket.status === 'completed'
                }"
              >
                {{ statusLabel(ticket.status) }}
              </span>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-center py-4">Nenhum ticket</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Service, Ticket } from '@/types'

definePageMeta({
  layout: 'default',
  middleware: 'auth'
})

const { user } = useAuth()
const { apiFetch } = useApi()
const { fetchTickets } = useTickets()

const services = ref<Service[]>([])
const tickets = ref<Ticket[]>([])
const showCreateServiceModal = ref(false)

const stats = computed(() => ({
  services: services.value.length,
  tickets: tickets.value.filter(t => t.status !== 'completed').length,
  rating: user.value?.freelancer?.average_rating?.toFixed(1) || '0.0'
}))

const statusLabel = (status: string) => {
  const labels: Record<string, string> = {
    open: 'Aberto',
    in_progress: 'Em Andamento',
    completed: 'Concluído'
  }
  return labels[status] || status
}

const loadData = async () => {
  try {
    const [servicesRes, ticketsRes] = await Promise.all([
      apiFetch<{ data: Service[] }>('/services'),
      fetchTickets()
    ])
    
    // Filtrar apenas serviços do freelancer logado
    services.value = servicesRes.data.filter(
      s => s.freelancer.id === user.value?.freelancer?.id
    )
    tickets.value = ticketsRes.data
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  }
}

onMounted(() => {
  loadData()
})
</script>
