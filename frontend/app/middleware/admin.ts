export default defineNuxtRouteMiddleware(async (to, from) => {
  const { token, fetchUser, user } = useAuth()

  // Se há token mas não há user, busca o usuário
  if (token.value && !user.value) {
    await fetchUser()
  }

  // Se não está autenticado, redireciona para login
  if (!token.value || !user.value) {
    return navigateTo('/login')
  }

  // Se não é admin, redireciona para home
  if (user.value.user_type !== 'admin') {
    return navigateTo('/')
  }
})
