<template>
  <div class="fixed top-4 right-4 z-50 space-y-2 max-w-md">
    <TransitionGroup name="notification">
      <div
        v-for="notification in notifications"
        :key="notification.id"
        class="p-4 rounded-lg shadow-lg flex items-start justify-between"
        :class="{
          'bg-blue-100 text-blue-800': notification.type === 'info',
          'bg-green-100 text-green-800': notification.type === 'success',
          'bg-yellow-100 text-yellow-800': notification.type === 'warning',
          'bg-red-100 text-red-800': notification.type === 'error',
        }"
      >
        <div class="flex-1">
          <p class="text-sm font-medium">{{ notification.message }}</p>
          <p class="text-xs mt-1 opacity-75">
            {{ formatTime(notification.timestamp) }}
          </p>
        </div>
        <button
          @click="removeNotification(notification.id)"
          class="ml-3 text-current opacity-50 hover:opacity-100"
        >
          ✕
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup lang="ts">
const { notifications, removeNotification } = useNotifications()

const formatTime = (date: Date) => {
  return new Intl.DateTimeFormat('pt-BR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
