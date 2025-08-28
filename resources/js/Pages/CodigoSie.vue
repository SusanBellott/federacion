<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { reactive, ref, computed,watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorCodigosSIE from "@/Components/Buscador.vue";
import validaciones from "@/Components/InputError.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";
const props = defineProps({
  codigosie: Object,
  distritos: Array,
    instituciones: Array, 
  filters: Object,
});

// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});
const errores = reactive({});
const currentPage = computed(() => props.codigosie.current_page);
const perPage = computed(() => props.codigosie.per_page);
const deleteDialog = ref(null);
const id_codigo = ref(null);
const codigoSeleccionado = ref(null);
const deleteCode = ref(null);

// Computed para determinar si estamos editando
const editing = computed(() => !!id_codigo.value);

const codigos_sie = ref([]);

const handleDelete = (codigo, cod, texto) => {
  codigoSeleccionado.value = codigo;
  deleteCode.value = cod;
  deleteDialog.value?.show(codigo.uuid_codigo_sie, cod, texto);
};

// Estado del modal
const showModal = ref(false);

// Datos del formulario
const form = ref({
  distrito_id: "",
   institucion_id: "",
  programa: "",
  unidad_educativa: "",
});

// Convertir listas de selección al formato para SearchableSelect
const distritosOptions = computed(() => {
  return props.distritos.map(d => ({
    value: d.id_distrito,
    label: `${d.codigo} - ${d.descripcion}`
  }));
});
const institucionesFiltradas = computed(() => {
  return props.instituciones
    .filter(i => i.id_distrito === form.value.distrito_id)
    .map(i => ({
      value: i.id_institucion,
      label: `${i.servicio} - ${i.nivel}`
    }));
});


const handleClick = () => {
  showModal.value = true;
};

const handleClickEditar = (uuid, distrito_id, institucion_id, programa, unidad_educativa) => {
  showModal.value = true;
  id_codigo.value = uuid;
  // Aquí modificamos el objeto dentro de ref()
  form.value.distrito_id = distrito_id;
  form.value.institucion_id = institucion_id;
  form.value.programa = programa;
  form.value.unidad_educativa = unidad_educativa;
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.value = {
    distrito_id: "",
    programa: "",
    unidad_educativa: "",
  };
  id_codigo.value = null;
  Object.keys(errores).forEach(key => delete errores[key]);
};

const submitForm = () => {
  const handler = {
    onSuccess: () => {
  closeModal();
  router.visit(route("codigosie.index"), {
    preserveScroll: true,
  });
    },
    onError: (errors) => {
      Object.assign(errores, errors);
      console.error("Errores de validación:", errors);
    },
    preserveScroll: true,
  };

  if (!id_codigo.value) {
    router.post(route("codigosie.create"), form.value, handler);
  } else {
    router.put(route("codigosie.update", { codigo_sie: id_codigo.value }), form.value, handler);

  }
};
</script>

<template>
  <AppLayout title="Códigos SIE">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Códigos SIE
      </h2>
    </template>

    <div class="flex-none w-full max-w-full px-3">
      <h6 class="text-black">Códigos SIE</h6>
      <div  class="relative flex flex-col min-w-0 break-words bg-red-600/15 backdrop-blur-md border border-red-600/60 shadow-xl rounded-2xl p-6 text-slate-800">    
        <!-- Encabezado de buscar mostrar y agregar nuevo codigo sie  -->
        <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
  <div class="flex flex-col lg:flex-row lg:items-center lg:gap-4 w-full">

    <!-- Buscador ocupa más espacio en escritorio -->
    <div class="flex-1">
      <BuscadorCodigosSIE
        :filters="filters"
        ruta="codigosie.index"
      />
    </div>

    <!-- Botón agregar -->
    <div class="mt-4 lg:mt-0 flex justify-end lg:justify-start">
      <button
        v-if="$page.props.permissions.includes('codigosie.create')"
        class="w-full sm:w-auto lg:w-fit
               inline-flex items-center justify-center
               px-4 py-2.5 sm:px-6 sm:py-3
               bg-blue-500 hover:bg-blue-600
               dark:bg-blue-600 dark:hover:bg-blue-700
               text-white font-semibold
               text-sm sm:text-base
               rounded-lg shadow-lg hover:shadow-xl
               dark:shadow-blue-900/25 dark:hover:shadow-blue-900/40
               transform hover:-translate-y-0.5
               transition-all duration-200
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
               dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800
               active:scale-95"
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
        <span class="whitespace-nowrap">Agregar Nuevo Código SIE</span>
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
<!-- Tabla de codigo sie -->
        <div class="flex-auto px-0 pt-0 pb-2">
          <div class="p-0 overflow-x-auto">
            <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
              <thead class="align-bottom">
                <tr>
                  <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Nro</th>
                  <th class="w-[250px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Distrito</th>
                   <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Nivel</th>
                  <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Codigo SIE</th>
                  <th class="w-[250px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Unidad Educativa</th>
                  <th v-if="$page.props.permissions.includes('editarestadodeletedistrito.update')" class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Estado</th>
                  <th v-if="$page.props.permissions.includes('editarestadodeletedistrito.update') || $page.props.permissions.includes('distritoseditar.update')" class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-red-500 text-black whitespace-normal break-words">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in codigosie.data" :key="item.id_codigo_sie" class="border-b border-red-500">
                  <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase">
                    {{ (currentPage - 1) * perPage + index + 1 }}</td>
                  <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase">
                    {{ item.distrito?.descripcion || 'Sin distrito' }}
                  </td>
                  <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase">
                   <td>{{ item.institucion?.nivel || 'Sin institución' }}</td>

                  </td>
                  <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase">
                    {{ item.programa }}
                  </td>
                  <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b border-red-500 text-[11px] font-semibold text-black whitespace-normal break-words uppercase">
                    {{ item.unidad_educativa }}
                  </td>
                  <td v-if="$page.props.permissions.includes('editarestadodeletedistrito.update')" class="p-2 text-center align-middle bg-transparent border-b border-red-500 whitespace-nowrap shadow-transparent">
                    <span v-if="item.estado == 'activo'"
                          class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                          @click="handleDelete(item, 2, '¿Estás seguro de que deseas deshabilitar este registro?')">
                      Activo
                    </span>
                    <span v-else-if="item.estado == 'inactivo'"
                          class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                          @click="handleDelete(item, 1, '¿Estás seguro de que deseas activar este registro?')">
                      Inactivo
                    </span>
                    <span v-else
                          class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                          @click="handleDelete(item, 1, '¿Estás seguro de que deseas registrar nuevamente?')">
                      Registrar
                    </span>
                  </td>
                  <td class="p-2 align-middle bg-transparent border-b border-red-500 whitespace-nowrap shadow-transparent">
                    <div class="flex justify-center space-x-2">
                      <button v-if="item.estado != 'eliminado' && item.estado != 'inactivo' && $page.props.permissions.includes('distritoseditar.update')"
                              @click="handleClickEditar(
                                item.uuid_codigo_sie,
                                item.distrito_id,
                                 item.institucion_id,
                                item.programa,
                                item.unidad_educativa
                              )"
                              class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                      </button>
                      <button v-if="item.estado != 'eliminado' && item.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeletedistrito.update')"
                              @click="handleDelete(item, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
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
            <Pagination :pagination="codigosie" :filters="filters" />

          </div>
        </div>
      </div>
    </div>

    <!-- Modal de formulario de codigo sie -->
    <Modal :show="showModal" @close="closeModal" max-width="2xl">
      <form @submit.prevent="submitForm">
        <div class="p-6">
          <h2 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-black opacity-70">
            {{ editing ? 'Editar Código SIE' : 'Agregar Nuevo Código SIE' }}
          </h2>

          <div class="space-y-4">

            <!-- Campo Distrito -->
            <div>
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-black">
                Distrito <span class="text-red-500">*</span>
              </label>
              <SearchableSelect
        v-model="form.distrito_id"
        :options="distritosOptions"
        placeholder="Buscar distrito"
        
      />
              <validaciones :message="errores.distrito_id" />
            </div>

