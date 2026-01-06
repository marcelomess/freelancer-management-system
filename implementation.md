# Plano de Implementação - Sistema de Gestão de Freelancers

## Visão Geral do Projeto

Plataforma marketplace onde prestadores de serviço (freelancers) podem cadastrar portfólios e clientes podem abrir chamados (tickets) para contratar serviços.

### Stack Tecnológica

- **Backend**: Laravel 12 (API RESTful)
- **Frontend**: Nuxt 4.2 (Client-Side Rendering)
- **Banco de Dados**: MySQL 8.4
- **Cache/Filas**: Redis
- **WebSockets**: Laravel Reverb
- **PHP**: 8.5
- **Node.js**: 20+
- **Ambiente**: Docker + Docker Compose

### Decisões Técnicas

- ✅ Autenticação: Laravel Sanctum (SPA tokens)
- ✅ Autorização: Gates e Policies nativos do Laravel
- ✅ Upload de Imagens: Laravel Media Library (processamento síncrono)
- ✅ Storage: Local (com preparação para S3 futuro)
- ✅ WebSockets: Laravel Reverb com Supervisor
- ✅ Rate Limiting: Throttle nas rotas da API
- ✅ Documentação API: Swagger/OpenAPI (L5-Swagger)
- ✅ Rendering Frontend: Client-Side (CSR)

---

## Etapa 1: Configurar Ambiente Docker

### 1.1 Criar Estrutura de Diretórios

```
freelancer-management-system/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   ├── Dockerfile
│   │   └── php.ini
│   ├── reverb/
│   │   └── supervisord.conf
│   └── mysql/
│       └── my.cnf
├── backend/
├── frontend/
├── docker-compose.yml
├── .env.example
└── .gitignore
```

### 1.2 Criar docker-compose.yml

**Services necessários:**

1. **nginx** - Proxy reverso
   - Porta: 80 (API)
   - Volume: backend/public

2. **php** - PHP 8.5-FPM
   - Imagem: php:8.5-fpm
   - Extensões: pdo_mysql, redis, gd, zip, intl
   - Volume: backend/

3. **mysql** - MySQL 8.4
   - Porta: 3306
   - Volume: mysql_data

4. **redis** - Cache e Filas
   - Porta: 6379
   - Volume: redis_data

5. **reverb** - Laravel Reverb WebSocket
   - Porta: 8080
   - Supervisor para auto-restart
   - Comando: php artisan reverb:start

6. **nuxt** - Nuxt Dev Server
   - Porta: 3000
   - Volume: frontend/
   - Comando: npm run dev

7. **queue-worker** - Laravel Queue Worker
   - Comando: php artisan queue:work
   - Supervisor para auto-restart

### 1.3 Configurar Nginx

