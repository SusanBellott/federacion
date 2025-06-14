<template>
    <div :class="['relative rounded-lg shadow-sm border overflow-hidden transition-transform hover:shadow-md hover:-translate-y-1', $attrs.class || 'bg-white border-gray-200']">
      <div class="p-5">
        <div class="flex items-center">
          <!-- Icono -->
          <div class="rounded-full p-3 mr-4 bg-white bg-opacity-50">
            <!-- Users -->
            <svg v-if="icon === 'users'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            
            <!-- Book open -->
            <svg v-else-if="icon === 'book-open'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            
            <!-- Clipboard -->
            <svg v-else-if="icon === 'clipboard-list'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            
            <!-- Badge check -->
            <svg v-else-if="icon === 'badge-check'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            
            <!-- Collection -->
            <svg v-else-if="icon === 'collection'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            
<!-- Inscripciones activas -->
<svg v-else-if="icon === 'user-check'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-8 0v2m4-10a4 4 0 100-8 4 4 0 000 8zm6 6l-2 2m0 0l-2-2m2 2v-6" />
</svg>

<!-- Desinscritos -->
<svg v-else-if="icon === 'user-x'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-8 0v2m4-10a4 4 0 100-8 4 4 0 000 8zm6 6l-4-4m0 0l-4 4" />
</svg>


            <!-- Default icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          
          <div>
            <!-- Título -->
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ title }}</h3>
            
            <!-- Valor -->
            <div class="mt-1 text-3xl font-semibold text-gray-800">{{ formatValue(value) }}</div>
          </div>
        </div>
      </div>
      
      <!-- Decorative accent bar -->
      <div :class="getAccentBarClass()" class="h-1 w-full absolute bottom-0"></div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue'
  
  const props = defineProps({
    title: {
      type: String,
      required: true
    },
    value: {
      type: [Number, String],
      required: true
    },
    icon: {
      type: String,
      default: 'default'
    }
  })
  
  // Formatea números grandes con separadores de miles
  const formatValue = (val) => {
    if (typeof val === 'number') {
      return val.toLocaleString('es-BO')
    }
    return val
  }
  
  // Determina el color de la barra de acento según el ícono
  const getAccentBarClass = () => {
    switch (props.icon) {
      case 'users':
        return 'bg-blue-600'
      case 'book-open':
        return 'bg-indigo-600'
      case 'clipboard-list':
        return 'bg-purple-600'
      case 'badge-check':
        return 'bg-green-600'
      case 'collection':
        return 'bg-teal-600' 
        case 'user-check':
  return 'bg-emerald-600'
case 'user-x':
  return 'bg-yellow-600'
      default:
        return 'bg-gray-600'
    }
  }
  </script>