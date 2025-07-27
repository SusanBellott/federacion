<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/MainLayout.vue";
import ReporteInscritos from "@/Components/m_Reportes/ReporteInscritos.vue";
import ReporteMensual from "@/Components/m_Reportes/ReporteMensual.vue";
import ReporteAnual from "@/Components/m_Reportes/ReporteAnual.vue";

const page = usePage();

const props = defineProps({
    cursos: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <AppLayout title="Reportes">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Reportes
            </h2>
        </template>
        <div class="flex-none w-full max-w-full px-3">
            <h6 class="text-gray-800 dark:text-white">Reportes</h6>
        </div>
        <ReporteInscritos :cursos="cursos" :stats="stats" />
        <ReporteMensual :stats="stats" />
        <ReporteAnual :stats="stats" />
    </AppLayout>
</template>
