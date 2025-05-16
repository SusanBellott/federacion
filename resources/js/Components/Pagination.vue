<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
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
</script>

<template>
    <div
        v-if="pagination?.links?.length > 3"
        class="flex items-center justify-center mt-6 space-x-1"
    >
        <!-- Botón Atrás -->
        <template v-if="pagination.links[0].url">
            <Link
                :href="pagination.links[0].url"
                :data="filters"
                preserve-scroll
                preserve-state
                replace
                class="px-3 py-1 bg-gradient-to-r from-blue-800 to-sky-600 text-white rounded-lg hover:from-blue-700 hover:to-sky-500 transition-colors flex items-center"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 mr-1"
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
            class="px-3 py-1 bg-gray-300 text-gray-500 rounded-lg flex items-center cursor-not-allowed"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
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

        <!-- Números de página -->
        <template v-for="(link, index) in pagination.links" :key="index">
            <Link
                v-if="
                    index > 0 && index < pagination.links.length - 1 && link.url
                "
                :href="link.url"
                 :data="filters"
                preserve-scroll
                preserve-state
                replace
                class="px-3 py-1 rounded-md transition-colors"
                :class="{
                    'bg-gradient-to-r from-sky-900 to-sky-500 text-white':
                        link.active,
                    'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300':
                        !link.active,
                }"
            >
                {{ link.label }}
            </Link>
            <span
                v-else-if="index > 0 && index < pagination.links.length - 1"
                class="px-3 py-1 text-gray-400"
            >
                {{ link.label }}
            </span>
        </template>

        <!-- Botón Siguiente -->
        <template v-if="pagination.links[pagination.links.length - 1].url">
            <Link
                :href="pagination.links[pagination.links.length - 1].url"
                  :data="filters"
                preserve-scroll
                preserve-state
                replace
                class="px-3 py-1 bg-gradient-to-r from-blue-800 to-sky-600 text-white rounded-lg hover:from-blue-700 hover:to-sky-500 transition-colors flex items-center"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-1"
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
            class="px-3 py-1 bg-gray-300 text-gray-500 rounded-lg flex items-center cursor-not-allowed"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 ml-1"
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