```nginx
server {
    listen 80;
    index index.php;
    root /var/www/html/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 1.4 Configurar Supervisor para Reverb

```ini
[program:reverb]
command=php /var/www/html/artisan reverb:start
autostart=true
autorestart=true
stderr_logfile=/var/log/reverb.err.log
stdout_logfile=/var/log/reverb.out.log
```

---

## Etapa 2: Estruturar Monorepo

### 2.1 Criar .gitignore

```gitignore
# Backend (Laravel)
backend/vendor/
backend/node_modules/
backend/.env
backend/storage/*.key
backend/storage/framework/cache/*
backend/storage/framework/sessions/*
backend/storage/framework/views/*
backend/storage/logs/*
backend/bootstrap/cache/*

# Frontend (Nuxt)
frontend/node_modules/
frontend/.nuxt/
frontend/.output/
frontend/dist/
frontend/.env

# Docker
docker/mysql/data/
docker/redis/data/

# IDE
.vscode/
.idea/
*.swp
*.swo
.DS_Store
```

### 2.2 Criar .env.example

```env
# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=freelancer_db
DB_USERNAME=root
DB_PASSWORD=root

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Reverb
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_APP_ID=app-id
REVERB_APP_KEY=app-key
REVERB_APP_SECRET=app-secret

# Frontend URL
FRONTEND_URL=http://localhost:3000
```

### 2.3 Criar Dockerfiles

**backend/Dockerfile:**
- Base: php:8.5-fpm
- Instalar extensões: pdo_mysql, redis, gd, zip, intl
- Instalar Composer
- Copiar código

**frontend/Dockerfile:**
- Base: node:20-alpine
- Instalar dependências
- Expor porta 3000

---

## Etapa 3: Inicializar Backend Laravel 12

### 3.1 Criar Projeto Laravel

```bash
docker-compose exec php composer create-project laravel/laravel:^12.0 .
```

### 3.2 Instalar Dependências

```bash
# Laravel Sanctum (já vem instalado no Laravel 11+)
composer require laravel/sanctum

# Laravel Media Library
composer require spatie/laravel-medialibrary

# L5-Swagger para documentação
composer require darkaonline/l5-swagger

# Laravel Reverb (já vem no Laravel 11+)
php artisan install:broadcasting
```

### 3.3 Configurar Laravel Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**config/sanctum.php:**
- Configurar stateful domains (localhost:3000)
- Configurar token expiration

**app/Http/Kernel.php:**
- Adicionar middleware api: EnsureFrontendRequestsAreStateful

### 3.4 Configurar Laravel Reverb

```bash
php artisan reverb:install
```

**config/broadcasting.php:**
- Configurar driver reverb
- Definir host e porta

### 3.5 Configurar Rate Limiting

**app/Http/Kernel.php:**
```php
'api' => [
    'throttle:60,1', // 60 requests por minuto
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 3.6 Configurar L5-Swagger

```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

**config/l5-swagger.php:**
- Configurar path: /api/documentation
- Definir API info (title, description, version)

---

## Etapa 4: Desenvolver Schema e API RESTful

### 4.1 Estrutura do Banco de Dados

#### Tabela: users
- id (PK)
- name
- email (unique)
- password
- user_type (enum: 'freelancer', 'client', 'admin')
- email_verified_at
- timestamps

#### Tabela: freelancers
- id (PK)
- user_id (FK)
- title (ex: "Designer Gráfico")
- description
- hourly_rate (decimal)
- availability (boolean)
- timestamps

#### Tabela: clients
- id (PK)
- user_id (FK)
- company_name (nullable)
- phone
- timestamps

#### Tabela: categories
- id (PK)
- name
- slug
- timestamps

#### Tabela: services (Portfólio)
- id (PK)
- freelancer_id (FK)
- category_id (FK)
- title
- description
- price (decimal)
- delivery_time (integer, dias)
- status (enum: 'active', 'inactive')
- timestamps

#### Tabela: tickets (Chamados)
- id (PK)
- client_id (FK)
- freelancer_id (FK, nullable)
- service_id (FK, nullable)
- title
- description
- budget (decimal)
- status (enum: 'open', 'in_progress', 'completed', 'cancelled')
- timestamps

#### Tabela: reviews
- id (PK)
- ticket_id (FK)
- reviewer_id (FK - user_id)
- reviewee_id (FK - user_id)
- rating (integer 1-5)
- comment
- timestamps

#### Tabela: notifications
- id (PK)
- user_id (FK)
- type (string)
- data (json)
- read_at (nullable)
- timestamps

### 4.2 Criar Models com Query Scopes

**User Model:**
```php
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    public function freelancer() { return $this->hasOne(Freelancer::class); }
    public function client() { return $this->hasOne(Client::class); }
    public function isFreelancer() { return $this->user_type === 'freelancer'; }
    public function isClient() { return $this->user_type === 'client'; }
}
```

**Service Model com Query Scopes:**
```php
class Service extends Model
{
    use HasMediaCollections;
    
    // Scope: Filtrar por categoria
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
    
    // Scope: Filtrar por faixa de preço
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }
    
    // Scope: Filtrar por avaliação mínima
    public function scopeByRating($query, $minRating)
    {
        return $query->whereHas('freelancer.reviews', function($q) use ($minRating) {
            $q->havingRaw('AVG(rating) >= ?', [$minRating]);
        });
    }
}
```

### 4.3 Criar API Controllers

**Controllers necessários:**
- AuthController (login, register, logout)
- FreelancerController (CRUD, filtros)
- ServiceController (CRUD, upload imagens, filtros)
- TicketController (CRUD, propostas)
- ReviewController (CRUD, calcular médias)
- NotificationController (listar, marcar como lida)

**Exemplo ServiceController:**
```php
/**
 * @OA\Get(
 *     path="/api/services",
 *     summary="List services with filters",
 *     @OA\Parameter(name="category", in="query", required=false),
 *     @OA\Parameter(name="min_price", in="query", required=false),
 *     @OA\Parameter(name="max_price", in="query", required=false),
 *     @OA\Parameter(name="min_rating", in="query", required=false),
 *     @OA\Response(response=200, description="Success")
 * )
 */
