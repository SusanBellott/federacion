<template>
    <div class="bg-white dark:bg-slate-800 shadow rounded-lg p-4 flex items-center">
      <div class="flex-1">
        <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ label }}</div>
        <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ value }}</div>
      </div>
      <svg class="h-8 w-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
        <!-- aquí podrías poner un ícono genérico -->
        <path d="M3 3h14v2H3V3zm0 5h14v2H3V8zm0 5h14v2H3v-2z" />
      </svg>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    label: String,
    value: [String, Number],
  })
  </script>
  