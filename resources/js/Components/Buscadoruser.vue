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
        default: () => [5, 10, 12, 24],
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
    <div class="flex items-center md:ml-auto md:pr-4 gap-4">
        <!-- Barra de búsqueda -->
        <div class="relative flex-1">
            <div class="text-sm ease leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd" />
            </svg>
        </div>
            <input v-model="searchTerm2" type="text"
                class="pl-9 text-sm focus:shadow-primary-outline ease w-full leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 dark:bg-slate-850 dark:text-white bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none focus:transition-shadow"
                :placeholder="placeholder" />
        </div>

        <!-- Selector de items por página -->
        <div class="flex items-center gap-2">
    <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Mostrar:</span>
    <select 
        v-model="perPage2"
        class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease inline-block appearance-none rounded-lg border border-solid border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 bg-clip-padding px-3 py-2 font-normal text-gray-700 dark:text-gray-300 outline-none transition-all placeholder:text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-auto min-w-[80px]"
    >
        <option v-for="option in perPageOptions2" :value="option" :key="option">
            {{ option }}
        </option>
    </select>
</div>
    </div>
</template>
