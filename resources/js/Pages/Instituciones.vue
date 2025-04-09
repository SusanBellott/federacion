<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Institucion Molquino
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                    <h6 class="text-gray-800 dark:text-white">Instituciones</h6>

                    <div class="flex items-center space-x-4">
                        <BuscadorInstituciones :filters="filters" ruta="instituciones.index" />

                        <button v-if="$page.props.permissions.includes('instituciones.create')"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200"
                            @click="handleClick">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Agregar Nueva Institución
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Alerts & Notifications -->
                <div class="px-6 pt-4">
                    <div v-if="flash.success">
                        <useSweetAlert :data="flash.datos_array" />
                    </div>
                    <editaralerta title="¡Registro editado correctamente!" />
                </div>

                <div class="flex-auto px-0 pt-0 pb-2">
                    <div class="p-0 overflow-x-auto">
                        <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Distrito</th>
                                    <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Subsistema</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Servicio</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Servicio Generado</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Nivel</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Programa</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Unidad Educativa</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodelete.update')" class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Estado</th>
                                    <th v-if="$page.props.permissions.includes('institucioneseditar.update') && $page.props.permissions.includes('editarestadodelete.update')" class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-400 opacity-70">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="institucion in instituciones.data" :key="institucion.id" class="border-b dark:border-white/40">
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">
                                            {{ getDistritoNombre(institucion.id_distrito) }}
                                        </p>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">{{ institucion.subsistema }}</p>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">{{ institucion.servicio }}</span>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">{{ institucion.servicio_generado }}</span>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">{{ institucion.nivel }}</span>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">{{ institucion.programa }}</span>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">{{ institucion.unidad_educativa }}</p>
                                    </td>
                                    <td v-if="$page.props.permissions.includes('editarestadodelete.update')" class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span v-if="institucion.estado == 'activo'"
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(institucion.uuid_institucion, 2, '¿Estás seguro de que deseas deshabilitar este registro?')">
                                            Activo
                                        </span>
                                        <span v-else-if="institucion.estado == 'inactivo'"
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(institucion.uuid_institucion, 1, '¿Estás seguro de que deseas activar este registro?')">
                                            Inactivo
                                        </span>
                                        <span v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(institucion.uuid_institucion, 1, '¿Estás seguro de que deseas registrar nuevamente?')">
                                            Registrar
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex justify-center space-x-2">
                                            <button v-if="institucion.estado != 'eliminado' && institucion.estado != 'inactivo' && $page.props.permissions.includes('institucioneseditar.update')"
                                                @click="handleClickEditar(
                                                    institucion.uuid_institucion,
                                                    institucion.id_distrito,
                                                    institucion.subsistema,
                                                    institucion.servicio,
                                                    institucion.servicio_generado,
                                                    institucion.nivel,
                                                    institucion.programa,
                                                    institucion.unidad_educativa
                                                )"
                                                class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                            <button v-if="institucion.estado != 'eliminado' && institucion.estado != 'inactivo' && $page.props.permissions.includes('editarestadodelete.update')"
                                                @click="handleDelete(institucion.uuid_institucion, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
                                                class="p-2 text-red-500 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-white/10">
                        <Pagination :pagination="instituciones" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal with Form -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                        {{ id_institucion ? 'Editar Institución' : 'Agregar Nueva Institución' }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Campo Distrito -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Distrito
                            </label>
                            <select
                                v-model="form.id_distrito"
                                required
                               class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            >
                                <option disabled value="">
                                    Selecciona un distrito
                                </option>
                                <option
                                    v-for="distrito in distritos"
                                    :key="distrito.id_distrito"
                                    :value="distrito.id_distrito"
                                >
                                    {{ distrito.descripcion }}
                                </option>
                            </select>
                        </div>

                        <!-- Campo Subsistema -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Subsistema
                            </label>
                            <input
                                v-model="form.subsistema"
                                type="text"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Servicio -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Servicio
                            </label>
                            <input
                                v-model="form.servicio"
                                type="number"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Servicio Generado -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Servicio Generado
                            </label>
                            <input
                                v-model="form.servicio_generado"
                                type="number"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Nivel -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Nivel
                            </label>
                            <input
                                v-model="form.nivel"
                                type="text"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Programa -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Programa
                            </label>
                            <input
                                v-model="form.programa"
                                type="number"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>

                        <!-- Campo Unidad Educativa -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Unidad Educativa
                            </label>
                            <input
                                v-model="form.unidad_educativa"
                                type="text"
                                required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200"
                        >
                            {{ id_institucion ? 'Actualizar' : 'Guardar' }} Institución
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <ConfirmDelete
            ref="deleteDialog"
            :method="'patch'"
            route-name="editarestadodelete.update"
            title="¿Eliminar este registro?"
        />
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorInstituciones from "@/Components/Buscador.vue";

const props = defineProps({
    instituciones: Object,
    distritos: Array,
    filters: Object,
});

const page = usePage();
const flash = computed(() => page.props.flash || {});
const deleteDialog = ref(null);
const id_institucion = ref(null);

const showModal = ref(false);
const form = ref({
    id_distrito: "",
    subsistema: "",
    servicio: "",
    servicio_generado: "",
    nivel: "",
    programa: "",
    unidad_educativa: "",
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
        programa: "",
        unidad_educativa: "",
    };
    id_institucion.value = null;
};

const handleClickEditar = (
    uuid,
    id_distrito,
    subsistema,
    servicio,
    servicio_generado,
    nivel,
    programa,
    unidad_educativa
) => {
    showModal.value = true;
    id_institucion.value = uuid;
    form.value.id_distrito = id_distrito;
    form.value.subsistema = subsistema;
    form.value.servicio = servicio;
    form.value.servicio_generado = servicio_generado;
    form.value.nivel = nivel;
    form.value.programa = programa;
    form.value.unidad_educativa = unidad_educativa;
};

const submitForm = () => {
    const config = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    };

    if (!id_institucion.value) {
        router.post("/instituciones", form.value, config);
    } else {
        router.post(`/institucioneseditar/${id_institucion.value}`, form.value, {
            ...config,
            method: 'post',
            headers: {
                'X-HTTP-Method-Override': 'PUT'
            }
        });
    }
};

const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};

// Función para obtener el nombre del distrito
const getDistritoNombre = (id_distrito) => {
    const distrito = props.distritos.find(d => d.id_distrito === id_distrito);
    return distrito ? distrito.descripcion : 'Desconocido';
};
</script>
