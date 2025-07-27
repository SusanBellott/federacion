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
    <!-- Contenedor responsive -->
    <div
        class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-4 w-full"
    >
        <!-- Buscador -->
        <div class="relative flex-1 min-w-0">
            <div
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
            >
                <svg
                    class="h-4 w-4 sm:h-5 sm:w-5 text-slate-500 dark:text-slate-400"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <input
                v-model="searchTerm"
                type="text"
                :placeholder="placeholder"
                class="pl-10 pr-3 py-2 block w-full rounded-lg border border-violet-700 bg-violet-950 bg-clip-padding text-sm text-white placeholder:text-violet-300 outline-none transition-all focus:border-blue-400 focus:shadow-primary-outline"
            />
        </div>

        <!-- Selector "Mostrar" -->
          <div class="relative flex-1 min-w-[10px]">
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <span
                class="text-sm text-gray-700 dark:text-white whitespace-nowrap"
            >
                Mostrar:
            </span>
            <select
                v-model="perPage"
                class="block w-full appearance-none rounded-lg border border-violet-700 bg-violet-950 bg-clip-padding px-3 py-2 font-normal text-sm leading-5.6 text-white placeholder:text-violet-300 outline-none transition-all focus:border-blue-400 focus:outline-none focus:shadow-primary-outline cursor-pointer"
            >
                <option v-for="opt in perPageOptions" :key="opt" :value="opt">
                    {{ opt }}
                </option>
            </select>
        </div>
         </div>
    </div>
</template>
