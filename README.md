# Sistema de Gestão de Freelancers

Plataforma marketplace onde prestadores de serviço (freelancers) podem cadastrar portfólios e clientes podem abrir chamados (tickets) para contratar serviços.

## 🚀 Stack Tecnológica

- **Backend**: Laravel 12 (API RESTful)
- **Frontend**: Nuxt 4.2 (Client-Side Rendering)
- **Banco de Dados**: MySQL 8.4
- **Cache/Filas**: Redis
- **WebSockets**: Laravel Reverb
- **PHP**: 8.5
- **Node.js**: 20+
- **Ambiente**: Docker + Docker Compose

## ✨ Recursos Principais

- ✅ **Autenticação**: Laravel Sanctum (SPA tokens)
- ✅ **Autorização**: Gates e Policies nativos do Laravel
- ✅ **Upload de Imagens**: Laravel Media Library (processamento síncrono)
- ✅ **Storage**: Local (preparado para S3 futuro)
- ✅ **WebSockets**: Laravel Reverb com Supervisor para auto-restart
- ✅ **Rate Limiting**: Throttle nas rotas da API (60 req/min)
- ✅ **Documentação API**: Swagger/OpenAPI (L5-Swagger)
- ✅ **Filtros Avançados**: Query Scopes por categoria, preço e avaliação
- ✅ **Notificações Real-Time**: Sistema de notificações via WebSocket
- ✅ **Painel Administrativo**: CRUD completo para usuários, categorias, serviços, tickets e avaliações
- ✅ **Sistema de Portfólio**: Freelancers podem cadastrar serviços com múltiplas imagens
- ✅ **Sistema de Tickets**: Clientes podem abrir chamados para contratar serviços
- ✅ **Sistema de Avaliações**: Avaliação de serviços e freelancers com ratings

## 📁 Estrutura do Projeto

```
freelancer-management-system/
├── docker/                 # Configurações Docker
│   ├── nginx/             # Config Nginx
│   ├── php/               # Dockerfile e config PHP
│   ├── reverb/            # Supervisor para Reverb
│   └── mysql/             # Config MySQL
├── backend/               # Laravel 12 API
├── frontend/              # Nuxt 4.2 Application
├── docker-compose.yml     # Orquestração dos containers
├── .env.example           # Template de variáveis de ambiente
└── implementation.md      # Plano detalhado de implementação
```

## 🛠️ Instalação e Configuração

### Pré-requisitos

- Docker
- Docker Compose
- Git

### Passo 1: Clone o repositório

```bash
git clone https://github.com/seu-usuario/freelancer-management-system.git
cd freelancer-management-system
```

### Passo 2: Configure as variáveis de ambiente

```bash
cp .env.example .env
# Edite o arquivo .env conforme necessário
```

### Passo 3: Suba os containers Docker

```bash
docker-compose up -d
```

### Passo 4: Instale as dependências do Laravel

```bash
docker exec freelancer-php composer install
docker exec freelancer-php php artisan key:generate
docker exec freelancer-php php artisan storage:link
docker exec freelancer-php php artisan migrate --seed
docker exec freelancer-php php artisan l5-swagger:generate
```

### Passo 5: Instale as dependências do Nuxt

```bash
docker exec freelancer-nuxt npm install
```

## 🔐 Credenciais Padrão

Após executar o seed (`php artisan migrate --seed`), as seguintes credenciais estarão disponíveis:

### Admin
- **Email**: admin@example.com
- **Senha**: password

### Freelancer
- **Email**: freelancer@example.com
- **Senha**: password

### Cliente
- **Email**: client@example.com
- **Senha**: password

## 🚀 Execução

### Backend (Laravel API)
```bash
# O backend estará disponível em http://localhost
docker-compose up -d
```

### Frontend (Nuxt)
```bash
# O frontend estará disponível em http://localhost:3000
# Já está incluído no docker-compose up
```

### WebSocket (Laravel Reverb)
```bash
# Reverb estará disponível em ws://localhost:8080
# Já está incluído no docker-compose up com auto-restart via Supervisor
```

### URLs do Sistema

- **Frontend**: http://localhost:3000
- **API Backend**: http://localhost/api
- **Documentação API (Swagger)**: http://localhost/api/documentation
- **WebSocket**: ws://localhost:8080
- **Painel Admin**: http://localhost:3000/admin

## 📚 Documentação da API

Após subir os containers, a documentação Swagger estará disponível em:

```
http://localhost/api/documentation
```

## 🔑 Principais Endpoints

### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Login
- `POST /api/logout` - Logout (autenticado)

### Services (Portfólio)
- `GET /api/services` - Listar serviços com filtros
- `POST /api/services` - Criar serviço (freelancer)
- `GET /api/services/{id}` - Detalhes do serviço
- `PUT /api/services/{id}` - Atualizar serviço
- `DELETE /api/services/{id}` - Deletar serviço

