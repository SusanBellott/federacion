<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed, toRefs,watch  } from "vue";
import { router, usePage,useForm } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorUsuarios from "@/Components/Buscador.vue";
import InputError from "@/Components/InputError.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";

const props = defineProps({
    usuarios: Object,

    distritos: Array,
    instituciones: Array,
    codigosSie: Array,
    roles: Object,
    filters: Object,
});
const page = usePage();
const currentPage = computed(() => props.usuarios.current_page);
const perPage = computed(() => props.usuarios.per_page);
const errors = computed(() => page.props.errors || {});
const erroresrecepcion = ref({}); // ✅ Esto es lo que falta para capturar errores del backend

// Acceso a flashes

const flash = computed(() => page.props.flash || {});
const deleteDialog = ref(null);
const usuarioSeleccionado = ref(null);
const deleteCode = ref(null);

const resetDialog = ref(null);

const id_user = ref(null);

const handleDelete = (uuid, code, mensaje) => {
    usuarioSeleccionado.value = { uuid_user: uuid };
    deleteCode.value = code;
    deleteDialog.value?.show(uuid, code, mensaje);
};


const roles = props.roles


// Estado del modal
const showModal = ref(false);
const isEditing = ref(false);
// Datos del formulario
const form = ref({
    distrito_id: '',
    codigo_sie_id: '',  
    institucion_id: '', 
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
    isEditing.value = false;
    resetForm();
    showModal.value = true;
};

const handleClickEditar = async (uuid, institucion_id, distrito_id, codigo_sie_id, ci, rda, name, primer_apellido, segundo_apellido, item, cargo, horas, email, roles) => {
    isEditing.value = true;
    id_user.value = uuid;

    // Ensure CI and RDA are strings
    form.value.ci = String(ci || '');
    form.value.rda = String(rda || '');
    
    // Ensure all other fields are populated
    form.value.name = name || '';
    form.value.primer_apellido = primer_apellido || '';
    form.value.segundo_apellido = segundo_apellido || '';
    form.value.item = item || '';
    form.value.cargo = cargo || '';
    form.value.horas = horas || '';
    form.value.email = email || '';
    form.value.role = roles?.length > 0 ? roles[0].id : '';
    
    // IMPORTANT: Set distrito_id first
    form.value.distrito_id = distrito_id || '';
    
    // Load related data and wait for it to complete
    if (distrito_id) {
        try {
            const relatedData = await cargarDatosRelacionados(distrito_id);
            
            // After data is loaded, set codigo_sie_id and institucion_id with slight delay to ensure the options are populated
            setTimeout(() => {
                form.value.codigo_sie_id = codigo_sie_id || '';
                form.value.institucion_id = institucion_id || '';
                
                console.log("Form values after loading:", {
                    distrito_id: form.value.distrito_id,
                    codigo_sie_id: form.value.codigo_sie_id,
                    institucion_id: form.value.institucion_id,
                    codigos_sie_length: codigos_sie.value.length,
                    instituciones_length: instituciones.value.length
                });
            }, 100);
        } catch (error) {
            console.error("Error loading related data:", error);
        }
    }
    
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    form.value = {
        distrito_id: "",
      
        codigo_sie_id: "",
        institucion_id: "",
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
    isEditing.value = false;
    instituciones.value = [];
    codigos_sie.value = [];
};


const codigos_sie = ref([]);
const instituciones = ref([]);

const cargarDatosRelacionados = async (distrito_id) => {
    if (!distrito_id) {
        instituciones.value = [];
        codigos_sie.value = [];
        return;
    }

    try {
        console.log("Cargando datos para distrito:", distrito_id);
        const response = await axios.get(`/api/distritos/${distrito_id}/datos-relacionados`);
        
        instituciones.value = response.data.instituciones || [];
        codigos_sie.value = response.data.codigos_sie || [];
        
        console.log("Datos cargados:", { 
            instituciones: instituciones.value.length, 
            codigos_sie: codigos_sie.value.length 
        });
        return response.data;
    } catch (error) {
        console.error("Error cargando datos relacionados:", error);

        if (error.response) {
            console.log("🔥 ERROR DEL BACKEND:");
            console.log("Mensaje:", error.response.data.message);
            console.log("Archivo:", error.response.data.file);
            console.log("Línea:", error.response.data.line);
            console.log("Traza:", error.response.data.trace);
        } else {
            console.log("Error inesperado:", error.message);
        }

        alert("No se pudieron cargar los datos relacionados. Revisa la consola.");
        return Promise.reject(error);
    }
};
watch(() => form.value.distrito_id, async (nuevoDistrito, antiguoDistrito) => {
    console.log("District changed:", nuevoDistrito, antiguoDistrito);
    
    if (nuevoDistrito === antiguoDistrito) return;
    
    if (nuevoDistrito) {
        // Only reset related fields when not editing
        if (!isEditing.value) {
            form.value.codigo_sie_id = '';
            form.value.institucion_id = '';
        }
        
        try {
            await cargarDatosRelacionados(nuevoDistrito);
            
            // Log state after loading data
            console.log("After loading district data:", {
                isEditing: isEditing.value,
                codigo_sie_id: form.value.codigo_sie_id,
                institucion_id: form.value.institucion_id,
                codigos_sie: codigos_sie.value.length,
                instituciones: instituciones.value.length
            });
        } catch (error) {
            console.error("Error in district watcher:", error);
        }
    } else {
        instituciones.value = [];
        codigos_sie.value = [];
    }
});
const submitForm = () => {
    // Ensure CI and RDA are strings
    form.value.ci = String(form.value.ci || '');
    form.value.rda = String(form.value.rda || '');
    
    // Log data being sent
    console.log("Datos a enviar:", form.value);
    
    // For edit mode, only send fields that have been changed
    if (id_user.value) {
        router.put(`/usuarios/${id_user.value}`, form.value, {
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    } else {
        router.post("/usuarios", form.value, {
            onSuccess: () => {
                closeModal();
                console.log("Respuesta recibida - Mensajes flash:", {
                    success: flash.value.success,
                    datos_array: flash.value.datos_array,
                });
            },
            onError: (errors) => {
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    }
};


// Convertir listas de selección al formato para SearchableSelect
const distritosOptions = computed(() => {
  return props.distritos.map(d => ({
    value: d.id_distrito,
    label: d.descripcion
  }));
});

const codigosSieOptions = computed(() => {
  return codigos_sie.value.map(c => ({
    value: c.id_codigo_sie,
    label: c.unidad_educativa
  }));
});

const institucionesOptions = computed(() => {
  return instituciones.value.map(i => ({
    value: i.id_institucion,
    label: i.nivel
  }));
});

watch(() => form.value.distrito_id, async (nuevoDistrito, antiguoDistrito) => {
  if (nuevoDistrito !== antiguoDistrito) {
    if (!isEditing.value) {
      form.value.codigo_sie_id = "";
      form.value.institucion_id = "";
    }
    await cargarDatosRelacionados(nuevoDistrito);
  }
});


const resetPassword = (id, cod, texto) => {
    resetDialog.value?.show(id, cod, texto);
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
                                        <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Nro</th> 
                                        <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Datos Personales</th>
                                            <th
                                            class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Datos Institucionales</th>
                                        <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Rol</th>
                                        <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update')"
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Estado</th>
                                            <th v-if="$page.props.permissions.includes('editarestadodeleteusuario.update') || $page.props.permissions.includes('usuarioseditar.update')"
                                            class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in usuarios.data" :key="user.id"
                                        class="border-b dark:border-white/40">
                                        <td class="p-6 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent text-center">{{ (currentPage - 1) * perPage + index + 1 }}</td>
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
                                            <div class="flex flex-col justify-center text-xs leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                <div class="mb-1">
                                                    <h6
                                                        class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        <span class="font-semibold">Distrito:</span> <div class="text-slate-400">{{ user.distrito?.descripcion || 'Sin distrito' }}</div>
                                                    </h6>
                                                    <h6
                                                        class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        <span class="font-semibold">Unidad Educativa:</span> <div class="text-slate-400">{{ user.codigo_sie?.unidad_educativa || 'Sin unidad educativa' }}</div>
                                                    </h6>
                                                    <h6
                                                        class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        <span class="font-semibold">Nivel:</span> <div class="text-slate-400">{{ user.institucion?.nivel || 'Sin institución' }}</div>
                                                    </h6>
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
                                                <button v-if="$page.props.permissions.includes('usuarios.resetearpassword')"
                                                @click="resetPassword(user.uuid_user, 0, '¿Estás seguro de que deseas Resetear Credenciales?')"
                                                class="p-1.5 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 rounded-full hover:bg-yellow-50 dark:hover:bg-yellow-900/30 transition-colors duration-200"
                                                    title="Reset Credenciales"
                                                >
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                                </svg>
                                                </button>
                                                <button
                                                    v-if="user.estado != 'eliminado' && user.estado != 'inactivo' && $page.props.permissions.includes('usuarioseditar.update')"
                                                    @click="handleClickEditar(
                                                        user.uuid_user,
                                                        user.institucion_id,
                                                        user.distrito_id,
                                                        user.codigo_sie_id,                                                       
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
                
                            <Pagination :pagination="usuarios" :filters="filters" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal with Form -->
            <Modal :show="showModal" @close="closeModal" max-width="2xl">
                <form @submit.prevent="submitForm">
                    <div class="p-6">
                        <h2 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
                            {{ id_user ? 'Editar Usuario' : 'Agregar Nuevo Usuario' }}
                        </h2>

                        <div class="space-y-4">
                            <!-- Campo CI -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Carnet <span class="text-red-500">*</span>
                                </label>
                                <input id="codigo" v-model="form.ci" type="number"
                                placeholder="Ingrese cédula de identidad" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                     <InputError :message="errors.ci" class="mt-2" />
                            </div>

                            <!-- Campo RDA -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    RDA <span class="text-red-500">*</span>
                                </label>
                                <input id="codigo" v-model="form.rda" type="number"
                                placeholder="Ingrese número de RDA" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.rda" class="mt-2" />
                            </div>

                            <!-- Campo Nombre -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Nombre Completo <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.name" type="text" required
                                placeholder="Ingrese nombre completo" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.name" class="mt-2" />
                            </div>

                            <!-- Campo Primer Apellido -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Apellido Paterno <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.primer_apellido" type="text" 
                                placeholder="Ingrese apellido paterno" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.primer_apellido" class="mt-2" />
                            </div>

                            <!-- Campo Segundo Apellido -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Apellido Materno <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.segundo_apellido" type="text" 
                                placeholder="Ingrese apellido materno" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.segundo_apellido" class="mt-2" />
                            </div>

                            <!-- Campo Correo -->
                            <div v-if="id_user">
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Correo Institucional<span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.email" type="email" required
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"/>
                                    <InputError :message="errors.email" class="mt-2" />
                            </div>

                            <!-- Campo Item -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Ítem <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.item" type="number" required
                                placeholder="Ingrese número de ítem" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.item" class="mt-2" />
                            </div>

                            <!-- Campo Cargo -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Cargo <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.cargo" type="number" required
                                placeholder="Ingrese número de cargo" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.cargo" class="mt-2" />
                            </div>

                            <!-- Campo Horas -->
                            <div>
                                <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                    Horas <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.horas" type="number" required
                                placeholder="Ingrese número de horas" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                                <InputError :message="errors.horas" class="mt-2" />
                            </div>

                      <!-- Distrito -->
  <!-- Distrito -->

    <div class="mb-4">
      <label for="distrito_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Distrito <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.distrito_id"
        :options="distritosOptions"
        placeholder="Buscar distrito..."
        
      />
      <InputError :message="errors.distrito_id" />


    </div>

  
  <!-- Código SIE -->

    <div class="mb-4">
      <label for="codigo_sie_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Código SIE <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.codigo_sie_id"
        :options="codigosSieOptions"
        placeholder="Buscar código SIE..."
      />
      <InputError :message="errors.codigo_sie_id" />

    </div>

  
  <!-- Institución -->

    <div class="mb-4">
      <label for="institucion_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Institución <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.institucion_id"
        :options="institucionesOptions"
        place
        holder="Buscar institución..."
        
      />
      <InputError :message="errors.institucion_id" />

    </div>



                            <div class="mb-4">
                                <label for="role" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Roles <span class="text-red-500">*</span></label>
                                <select v-model="form.role" id="id_curso"
                                    class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
                                    <option disabled value="">Selecciona un Rol</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError :message="errors.role" />

                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                            class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-slate-500 leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                                {{ id_user ? 'Actualizar' : 'Guardar' }} Usuario
                            </button>
                        </div>
                    </div>
                </form>
            </Modal>

            <ConfirmDelete
  ref="deleteDialog"
  :method="'patch'"
  route-name="editarestadodeleteusuario.update"
  :params="{ uuid: usuarioSeleccionado?.uuid_user || '', code: deleteCode || '' }"
  title="¿Eliminar este registro?" />
            <ConfirmDelete ref="resetDialog" :method="'patch'" route-name="usuarios.resetearpassword"
                title="Desea Resetear Credenciales?" />
        </div>
    </AppLayout>
</template>
