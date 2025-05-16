<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    ruta: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    placeholder: {
        type: String,
        default: "Buscar...",
    },
    perPageOptions: {
        type: Array,
        default: () => [10, 12, 24, 48],
    },
});

const searchTerm = ref(props.filters.search || "");
const perPage = ref(props.filters.perPage || props.perPageOptions[0]);

// Debounce para búsqueda
let timeout = null;
watch(searchTerm, () => {
    clearTimeout(timeout);
    timeout = setTimeout(buscar, 50);
});

watch(perPage, buscar);

function buscar() {
  console.log("Searching with perPage:", perPage.value); // Debug
    router.get(
        route(props.ruta),
        {
            search: searchTerm.value,
            perPage: perPage.value,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    );
}
</script>

<template>
    <div class="flex items-center space-x-4">
      <!-- Buscador -->
      <div class="relative flex-1 max-w-xs">
        <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
          <svg class="h-5 w-5 text-slate-500 dark:text-slate-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd" />
          </svg>
        </div>
        <input
          v-model="searchTerm"
          type="text"
          :placeholder="placeholder"
          class="
            block w-full pl-9 pr-3 py-2 
            bg-white dark:bg-slate-850 
            border border-gray-300 dark:border-white/40 
            rounded-lg 
            text-gray-800 dark:text-white
            placeholder-gray-500 dark:placeholder-slate-400
            focus:outline-none focus:ring-2 focus:ring-blue-500
          "
        />
      </div>
  
      <!-- Selector “Mostrar” -->
      <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-700 dark:text-white">Mostrar:</span>
        <select
          v-model="perPage"
          class="
            block py-2 pr-8 pl-3  
            bg-white dark:bg-slate-850 
            border border-gray-300 dark:border-white/40 
            rounded-lg 
            text-gray-800 dark:text-white
            placeholder-gray-500 dark:placeholder-slate-400
            focus:outline-none focus:ring-2 focus:ring-blue-500
          "
        >
          <option v-for="opt in perPageOptions" :key="opt" :value="opt">
            {{ opt }}
          </option>
        </select>
      </div>
    </div>
  </template>
  