<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Instituciones
            </h2>
        </template>

        <div class="flex-none w-full max-w-full px-3">
            <h6 class="text-gray-800 dark:text-white">Instituciones</h6>
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <BuscadorInstituciones :filters="filters" ruta="instituciones.index" />
                        </div>
                        

                    </div>

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
                                Nueva Institución
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
                        <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Nro</th> 
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Distrito</th>
                                    <th class="w-[200px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Unidad educativa</th>
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Subsistema</th>
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Servicio</th>
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Servicio Generado</th>
                                    <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Nivel</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodelete.update')" class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Estado</th>
                                    <th v-if="$page.props.permissions.includes('institucioneseditar.update') && $page.props.permissions.includes('editarestadodelete.update')" class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(institucion, index) in instituciones.data" :key="institucion.id" class="border-b dark:border-white/40">
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase"
                                    >{{ (currentPage - 1) * perPage + index + 1 }}</td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                        {{ institucion.distrito?.descripcion || 'Desconocido' }}
                                    </td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                        {{ institucion.codigo_sie?.unidad_educativa || 'Desconocido' }}
                                    </td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                       {{ institucion.subsistema }}
                                    </td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                        {{ institucion.servicio }}
                                    </td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                       {{ institucion.servicio_generado }}
                                    </td>
                                    <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words uppercase">
                                        {{ institucion.nivel }}
                                    </td>
    
                                    
                                    <td v-if="$page.props.permissions.includes('editarestadodelete.update')" class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span v-if="institucion.estado == 'activo'"
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(institucion.uuid_institucion,2,  '¿Estás seguro de que deseas deshabilitar este registro?')">
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
                                                    institucion.unidad_educativa_id,
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
                        <Pagination :pagination="instituciones" :filters="filters" />
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
                        <!-- Campo Distrito -->
<div>
  <label class="px-6 inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
    Distrito <span class="text-red-500">*</span>
  </label>
  <SearchableSelect
    v-model="form.id_distrito"
    :options="distritosOptions"
    placeholder="Buscar distrito..."
  />
  <validacioens :message="erroresinstitucion?.value?.id_distrito" />
</div>


    <!-- Campo Unidad Educativa (Nuevo) -->
<!-- Campo Unidad Educativa -->
<div>
  <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
    Unidad Educativa
  </label>
  <SearchableSelect
    v-model="form.unidad_educativa_id"
    :options="unidadEducativaOptions"
    placeholder="Buscar unidad educativa..."
  />
  <validacioens :message="erroresinstitucion?.value?.unidad_educativa_id" />
</div>


    <div>
      <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Subsistema <span class="text-red-500">*</span>
      </label>
      <select 
        v-model="form.subsistema" 
        required 
        class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
        <option value="">Seleccione un subsistema</option>
        <option value="EDUCACION SUPERIOR">Educación Superior</option>
        <option value="REGULAR">Regular</option>
        <option value="ALTERNATIVA">Alternativa</option>
        <option value="ESPECIAL PERMANENTE">Especial Permanente</option>

      </select>
      <validacioens :message="erroresinstitucion?.value?.subsistema" />

    </div>

                        <!-- Campo Servicio -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Servicio <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.servicio"
                                type="number"
                                required
                                placeholder="Ingrese número de servicio" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresinstitucion?.value?.servicio" />

                        </div>

                        <!-- Campo Servicio Generado -->
                        <div>
                            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Servicio Generado <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.servicio_generado"
                                type="number"
                                required  disabled
                                placeholder="Número de servicio generado" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                                dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                        </div>

                        <!-- Campo Nivel -->
                        <!-- Campo Nivel (Select) -->
    <div>
      <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Nivel <span class="text-red-500">*</span>
      </label>
      <select 
        v-model="form.nivel" 
        required 
        class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
        <option value="">Seleccione un nivel</option>
        <option value="ALTERNATIVA">Alternativa</option>
        <option value="ESPECIAL">Especial</option>
        <option value="INICIAL">Inicial</option>
        <option value="PERMANENTE">Permanente</option>
        <option value="PRIMARIA">Primaria</option>
        <option value="SECUNDARIA">Secundaria</option>
      </select>
      <validacioens :message="erroresinstitucion?.value?.nivel" />
    </div>

                        <!-- Campo Programa -->


                        <!-- Campo Unidad Educativa -->

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
    :params="{ uuid: institucionSeleccionada?.uuid_institucion || '', code: deleteCode || '' }"
    title="¿Eliminar este registro?"
