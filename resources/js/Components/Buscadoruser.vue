<script setup>
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    ruta: {
        type: String,
        required: true,
    },
    filters2: {
        type: Object,
        default: () => ({}),
    },
    placeholder: {
        type: String,
        default: "Buscar...",
    },
    perPageOptions2: {
        type: Array,
        default: () => [5,10, 12, 24],
    },
});

const searchTerm2 = ref(props.filters2.searchuser || "");
const perPage2 = ref(props.filters2.perPage2 || props.perPageOptions2[0]);

// Debounce para búsqueda
let timeout = null;
watch(searchTerm2, () => {
    clearTimeout(timeout);
    timeout = setTimeout(buscar, 50);
});

watch(perPage2, buscar);

function buscar() {
    router.get(
        route(props.ruta),
        {
            searchuser: searchTerm2.value,
            perPage2: perPage2.value,
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        }
    );
}
defineExpose({ searchTerm2 });
</script>

<template>
    <div
        class="flex flex-col md:flex-row gap-4 mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow"
    >
        <!-- Barra de búsqueda -->
        <div class="relative flex-1">
            <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500"
            >
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <input
                v-model="searchTerm2"
                type="text"
                class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                :placeholder="placeholder"
            />
        </div>

        <!-- Selector de items por página -->
        <div class="flex items-center">
            <span class="mr-2 text-sm text-gray-600 dark:text-gray-400"
                >Mostrar:</span
            >
            <select
                v-model="perPage2"
                class="border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
                <option
                    v-for="option in perPageOptions2"
                    :value="option"
                    :key="option"
                >
                    {{ option }}
                </option>
            </select>
        </div>
    </div>
</template>
