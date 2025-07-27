<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Instituciones
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <h6 class="text-gray-800 dark:text-white">Instituciones</h6>
            <div
                class="relative flex flex-col min-w-0 break-words bg-gradient-to-br from-violet-900 to-indigo-900 border-0 shadow-xl dark:shadow-dark-xl rounded-2xl bg-clip-border"
            >
                <!-- Encabezado de Buscar Mostrar y agregar nueva institucion -->
                <div
                    class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent"
                >
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:gap-4 w-full"
                    >
                        <!-- Buscador ocupa todo el espacio en desktop -->
                        <div class="flex-1">
                            <BuscadorInstituciones
                                :filters="filters"
                                ruta="instituciones.index"
                            />
                        </div>

                        <!-- Botón agregar -->
                        <div
                            class="mt-4 lg:mt-0 flex justify-end lg:justify-start"
                        >
                            <button
                                v-if="
                                    $page.props.permissions.includes(
                                        'instituciones.create'
                                    )
                                "
                                class="w-full sm:w-auto lg:w-fit inline-flex items-center justify-center px-4 py-2.5 sm:px-6 sm:py-3 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold text-sm sm:text-base rounded-lg shadow-lg hover:shadow-xl dark:shadow-blue-900/25 dark:hover:shadow-blue-900/40 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800 active:scale-95"
                                @click="handleClick"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span class="whitespace-nowrap"
                                    >Agregar Nueva Institución</span
                                >
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Alerts & Notifications -->
                <div class="px-6 pt-4">
                    <div v-if="flash.success">
                        <useSweetAlert :data="flash.datos_array" />
                    </div>
                    <editaralerta title="¡Registro editado correctamente!" />
                </div>
                <!-- Tabla de instituciones -->
                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-0 overflow-x-auto">
                        <table
                            class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500"
                        >
                            <thead class="align-bottom">
                                <tr>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Nro
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Distrito
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Subsistema
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Servicio
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Servicio Generado
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Nivel
                                    </th>
                                    <th
                                        v-if="
                                            $page.props.permissions.includes(
                                                'editarestadodelete.update'
                                            )
                                        "
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        v-if="
                                            $page.props.permissions.includes(
                                                'institucioneseditar.update'
                                            ) &&
                                            $page.props.permissions.includes(
                                                'editarestadodelete.update'
                                            )
                                        "
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        institucion, index
                                    ) in instituciones.data"
                                    :key="institucion.id"
                                    class="border-b dark:border-white/40"
                                >
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{
                                            (currentPage - 1) * perPage +
                                            index +
                                            1
                                        }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{
                                            institucion.distrito?.descripcion ||
                                            "Desconocido"
                                        }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{ institucion.subsistema }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{ institucion.servicio }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{ institucion.servicio_generado }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{ institucion.nivel }}
                                    </td>

                                    <td
                                        v-if="
                                            $page.props.permissions.includes(
                                                'editarestadodelete.update'
                                            )
                                        "
                                        class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent"
                                    >
                                        <span
                                            v-if="
                                                institucion.estado == 'activo'
                                            "
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="
                                                handleDelete(
                                                    institucion.uuid_institucion,
                                                    2,
                                                    '¿Estás seguro de que deseas deshabilitar este registro?'
                                                )
                                            "
                                        >
                                            Activo
                                        </span>
                                        <span
                                            v-else-if="
                                                institucion.estado == 'inactivo'
                                            "
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="
                                                handleDelete(
                                                    institucion.uuid_institucion,
                                                    1,
                                                    '¿Estás seguro de que deseas activar este registro?'
                                                )
                                            "
                                        >
                                            Inactivo
                                        </span>
                                        <span
                                            v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="
                                                handleDelete(
                                                    institucion.uuid_institucion,
                                                    1,
                                                    '¿Estás seguro de que deseas registrar nuevamente?'
                                                )
                                            "
                                        >
                                            Registrar
                                        </span>
                                    </td>
                                    <td
                                        class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent"
                                    >
                                        <div
                                            class="flex justify-center space-x-2"
                                        >
                                            <button
                                                v-if="
                                                    institucion.estado !=
                                                        'eliminado' &&
                                                    institucion.estado !=
                                                        'inactivo' &&
                                                    $page.props.permissions.includes(
                                                        'institucioneseditar.update'
                                                    )
                                                "
                                                @click="
                                                    handleClickEditar(
                                                        institucion.uuid_institucion,
                                                        institucion.id_distrito,
                                                        institucion.subsistema,
                                                        institucion.servicio,
                                                        institucion.servicio_generado,
                                                        institucion.nivel
                                                    )
                                                "
                                                class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-5 h-5"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                                    />
                                                </svg>
                                            </button>
                                            <button
                                                v-if="
                                                    institucion.estado !=
                                                        'eliminado' &&
                                                    institucion.estado !=
                                                        'inactivo' &&
                                                    $page.props.permissions.includes(
                                                        'editarestadodelete.update'
                                                    )
                                                "
                                                @click="
                                                    handleDelete(
                                                        institucion.uuid_institucion,
                                                        3,
                                                        '¿Estás seguro de que deseas eliminar este registro de forma permanente?'
                                                    )
                                                "
                                                class="p-2 text-red-500 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-5 h-5"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                                    />
                                                </svg>
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
                            :pagination="instituciones"
                            :filters="filters"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal del formulario de instituciones -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2
                        class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"
                    >
                        {{
                            id_institucion
                                ? "Editar Institución"
                                : "Agregar Nueva Institución"
                        }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Campo Distrito -->
                        <div>
                            <label
                                class="px-6 inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Distrito <span class="text-red-500">*</span>
                            </label>
                            <SearchableSelect
                                v-model="form.id_distrito"
                                :options="distritosOptions"
                                placeholder="Buscar distrito"
                            />
                            <validacioens
                                :message="
                                    erroresinstitucion?.value?.id_distrito
                                "
                            />
                        </div>

                        <!-- Campo Unidad Educativa (Nuevo) -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Subsistema <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.subsistema"
                                class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none"
                            >
                                <option value="">
                                    Seleccione un subsistema
                                </option>
                                <option
                                    value="ALTERNATIVA Y ESPECIAL PERMANENTE"
                                >
                                    ALTERNATIVA Y ESPECIAL PERMANENTE
                                </option>
                                <option value="EDUCACION SUPERIOR">
                                    EDUCACION SUPERIOR
                                </option>
                                <option value="REGULAR">REGULAR</option>
                            </select>
                            <validacioens
                                :message="erroresinstitucion?.value?.subsistema"
                            />
                        </div>

                        <!-- Campo Servicio -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Servicio <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.servicio"
                                type="number"
                                placeholder="Ingrese número de servicio"
                                class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none"
                            />

                            <validacioens
                                :message="erroresinstitucion?.value?.servicio"
                            />
                        </div>

                        <!-- Campo Servicio Generado -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Servicio Generado
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.servicio_generado"
                                type="number"
                                disabled
                                placeholder="Número de servicio generado"
                                class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Nivel -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Nivel <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.nivel"
                                class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none"
                            >
                                <option value="">Seleccione un nivel</option>
                                <option value="ALTERNATIVA">ALTERNATIVA</option>
                                <option value="ESPECIAL">ESPECIAL</option>
                                <option value="INICIAL">INICIAL</option>
                                <option value="PERMANENTE">PERMANENTE</option>
                                <option value="PRIMARIA">PRIMARIA</option>
                                <option value="SECUNDARIA">SECUNDARIA</option>
                                <option value="SUPERIOR">SUPERIOR</option>
                            </select>
                            <validacioens
                                :message="erroresinstitucion?.value?.nivel"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-slate-500 leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200"
                        >
                            {{ id_institucion ? "Actualizar" : "Guardar" }}
                            Institución
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <!-- Boton de Eliminacion -->
        <ConfirmDelete
            ref="deleteDialog"
            :method="'patch'"
            route-name="editarestadodelete.update"
            :params="{
                uuid: institucionSeleccionada?.uuid_institucion || '',
                code: deleteCode || '',
            }"
            title="¿Eliminar este registro?"
        />
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { reactive, ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorInstituciones from "@/Components/Buscador.vue";
import validacioens from "@/Components/InputError.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";

const props = defineProps({
    instituciones: Object,
    distritos: Array,
    filters: Object,
});

const currentPage = computed(() => props.instituciones.current_page);
const perPage = computed(() => props.instituciones.per_page);

const page = usePage();
const flash = computed(() => page.props.flash || {});
const deleteDialog = ref(null);
const institucionSeleccionada = ref(null);
const deleteCode = ref(null);
const id_institucion = ref(null);
const erroresinstitucion = reactive({});

const showModal = ref(false);
const form = ref({
    id_distrito: "",
    subsistema: "",
    servicio: "",
    servicio_generado: "",
    nivel: "",
});

const handleClick = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    form.value = {
        id_distrito: "",
        subsistema: "",
        servicio: "",
        servicio_generado: "",
        nivel: "",
    };
    id_institucion.value = null;
};

