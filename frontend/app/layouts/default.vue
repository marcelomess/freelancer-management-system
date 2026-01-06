<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-8">
            <NuxtLink to="/" class="text-xl font-bold text-indigo-600">
              FreelancerMS
            </NuxtLink>
            <NuxtLink 
              to="/services" 
              class="text-gray-700 hover:text-indigo-600"
            >
              Serviços
            </NuxtLink>
            <NuxtLink 
              v-if="isAuthenticated"
              to="/tickets" 
              class="text-gray-700 hover:text-indigo-600"
            >
              Tickets
            </NuxtLink>
          </div>

          <div class="flex items-center space-x-4">
            <template v-if="isAuthenticated">
              <span class="text-gray-700">{{ user?.name }}</span>
              <NuxtLink 
                :to="user?.user_type === 'admin' ? '/admin' : user?.user_type === 'freelancer' ? '/dashboard/freelancer' : '/dashboard/client'"
                class="text-gray-700 hover:text-indigo-600"
              >
                Dashboard
              </NuxtLink>
              <button 
                @click="handleLogout"
                class="text-gray-700 hover:text-indigo-600"
              >
                Sair
              </button>
            </template>
            <template v-else>
              <NuxtLink 
                to="/login" 
                class="text-gray-700 hover:text-indigo-600"
              >
                Entrar
              </NuxtLink>
              <NuxtLink 
                to="/register" 
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >
                Cadastrar
              </NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>

    <NotificationToast />

    <footer class="bg-gray-800 text-white mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p class="text-center text-gray-400">
          © 2026 FreelancerMS. Todos os direitos reservados.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
const { user, isAuthenticated, logout } = useAuth()

const handleLogout = async () => {
  await logout()
}
</script>
