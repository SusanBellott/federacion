<template>
    <div class="max-w-3xl mx-auto px-3 py-3">
        <!-- Contenedor principal más compacto -->
        <div
            class="relative z-30 bg-red-600/15 backdrop-blur-md p-3 rounded-lg shadow-2xl border border-red-600/60"
        >
            <div class="text-center mb-2 col-span-2">
                <h3 class="text-sm font-bold text-black mb-1">
                    📊 Reporte Anual
                </h3>
            </div>
            <!-- Reporte Anual -->
            <div
                class="bg-red-600/10 backdrop-blur-md p-3 rounded-md shadow-md border border-red-600/50"
            >
                <!-- Selección de año -->
                <div class="grid grid-cols-2 gap-3 items-end">
                    <div>
                        <label
                            class="block text-xs font-semibold text-black mb-1"
                        >
                            Seleccionar año
                        </label>
                        <select
                            v-model="selectedYear"
                            class="w-full bg-white text-black border border-red-600/50 rounded text-sm px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:border-red-600 shadow-sm appearance-none pr-7 bg-no-repeat bg-[length:14px_14px] bg-[right_0.5rem_center] bg-[url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23dc2626%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>')]"
                        >
                            <option
                                v-for="year in añosDisponibles"
                                :key="year"
                                :value="year"
                            >
                                {{ year }}
                            </option>
                        </select>
                    </div>

                    <button
                        @click="descargarReporteCursosPorMes"
                        class="w-full px-3 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded text-xs font-medium shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-1"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3 w-3"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                            />
                        </svg>
                        Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
const { stats } = usePage().props;

const selectedYear = ref(null);
const añosDisponibles = ref([]);

onMounted(async () => {
    try {
        const response = await fetch("/reportes/anios-disponibles");
        const data = await response.json();
        añosDisponibles.value = data;

        if (data.length > 0) {
            selectedYear.value = data.at(-1);
        }
    } catch (error) {
        console.error("Error al obtener años disponibles:", error);
    }
});

//const selectedYear = new Date().getFullYear(); // O puedes hacer esto dinámico según tu selector

// Año actual y rango
const currentYear = new Date().getFullYear();
const years = computed(() => {
    const start = 2025;
    const end = 2050;
    const list = [];
    for (let y = start; y <= end; y++) list.push(y);
    return list;
});

//const selectedYear = ref(currentYear >= 2025 ? currentYear : 2025);

const descargarReporteCursosPorMes = () => {
    const year = selectedYear.value;
    window.open(`/reportes/cursos-por-mes/${year}`, "_blank");
};

//////////////

const selectedMonth = ref(new Date().getMonth() + 1);

const meses = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
];
const generarReporte = async () => {
    if (!selectedYear.value || !selectedMonth.value) {
        Swal.fire({
            icon: "warning",
            title: "Campos faltantes",
            text: "Por favor, seleccione un año y un mes.",
        });
        return;
    }

    const año = selectedYear.value;
    const mes = selectedMonth.value;

    try {
        const response = await fetch(`/reporte/verificar-mes/${año}/${mes}`);
        const data = await response.json();

        if (data.existe) {
            window.open(`/reporte/mes/${año}/${mes}`, "_blank");
        } else {
            let mensaje =
                "No hay cursos registrados en ese mes. No se puede generar el PDF.";
            if (!data.soloAnio) {
                mensaje = "No hay reportes en el año y mes seleccionados.";
            }

            Swal.fire({
                icon: "warning",
                title: "Sin datos",
                text: mensaje,
                confirmButtonColor: "#6366F1",
                confirmButtonText: "Aceptar",
            });
        }
    } catch (error) {
        console.error("Error al verificar el mes:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un error al verificar los datos.",
        });
    }
};

//

const generarReporteInscritos = (uuidCurso) => {
    const url = `/reporte/inscritos/${uuidCurso}`;
    window.open(url, "_blank"); // Abre el reporte en nueva pestaña
};
///////////
</script>

<style scoped>
/* Estilos para mejorar la legibilidad en móviles */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .text-lg {
        font-size: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-xs {
        font-size: 0.75rem;
    }
}
</style>