// Modifica la función handleClickEditar para manejar correctamente la asincronía
const handleClickEditar = async (
    uuid,
    id_distrito,
    subsistema,
    servicio,
    servicio_generado,
    nivel
) => {
    showModal.value = true;
    id_institucion.value = uuid;

    // Primero establece el distrito para que la carga de unidades funcione
    form.value.id_distrito = id_distrito;

    // Carga los datos restantes
    form.value.subsistema = subsistema;
    form.value.servicio = servicio;
    form.value.servicio_generado = servicio_generado;
    form.value.nivel = nivel;
};

const submitForm = () => {
    const config = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            router.reload({ only: ["instituciones"] }); // no más setTimeout ni onFinish
        },
        onError: (errors) => {
            erroresinstitucion.value = errors;
        },
    };

    if (!id_institucion.value) {
        router.post("/instituciones", form.value, config);
    } else {
        router.put(
            `/instituciones/${id_institucion.value}`,
            form.value,
            config
        );
    }
};

const distritosOptions = computed(() =>
    props.distritos.map((d) => ({
        value: d.id_distrito,
        label: `${d.codigo} - ${d.descripcion}`,
    }))
);

watch(
    () => form.value.id_distrito,
    async (nuevoDistrito) => {
        if (nuevoDistrito) {
        }
    }
);

