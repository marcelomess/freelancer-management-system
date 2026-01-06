export default defineNuxtRouteMiddleware(async (to, from) => {
  const { token, fetchUser, user } = useAuth()

  // Se há token mas não há user, busca o usuário
  if (token.value && !user.value) {
    await fetchUser()
  }

  // Se após buscar ainda não há usuário, redireciona para login
  if (!token.value || !user.value) {
    return navigateTo('/login')
  }
})
