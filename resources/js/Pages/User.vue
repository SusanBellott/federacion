<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed, toRefs } from "vue";
import { router, usePage,useForm } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorUsuarios from "@/Components/Buscador.vue";
import InputError from "@/Components/InputError.vue";


const props = defineProps({
    usuarios: Object,
    roles: Object,
    instituciones: Object,
    filters: Object,
});
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});

const deleteDialog = ref(null);
const id_user = ref(null);
const handleDelete = (id, cod, texto) => {
    //console.log("hola este es el id ", id);
    deleteDialog.value?.show(id, cod, texto);
};

const roles = toRefs(props).roles

// Estado del modal
const showModal = ref(false);

// Datos del formulario
const form = ref({
        id_institucion: '',
        ci: '',
        rda: '',
        name: '',
        primer_apellido: '',
        segundo_apellido: '',
        item: '',
        cargo: '',
        horas: '',
        email: '',
        role: '',
});

const handleClick = () => {
    showModal.value = true;
};
const handleClickEditar = (uuid, id_institucion, ci, rda, name, primer_apellido, segundo_apellido, item, cargo, horas, email, roles) => {
    //console.log(id_user, "  ", subsistema);
    showModal.value = true;
    id_user.value = uuid;
    // Aquí modificamos el objeto dentro de ref()
    form.value.id_institucion = id_institucion;
    form.value.ci = ci;
    form.value.rda = rda;
    form.value.name = name;
    form.value.primer_apellido = primer_apellido;
    form.value.segundo_apellido = segundo_apellido;
    form.value.item = item;
    form.value.cargo = cargo;
    form.value.horas = horas;
    form.value.email = email;
    form.value.role = roles.length > 0 ? roles[0].id : null;
    console.log(role)
};
const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    form.value = {
        id_institucion: "",
        ci: "",
        rda: "",
        name: "",
        primer_apellido: "",
        segundo_apellido: "",
        item: "",
        cargo: "",
        horas: "",
        email: "",
        role: "",
    };
    id_user.value = null;
};
const submitForm = () => {
    // 1. Log de datos enviados
    if (!id_user.value) {
        router.post("/usuarios", form.value, {
            onSuccess: () => {
                closeModal();
                // 2. Log de respuesta
                console.log("Respuesta recibida - Mensajes flash:", {
                    success: flash.value.success,
                    datos_array: flash.value.datos_array,
                });
            },
            onError: (errors) => {
                // 3. Log de errores
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    } else {
        //console.log("listo para editar");
        // Editar registro existente (PUT)
        router.put(`/usuarioseditar/${id_user.value}`, form.value, {
            onSuccess: () => {
                closeModal();
                //console.log("Institución actualizada");
            },
            onError: (errors) => {
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuarios
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                    <h6 class="text-gray-800 dark:text-white">Usuarios</h6>

                    <div class="flex items-center space-x-4">
                        <BuscadorUsuarios :filters="filters" ruta="usuarios.index" />

                        <button
                            v-if="$page.props.permissions.includes('usuarios.create')"
                            class="px-6 py-3 bg-gradient-to-r from-blue-800 to-sky-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-opacity-50"
                            @click="handleClick">
                            <span class="flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Agregar Nuevo Usuario
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
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Datos Personales</th>
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Rol</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update')" class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Estado</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update') && $page.props.permissions.includes('usuarioseditar.update')" class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-400 opacity-70">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in usuarios.data" :key="user.id" class="border-b dark:border-white/40">
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p> <span class="text-gray-700 dark:text-gray-50 font-semibold">C.I.: </span> {{ user.ci }}</p>
                                        <p> <span class="text-gray-700 dark:text-gray-50 font-semibold">R.D.A.: </span> {{ user.rda }}</p>
                                        <p> <span class="text-gray-700 dark:text-gray-50 font-semibold"> Nombre Completo: </span> {{ user.name +' '+ user.primer_apellido +' '+ user.segundo_apellido }}</p>
                                        <p> <span class="text-gray-700 dark:text-gray-50 font-semibold">Correo:</span> {{ user.email }} </p>
                                        <p>
                                            <span class="text-gray-700 dark:text-gray-50 font-semibold">Item:</span> {{ user.item }}
                                            <span class="text-gray-700 dark:text-gray-50 font-semibold">Cargo:</span> {{ user.cargo }}
                                            <span class="text-gray-700 dark:text-gray-50 font-semibold">Horas:</span> {{ user.horas }}
                                        </p>

                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex flex-col">
                                            <span v-for="r in user.roles" :key="r.id" class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                                {{ r.name }}
                                            </span>
                                        </div>
                                    </td>

                                    <td v-if="$page.props.permissions.includes('editarestadodeleteusuario.update')" class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span v-if="user.estado == 'activo'"
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(user.uuid_user, 2, '¿Estás seguro de que deseas deshabilitar este registro?')">
                                            Activo
                                        </span>
                                        <span v-else-if="user.estado == 'inactivo'"
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(user.uuid_user, 1, '¿Estás seguro de que deseas activar este registro?')">
                                            Inactivo
                                        </span>
                                        <span v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(user.uuid_user, 1, '¿Estás seguro de que deseas registrar nuevamente?')">
                                            Registrar
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex justify-center space-x-2">
                                            <button v-if="user.estado != 'eliminado' && user.estado != 'inactivo' && $page.props.permissions.includes('usuarioseditar.update')"
                                                @click="handleClickEditar(
                                                    user.uuid_user,
                                                    user.id_institucion,
                                                    user.ci,
                                                    user.rda,
                                                    user.name,
                                                    user.primer_apellido,
                                                    user.segundo_apellido,
                                                    user.item,
                                                    user.cargo,
                                                    user.horas,
                                                    user.email,
                                                    user.roles
                                                )"
                                                class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                            <button v-if="user.estado != 'eliminado' && user.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeleteusuario.update')"
                                                @click="handleDelete(user.uuid_user, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
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
                        <Pagination :pagination="usuarios" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal with Form -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                        {{ editing ? 'Editar Distrito' : 'Agregar Nuevo Distrito' }}
                    </h2>

                    <div class="space-y-4">
                        <!-- Campo CI -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Cedula de Identidad
                            </label>
                            <input id="codigo" v-model="form.ci" type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>

                        <!-- Campo RDA -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                RDA
                            </label>
                            <input id="codigo" v-model="form.rda" type="number"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>

                        <!-- Campo Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre
                            </label>
                            <input v-model="form.name" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>

                        <!-- Campo Primer Apellido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Primer Apellido
                            </label>
                            <input v-model="form.primer_apellido" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                                <InputError :message="errors" class="mt-2" />
                        </div>

                        <!-- Campo Segundo Apellido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Segundo Apellido
                            </label>
                            <input v-model="form.segundo_apellido" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                                <InputError :message="errors" class="mt-2" />
                        </div>

                        <!-- Campo Correo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Correo
                            </label>
                            <input v-model="form.email" type="email" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                                <InputError :message="errors" class="mt-2" />
                        </div>

                         <!-- Campo Item -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Item
                            </label>
                            <input v-model="form.item" type="number" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>


                         <!-- Campo Cargo -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Cargo
                            </label>
                            <input v-model="form.cargo" type="number" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>


                         <!-- Campo Horas -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Horas
                            </label>
                            <input v-model="form.horas" type="number" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 text-gray-800 dark:text-white" />
                            <InputError :message="errors" class="mt-2" />
                        </div>

                        <div>
                            <label for="id_institucion" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Institución</label>
                            <select v-model="form.id_institucion" id="id_institucion" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
                                <option disabled value="">Selecciona una institución</option>
                                <option v-for="institucion in instituciones" :key="institucion.id_institucion"
                                    :value="institucion.id_institucion">
                                    {{ institucion.subsistema }}
                                </option>
                            </select>
                        </div>

                        <!-- <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"> -->
                        <div class="mb-4">
                            <label for="role" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Roles</label>
                            <select v-model="form.role" id="id_curso"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
                                <option disabled value="">Selecciona un Rol</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">
                                    {{ role.name }}
                                </option>
                            </select>
                        </div>
                    <!-- </div> -->




                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                            {{ editing ? 'Actualizar' : 'Guardar' }} Distrito
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeleteusuario.update"
            title="¿Eliminar este registro?" />
    </AppLayout>
</template>
