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
});

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
    deleteDialog.value?.show(id, cod, texto);
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
    console.log(uuid_curso);
    const uuidString = String(uuid_curso);
    router.put(`/estudianteseditar/${uuidString}`, {
        onSuccess: () => {
            closeModal();
            console.log("Inscrito correctamente");
        },
        onError: (errors) => {
            console.error("Errores de validación:", errors);
        },
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Mis Cursos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bienvenido {{ nombre_user }}
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                    <h6 class="text-gray-800 dark:text-white">Mis Cursos</h6>
                    
                    <div class="flex items-center space-x-4">
                        <BuscadorDistritos :filters="filters" ruta="estudiantes.index" />
                        
                        <button
                            class="px-6 py-3 bg-gradient-to-r from-blue-800 to-sky-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-50"
                            @click="handleClick">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Agregar Nueva Inscripción
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
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Curso</th>
                                    <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Descripción</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Estado</th>
                                    <th class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-400 opacity-70">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="micurso in miscursos.data" :key="micurso.id_inscripcion" class="border-b dark:border-white/40">
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white font-semibold">
                                            {{ micurso.curso.nombre }}
                                        </p>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">
                                            {{ micurso.curso.descripcion }}
                                        </p>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span v-if="micurso.estado_ins == 'activo'" 
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                            Activo
                                        </span>
                                        <span v-else-if="micurso.estado_ins == 'inactivo'" 
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                            Inactivo
                                        </span>
                                        <span v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                            {{ micurso.estado_ins }}
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex justify-center space-x-2">
                                            <button v-if="micurso.estado != 'eliminado' && micurso.estado != 'inactivo'" 
                                                @click="handleDelete(
                                                    micurso.uuid_inscripcion,
                                                    2,
                                                    '¿Estás seguro de que deseas eliminar este registro de forma permanente?'
                                                )" 
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
                        <Pagination :pagination="miscursos" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para inscribirse en cursos -->
        <Modal :show="showModal" @close="closeModal" max-width="6xl">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                    Inscribirme en Cursos
                </h2>
                
                <div class="mb-6">
                    <BuscadorCrusos :filters="filters" ruta="estudiantes.index" />
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="curso in cursos.data" :key="curso.id_curso"
                        class="bg-gradient-to-r from-sky-800 to-sky-600 rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border-4 border-sky-500 dark:border-sky-700">
                        <!-- Header con gradiente Sky -->
                        <div class="bg-gradient-to-r from-cyan-400 to-blue-950 p-6 text-blue-900">
                            <h3 class="text-2xl font-bold truncate text-center text-white">
                                {{ curso.nombre }}
                            </h3>
                        </div>

                        <!-- Body con efecto vidrio (glass morphism) -->
                        <div class="p-6 backdrop-blur-sm bg-white/80 dark:bg-gray-800/80">
                            <p class="text-gray-700 dark:text-gray-200 line-clamp-3 text-lg">
                                {{ curso.descripcion }}
                            </p>
                            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                                <p><span class="font-semibold">Carga Horaria:</span> {{ curso.carga_horaria }} hrs</p>
                                <p><span class="font-semibold">Instructor:</span> {{ curso.encargado }}</p>
                            </div>
                        </div>

                        <!-- Footer con botón flotante -->
                        <div class="px-6 pb-6 pt-3 text-right">
                            <button @click="tomarmateria(curso.uuid_curso)"
                                class="inline-flex items-center px-5 py-3 rounded-full bg-gray-900 hover:bg-lime-500 text-white font-bold shadow-md hover:shadow-lg transition-all">
                                Tomar curso
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" @click="closeModal"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>

        <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeleteestudiantes.update"
            title="¿Eliminar este registro?" />
    </AppLayout>
</template>