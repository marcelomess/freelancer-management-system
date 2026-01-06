export const useNotifications = () => {
  const notifications = useState<Array<{
    id: number
    message: string
    type: 'info' | 'success' | 'warning' | 'error'
    timestamp: Date
  }>>('notifications', () => [])

  const { $echo } = useNuxtApp()
  const { user, isAuthenticated } = useAuth()

  const addNotification = (message: string, type: 'info' | 'success' | 'warning' | 'error' = 'info') => {
    const notification = {
      id: Date.now(),
      message,
      type,
      timestamp: new Date()
    }
    
    notifications.value.unshift(notification)

    // Auto-remover após 5 segundos
    setTimeout(() => {
      removeNotification(notification.id)
    }, 5000)
  }

  const removeNotification = (id: number) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  const clearNotifications = () => {
    notifications.value = []
  }

  const setupWebSocket = () => {
    if (!isAuthenticated.value || !user.value || !$echo) return

    try {
      // Canal privado do usuário
      const userChannel = $echo.private(`user.${user.value.id}`)

      userChannel
        .listen('TicketCreated', (data: any) => {
          addNotification(data.message, 'success')
        })
        .listen('TicketUpdated', (data: any) => {
          addNotification(data.message, 'info')
        })

      // Cleanup ao desmontar
      onUnmounted(() => {
        try {
          userChannel.stopListening('TicketCreated')
          userChannel.stopListening('TicketUpdated')
          $echo.leave(`user.${user.value.id}`)
        } catch (e) {
          console.error('Erro ao desconectar WebSocket:', e)
        }
      })
    } catch (error) {
      console.error('Erro ao configurar WebSocket:', error)
    }
  }

  return {
    notifications: readonly(notifications),
    addNotification,
    removeNotification,
    clearNotifications,
    setupWebSocket
  }
}
