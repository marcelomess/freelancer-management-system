export interface User {
  id: number
  name: string
  email: string
  user_type: 'freelancer' | 'client' | 'admin'
  freelancer?: Freelancer
  client?: Client
  created_at: string
  updated_at: string
}

export interface Freelancer {
  id: number
  user_id: number
  title: string
  description: string
  hourly_rate: number
  availability: boolean
  average_rating: number
  services_count?: number
}

export interface Client {
  id: number
  user_id: number
}

export interface Service {
  id: number
  title: string
  description: string
  price: number
  delivery_time: number
  status: 'active' | 'inactive'
  category: Category
  freelancer: {
    id: number
    name: string
    title: string
    hourly_rate: number
    average_rating: number
  }
  images: MediaItem[]
  average_rating: number
  created_at: string
  updated_at: string
}

export interface Category {
  id: number
  name: string
  slug: string
}

export interface MediaItem {
  id: number
  url: string
  thumb: string
  medium: string
}

export interface Ticket {
  id: number
  title: string
  description: string
  budget: number
  deadline: string
  status: 'open' | 'in_progress' | 'completed' | 'cancelled'
  client: {
    id: number
    name: string
    email: string
  }
  service?: {
    id: number
    title: string
    price: number
  }
  freelancer?: {
    id: number
    name: string
    title: string
  }
  created_at: string
  updated_at: string
}

export interface Review {
  id: number
  rating: number
  comment: string
  ticket: {
    id: number
    title: string
  }
  reviewer: {
    id: number
    name: string
  }
  reviewee: {
    id: number
    name: string
  }
  created_at: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  user_type: 'freelancer' | 'client'
}

export interface ServiceFilters {
  category?: number
  min_price?: number
  max_price?: number
  min_rating?: number
  status?: 'active' | 'inactive'
}
