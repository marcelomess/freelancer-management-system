<template>
  <div>
    <NuxtLayout name="admin">
      <div>
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Gerenciar Avaliações</h2>

        <!-- Lista de Avaliações -->
        <div class="space-y-4">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="bg-white rounded-lg shadow p-6"
          >
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      class="w-5 h-5"
                      :class="i <= review.rating ? 'fill-current' : 'stroke-current fill-none'"
                      viewBox="0 0 24 24"
                    >
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                  </div>
                  <span class="ml-2 text-sm text-gray-600">{{ review.rating }}/5</span>
                </div>
                <p class="text-gray-800 mb-2">{{ review.comment }}</p>
                <div class="text-sm text-gray-500">
                  Por: {{ review.client?.user?.name }} • Serviço: {{ review.service?.title }}
                </div>
                <div class="text-xs text-gray-400 mt-1">
                  {{ formatDate(review.created_at) }}
                </div>
              </div>
              <button
                @click="confirmDelete(review)"
                class="ml-4 text-red-600 hover:text-red-900"
              >
                Excluir
              </button>
            </div>
          </div>

          <div v-if="loading" class="text-center py-12">
            <p class="text-gray-600">Carregando avaliações...</p>
          </div>

          <div v-else-if="reviews.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-gray-600">Nenhuma avaliação encontrada.</p>
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
              Tem certeza que deseja excluir esta avaliação?
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

const { getAllReviews, deleteReview } = useAdmin()
const { addNotification } = useNotifications()

const reviews = ref<any[]>([])
const loading = ref(true)
const showDeleteModal = ref(false)
const reviewToDelete = ref<any>(null)

const loadReviews = async () => {
  loading.value = true
  try {
    const response = await getAllReviews()
    if (response && response.success && response.data) {
      reviews.value = response.data
    } else if (Array.isArray(response)) {
      reviews.value = response
    }
  } catch (error: any) {
    console.error('Error loading reviews:', error)
    addNotification(error.message || 'Erro ao carregar avaliações', 'error')
  } finally {
    loading.value = false
  }
}

const confirmDelete = (review: any) => {
  reviewToDelete.value = review
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!reviewToDelete.value) return

  try {
    const response = await deleteReview(reviewToDelete.value.id)
    if (response.success) {
      addNotification('Avaliação excluída com sucesso', 'success')
      reviews.value = reviews.value.filter(r => r.id !== reviewToDelete.value.id)
    }
  } catch (error: any) {
    addNotification(error.message || 'Erro ao excluir avaliação', 'error')
  } finally {
    showDeleteModal.value = false
    reviewToDelete.value = null
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  })
}

onMounted(() => {
  loadReviews()
})
</script>