### Tickets (Chamados)
- `GET /api/tickets` - Listar tickets
- `POST /api/tickets` - Criar ticket (cliente)
- `GET /api/tickets/{id}` - Detalhes do ticket
- `PUT /api/tickets/{id}` - Atualizar ticket
- `DELETE /api/tickets/{id}` - Deletar ticket

### Reviews (Avaliações)
- `GET /api/reviews` - Listar avaliações
- `POST /api/reviews` - Criar avaliação
- `GET /api/reviews/{id}` - Detalhes da avaliação

### Notifications (Notificações)
- `GET /api/notifications` - Listar notificações
- `POST /api/notifications/{id}/read` - Marcar como lida

### Admin Panel (Painel Administrativo)
- `GET /api/admin/stats` - Estatísticas do sistema
- `GET /api/admin/users` - Listar usuários (com filtros)
- `DELETE /api/admin/users/{id}` - Deletar usuário
- `GET /api/admin/services` - Listar todos os serviços
- `DELETE /api/admin/services/{id}` - Deletar serviço
- `GET /api/admin/tickets` - Listar todos os tickets
- `GET /api/admin/reviews` - Listar todas as avaliações
- `DELETE /api/admin/reviews/{id}` - Deletar avaliação

### Categories (Categorias)
- `GET /api/categories` - Listar categorias
- `POST /api/categories` - Criar categoria (admin)
- `PUT /api/categories/{id}` - Atualizar categoria (admin)
- `DELETE /api/categories/{id}` - Deletar categoria (admin)

## 🎯 Filtros Avançados

A API suporta filtros avançados nos endpoints de serviços:

```bash
GET /api/services?category=1&min_price=100&max_price=500&min_rating=4
```

**Parâmetros disponíveis:**
- `category` - ID da categoria
- `min_price` - Preço mínimo
- `max_price` - Preço máximo
- `min_rating` - Avaliação mínima (1-5)
- `page` - Número da página (paginação)

## 🔔 WebSockets e Notificações

O sistema utiliza Laravel Reverb para notificações em tempo real:

- Nova proposta recebida
- Ticket atualizado
- Mensagem enviada
- Avaliação recebida

## 📦 Estrutura do Banco de Dados

### Principais Tabelas

- `users` - Usuários do sistema (com campo `user_type`: admin, freelancer, client)
- `freelancers` - Perfis de freelancers
- `clients` - Perfis de clientes
- `categories` - Categorias de serviços (com descrição)
- `services` - Portfólio de serviços
- `tickets` - Chamados/solicitações
- `reviews` - Avaliações
- `notifications` - Notificações
- `media` - Biblioteca de mídia (Spatie Media Library)

## 🧪 Testes

### Backend (Laravel)
```bash
docker-compose exec php php artisan test
```

### Frontend (Nuxt)
```bash
docker-compose exec nuxt npm run test
```

## 📝 Comandos Úteis

### Docker
```bash
# Ver logs
docker-compose logs -f

# Entrar no container PHP
docker-compose exec php bash

# Entrar no container Nuxt
docker-compose exec nuxt sh

# Parar containers
docker-compose down

# Rebuild containers
docker-compose up -d --build
```

### Laravel
```bash
# Criar migration
docker exec freelancer-php php artisan make:migration create_example_table

# Criar model
docker exec freelancer-php php artisan make:model Example -m

# Criar controller
docker exec freelancer-php php artisan make:controller ExampleController --api

# Gerar documentação Swagger
docker exec freelancer-php php artisan l5-swagger:generate

# Rodar queue worker
docker exec freelancer-php php artisan queue:work

# Iniciar Reverb (já inicia automaticamente via Supervisor)
docker exec freelancer-php php artisan reverb:start

# Ver logs do Reverb
docker logs -f freelancer-reverb

# Executar seeders
docker exec freelancer-php php artisan db:seed
```

## 🎨 Interface e Páginas

### Área Pública
- `/` - Home page com listagem de serviços
- `/services` - Listagem completa de serviços com filtros
- `/services/{id}` - Detalhes do serviço
- `/login` - Login
- `/register` - Registro

### Dashboard do Cliente
- `/dashboard/client` - Dashboard principal
- `/tickets` - Meus tickets

### Dashboard do Freelancer
- `/dashboard/freelancer` - Dashboard principal
- Criação e gerenciamento de serviços (em desenvolvimento)

### Painel Administrativo
- `/admin` - Dashboard com estatísticas
- `/admin/users` - Gerenciar usuários (listar, filtrar, deletar)
- `/admin/categories` - Gerenciar categorias (CRUD completo)
- `/admin/services` - Visualizar e moderar serviços
- `/admin/tickets` - Visualizar todos os tickets
- `/admin/reviews` - Visualizar e moderar avaliações

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT.

## 📞 Suporte

Para mais informações detalhadas sobre implementação, consulte o arquivo [implementation.md](implementation.md).

---

**Desenvolvido com ❤️ usando Laravel 12 e Nuxt 4.2**
