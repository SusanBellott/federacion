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
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Usuarios
            </h2>
        </template>
        <div class="p-3">
            <!-- Alerts & Notifications -->
            <div v-if="flash.success">
                <useSweetAlert :data="flash.datos_array" />
            </div>
            <editaralerta title="¡Registro editado correctamente!" />

            <!-- Table Container with Modern Styling -->
            <div class="flex-none w-full max-w-full px-3">
                <h6 class="text-gray-800 dark:text-white">Usuarios</h6>
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                        <!-- Search & Add Btton Row -->
                        <div class="flex items-center space-x-4">
                            <!-- Buscador (Search) -->
                            <div class="relative">
                                <BuscadorUsuarios :filters="filters" ruta="usuarios.index" />
                            </div>
                        </div>
                        <!-- Add Button -->
                        <button v-if="$page.props.permissions.includes('usuarios.create')"
                            class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all bg-blue-500 rounded-lg cursor-pointer leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md"
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

                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-0 overflow-x-auto">
                            <table
                                class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                                <thead class="align-bottom">
                                    <tr>
                                        <th
                                            class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                                            Datos Personales</th>
                                        <th
                                            class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                                            Rol</th>
                                        <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update')"
                                            class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                                            Estado</th>
                                        <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update') || $page.props.permissions.includes('usuarioseditar.update')"
                                            class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-400 opacity-70">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in usuarios.data" :key="user.id"
                                        class="border-b dark:border-white/40">
                                        <td
                                            class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <div class="flex px-2 py-1">
                                                <div>
                                                    <!-- User Initial Circle -->
                                                    <div
                                                        class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 ease-in-out text-sm h-9 w-9 rounded-xl bg-slate-700 dark:bg-slate-600">
                                                        {{ user.name.charAt(0) }}
                                                    </div>
                                                </div>
                                                <div class="flex flex-col justify-center">
                                                    <h6
                                                        class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        {{ user.name }}</h6>
                                                    <p
                                                        class="mb-0 text-xs leading-tight dark:text-white dark:opacity-80 text-slate-400">
                                                        {{ user.primer_apellido }} {{
                                                        user.segundo_apellido }}
                                                    </p>
                                                    <p class="text-xs text-slate-400">
                                                        <span class="font-semibold">C.I.:</span> {{ user.ci }} | 
                                                        <span class="font-semibold">R.D.A.:</span> {{ user.rda }}
                                                    </p>
                                                    <p class="text-xs text-slate-400">
                                                        <span class="font-semibold">Email:</span> {{ user.email }}
                                                    </p>
                                                    <p class="text-xs text-slate-400">
                                                        <span class="font-semibold">Item:</span> {{ user.item }} | 
                                                        <span class="font-semibold">Cargo:</span> {{ user.cargo }} | 
                                                        <span class="font-semibold">Horas:</span> {{ user.horas }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <div class="flex flex-col space-y-1">
                                                <span v-for="r in user.roles" :key="r.id" 
                                                    class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">
                                                    {{ r.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td v-if="$page.props.permissions.includes('editarestadodeleteusuario.update')"
                                            class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
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
                                        <td
                                            class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <div class="flex space-x-3">
                                                <!-- Editar -->
                                                <button
                                                    v-if="user.estado != 'eliminado' && user.estado != 'inactivo' && $page.props.permissions.includes('usuarioseditar.update')"
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
                                                    class="p-1.5 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 rounded-full hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors duration-200"
                                                    title="Editar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <!-- Eliminar -->
                                                <button
                                                    v-if="user.estado != 'eliminado' && user.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeleteusuario.update')"
                                                    @click="handleDelete(user.uuid_user, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
                                                    class="p-1.5 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors duration-200"
                                                    title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
                        <h2 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                            {{ editing ? 'Editar Usuario' : 'Agregar Nuevo Usuario' }}
                        </h2>

                        <div class="space-y-4">
                            <!-- Campo CI -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Cedula de Identidad
                                </label>
                                <input id="codigo" v-model="form.ci" type="number"
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo RDA -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    RDA
                                </label>
                                <input id="codigo" v-model="form.rda" type="number"
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Nombre -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Nombre
                                </label>
                                <input v-model="form.name" type="text" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Primer Apellido -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Primer Apellido
                                </label>
                                <input v-model="form.primer_apellido" type="text" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                    <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Segundo Apellido -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Segundo Apellido
                                </label>
                                <input v-model="form.segundo_apellido" type="text" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                    <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Correo -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Correo
                                </label>
                                <input v-model="form.email" type="email" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                    <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Item -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Item
                                </label>
                                <input v-model="form.item" type="number" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Cargo -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Cargo
                                </label>
                                <input v-model="form.cargo" type="number" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                <InputError :message="errors" class="mt-2" />
                            </div>

                            <!-- Campo Horas -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Horas
                                </label>
                                <input v-model="form.horas" type="number" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
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
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                            class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-slate-500 leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                                {{ editing ? 'Actualizar' : 'Guardar' }} Usuario
                            </button>
                        </div>
                    </div>
                </form>
            </Modal>

            <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeleteusuario.update"
                title="¿Eliminar este registro?" />
        </div>
    </AppLayout>
</template>
