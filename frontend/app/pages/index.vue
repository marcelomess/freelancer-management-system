<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">
        Sistema de Gestão de Freelancers
      </h1>
      <p class="text-xl text-gray-600">
        Conecte freelancers talentosos com clientes que precisam de seus serviços
      </p>
    </div>

    <div class="grid md:grid-cols-2 gap-8 mb-12">
      <div class="bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-indigo-600 mb-4">Para Freelancers</h2>
        <ul class="space-y-3 text-gray-700">
          <li>✓ Crie seu portfólio de serviços</li>
          <li>✓ Receba propostas de clientes</li>
          <li>✓ Gerencie seus projetos</li>
          <li>✓ Receba avaliações</li>
        </ul>
        <NuxtLink 
          to="/register?type=freelancer"
          class="mt-6 block w-full bg-indigo-600 text-white text-center py-3 rounded-md hover:bg-indigo-700"
        >
          Cadastrar como Freelancer
        </NuxtLink>
      </div>

      <div class="bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-green-600 mb-4">Para Clientes</h2>
        <ul class="space-y-3 text-gray-700">
          <li>✓ Encontre profissionais qualificados</li>
          <li>✓ Abra chamados para seus projetos</li>
          <li>✓ Acompanhe o andamento</li>
          <li>✓ Avalie os serviços</li>
        </ul>
        <NuxtLink 
          to="/register?type=client"
          class="mt-6 block w-full bg-green-600 text-white text-center py-3 rounded-md hover:bg-green-700"
        >
          Cadastrar como Cliente
        </NuxtLink>
      </div>
    </div>

    <div class="text-center">
      <h2 class="text-3xl font-bold text-gray-900 mb-8">
        Serviços em Destaque
      </h2>
      <NuxtLink 
        to="/services"
        class="inline-block bg-gray-900 text-white px-8 py-3 rounded-md hover:bg-gray-800"
      >
        Ver Todos os Serviços
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default'
})
</script>
