<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Meus Tickets</h1>
      <p class="text-gray-600 mt-2">Gerencie suas solicitações de serviço</p>
    </div>

    <!-- Filtros por Status -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
      <div class="flex gap-2">
        <button 
          @click="filterStatus = undefined"
          class="px-4 py-2 rounded-md"
          :class="filterStatus === undefined ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
        >
          Todos
        </button>
        <button 
          @click="filterStatus = 'open'"
          class="px-4 py-2 rounded-md"
          :class="filterStatus === 'open' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
        >
          Abertos
        </button>
        <button 
          @click="filterStatus = 'in_progress'"
          class="px-4 py-2 rounded-md"
          :class="filterStatus === 'in_progress' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
        >
          Em Andamento
        </button>
        <button 
          @click="filterStatus = 'completed'"
          class="px-4 py-2 rounded-md"
          :class="filterStatus === 'completed' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
        >
          Concluídos
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Carregando tickets...</p>
    </div>

    <!-- Lista de Tickets -->
    <div v-else-if="tickets.length > 0" class="space-y-4">
      <div 
        v-for="ticket in tickets" 
        :key="ticket.id"
        class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h3 class="text-xl font-semibold text-gray-900">{{ ticket.title }}</h3>
              <span 
                class="px-3 py-1 rounded-full text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-700': ticket.status === 'open',
                  'bg-blue-100 text-blue-700': ticket.status === 'in_progress',
                  'bg-gray-100 text-gray-700': ticket.status === 'completed',
                  'bg-red-100 text-red-700': ticket.status === 'cancelled'
                }"
              >
                {{ statusLabel(ticket.status) }}
              </span>
            </div>
            <p class="text-gray-600 mb-3">{{ ticket.description }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-600 mb-4">
          <div>
            <span class="font-medium">Cliente:</span> {{ ticket.client.name }}
          </div>
          <div v-if="ticket.service">
            <span class="font-medium">Serviço:</span> {{ ticket.service.title }}
          </div>
          <div v-if="ticket.freelancer">
            <span class="font-medium">Freelancer:</span> {{ ticket.freelancer.name }}
          </div>
          <div v-if="ticket.budget">
            <span class="font-medium">Orçamento:</span> R$ {{ ticket.budget.toFixed(2) }}
          </div>
          <div v-if="ticket.deadline">
            <span class="font-medium">Prazo:</span> {{ formatDate(ticket.deadline) }}
          </div>
          <div>
            <span class="font-medium">Criado em:</span> {{ formatDate(ticket.created_at) }}
          </div>
        </div>

        <div class="flex gap-3">
          <NuxtLink 
            :to="`/tickets/${ticket.id}`"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm"
          >
            Ver Detalhes
          </NuxtLink>
          
          <button 
            v-if="isFreelancer && ticket.status === 'open'"
            @click="acceptTicket(ticket.id)"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm"
          >
            Aceitar
          </button>
          
          <button 
            v-if="ticket.status === 'completed' && !ticket.review"
            @click="openReviewModal(ticket)"
            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 text-sm"
          >
            Avaliar
          </button>
        </div>
      </div>
    </div>

    <!-- Vazio -->
    <div v-else class="text-center py-12">
      <p class="text-gray-600">Nenhum ticket encontrado.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Ticket } from '@/types'

definePageMeta({
  layout: 'default',
  middleware: 'auth'
})

const { fetchTickets, updateTicket } = useTickets()
const { isFreelancer } = useAuth()

const tickets = ref<Ticket[]>([])
const loading = ref(true)
const filterStatus = ref<string | undefined>(undefined)

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

const loadTickets = async () => {
  loading.value = true
  try {
    const response = await fetchTickets(filterStatus.value)
    tickets.value = response.data
  } catch (error) {
    console.error('Erro ao carregar tickets:', error)
  } finally {
    loading.value = false
  }
}

const acceptTicket = async (ticketId: number) => {
  try {
    await updateTicket(ticketId, { status: 'in_progress' })
    loadTickets()
  } catch (error) {
    alert('Erro ao aceitar ticket')
  }
}

const openReviewModal = (ticket: Ticket) => {
  // TODO: Implementar modal de avaliação
  console.log('Avaliar ticket:', ticket)
}

watch(filterStatus, () => {
  loadTickets()
})

onMounted(() => {
  loadTickets()
})
</script>
