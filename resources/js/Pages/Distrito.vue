<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { reactive, ref, computed } from "vue";
import { router, usePage, useForm } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorDistritos from "@/Components/Buscador.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    distritos: Object,
    filters: Object,
});
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});
const erroresdistrito = reactive({});
const currentPage = computed(() => props.distritos.current_page);
const perPage = computed(() => props.distritos.per_page);
const deleteDialog = ref(null);
const id_distrito = ref(null);
const distritoSeleccionado = ref(null);

const deleteCode = ref(null);
// Computed para determinar si estamos editando
const editing = computed(() => !!id_distrito.value);

const handleDelete = (distrito, cod, texto) => {
    distritoSeleccionado.value = distrito;
    deleteCode.value = cod;
    deleteDialog.value?.show(distrito.uuid_distrito, cod, texto);
};

// Estado del modal
const showModal = ref(false);

// Datos del formulario
const form = ref({
    codigo: "",
    descripcion: "",
});
/* const form = useForm({
    codigo: "",
    descripcion: "",
}); */
const handleClick = () => {
    showModal.value = true;
};
const handleClickEditar = (uuid, codigo, descripcion) => {
    //console.log(id_distrito, "  ", subsistema);
    showModal.value = true;
    id_distrito.value = uuid;
    // Aquí modificamos el objeto dentro de ref()
    form.value.codigo = codigo;
    form.value.descripcion = descripcion;
};
const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    form.value = {
        codigo: "",
        descripcion: "",
    };
    id_distrito.value = null;
    erroresdistrito.value = {};
};
const submitForm = () => {
    // 1. Log de datos enviados
    if (!id_distrito.value) {
        router.post("/distritos", form.value, {
            onSuccess: () => {
                closeModal();
                // 2. Log de respuesta
                /*  console.log("Respuesta recibida - Mensajes flash:", {
                    success: flash.value.success,
                    datos_array: flash.value.datos_array,
                }); */
            },
            onError: (errors) => {
                // 3. Log de errores
                Object.assign(erroresdistrito, errors);
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    } else {
        //console.log("listo para editar");
        // Editar registro existente (PUT)
        router.put(
            route("distritoseditar.update", { uuid: id_distrito.value }),
            form.value,
            {
                onSuccess: () => {
                    router.visit("/distritos", { preserveScroll: true });

                    //console.log("Institución actualizada");
                },
                onError: (errors) => {
                    Object.assign(erroresdistrito, errors);
                    console.error("Errores de validación:", errors);
                },
                preserveScroll: true,
            }
        );
    }
};
</script>

<template>
    <AppLayout title="Distritos">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Distrito
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <h6 class="text-gray-800 dark:text-white">Distritos</h6>
            <div
                class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border"
            >
                <div
                    class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center"
                >
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <BuscadorDistritos
                                :filters="filters"
                                ruta="distritos.index"
                            />
                        </div>
                    </div>
                    <button
                        v-if="
                            $page.props.permissions.includes('distritos.create')
                        "
                        class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all bg-blue-500 rounded-lg cursor-pointer leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md"
                        @click="handleClick"
                    >
                        <span class="flex items-center justify-center">
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
                            Agregar Nuevo Distrito
                        </span>
                    </button>
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
                                        Código
                                    </th>
                                    <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Distrito
                                    </th>
                                    <th
                                        v-if="
                                            $page.props.permissions.includes(
                                                'editarestadodeletedistrito.update'
                                            )
                                        "
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        Estado
                                    </th>
                                    <th
                                        v-if="
                                            $page.props.permissions.includes(
                                                'editarestadodeletedistrito.update'
                                            ) ||
                                            $page.props.permissions.includes(
                                                'distritoseditar.update'
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
                                    v-for="(distrito, index) in distritos.data"
                                    :key="distrito.id"
                                    class="border-b dark:border-white/40"
                                >
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        {{
                                            (currentPage - 1) * perPage +
                                            index +
                                            1
                                        }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words"
                                    >
                                        {{ distrito.codigo }}
                                    </td>
                                    <td
                                        class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >
                                        {{ distrito.descripcion }}
                                    </td>
                                    <td
                                        v-if="
                                            $page.props.permissions.includes(
                                                'editarestadodeletedistrito.update'
                                            )
                                        "
                                        class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent"
                                    >
                                        <span
                                            v-if="distrito.estado == 'activo'"
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="
                                                handleDelete(
                                                    distrito,
                                                    2,
                                                    '¿Estás seguro de que deseas deshabilitar este registro?'
                                                )
                                            "
                                        >
                                            Activo
                                        </span>
                                        <span
                                            v-else-if="
                                                distrito.estado == 'inactivo'
                                            "
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="
                                                handleDelete(
                                                    distrito,
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
                                                    distrito,
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
                                                    distrito.estado !=
                                                        'eliminado' &&
                                                    distrito.estado !=
                                                        'inactivo' &&
                                                    $page.props.permissions.includes(
                                                        'distritoseditar.update'
                                                    )
                                                "
                                                @click="
                                                    handleClickEditar(
                                                        distrito.uuid_distrito,
                                                        distrito.codigo,
                                                        distrito.descripcion
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
                                                    distrito.estado !=
                                                        'eliminado' &&
                                                    distrito.estado !=
                                                        'inactivo' &&
                                                    $page.props.permissions.includes(
                                                        'editarestadodeletedistrito.update'
                                                    )
                                                "
                                                @click="
                                                    handleDelete(
                                                        distrito,
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
                            :pagination="distritos"
                            :filters="filters"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal with Form -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2
                        class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70"
                    >
                        {{
                            editing
                                ? "Editar Distrito"
                                : "Agregar Nuevo Distrito"
                        }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Campo Código -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Código de Distrito
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="codigo"
                                v-model="form.codigo"
                                type="number"
                                placeholder="Ingrese código de distrito"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <InputError :message="erroresdistrito.codigo" />
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Nombre de Distrito
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.descripcion"
                                type="text"
                                required
                                placeholder="Ingrese nombre de distrito"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <InputError
                                :message="erroresdistrito.descripcion"
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
                            {{ editing ? "Actualizar" : "Guardar" }} Distrito
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <ConfirmDelete
            ref="deleteDialog"
            :method="'patch'"
            route-name="editarestadodeletedistrito.update"
            :params="{
                uuid: distritoSeleccionado?.uuid_distrito || '',
                code: deleteCode || '',
            }"
            title="¿Eliminar este registro?"
        />
    </AppLayout>
</template>
