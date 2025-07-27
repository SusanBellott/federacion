<template>
    <div class="max-w-3xl mx-auto px-3 py-3">
        <div
            class="bg-gradient-to-br from-violet-900 to-indigo-900 p-4 rounded-lg shadow-lg border border-purple-500/20"
        >
            <div class="text-center mb-2">
                <h3 class="text-sm font-bold text-white mb-1">
                    📅 Generar Lista de Inscritos
                </h3>
            </div>

            <div
                class="bg-gradient-to-br from-violet-900 to-indigo-900 p-3 rounded-md shadow-md border border-purple-600/50"
            >
                <!-- Encabezado -->
                <label class="block text-purple-300 text-sm mb-2 font-semibold"
                    >Buscar Cursos</label
                >

                <!-- Grid dinámico -->
                <div
                    :class="[
                        'w-full grid gap-4 transition-all duration-300',
                        cursoSeleccionado
                            ? 'grid-cols-1 md:grid-cols-2'
                            : 'grid-cols-1',
                    ]"
                >
                    <!-- Input de búsqueda -->
                    <div class="relative w-full" ref="wrapperRef">
                        <input
                            v-model="busquedaCurso"
                            @focus="dropdownVisible = true"
                            @input="dropdownVisible = true"
                            type="text"
                            placeholder="Escriba el nombre del curso"
                            class="w-full px-4 py-2 rounded-md bg-violet-950 text-white text-sm border border-violet-700 focus:outline-none focus:ring-2 focus:ring-purple-400 transition"
                        />

                        <!-- Lista desplegable -->
                        <ul
                            v-if="dropdownVisible && cursosFiltrados.length > 0"
                            class="absolute z-20 w-full bg-violet-950 border border-violet-700 rounded mt-1 max-h-64 overflow-y-auto shadow-lg"
                        >
                            <li
                                v-for="curso in cursosFiltrados"
                                :key="curso.uuid_curso"
                                @click="seleccionarCurso(curso)"
                                class="px-4 py-2 text-sm text-white hover:bg-purple-700 cursor-pointer transition"
                            >
                                {{ curso.nombre }} ({{ curso.codigo_curso }})
                            </li>
                        </ul>

                        <!-- Sin resultados -->
                        <div
                            v-if="
                                dropdownVisible && cursosFiltrados.length === 0
                            "
                            class="absolute z-10 w-full bg-violet-950 border border-violet-700 rounded mt-1 px-4 py-2 text-pink-300 text-sm"
                        >
                            No se encontraron cursos.
                        </div>
                    </div>

                    <!-- Resultado seleccionado -->
                    <div
                        v-if="cursoSeleccionado"
                        class="flex items-center justify-between bg-violet-950 px-4 py-3 rounded-md border border-violet-700"
                    >
                        <span class="text-white text-sm font-medium">
                            {{ cursoSeleccionado.nombre }}
                        </span>

                        <div class="flex gap-2 items-center">
                            <button
                                @click="
                                    abrirReporte(cursoSeleccionado.uuid_curso)
                                "
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition"
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
                            </button>

                            <button
                                @click="limpiarSeleccion"
                                class="bg-red-500/20 hover:bg-red-600/40 border border-red-400/40 text-red-300 hover:text-white text-xs px-3 py-1 rounded transition font-semibold"
                                title="Quitar selección"
                            >
                                ✖
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const props = defineProps({
    cursos: {
        type: Array,
        default: () => [], // ✅ evita undefined
    },

    stats: {
        type: Object,
        default: () => ({}),
    },
});
console.log("Cursos recibidos:", props.cursos);

const cursoSeleccionado = ref(null);
const busquedaCurso = ref("");
const dropdownVisible = ref(false);
//
const wrapperRef = ref(null);
//
const cursos = computed(() => props.cursos);

const cursosFiltrados = computed(() => {
    const query = busquedaCurso.value.toLowerCase();
    return cursos.value.filter(
        (curso) =>
            curso.nombre.toLowerCase().includes(query) ||
            curso.codigo_curso.toLowerCase().includes(query)
    );
});

function seleccionarCurso(curso) {
    cursoSeleccionado.value = curso;
    busquedaCurso.value = curso.nombre; // Opcional: poner nombre en input
    dropdownVisible.value = false;
}

function abrirReporte(uuid) {
    window.open(`/reporte/inscritos/curso/${uuid}`, "_blank");
}

watch(cursos, (nuevos) => {
    console.log("Cursos actualizados:", nuevos);
});

function limpiarSeleccion() {
    cursoSeleccionado.value = null;
    busquedaCurso.value = "";
    dropdownVisible.value = false;
}

function handleClickOutside(event) {
    if (wrapperRef.value && !wrapperRef.value.contains(event.target)) {
        dropdownVisible.value = false;
    }
}
onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
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
