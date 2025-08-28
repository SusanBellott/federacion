<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorDistritos from "@/Components/Buscador.vue";
import BuscadorCrusos from "@/Components/Buscadoruser.vue";

const props = defineProps({
    nombre_user: String,
    miscursos: Object,
    filters: Object,
    cursos: Object,
    // Si tienes una lista de tipos, agrégalo aquí
    tipos: {
        type: Array,
        default: () => [],
    },
});

const currentPage = computed(() => props.miscursos.current_page);
const perPage = computed(() => props.miscursos.per_page);

const id_distrito = ref(null);
const showModal = ref(false);
const page = usePage();
const flash = computed(() => page.props.flash || {});

const deleteDialog = ref(null);

const form = ref({
    codigo: "",
    descripcion: "",
});

const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto, () => {
        router.reload(); // ✅ esto recarga la lista para mostrar el curso nuevamente
    });
};

const handleClick = () => {
    showModal.value = true;
};

const handleClickEditar = (uuid, codigo, descripcion) => {
    showModal.value = true;
    id_distrito.value = uuid;
    form.value.codigo = codigo;
    form.value.descripcion = descripcion;
};

const closeModal = () => {
    showModal.value = false;
};

const tomarmateria = (uuid_curso) => {
    if (!uuid_curso) {
        console.error("UUID del curso es undefined");
        return;
    }

    const uuidString = String(uuid_curso);
    router.put(`/estudianteseditar/${uuidString}`, {
        onSuccess: () => {
            console.log("Inscrito correctamente");
        },
        onError: (errors) => {
            console.error("Errores de validación:", errors);
        },
        preserveScroll: true,
    });
};

// Mapea el valor interno a un texto legible
const humanizeTipo = (tipo) => {
    const map = {
        curso_taller: "Curso - Taller",
        seminario: "Seminario",
        taller: "Taller",
        congreso: "Congreso",
    };
    return map[tipo] || tipo;
};

// Añadimos la función getTipoActividad que faltaba
const getTipoActividad = (tipoId) => {
    // Si tienes acceso a una lista de tipos
    if (props.tipos && props.tipos.length > 0) {
        // Busca el tipo en la lista de tipos por su ID
        const tipo = props.tipos.find((t) => t.id === tipoId);
        // Si encuentra el tipo, devuelve su nombre
        if (tipo) {
            return tipo.nombre;
        }
    }

    // Si no encuentra el tipo o no hay lista de tipos disponible
    // devuelve un valor predeterminado
    return "SIN TIPO";
};

function puedeDesinscribir(fechaFinInscripcionISO) {
    if (!fechaFinInscripcionISO) return false;

    const hoy = new Date();
    const finInscripcion = new Date(fechaFinInscripcionISO);

    // Forzar hora a las 23:59:59 del día de fin de inscripción
    finInscripcion.setHours(23, 59, 59, 999);
    console.log("Hoy:", hoy.toISOString());
    console.log("FinInscripción:", finInscripcion.toISOString());

    return hoy <= finInscripcion;
}

function puedeAnularInscripcion(curso) {
    if (!curso || !curso.fecha_fin_inscripcion) return false;

    const hoy = new Date();
    const finInscripcion = new Date(curso.fecha_fin_inscripcion);
    finInscripcion.setHours(23, 59, 59, 999);

    // ✅ Solo cursos gratuitos pueden anularse
    return curso.tipo_pago === "gratuito" && hoy <= finInscripcion;
}

const formatDate = (dateString) => {
    if (!dateString) return "N/A";

    // Formatear la fecha como DD/MM/YYYY o el formato que prefieras
    const date = new Date(dateString);
    return date.toLocaleDateString("es-ES");
};
const hoy = new Date().toISOString().split("T")[0];

const cursosFiltrados = computed(() => {
    return props.cursos.data?.filter(
        (curso) => curso.fecha_inicio_inscripcion <= hoy
    );
});
</script>