<!-- Campo Institución -->
<div>
  <label class="inline-block mb-2 ml-1 font-bold text-xs text-black">
    Institución <span class="text-red-500">*</span>
  </label>
  <SearchableSelect
    v-model="form.institucion_id"
   :options="institucionesFiltradas"
    placeholder="Seleccionar nivel"
  />
  <validaciones :message="errores.institucion_id" />
</div>

            <!-- Campo Codigo SIE -->
            <div>
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-black">
                Codigo SIE <span class="text-red-500">*</span>
              </label>
              <input v-model="form.programa" type="number" 
                     placeholder="Ingrese código SIE" 
                        class="block w-full rounded-lg border border-red-300 
         bg-white text-black text-sm px-3 py-2 
         placeholder:text-gray-500
         focus:border-red-500 focus:ring focus:ring-red-200 
         outline-none transition-all"
/>     
   <validaciones :message="errores.programa" />
            </div>

            <!-- Campo Unidad Educativa -->
            <div>
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-black">
                Unidad Educativa <span class="text-red-500">*</span>
              </label>
              <input v-model="form.unidad_educativa" type="text" 
                     placeholder="Ingrese nombre de unidad educativa" 
                   class="block w-full rounded-lg border border-red-300 
         bg-white text-black text-sm px-3 py-2 
         placeholder:text-gray-500
         focus:border-red-500 focus:ring focus:ring-red-200 
         outline-none transition-all"
/>
                <validaciones :message="errores.unidad_educativa" />
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="closeModal"
                    class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-slate-500 leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md">
              Cancelar
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
              {{ editing ? 'Actualizar' : 'Guardar' }} Código SIE
            </button>
          </div>
        </div>
      </form>
    </Modal>
       <!-- Boton de Eliminacion -->
    <ConfirmDelete
      ref="deleteDialog"
      :method="'patch'"
      route-name="codigosie.estado.update"
      :params="{ uuid: codigoSeleccionado?.uuid_codigo_sie || '', code: deleteCode || '' }"
      title="¿Eliminar este registro?" />
  </AppLayout>
</template>

<style scoped>
/* Para ocultar flechas en Chrome, Safari, Edge */
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Para ocultar flechas en Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>