const handleDelete = (uuid, code, mensaje) => {
    institucionSeleccionada.value = { uuid_institucion: uuid };
    deleteCode.value = code;
    deleteDialog.value?.show(uuid, code, mensaje);
};

// Función para obtener el nombre del distrito
const getDistritoNombre = (id_distrito) => {
    const distrito = props.distritos.find((d) => d.id_distrito === id_distrito);
    return distrito ? distrito.descripcion : "Desconocido";
};
// Función para obtener la unidad educativa vinculada a una institución
const getUnidadEducativa = (institucion) => {
    if (institucion.codigosSie && institucion.codigosSie.length > 0) {
        return institucion.codigosSie[0].unidad_educativa;
    }
    return "No asignada";
};
watch(
    () => form.value.servicio,
    (newServicio) => {
        // Convertir el valor de servicio a cadena, por si es un número
        const servicioStr = newServicio.toString();

        // Comprobar si la longitud de la cadena es mayor o igual a 2
        if (servicioStr && servicioStr.length >= 2) {
            // Extraer los últimos dos caracteres
            const lastTwoChars = servicioStr.slice(-2);
            form.value.servicio_generado = lastTwoChars; // Generar un valor basado en los dos últimos caracteres
        } else {
            form.value.servicio_generado = ""; // Limpiar si el valor no cumple
        }
    }
);

watch(flash, (nuevoFlash) => {
    if (nuevoFlash.success && nuevoFlash.datos_array) {
        useSweetAlert({
            title: nuevoFlash.datos_array.title || "¡Actualizado!",
            text: nuevoFlash.datos_array.text || "Se actualizó correctamente.",
            icon: nuevoFlash.datos_array.icon || "success",
        });
    }
});
</script>

<style scoped>
/* Para ocultar flechas en Chrome, Safari, Edge */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Para ocultar flechas en Firefox */
input[type="number"] {
    -moz-appearance: textfield;
}
</style>
