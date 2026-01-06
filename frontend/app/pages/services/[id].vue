<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Carregando...</p>
    </div>

    <div v-else-if="service" class="grid md:grid-cols-2 gap-8">
      <!-- Galeria de Imagens -->
      <div>
        <div class="bg-gray-200 rounded-lg overflow-hidden mb-4" style="height: 400px">
          <img 
            v-if="currentImage"
            :src="currentImage.url"
            :alt="service.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="flex items-center justify-center h-full text-gray-400">
            Sem imagens
          </div>
        </div>

        <!-- Miniaturas -->
        <div v-if="service.images.length > 1" class="grid grid-cols-4 gap-2">
          <div 
            v-for="(image, index) in service.images" 
            :key="image.id"
            @click="currentImageIndex = index"
            class="cursor-pointer border-2 rounded overflow-hidden"
            :class="currentImageIndex === index ? 'border-indigo-600' : 'border-gray-200'"
          >
            <img :src="image.thumb" :alt="`Imagem ${index + 1}`" class="w-full h-20 object-cover" />
          </div>
        </div>

        <!-- Info do Freelancer -->
        <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-lg font-semibold mb-4">Sobre o Freelancer</h3>
          <div class="flex items-center mb-3">
            <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold mr-3">
              {{ service.freelancer.name[0] }}
            </div>
            <div>
              <p class="font-semibold">{{ service.freelancer.name }}</p>
              <p class="text-sm text-gray-600">{{ service.freelancer.title }}</p>
            </div>
          </div>
          <div class="flex items-center text-yellow-500 mb-2">
            <span class="mr-1">⭐</span>
            <span class="text-gray-700">{{ Number(service.freelancer.average_rating).toFixed(1) }}</span>
          </div>
          <p class="text-sm text-gray-600">
            Taxa horária: R$ {{ Number(service.freelancer.hourly_rate).toFixed(2) }}/hora
          </p>
        </div>
      </div>

      <!-- Detalhes do Serviço -->
      <div>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-sm mb-3">
            {{ service.category.name }}
          </span>

          <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ service.title }}
          </h1>

          <div class="flex items-center mb-6">
            <div class="flex items-center text-yellow-500 mr-4">
              <span class="mr-1">⭐</span>
              <span class="text-gray-700">{{ Number(service.average_rating).toFixed(1) }}</span>
            </div>
            <span class="text-gray-600">Entrega em {{ service.delivery_time }} dias</span>
          </div>

          <div class="border-t border-b py-6 mb-6">
            <p class="text-gray-700 whitespace-pre-line">
              {{ service.description }}
            </p>
          </div>

          <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
              <span class="text-gray-600">Preço do serviço:</span>
              <span class="text-3xl font-bold text-indigo-600">
                R$ {{ Number(service.price).toFixed(2) }}
              </span>
            </div>
          </div>

          <template v-if="isClient">
            <button 
              @click="openTicketModal"
              class="w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700 font-semibold"
            >
              Solicitar Serviço
            </button>
          </template>
          <template v-else-if="!isAuthenticated">
            <NuxtLink 
              to="/login"
              class="block w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700 font-semibold text-center"
            >
              Faça login para solicitar
            </NuxtLink>
          </template>
        </div>
      </div>
    </div>

    <!-- Modal de Ticket -->
    <div v-if="showTicketModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4">Criar Ticket</h2>
        
        <form @submit.prevent="submitTicket" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input 
              v-model="ticketForm.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea 
              v-model="ticketForm.description"
              required
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Orçamento (opcional)</label>
            <input 
              v-model.number="ticketForm.budget"
              type="number"
              step="0.01"
              class="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prazo (opcional)</label>
            <input 
              v-model="ticketForm.deadline"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
          </div>

          <div class="flex gap-3">
            <button 
              type="submit"
              :disabled="submitting"
              class="flex-1 bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ submitting ? 'Enviando...' : 'Criar Ticket' }}
            </button>
            <button 
              type="button"
              @click="showTicketModal = false"
              class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-md hover:bg-gray-300"
            >
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Service } from '@/types'

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const router = useRouter()
const { fetchService } = useServices()
const { createTicket } = useTickets()
const { isAuthenticated, isClient } = useAuth()

const service = ref<Service | null>(null)
const loading = ref(true)
const currentImageIndex = ref(0)
const showTicketModal = ref(false)
const submitting = ref(false)

const ticketForm = ref({
  service_id: 0,
  title: '',
  description: '',
  budget: null as number | null,
  deadline: ''
})

const currentImage = computed(() => {
  if (!service.value?.images.length) return null
  return service.value.images[currentImageIndex.value]
})

const openTicketModal = () => {
  ticketForm.value = {
    service_id: service.value!.id,
    title: `Solicitação: ${service.value!.title}`,
    description: '',
    budget: service.value!.price,
    deadline: ''
  }
  showTicketModal.value = true
}

const submitTicket = async () => {
  submitting.value = true
  try {
    await createTicket(ticketForm.value)
    showTicketModal.value = false
    alert('Ticket criado com sucesso!')
    router.push('/tickets')
  } catch (error) {
    alert('Erro ao criar ticket')
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  try {
    const response = await fetchService(Number(route.params.id))
    service.value = response.data
  } catch (error) {
    console.error('Erro ao carregar serviço:', error)
  } finally {
    loading.value = false
  }
})
</script>
