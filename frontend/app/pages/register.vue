<template>
  <div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">
        Criar Conta
      </h1>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Tipo de Conta
          </label>
          <div class="grid grid-cols-2 gap-3">
            <button
              type="button"
              @click="form.user_type = 'freelancer'"
              class="px-4 py-2 rounded-md border-2"
              :class="form.user_type === 'freelancer' 
                ? 'border-indigo-600 bg-indigo-50 text-indigo-700' 
                : 'border-gray-300 text-gray-700'"
            >
              Freelancer
            </button>
            <button
              type="button"
              @click="form.user_type = 'client'"
              class="px-4 py-2 rounded-md border-2"
              :class="form.user_type === 'client' 
                ? 'border-green-600 bg-green-50 text-green-700' 
                : 'border-gray-300 text-gray-700'"
            >
              Cliente
            </button>
          </div>
        </div>

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
            Nome Completo
          </label>
          <input
            id="name"
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
            Email
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Senha
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            minlength="8"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
            Confirmar Senha
          </label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="8"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-2 rounded-md text-white disabled:opacity-50"
          :class="form.user_type === 'freelancer' ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-green-600 hover:bg-green-700'"
        >
          {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
        </button>
      </form>

      <p class="mt-4 text-center text-sm text-gray-600">
        Já tem conta?
        <NuxtLink to="/login" class="text-indigo-600 hover:text-indigo-700">
          Faça login
        </NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
  middleware: 'guest'
})

const route = useRoute()
const router = useRouter()
const { register } = useAuth()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  user_type: (route.query.type as 'freelancer' | 'client') || 'freelancer'
})

const loading = ref(false)
const error = ref('')

const handleRegister = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'As senhas não coincidem'
    return
  }

  loading.value = true
  error.value = ''

  try {
    await register(form.value)
    
    if (form.value.user_type === 'freelancer') {
      router.push('/dashboard/freelancer')
    } else {
      router.push('/dashboard/client')
    }
  } catch (err: any) {
    error.value = err.data?.message || 'Erro ao criar conta'
  } finally {
    loading.value = false
  }
}
</script>
