import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

declare global {
  interface Window {
    Pusher: typeof Pusher
  }
}

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')

  window.Pusher = Pusher

  const echo = new Echo({
    broadcaster: 'reverb',
    key: 'freelancer-app-key',
    wsHost: config.public.wsHost,
    wsPort: config.public.wsPort,
    wssPort: config.public.wsPort,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: `${config.public.apiBase}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: token.value ? `Bearer ${token.value}` : '',
      },
    },
  })

  return {
    provide: {
      echo,
    },
  }
})
