<script setup>
import { Link } from "@inertiajs/vue3";
import { computed, ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
        validator: (value) => value.links !== undefined,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

// Reactive screen width
const screenWidth = ref(window.innerWidth);

const updateScreenWidth = () => {
    screenWidth.value = window.innerWidth;
};

onMounted(() => {
    window.addEventListener('resize', updateScreenWidth);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScreenWidth);
});

// Función para generar links inteligentes según el tamaño de pantalla
const getVisiblePages = computed(() => {
    const links = props.pagination.links;
    if (!links || links.length <= 3) return links.slice(1, -1);
    
    const pageLinks = links.slice(1, -1); // Excluir prev/next
    const currentPageIndex = pageLinks.findIndex(link => link.active);
    
    // Responsive: mostrar menos páginas en pantallas pequeñas
    let maxVisible;
    if (screenWidth.value < 360) {
        maxVisible = 1; // Solo página actual en pantallas muy pequeñas
    } else if (screenWidth.value < 480) {
        maxVisible = 2; // 2 páginas en móviles pequeños
    } else if (screenWidth.value < 640) {
        maxVisible = 3; // 3 páginas en móviles
    } else {
        maxVisible = 5; // 5 páginas en desktop
    }
    
    if (pageLinks.length <= maxVisible) {
        return pageLinks;
    }
    
    const start = Math.max(0, Math.min(
        currentPageIndex - Math.floor(maxVisible / 2),
        pageLinks.length - maxVisible
    ));
    
    const visiblePages = pageLinks.slice(start, start + maxVisible);
    
    // Para pantallas muy pequeñas, solo mostrar la página actual
    if (screenWidth.value < 360) {
        return pageLinks.filter(link => link.active);
    }
    
    // Agregar puntos suspensivos y primera/última página si es necesario
    const result = [];
    
    if (start > 0) {
        result.push(pageLinks[0]);
        if (start > 1) {
            result.push({ label: '...', url: null, active: false });
        }
    }
    
    result.push(...visiblePages);
    
    if (start + maxVisible < pageLinks.length) {
        if (start + maxVisible < pageLinks.length - 1) {
            result.push({ label: '...', url: null, active: false });
        }
        result.push(pageLinks[pageLinks.length - 1]);
    }
    
    return result;
});

// Computed para obtener información de la página actual
const currentPageInfo = computed(() => {
    const links = props.pagination.links;
    if (!links) return null;
    
    const pageLinks = links.slice(1, -1);
    const currentPage = pageLinks.find(link => link.active);
    const totalPages = pageLinks.length;
    
    return {
        current: currentPage ? currentPage.label : '1',
        total: totalPages
    };
});
</script>

<template>
    <div
        v-if="pagination?.links?.length > 3"
        class="flex justify-center items-center mt-4 sm:mt-6 gap-1 px-2 py-1"
    >
        <!-- Botón Atrás -->
        <template v-if="pagination.links[0].url">
            <Link
                :href="pagination.links[0].url"
                :data="filters"
                preserve-scroll
                preserve-state
                replace
                class="group flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 sm:h-5 sm:w-5 group-hover:-translate-x-0.5 transition-transform duration-200"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>
            </Link>
        </template>
        <span
            v-else
            class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed shadow-sm"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 sm:h-5 sm:w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                />
            </svg>
        </span>

        <!-- Información de página en pantallas muy pequeñas -->
        <div
            v-if="screenWidth < 360"
            class="flex items-center justify-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium min-w-[60px]"
        >
            {{ currentPageInfo?.current }} / {{ currentPageInfo?.total }}
        </div>

        <!-- Números de página (solo en pantallas >= 360px) -->
        <template v-else>
            <template v-for="(link, index) in getVisiblePages" :key="index">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :data="filters"
                    preserve-scroll
                    preserve-state
                    replace
                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg transition-all duration-300 text-center text-xs sm:text-sm font-semibold flex items-center justify-center hover:scale-110 shadow-sm hover:shadow-md"
                    :class="{
                        'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md shadow-indigo-500/30 ring-1 ring-indigo-500/20':
                            link.active,
                        'bg-white text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 border border-gray-200 hover:border-indigo-300':
                            !link.active,
                    }"
                >
                    {{ link.label }}
                </Link>
                <span
                    v-else
                    class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 text-center text-xs sm:text-sm font-semibold flex items-center justify-center"
                >
                    {{ link.label }}
                </span>
            </template>
        </template>

        <!-- Botón Siguiente -->
        <template v-if="pagination.links[pagination.links.length - 1].url">
            <Link
                :href="pagination.links[pagination.links.length - 1].url"
                :data="filters"
                preserve-scroll
                preserve-state
                replace
                class="group flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 sm:h-5 sm:w-5 group-hover:translate-x-0.5 transition-transform duration-200"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd"
                    />
                </svg>
            </Link>
        </template>
        <span
            v-else
            class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed shadow-sm"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 sm:h-5 sm:w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                />
            </svg>
        </span>
    </div>
</template>