export const useAdmin = () => {
  const { apiFetch } = useApi()

  // Estatísticas
  const getStats = async () => {
    return await apiFetch('/admin/stats')
  }

  // Usuários
  const getUsers = async (params?: any) => {
    const filteredParams: any = {}
    if (params) {
      if (params.search) filteredParams.search = params.search
      if (params.role) filteredParams.role = params.role
    }
    const query = new URLSearchParams(filteredParams).toString()
    return await apiFetch(`/admin/users${query ? '?' + query : ''}`)
  }

  const updateUser = async (id: number, data: any) => {
    return await apiFetch(`/admin/users/${id}`, {
      method: 'PUT',
      body: data
    })
  }

  const deleteUser = async (id: number) => {
    return await apiFetch(`/admin/users/${id}`, {
      method: 'DELETE'
    })
  }

  // Categorias
  const getCategories = async () => {
    return await apiFetch('/categories')
  }

  const createCategory = async (data: { name: string; description?: string }) => {
    return await apiFetch('/categories', {
      method: 'POST',
      body: data
    })
  }

  const updateCategory = async (id: number, data: { name: string; description?: string }) => {
    return await apiFetch(`/categories/${id}`, {
      method: 'PUT',
      body: data
    })
  }

  const deleteCategory = async (id: number) => {
    return await apiFetch(`/categories/${id}`, {
      method: 'DELETE'
    })
  }

  // Serviços
  const getAllServices = async (params?: any) => {
    const query = new URLSearchParams(params).toString()
    return await apiFetch(`/admin/services${query ? '?' + query : ''}`)
  }

  const updateServiceStatus = async (id: number, status: string) => {
    return await apiFetch(`/admin/services/${id}/status`, {
      method: 'PUT',
      body: { status }
    })
  }

  const deleteService = async (id: number) => {
    return await apiFetch(`/admin/services/${id}`, {
      method: 'DELETE'
    })
  }

  // Tickets
  const getAllTickets = async (params?: any) => {
    const query = new URLSearchParams(params).toString()
    return await apiFetch(`/admin/tickets${query ? '?' + query : ''}`)
  }

  // Avaliações
  const getAllReviews = async (params?: any) => {
    const query = new URLSearchParams(params).toString()
    return await apiFetch(`/admin/reviews${query ? '?' + query : ''}`)
  }

  const updateReviewStatus = async (id: number, status: string) => {
    return await apiFetch(`/admin/reviews/${id}/status`, {
      method: 'PUT',
      body: { status }
    })
  }

  const deleteReview = async (id: number) => {
    return await apiFetch(`/admin/reviews/${id}`, {
      method: 'DELETE'
    })
  }

  return {
    getStats,
    getUsers,
    updateUser,
    deleteUser,
    getCategories,
    createCategory,
    updateCategory,
    deleteCategory,
    getAllServices,
    updateServiceStatus,
    deleteService,
    getAllTickets,
    getAllReviews,
    updateReviewStatus,
    deleteReview,
  }
}