public function index(Request $request)
{
    $query = Service::with(['freelancer', 'category']);
    
    if ($request->has('category')) {
        $query->byCategory($request->category);
    }
    
    if ($request->has('min_price') && $request->has('max_price')) {
        $query->byPriceRange($request->min_price, $request->max_price);
    }
    
    if ($request->has('min_rating')) {
        $query->byRating($request->min_rating);
    }
    
    return ServiceResource::collection($query->paginate(15));
}
```

### 4.4 Criar API Resources

```php
class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'freelancer' => new FreelancerResource($this->whenLoaded('freelancer')),
            'images' => $this->getMedia('portfolio'),
            'rating' => $this->averageRating(),
        ];
    }
}
```

### 4.5 Criar Gates e Policies

**ServicePolicy:**
```php
class ServicePolicy
{
    public function update(User $user, Service $service)
    {
        return $user->id === $service->freelancer->user_id;
    }
    
    public function delete(User $user, Service $service)
    {
        return $user->id === $service->freelancer->user_id;
    }
}
```

**TicketPolicy:**
```php
class TicketPolicy
{
    public function view(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->client->user_id 
            || $user->id === $ticket->freelancer?->user_id;
    }
}
```

### 4.6 Definir Rotas API

**routes/api.php:**
```php
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Services (Portfólio)
    Route::apiResource('services', ServiceController::class);
    
    // Tickets (Chamados)
    Route::apiResource('tickets', TicketController::class);
    
    // Reviews
    Route::apiResource('reviews', ReviewController::class);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});
```

---

## Etapa 5: Configurar Storage, Uploads e Documentação

### 5.1 Configurar Laravel Media Library

**Service Model:**
```php
class Service extends Model
{
    use InteractsWithMedia;
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portfolio')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(300)
                    ->height(300);
                    
                $this->addMediaConversion('medium')
                    ->width(800)
                    ->height(600);
            });
    }
}
```

**Upload Controller:**
```php
public function uploadImage(Request $request, Service $service)
{
    $request->validate([
        'image' => 'required|image|max:5120', // 5MB
    ]);
    
    $service->addMediaFromRequest('image')
        ->toMediaCollection('portfolio');
    
    return response()->json(['message' => 'Image uploaded']);
}
```

### 5.2 Configurar Filesystems

**config/filesystems.php:**
```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
    ],
    
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
    ],
],

// Preparado para mudar de local para s3 no .env
'default' => env('FILESYSTEM_DISK', 'local'),
```

### 5.3 Configurar Swagger

**Adicionar anotações nos Controllers:**
```php
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Freelancer Management API",
 *     description="API RESTful para sistema de gestão de freelancers"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 */
class Controller extends BaseController
{
    //
}
```

**Gerar documentação:**
```bash
php artisan l5-swagger:generate
```

Acessível em: http://localhost/api/documentation

---

## Etapa 6: Inicializar Frontend Nuxt 4.2

### 6.1 Criar Projeto Nuxt

```bash
npx nuxi@latest init frontend
cd frontend
npm install
```

### 6.2 Instalar Dependências

```bash
# Axios/Fetch está built-in no Nuxt 3+
# Laravel Echo para WebSockets
npm install laravel-echo pusher-js

