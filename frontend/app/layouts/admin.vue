<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="flex items-center justify-between px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-900">Painel Administrativo</h1>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">{{ user?.name }}</span>
          <NuxtLink to="/" target="_blank" class="text-indigo-600 hover:text-indigo-800">
            Ver Site
          </NuxtLink>
          <button @click="handleLogout" class="text-red-600 hover:text-red-800">
            Sair
          </button>
        </div>
      </div>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 bg-white shadow-sm min-h-screen">
        <nav class="p-4 space-y-2">
          <NuxtLink
            to="/admin"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path === '/admin' }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
          </NuxtLink>

          <NuxtLink
            to="/admin/users"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path.startsWith('/admin/users') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Usuários
          </NuxtLink>

          <NuxtLink
            to="/admin/categories"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path.startsWith('/admin/categories') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Categorias
          </NuxtLink>

          <NuxtLink
            to="/admin/services"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path.startsWith('/admin/services') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Serviços
          </NuxtLink>

          <NuxtLink
            to="/admin/tickets"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path.startsWith('/admin/tickets') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            Tickets
          </NuxtLink>

          <NuxtLink
            to="/admin/reviews"
            class="flex items-center px-4 py-3 rounded-lg hover:bg-indigo-50 transition-colors"
            :class="{ 'bg-indigo-100 text-indigo-700 font-medium': $route.path.startsWith('/admin/reviews') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            Avaliações
          </NuxtLink>
        </nav>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>

    <NotificationToast />
  </div>
</template>

<script setup lang="ts">
const { user, logout } = useAuth()

const handleLogout = async () => {
  await logout()
  navigateTo('/login')
}
</script>