/>

    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import {reactive, ref, computed,watch } from "vue";
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
    codigosSie: Array, // Añadido para recibir los códigos SIE
    filters: Object,
});
const unidadesEducativas = ref([]);


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
    unidad_educativa_id: "",
});

// Modifica la función cargarUnidadesEducativas para que devuelva la promesa de axios
const cargarUnidadesEducativas = () => {
    console.log("Cargando unidades para distrito:", form.value.id_distrito);

    if (form.value.id_distrito) {
        return axios.get(`/api/distritos/${form.value.id_distrito}/unidades-educativas`)
            .then(response => {
                unidadesEducativas.value = response.data;
                console.log("Unidades cargadas:", unidadesEducativas.value);
                return response.data;
            })
            .catch(error => {
                console.error("Error al cargar unidades educativas:", error);
                return [];
            });
    } else {
        unidadesEducativas.value = [];
        return Promise.resolve([]);
    }
};



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
        unidad_educativa_id: "", 
    };
    id_institucion.value = null;
    unidadesEducativas.value = []; 
};

// Modifica la función handleClickEditar para manejar correctamente la asincronía
const handleClickEditar = async (
    uuid,
    id_distrito,
    subsistema,
    servicio,
    servicio_generado,
    nivel,
    unidad_educativa_id = "",
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

    try {
        // Espera a que las unidades educativas se carguen completamente
        const unidades = await cargarUnidadesEducativas();
        
        // Verifica si existe la unidad educativa en las opciones cargadas
        const match = unidades.find(u => u.id_codigo_sie == unidad_educativa_id);
        
        // Establece el valor solo después de confirmar que las unidades se cargaron
        form.value.unidad_educativa_id = match ? unidad_educativa_id : "";
        
        console.log("Unidad educativa seleccionada:", form.value.unidad_educativa_id);
    } catch (error) {
        console.error("Error al cargar unidades educativas:", error);
        form.value.unidad_educativa_id = "";
    }
};

const submitForm = () => {
  const config = {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
      router.reload({ only: ['instituciones'] }); // no más setTimeout ni onFinish
    },
    onError: (errors) => {
      erroresinstitucion.value = errors;
    },
  };

  if (!id_institucion.value) {
    router.post("/instituciones", form.value, config);
  } else {
    router.put(`/instituciones/${id_institucion.value}`, form.value, config);
  }
};


const distritosOptions = computed(() =>
  props.distritos.map(d => ({
    value: d.id_distrito,
    label: `${d.codigo} - ${d.descripcion}`,
  }))
);

const unidadEducativaOptions = computed(() =>
  unidadesEducativas.value.map(u => ({
    value: u.id_codigo_sie,
    label: u.unidad_educativa,
  }))
);

watch(() => form.value.id_distrito, async (nuevoDistrito) => {
  if (nuevoDistrito) {
    await cargarUnidadesEducativas(); // llama tu función
  } else {
    unidadesEducativas.value = [];
    form.value.unidad_educativa_id = "";
  }
});


const handleDelete = (uuid, code, mensaje) => {
    institucionSeleccionada.value = { uuid_institucion: uuid };
    deleteCode.value = code;
    deleteDialog.value?.show(uuid, code, mensaje);
};



// Función para obtener el nombre del distrito
const getDistritoNombre = (id_distrito) => {
    const distrito = props.distritos.find(d => d.id_distrito === id_distrito);
    return distrito ? distrito.descripcion : 'Desconocido';
};
// Función para obtener la unidad educativa vinculada a una institución
const getUnidadEducativa = (institucion) => {
    if (institucion.codigosSie && institucion.codigosSie.length > 0) {
        return institucion.codigosSie[0].unidad_educativa;
    }
    return 'No asignada';
};
watch(() => form.value.servicio, (newServicio) => {
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
});

watch(flash, (nuevoFlash) => {
  if (nuevoFlash.success && nuevoFlash.datos_array) {
    useSweetAlert({
      title: nuevoFlash.datos_array.title || '¡Actualizado!',
      text: nuevoFlash.datos_array.text || 'Se actualizó correctamente.',
      icon: nuevoFlash.datos_array.icon || 'success',
    });
  }
});

</script>
