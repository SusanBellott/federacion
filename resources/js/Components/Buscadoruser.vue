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
    <div class="flex items-center md:ml-auto md:pr-4 gap-4 w-full flex-wrap">
        <!-- Input de búsqueda -->
        <div class="relative flex-1 min-w-[200px]">
            <div
                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
            >
                <svg
                    class="h-5 w-5 text-black"
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
                v-model="searchTerm2"
                type="text"
                :placeholder="placeholder"
                class="pl-10 pr-3 py-2 block w-full rounded-lg border border-red-300 bg-white bg-clip-padding text-sm text-black placeholder:text-gray-600 outline-none transition-all focus:border-red-500 focus:ring focus:ring-red-200"
            />
        </div>

        <!-- Selector "Mostrar" -->
        <div class="relative flex-1 min-w-[200px]">
            <div
                class="flex items-center gap-2 sm:gap-3 mt-2 sm:mt-0 sm:w-auto"
            >
                <span class="text-sm text-black whitespace-nowrap"
                    >Mostrar:</span
                >
                <select
                    v-model="perPage2"
                    class="block w-full sm:w-auto appearance-none rounded-lg border border-red-300 bg-white bg-clip-padding px-3 py-2 text-sm text-black outline-none transition-all focus:border-red-500 focus:ring focus:ring-red-200 cursor-pointer min-w-[80px]"
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
    </div>
</template>