# UI (opcional)
npm install @nuxtjs/tailwindcss
```

### 6.3 Configurar nuxt.config.ts

```typescript
export default defineNuxtConfig({
  ssr: false, // Client-Side Rendering
  
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost/api',
      wsHost: 'localhost',
      wsPort: 8080,
    }
  },
  
  modules: [
    '@nuxtjs/tailwindcss',
  ],
})
```

### 6.4 Criar Composables de API

**composables/useApi.ts:**
```typescript
export const useApi = () => {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')
  
  const apiFetch = $fetch.create({
    baseURL: config.public.apiBase,
    headers: {
      'Accept': 'application/json',
      'Authorization': token.value ? `Bearer ${token.value}` : ''
    }
  })
  
  return { apiFetch }
}
```

**composables/useAuth.ts:**
```typescript
export const useAuth = () => {
  const token = useCookie('auth_token')
  const user = useState('user', () => null)
  
  const login = async (email: string, password: string) => {
    const { apiFetch } = useApi()
    const data = await apiFetch('/login', {
      method: 'POST',
      body: { email, password }
    })
    token.value = data.token
    user.value = data.user
  }
  
  const logout = async () => {
    const { apiFetch } = useApi()
    await apiFetch('/logout', { method: 'POST' })
    token.value = null
    user.value = null
  }
  
  return { user, login, logout }
}
```

**composables/useServices.ts:**
```typescript
export const useServices = () => {
  const { apiFetch } = useApi()
  
  const fetchServices = (filters = {}) => {
    return useFetch('/services', {
      $fetch: apiFetch,
      query: filters,
    })
  }
  
  const createService = async (service: any) => {
    return await apiFetch('/services', {
      method: 'POST',
      body: service
    })
  }
  
  return { fetchServices, createService }
}
```

### 6.5 Configurar Laravel Echo (WebSockets)

**plugins/echo.client.ts:**
```typescript
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const token = useCookie('auth_token')
  
  window.Pusher = Pusher
  
  const echo = new Echo({
    broadcaster: 'reverb',
    key: 'app-key',
    wsHost: config.public.wsHost,
    wsPort: config.public.wsPort,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: `${config.public.apiBase}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    }
  })
  
  return {
    provide: {
      echo
    }
  }
})
```

### 6.6 Criar Estrutura de Pages

```
frontend/
├── pages/
│   ├── index.vue (home)
│   ├── login.vue
│   ├── register.vue
│   ├── services/
│   │   ├── index.vue (lista com filtros)
│   │   ├── [id].vue (detalhes)
│   │   └── create.vue
│   ├── tickets/
│   │   ├── index.vue
│   │   ├── [id].vue
│   │   └── create.vue
│   └── profile/
│       ├── index.vue
│       └── portfolio.vue
├── components/
│   ├── ServiceCard.vue
│   ├── ServiceFilters.vue
│   ├── TicketCard.vue
│   └── NotificationBell.vue
└── layouts/
    └── default.vue
```

---

## Etapa 7: Implementar Features Principais

### 7.1 Sistema de Portfólios com Galeria

**Backend - ServiceController:**
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string',
        'description' => 'required|string',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'images.*' => 'image|max:5120',
    ]);
    
    $service = auth()->user()->freelancer->services()->create($validated);
    
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $service->addMedia($image)->toMediaCollection('portfolio');
        }
    }
    
    return new ServiceResource($service);
}
```

**Frontend - ServiceCreate.vue:**
```vue
<template>
  <form @submit.prevent="createService">
    <input v-model="form.title" placeholder="Título" />
    <textarea v-model="form.description" placeholder="Descrição" />
    <input v-model="form.price" type="number" placeholder="Preço" />
    <input type="file" multiple @change="handleFiles" accept="image/*" />
    <button type="submit">Criar Serviço</button>
  </form>
</template>

<script setup>
const form = ref({
  title: '',
  description: '',
  price: 0,
  category_id: 1,
  images: []
})

const handleFiles = (e) => {
  form.value.images = Array.from(e.target.files)
}

const createService = async () => {
  const formData = new FormData()
  Object.keys(form.value).forEach(key => {
    if (key === 'images') {
      form.value.images.forEach(img => formData.append('images[]', img))
    } else {
      formData.append(key, form.value[key])
    }
  })
  
  const { apiFetch } = useApi()
  await apiFetch('/services', {
    method: 'POST',
    body: formData
  })
}
</script>
```

### 7.2 Sistema de Chamados/Tickets

**Backend - Broadcasting:**
```php
// Event: TicketCreated
class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public function __construct(public Ticket $ticket) {}
    
    public function broadcastOn()
    {
        return new PrivateChannel('freelancer.' . $this->ticket->freelancer_id);
    }
}
```

**Frontend - Listen Notifications:**
```vue
<script setup>
const { $echo } = useNuxtApp()
const notifications = ref([])

onMounted(() => {
  $echo.private(`freelancer.${user.value.id}`)
    .listen('TicketCreated', (e) => {
      notifications.value.unshift(e.ticket)
      // Mostrar toast notification
    })
})
</script>
```

### 7.3 Filtros Avançados com Paginação

**Frontend - ServiceFilters.vue:**
```vue
<template>
  <div class="filters">
    <select v-model="filters.category">
      <option value="">Todas Categorias</option>
      <option v-for="cat in categories" :value="cat.id">{{ cat.name }}</option>
    </select>
    
    <input v-model="filters.min_price" type="number" placeholder="Preço Mín" />
    <input v-model="filters.max_price" type="number" placeholder="Preço Máx" />
    
    <select v-model="filters.min_rating">
      <option value="">Qualquer Avaliação</option>
      <option value="4">4+ estrelas</option>
      <option value="3">3+ estrelas</option>
    </select>
    
    <button @click="applyFilters">Filtrar</button>
  </div>