<template>
    <AppLayout title="Mis Actividades">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Bienvenido {{ nombre_user }}
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <h6 class="text-black">Mis Actividades</h6>
            <div
                class="relative flex flex-col min-w-0 break-words bg-red-600/15 backdrop-blur-md border border-red-600/60 shadow-xl rounded-xl p-6 text-slate-800"
            >
                <!-- Buscar, mostrar -->
                <div
                    class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent"
                >
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:gap-4 w-full"
                    >
                        <!-- Buscador -->
                        <div class="flex-1">
                            <BuscadorDistritos
                                :filters="filters"
                                ruta="estudiantes.index"
                                placeholder="Buscar estudiantes..."
                            />
                        </div>
                    </div>
                </div>

                <!-- Alerts & Notifications -->
                <div class="px-6 pt-4">
                    <div v-if="flash.success">
                        <AlertNotification :data="flash.datos_array" />
                    </div>
                    <div v-if="mostrarAlertaEdicion">
                        <AlertNotification
                            title="¡Registro editado correctamente!"
                            action="update"
                        />
                    </div>
                    <div v-if="mostrarAlertaEliminacion">
                        <AlertNotification
                            title="¡Registro eliminado correctamente!"
                            action="delete"
                        />
                    </div>
                </div>

                <!-- Tabla de mis cursos -->
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-0 overflow-x-auto">
                        <table
                            class="items-center w-full mb-0 align-top border-collapse border-red-500 text-black"
                        >
                            <thead class="align-bottom">
                                <tr>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Nro
                                    </th>
                                    <th
                                        class="w-[200px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Título de actividad
                                    </th>
                                    <th
                                        class="w-[200px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Descripción de actividad
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Costo
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(micurso, index) in miscursos.data"
                                    :key="micurso.id_inscripcion"
                                    class="border-b border-red-500"
                                >
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase"
                                    >
                                        {{
                                            (currentPage - 1) * perPage +
                                            index +
                                            1
                                        }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase"
                                    >
                                        <p
                                            class="text-xs font-semibold leading-tight text-black max-w-xs break-words"
                                        >
                                            {{
                                                micurso.curso
                                                    ? micurso.curso.nombre
                                                    : "N/A"
                                            }}
                                        </p>
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase"
                                    >
                                        <p
                                            class="text-xs font-semibold leading-tight text-black max-w-xs break-words"
                                        >
                                            {{
                                                micurso.curso
                                                    ? micurso.curso.descripcion
                                                    : "N/A"
                                            }}
                                        </p>
                                    </td>
                                    <td
                                        class="p-2 text-center align-middle bg-transparent border-b border-red-500 whitespace-nowrap text-xs font-semibold text-black"
                                    >
                                        <span
                                            :class="{
                                                'text-green-500 font-semibold':
                                                    micurso.curso?.tipo_pago ===
                                                    'gratuito',
                                                'text-yellow-400 font-semibold':
                                                    micurso.curso?.tipo_pago ===
                                                    'pago',
                                            }"
                                        >
                                            {{
                                                micurso.curso?.tipo_pago ===
                                                "pago"
                                                    ? "Bs. " +
                                                      micurso.curso.precio
                                                    : "Gratuito"
                                            }}
                                        </span>
                                    </td>

                                    <td
                                        class="p-2 text-center align-middle bg-transparent border-b border-red-500 whitespace-nowrap shadow-transparent"
                                    >
                                        <span
                                            v-if="
                                                micurso.estado_ins == 'activo'
                                            "
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                        >
                                            Activo
                                        </span>
                                        <span
                                            v-else-if="
                                                micurso.estado_ins == 'inactivo'
                                            "
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                        >
                                            Inactivo
                                        </span>
                                        <span
                                            v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                        >
                                            {{ micurso.estado_ins }}
                                        </span>
                                    </td>
                                    <td
                                        class="p-2 align-middle bg-transparent border-b border-red-500 whitespace-nowrap"
                                    >
                                        <div class="flex justify-center">
                                            <button
                                                v-if="
                                                    puedeAnularInscripcion(
                                                        micurso.curso
                                                    )
                                                "
                                                @click="
                                                    handleDelete(
                                                        micurso.uuid_inscripcion,
                                                        2,
                                                        '¿Estás seguro de que deseas anular esta inscripción?'
                                                    )
                                                "
                                                class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-600 hover:bg-red-600 text-white text-xs font-semibold shadow-sm hover:shadow-md transition-all duration-200"
                                            >
                                                <span>Anular Inscripción</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="px-4 py-3 border-t border-gray-200 dark:border-white/10"
                    >
                        <Pagination
                            :pagination="miscursos"
                            :filters="filters"
                        />
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDelete
            ref="deleteDialog"
            :method="'patch'"
            route-name="editarestadodeleteestudiantes.update"
            title="¿Eliminar este registro?"
        />
    </AppLayout>
</template>

<style scoped>
/* Estilos adicionales si son necesarios */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
