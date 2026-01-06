<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Serviços Disponíveis</h1>
      <p class="text-gray-600 mt-2">Encontre o profissional ideal para seu projeto</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <div class="grid md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
          <select 
            v-model="filters.category"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          >
            <option :value="undefined">Todas</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Preço Mínimo</label>
          <input 
            v-model.number="filters.min_price"
            type="number"
            placeholder="R$ 0"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Preço Máximo</label>
          <input 
            v-model.number="filters.max_price"
            type="number"
            placeholder="R$ 10000"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Avaliação Mínima</label>
          <select 
            v-model.number="filters.min_rating"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          >
            <option :value="undefined">Qualquer</option>
            <option :value="5">⭐ 5 estrelas</option>
            <option :value="4">⭐ 4+ estrelas</option>
            <option :value="3">⭐ 3+ estrelas</option>
          </select>
        </div>
      </div>

      <div class="mt-4 flex gap-4">
        <button 
          @click="applyFilters"
          class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700"
        >
          Aplicar Filtros
        </button>
        <button 
          @click="clearFilters"
          class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300"
        >
          Limpar
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Carregando serviços...</p>
    </div>

    <!-- Grid de Serviços -->
    <div v-else-if="services.length > 0" class="grid md:grid-cols-3 gap-6">
      <div 
        v-for="service in services" 
        :key="service.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition"
      >
        <div class="h-48 bg-gray-200 relative">
          <img 
            v-if="service.images?.[0]"
            :src="service.images[0].medium"
            :alt="service.title"
            class="w-full h-full object-cover"
          />
          <div v-else class="flex items-center justify-center h-full text-gray-400">
            Sem imagem
          </div>
        </div>

        <div class="p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-indigo-600 font-medium">
              {{ service.category.name }}
            </span>
            <div class="flex items-center text-yellow-500">
              <span class="mr-1">⭐</span>
              <span class="text-sm text-gray-700">{{ service.average_rating.toFixed(1) }}</span>
            </div>
          </div>

          <h3 class="text-xl font-semibold text-gray-900 mb-2">
            {{ service.title }}
          </h3>

          <p class="text-gray-600 text-sm mb-4 line-clamp-2">
            {{ service.description }}
          </p>

          <div class="border-t pt-4 mb-4">
            <div class="flex items-center text-sm text-gray-600 mb-2">
              <span class="font-medium mr-2">{{ service.freelancer.name }}</span>
              <span class="text-gray-400">{{ service.freelancer.title }}</span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-600">
              <span>Entrega: {{ service.delivery_time }} dias</span>
              <span class="text-lg font-bold text-indigo-600">
                R$ {{ service.price.toFixed(2) }}
              </span>
            </div>
          </div>

          <NuxtLink 
            :to="`/services/${service.id}`"
            class="block w-full bg-indigo-600 text-white text-center py-2 rounded-md hover:bg-indigo-700"
          >
            Ver Detalhes
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Vazio -->
    <div v-else class="text-center py-12">
      <p class="text-gray-600">Nenhum serviço encontrado com os filtros aplicados.</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Service, Category, ServiceFilters } from '@/types'

definePageMeta({
  layout: 'default'
})

const { fetchServices } = useServices()
const { apiFetch } = useApi()

const services = ref<Service[]>([])
const categories = ref<Category[]>([])
const loading = ref(true)

const filters = ref<ServiceFilters>({
  category: undefined,
  min_price: undefined,
  max_price: undefined,
  min_rating: undefined,
})

const loadServices = async () => {
  loading.value = true
  try {
    const response = await fetchServices(filters.value)
    services.value = response.data
  } catch (error) {
    console.error('Erro ao carregar serviços:', error)
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await apiFetch<Category[]>('/categories')
    categories.value = response
  } catch (error) {
    console.error('Erro ao carregar categorias:', error)
  }
}

const applyFilters = () => {
  loadServices()
}

const clearFilters = () => {
  filters.value = {
    category: undefined,
    min_price: undefined,
    max_price: undefined,
    min_rating: undefined,
  }
  loadServices()
}

onMounted(() => {
  loadServices()
  loadCategories()
})
</script>
