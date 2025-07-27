<template>
    <div class="max-w-3xl mx-auto px-3 py-3">
        <!-- Contenedor principal más compacto -->
        <div
            class="bg-gradient-to-br from-violet-900 to-indigo-900 p-3 rounded-lg shadow-lg border border-purple-500/20"
        >
            <div class="text-center mb-2">
                <h3 class="text-sm font-bold text-white mb-1">
                    📅 Reporte Mensual
                </h3>
            </div>
            <!-- Reporte Mensual -->
            <div
                class="bg-gradient-to-br from-violet-900 to-indigo-900 p-3 rounded-md shadow-md border border-purple-600/50"
            >
                <!-- Selectores compactos -->
                <div class="grid grid-cols-3 gap-3 mb-2">
                    <!-- Selector de Año -->
                    <div>
                        <label
                            class="block text-white text-xs font-medium text-center mb-1"
                        >
                            Año
                        </label>
                        <select
                            v-model="selectedYear"
                            class="w-full bg-violet-950 text-white border border-violet-700 rounded text-xs px-2 py-1.5 focus:ring-1 focus:ring-purple-400"
                        >
                            <option
                                v-for="year in years"
                                :key="year"
                                :value="year"
                            >
                                {{ year }}
                            </option>
                        </select>
                    </div>

                    <!-- Selector de Mes en formato radio -->
                    <div>
                        <label
                            class="block text-white text-xs font-medium text-center mb-1"
                            >Mes</label
                        >
                        <div class="grid grid-cols-2 gap-1">
                            <label
                                v-for="(mes, index) in meses"
                                :key="index"
                                class="flex items-center text-white text-xs"
                            >
                                <input
                                    type="radio"
                                    :value="index + 1"
                                    v-model="selectedMonth"
                                    class="form-radio text-purple-600 mr-2"
                                />
                                {{ mes }}
                            </label>
                        </div>
                    </div>
                    <!-- Botón Generar PDF -->
                    <div class="flex items-start justify-center pt-5">
                        <button
                            @click="generarReporte"
                            class="w-full px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded text-xs font-medium shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-center gap-1"
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
                            Generar PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";

import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const years = ref([]);
const mesesDisponibles = ref([]);

const selectedYear = ref(null);
const selectedMonth = ref(null);

// Trae años disponibles al cargar
onMounted(async () => {
    try {
        const res = await fetch("/reporte/anios-disponibles");
        years.value = await res.json();

        // Si hay años, selecciona el último por defecto y carga meses
        if (years.value.length > 0) {
            selectedYear.value = years.value[years.value.length - 1];
            await cargarMesesDisponibles(selectedYear.value);
        }
    } catch (err) {
        console.error("Error al cargar años disponibles:", err);
    }
});

watch(selectedYear, async (nuevoAnio) => {
    if (nuevoAnio) {
        await cargarMesesDisponibles(nuevoAnio);
    }
});

const cargarMesesDisponibles = async (anio) => {
    try {
        const res = await fetch(`/reporte/meses-disponibles/${anio}`);
        mesesDisponibles.value = await res.json();

        // Si hay meses, selecciona el último por defecto
        if (mesesDisponibles.value.length > 0) {
            selectedMonth.value =
                mesesDisponibles.value[mesesDisponibles.value.length - 1];
        }
    } catch (err) {
        console.error("Error al cargar meses:", err);
    }
};

const { stats } = usePage().props;

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
