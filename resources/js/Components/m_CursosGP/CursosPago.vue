<script setup>
import { ref, computed, onMounted, nextTick, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    cursos_pago: {
        type: [Array, Object],
        required: true,
    },
    misCursosIds: {
        type: Array,
        default: () => [],
    },
});

// Estados reactivos
const misCursos = ref([...props.misCursosIds.map((id) => Number(id))]);
const loadingId = ref(null);
const contadoresInscritos = ref({});

// funciones de modal
const cursoModalId = ref(null);

const abrirModal = (curso) => {
    cursoModalId.value = curso.id_curso;
};

const cerrarModal = () => {
    cursoModalId.value = null;
};

// Constante para la fecha actual
const hoy = new Date();
hoy.setHours(0, 0, 0, 0); // Normalizar la hora para comparaciones

// Función helper para normalizar la lista de cursos
const getCursosList = () => {
    if (
        props.cursos_pago &&
        typeof props.cursos_pago === "object" &&
        Array.isArray(props.cursos_pago.data)
    ) {
        return props.cursos_pago.data;
    }

    if (Array.isArray(props.cursos_pago)) {
        return props.cursos_pago;
    }

    return [];
};

//const cursosFiltrados = computed(() => getCursosList());

// Inicializar contadores y configuración
onMounted(() => {
    const cursosList = getCursosList();
    cursosList.forEach((curso) => {
        console.log(`Curso ID ${curso.id_curso}: ${curso.total_inscritos}`);
    });
    // Inicializar contadores con los valores del backend
    cursosList.forEach((curso) => {
        contadoresInscritos.value[curso.id_curso] = curso.total_inscritos || 0;
    });

    // Configurar tema dark
    document.documentElement.classList.add("dark");

    console.log("Cursos cargados:", cursosList.length);
    console.log("Mis cursos iniciales:", props.misCursosIds);
});
console.log("Cursos recibidos del backend:", props.cursos_pago);

console.log("Primer curso (debug):", getCursosList()[0]);

console.log("Cursos list:", getCursosList());
console.log("props.cursos_pago:", props.cursos_pago); // ✅

// Watcher para sincronizar cambios en misCursosIds desde el parent
watch(
    () => props.misCursosIds,
    (newIds) => {
        misCursos.value = [...newIds.map((id) => Number(id))];
    },
    { deep: true }
);

// Computed para filtrar cursos por fecha de inscripción
const cursosFiltrados = computed(() => {
    const cursosList = getCursosList();

    return cursosList.filter((curso) => {
        // Mostrar aunque no tenga fechas de inscripción (por ejemplo cursos de pago)
        if (!curso.fecha_inicio_inscripcion || !curso.fecha_fin_inscripcion) {
            return true;
        }

        const inicio = new Date(curso.fecha_inicio_inscripcion);
        inicio.setHours(0, 0, 0, 0);

        const fin = new Date(curso.fecha_fin_inscripcion);
        fin.setHours(23, 59, 59, 999);

        return hoy >= inicio && hoy <= fin;
    });
});

onMounted(() => {
    console.log("props.cursos_pago:", props.cursos_pago);

    if (Array.isArray(props.cursos_pago)) {
        console.log(
            "Es un array directo con",
            props.cursos_pago.length,
            "elementos"
        );
    } else if (
        typeof props.cursos_pago === "object" &&
        props.cursos_pago.data
    ) {
        console.log(
            "Es paginación con",
            props.cursos_pago.data.length,
            "elementos"
        );
    } else {
        console.warn("Formato de cursos no reconocido");
    }
});