</template>

<script setup>
const filters = ref({
  category: '',
  min_price: null,
  max_price: null,
  min_rating: null,
  page: 1
})

const { data: services, refresh } = await useServices().fetchServices(filters.value)

const applyFilters = () => {
  refresh()
}
</script>
```

### 7.4 Sistema de Avaliações

**Backend - ReviewController:**
```php
public function store(Request $request, Ticket $ticket)
{
    $this->authorize('review', $ticket);
    
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
    ]);
    
    $review = $ticket->reviews()->create([
        'reviewer_id' => auth()->id(),
        'reviewee_id' => $ticket->freelancer_id,
        'rating' => $validated['rating'],
        'comment' => $validated['comment'],
    ]);
    
    // Atualizar média do freelancer
    $ticket->freelancer->updateAverageRating();
    
    return new ReviewResource($review);
}
```

### 7.5 Notificações Real-Time

**Backend - Enviar Notificação:**
```php
// Ao criar ticket
$ticket = Ticket::create($validated);

// Notificar freelancer
$ticket->freelancer->notify(new NewTicketNotification($ticket));

// Broadcast
broadcast(new TicketCreated($ticket));
```

**Frontend - Notification Bell:**
```vue
<template>
  <div class="notification-bell" @click="toggleNotifications">
    <Icon name="bell" />
    <span v-if="unreadCount > 0">{{ unreadCount }}</span>
    
    <div v-if="showNotifications" class="dropdown">
      <div v-for="notif in notifications" :key="notif.id">
        {{ notif.data.message }}
      </div>
    </div>
  </div>
</template>

<script setup>
const { $echo } = useNuxtApp()
const notifications = ref([])
const unreadCount = computed(() => notifications.value.filter(n => !n.read_at).length)

onMounted(() => {
  // Carregar notificações existentes
  loadNotifications()
  
  // Listen para novas
  $echo.private(`user.${user.value.id}`)
    .notification((notification) => {
      notifications.value.unshift(notification)
    })
})
</script>
```

---

## Checklist de Implementação

### Docker e Infraestrutura
- [ ] Criar docker-compose.yml
- [ ] Configurar Dockerfile do PHP 8.5
- [ ] Configurar Dockerfile do Node
- [ ] Configurar Nginx
- [ ] Configurar Supervisor para Reverb
- [ ] Criar .gitignore
- [ ] Criar .env.example
- [ ] Testar subida dos containers

### Backend Laravel
- [ ] Instalar Laravel 12
- [ ] Configurar Sanctum
- [ ] Instalar Media Library
- [ ] Instalar L5-Swagger
- [ ] Configurar Reverb
- [ ] Criar migrations
- [ ] Criar models
- [ ] Criar policies
- [ ] Criar controllers
- [ ] Criar API resources
- [ ] Definir rotas com throttle
- [ ] Adicionar anotações Swagger
- [ ] Gerar documentação
- [ ] Criar events e listeners
- [ ] Testar endpoints

### Frontend Nuxt
- [ ] Criar projeto Nuxt 4.2
- [ ] Configurar CSR
- [ ] Instalar Laravel Echo
- [ ] Criar composables de API
- [ ] Configurar autenticação
- [ ] Criar plugin Echo
- [ ] Criar layouts
- [ ] Criar páginas
- [ ] Criar componentes
- [ ] Implementar filtros
- [ ] Implementar upload de imagens
- [ ] Testar WebSockets
- [ ] Testar notificações

### Testes e Deploy
- [ ] Testar autenticação
- [ ] Testar CRUD de serviços
- [ ] Testar CRUD de tickets
- [ ] Testar filtros avançados
- [ ] Testar upload de imagens
- [ ] Testar notificações real-time
- [ ] Testar rate limiting
- [ ] Revisar documentação Swagger
- [ ] Preparar para produção

---

## Comandos Úteis

### Docker
```bash
# Subir containers
docker-compose up -d

# Ver logs
docker-compose logs -f

