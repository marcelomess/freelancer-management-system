import type { Service, ServiceFilters } from '@/types'

export const useServices = () => {
  const { apiFetch } = useApi()

  const fetchServices = async (filters?: ServiceFilters) => {
    const query = new URLSearchParams()
    
    if (filters?.category) query.append('category', filters.category.toString())
    if (filters?.min_price) query.append('min_price', filters.min_price.toString())
    if (filters?.max_price) query.append('max_price', filters.max_price.toString())
    if (filters?.min_rating) query.append('min_rating', filters.min_rating.toString())
    if (filters?.status) query.append('status', filters.status)

    const queryString = query.toString() ? `?${query.toString()}` : ''
    return await apiFetch<{ data: Service[] }>(`/services${queryString}`)
  }

  const fetchService = async (id: number) => {
    return await apiFetch<{ data: Service }>(`/services/${id}`)
  }

  const createService = async (formData: FormData) => {
    return await apiFetch<{ data: Service }>('/services', {
      method: 'POST',
      body: formData,
    })
  }

  const updateService = async (id: number, data: Partial<Service>) => {
    return await apiFetch<{ data: Service }>(`/services/${id}`, {
      method: 'PUT',
      body: data,
    })
  }

  const deleteService = async (id: number) => {
    return await apiFetch(`/services/${id}`, {
      method: 'DELETE',
    })
  }

  return {
    fetchServices,
    fetchService,
    createService,
    updateService,
    deleteService,
  }
}