// Formatear fecha para mostrar
const formatDate = (dateString) => {
    if (!dateString) return "N/A";

    try {
        const parts = dateString.split("-");
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const day = parseInt(parts[2], 10);
        const date = new Date(year, month, day); // esto NO usa UTC

        return date.toLocaleDateString("es-BO", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    } catch (error) {
        console.error("Fecha inválida", error);
        return "Fecha inválida";
    }
};

// Obtener contador de inscritos
const getContadorInscritos = (cursoId) => {
    return contadoresInscritos.value[cursoId] || 0;
};

// Función helper para mostrar alertas
const showAlert = (type, title, text) => {
    const config = {
        success: {
            background: "#10b981",
            iconHtml: createCheckIcon(),
            buttonClass:
                "bg-white text-emerald-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100",
        },
        error: {
            background: "#ef4444",
            iconHtml: createErrorIcon(),
            buttonClass:
                "bg-white text-red-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100",
        },
        info: {
            background: "#3b82f6",
            iconHtml: createInfoIcon(),
            buttonClass:
                "bg-white text-blue-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100",
        },
    };

    const alertConfig = config[type] || config.info;

    Swal.fire({
        iconHtml: alertConfig.iconHtml,
        title,
        text,
        background: alertConfig.background,
        color: "#fff",
        customClass: {
            popup: "rounded-2xl shadow-lg px-8 py-6",
            title: "text-2xl font-bold",
            confirmButton: alertConfig.buttonClass,
        },
        showConfirmButton: true,
        confirmButtonText: "Aceptar",
    });
};

// Funciones para crear iconos SVG
const createCheckIcon = () => `
    <div class="flex items-center justify-center">
        <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" fill="transparent"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4 -4" stroke="white" stroke-width="2"/>
        </svg>
    </div>
`;

const createErrorIcon = () => `
    <div class="flex items-center justify-center">
        <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" fill="transparent"/>
            <line x1="9" y1="9" x2="15" y2="15" stroke="white" stroke-width="2" stroke-linecap="round"/>
            <line x1="15" y1="9" x2="9" y2="15" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </div>
`;

const createInfoIcon = () => `
    <div class="flex items-center justify-center">
        <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" fill="transparent"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4M12 8h.01" stroke="white" stroke-width="2"/>
        </svg>
    </div>
`;
</script>

<template>
    <div class="mb-12">
        <!-- Estado vacío mejorado -->
        <div
            v-if="cursosFiltrados.length === 0"
            class="text-center py-12 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600"
        >
            <svg
                class="mx-auto h-16 w-16 text-gray-400 mb-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                />
            </svg>
            <h3 class="text-xl font-semibold mb-2">
                No hay actividades disponibles
            </h3>
            <p class="text-sm">
                No se encontraron cursos con inscripciones abiertas en este
                momento.
            </p>
        </div>

        <!-- Estado sin cursos filtrados -->
        <div
            v-else-if="cursosFiltrados.length === 0"
            class="text-center py-12 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 rounded-lg border-2 border-dashed border-yellow-300 dark:border-yellow-600"
        >
            <svg
                class="mx-auto h-16 w-16 text-yellow-500 mb-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <h3 class="text-xl font-semibold mb-2">
                No hay inscripciones abiertas
            </h3>
            <p class="text-sm">
                Las fechas de inscripción para los cursos disponibles han
                cerrado o aún no han comenzado.
            </p>
        </div>

        <!-- Grid de cursos -->
        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 auto-rows-fr"
        >
            <div
                v-for="curso in cursosFiltrados"
                :key="`curso-${curso.id_curso}`"
                class="flex flex-col rounded-2xl border-2 border-transparent transform hover:scale-[1.02] transition-all duration-300 shadow-lg hover:shadow-xl bg-gradient-to-br from-sky-700 to-sky-500"
            >
                <!-- Header del curso -->
                <div
                    class="w-full p-4 overflow-hidden rounded-t-2xl bg-white/20 flex items-center justify-center min-h-[80px]"
                >
                    <h3
                        class="text-xl font-bold text-white text-center leading-tight"
                    >
                        {{ curso.nombre }}
                    </h3>
                </div>

                <!-- Contenido principal -->
                <div
                    class="bg-white/90 dark:bg-gray-800/90 rounded-b-2xl p-6 flex flex-col justify-between h-full"
                >
                    <div
                        class="space-y-4 text-gray-600 dark:text-gray-400 text-sm"
                    >
                        <!-- Descripción -->
                        <p
                            class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed line-clamp-3"
                        >
                            {{
                                curso.descripcion ||
                                "Sin descripción disponible"
                            }}
                        </p>

                        <!-- Información del curso -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-start">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200"
                                    >Inscripción:</span
                                >
                                <span class="text-right text-xs">
                                    {{
                                        formatDate(
                                            curso.fecha_inicio_inscripcion
                                        )
                                    }}
                                    –
                                    {{
                                        formatDate(curso.fecha_fin_inscripcion)
                                    }}
                                </span>
                            </div>

                            <div class="flex justify-between items-start">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200"
                                    >Actividad:</span
                                >
                                <span class="text-right text-xs">
                                    {{ formatDate(curso.fecha_inicio) }}
                                    –
                                    {{ formatDate(curso.fecha_fin) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200"
                                    >Carga Horaria:</span
                                >
                                <span class="font-semibold"
                                    >{{
                                        curso.carga_horaria || "N/A"
                                    }}
                                    hrs</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200"
                                    >Costo:</span
                                >
                                <span class="font-semibold">
                                    <span
                                        :class="{
                                            'font-semibold':
                                                curso.tipo_pago === 'gratuito',
                                            'font-semibold':
                                                curso.tipo_pago === 'pago',
                                        }"
                                    >
                                        {{
                                            curso.tipo_pago === "pago"
                                                ? "Bs. " + curso.precio
                                                : "Gratuito"
                                        }}
                                    </span></span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200"
                                    >Inscritos:</span
                                >
                                <span class="font-semibold">
                                    {{ getContadorInscritos(curso.id_curso) }} -
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 mr-2 flex-shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m4-5a4 4 0 11-8 0 4 4 0 018 0zM17 11a4 4 0 100-8 4 4 0 000 8z"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Botón para cursos de pago -->
                    <div class="mt-6">
                        <button
                            @click="abrirModal(curso)"
                            class="w-full py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-semibold rounded-full shadow-inner transition-all duration-200"
                        >
                            Ver información 
                        </button>
                    </div>

                    <!-- Modal -->
                    <div
                        v-if="cursoModalId === curso.id_curso"
                        class="fixed inset-0 flex items-center justify-center z-50 bg-black/40 backdrop-blur-sm rounded-3xl"
                    >
                        <!-- Caja del modal con el color principal -->
                        <div
                            class="bg-gradient-to-br from-sky-800 to-sky-950 p-4 rounded-3xl shadow-2xl w-[90%] max-w-sm text-white"
                        >
                            <h3
                                class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4"
                            >
                                Información del curso
                            </h3>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 mb-4"
                            >
                                Para inscribirte en el curso
                                <strong>{{ curso.nombre }}</strong
                                >, por favor dirígete a la
                                <strong
                                    >Oficina Central en la Casa Social del
                                    Maestro</strong
                                >, ubicada en la Calle Genaro Sanjinés Nro. 607.
                            </p>
                            <p class="font-bold text-center">
                                La Paz - Bolivia
                            </p>

                            <button
                                @click="cerrarModal"
                                class="mt-4 w-full py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-semibold"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