# Entrar no container PHP
docker-compose exec php bash

# Entrar no container Nuxt
docker-compose exec nuxt sh
```

### Laravel
```bash
# Migrations
php artisan migrate

# Criar controller
php artisan make:controller ServiceController --api

# Criar model com migration
php artisan make:model Service -m

# Criar policy
php artisan make:policy ServicePolicy

# Gerar Swagger
php artisan l5-swagger:generate

# Rodar queue worker
php artisan queue:work

# Rodar Reverb
php artisan reverb:start
```

### Nuxt
```bash
# Dev mode
npm run dev

# Build
npm run build

# Preview
npm run preview
```

---

## Próximos Passos Após Implementação

1. **Testes Automatizados**
   - PHPUnit para backend
   - Vitest para frontend

2. **CI/CD**
   - GitHub Actions
   - Deploy automático

3. **Monitoramento**
   - Laravel Telescope (dev)
   - Sentry (produção)

4. **Performance**
   - Cache de queries
   - CDN para imagens
   - Redis cache

5. **Segurança**
   - CORS configurado
   - Rate limiting ajustado
   - Validações reforçadas
   - HTTPS em produção

---

**Data de Criação**: 6 de Janeiro de 2026  
**Versão**: 1.0  
**Última Atualização**: 6 de Janeiro de 2026

---

## ✅ Status de Implementação

### Infraestrutura (100% Completo)
- ✅ Docker Compose com 7 containers
- ✅ Nginx configurado como proxy reverso
- ✅ PHP 8.3-FPM com todas extensões necessárias
- ✅ MySQL 8.4 configurado
- ✅ Redis para cache e filas
- ✅ Laravel Reverb com Supervisor
- ✅ Queue Worker com Supervisor

### Backend - Laravel 12 (100% Completo)
- ✅ API RESTful estruturada
- ✅ Autenticação com Laravel Sanctum
- ✅ Sistema de autorização (Gates e Policies)
- ✅ Models com relationships completos
- ✅ Controllers com validações
- ✅ Resources para transformação de dados
- ✅ Media Library (Spatie) integrado
- ✅ Sistema de notificações com WebSocket
- ✅ Documentação Swagger completa
- ✅ Migrations e Seeders funcionais
- ✅ Query Scopes para filtros avançados

### Frontend - Nuxt 4.2 (95% Completo)
- ✅ Estrutura CSR configurada
- ✅ Autenticação com cookies
- ✅ Layouts (default, admin)
- ✅ Páginas principais (home, services, login, register)
- ✅ Dashboard por tipo de usuário
- ✅ Painel administrativo completo
- ✅ Sistema de notificações real-time
- ✅ Composables para API integration
- ✅ Middleware de autenticação e autorização
- ⏳ Formulário de criação de serviços (pendente)
- ⏳ Sistema de mensagens em tickets (pendente)

### Recursos Implementados
- ✅ Cadastro e login de usuários
- ✅ Perfis de freelancer e cliente
- ✅ Categorias de serviços com CRUD
- ✅ Listagem de serviços com filtros avançados
- ✅ Visualização detalhada de serviços
- ✅ Sistema de tickets (backend completo)
- ✅ Sistema de avaliações (backend completo)
- ✅ Notificações em tempo real via WebSocket
- ✅ Painel administrativo com 6 páginas
- ✅ Gerenciamento de usuários (admin)
- ✅ Gerenciamento de categorias (admin)
- ✅ Moderação de serviços (admin)
- ✅ Visualização de tickets (admin)
- ✅ Moderação de avaliações (admin)

### Funcionalidades Pendentes
- ⏳ Upload e gerenciamento de imagens de serviços (frontend)
- ⏳ Formulário de criação/edição de serviços (freelancer)
- ⏳ Página de detalhes do ticket com mensagens
- ⏳ Interface de avaliação de serviços (cliente)
- ⏳ Notificações visuais no frontend
- ⏳ Filtros na página de serviços
- ⏳ Paginação no frontend

### Bugs Corrigidos
- ✅ Estrutura de pastas duplicadas do Nuxt 4
- ✅ Middleware de autenticação assíncrono
- ✅ Inconsistência role vs user_type
- ✅ Tabela media faltando no banco
- ✅ Conversão de strings para números em valores decimais
- ✅ Rotas de categorias protegidas
- ✅ Login redirecionando corretamente por tipo de usuário
