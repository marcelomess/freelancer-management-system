<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup lang="ts">
const { fetchUser } = useAuth()

// Recuperar usuário autenticado ao carregar a aplicação
onMounted(async () => {
  await fetchUser()
})
</script>
