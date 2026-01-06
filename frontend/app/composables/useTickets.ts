import type { Ticket } from '@/types'

export const useTickets = () => {
  const { apiFetch } = useApi()

  const fetchTickets = async (status?: string) => {
    const query = status ? `?status=${status}` : ''
    return await apiFetch<{ data: Ticket[] }>(`/tickets${query}`)
  }

  const fetchTicket = async (id: number) => {
    return await apiFetch<{ data: Ticket }>(`/tickets/${id}`)
  }

  const createTicket = async (data: Partial<Ticket>) => {
    return await apiFetch<{ data: Ticket }>('/tickets', {
      method: 'POST',
      body: data,
    })
  }

  const updateTicket = async (id: number, data: Partial<Ticket>) => {
    return await apiFetch<{ data: Ticket }>(`/tickets/${id}`, {
      method: 'PUT',
      body: data,
    })
  }

  const deleteTicket = async (id: number) => {
    return await apiFetch(`/tickets/${id}`, {
      method: 'DELETE',
    })
  }

  return {
    fetchTickets,
    fetchTicket,
    createTicket,
    updateTicket,
    deleteTicket,
  }
}
