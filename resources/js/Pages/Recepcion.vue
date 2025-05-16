<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { reactive, ref, computed,watch  } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorInstituciones from "@/Components/Buscador.vue";
import validacioens from "@/Components/InputError.vue";
import SearchableSelect from "@/Components/SearchableSelect.vue";

const props = defineProps({
    usuarios: Object,
  instituciones: Object,
  distritos: Array,
  codigosSie: Array,
    cursos: Object,
    filters: Object,
});
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});

const deleteDialog = ref(null);
const id_user = ref(null);
const codigos_sie = ref([]);
const instituciones = ref([]);
const isEditing = ref(false);
// Estado del modal
const showModal = ref(false);
const erroresrecepcion = reactive({});

const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};



// Datos del formulario
const form = ref({
    distrito_id: "",
  codigo_sie_id: "",
  institucion_id: "",
    ci: "",
    rda: "",
    name: "",
    name2: "",
    primer_apellido: "",
    segundo_apellido: "",
    item: "",
    cargo: "",
    horas: "",
    id_curso: "",
    email: "", 
});

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

watch([() => form.value.ci, () => form.value.name], ([newCi, newName]) => {
  // Si hay un nombre y CI válido, generar el email automáticamente
  const initial = newName ? newName.charAt(0).toUpperCase() : '';

  if (newCi && newCi.toString().length >= 7) {
    form.value.email = `${initial}_${newCi}@fdteulp.com`;
  } else {
    form.value.email = ''; // Limpiar el email si el CI no es válido
  }
});
const handleClick = () => {
    showModal.value = true;
};

const handleClickEditar = (
    uuid,
    distrito_id,
    codigo_sie_id,
    institucion_id,
    subsistema,
    servicio,
    servicio_generado,
    nivel,
    programa,
    unidad_educativa
) => {
    showModal.value = true;
    isEditing.value = true;
    id_user.value = uuid;

    form.value.distrito_id = distrito_id;
    form.value.codigo_sie_id = codigo_sie_id;
    form.value.institucion_id = institucion_id;

    form.value.subsistema = subsistema;
    form.value.servicio = servicio;
    form.value.servicio_generado = servicio_generado;
    form.value.nivel = nivel;
    form.value.programa = programa;
    form.value.unidad_educativa = unidad_educativa;

    cargarDatosRelacionados(distrito_id);
};



const closeModal = () => {
    showModal.value = false;
    resetForm();
    isEditing.value = false;
};


const resetForm = () => {
    form.value = {
        distrito_id: "",
    codigo_sie_id: "",
    institucion_id: "",
        ci: "",
        rda: "",
        name: "",
        name2: "",
        primer_apellido: "",
        segundo_apellido: "",
        item: "",
        cargo: "",
        horas: "",
        id_curso: "",
    };
    id_user.value = null;
};
const submitForm = () => {
    if (!id_user.value) {
        router.post("/recepciones", form.value, {
            onSuccess: () => {
                closeModal();
                /* console.log("Respuesta recibida - Mensajes flash:", {
                    success: flash.value.success,
                    datos_array: flash.value.datos_array,
                }); */
            },
            onError: (errors) => {
                erroresrecepcion.value = errors;
                //console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    } else {
        router.put(`/institucioneseditar/${id_institucion.value}`, form.value, {
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                erroresrecepcion.value = errors;
                //console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    }
};
const cargarDatosRelacionados = async (distrito_id) => {
  if (!distrito_id) {
    instituciones.value = [];
    codigos_sie.value = [];
    return;
  }
  try {
    const response = await axios.get(`/api/distritos/${distrito_id}/datos-relacionados`);
    instituciones.value = response.data.instituciones || [];
    codigos_sie.value = response.data.codigos_sie || [];
  } catch (error) {
    console.error("Error cargando datos relacionados:", error);
    alert("No se pudieron cargar los datos relacionados.");
  }
};

watch(() => form.value.distrito_id, async (nuevoDistrito, antiguoDistrito) => {
  if (nuevoDistrito !== antiguoDistrito) {
    if (!isEditing.value) {
      form.value.codigo_sie_id = "";
      form.value.institucion_id = "";
    }
    await cargarDatosRelacionados(nuevoDistrito);
  }
});


</script>

<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Institucion Molquino
            </h2>
        </template>

        <div class="row flex justify-center">
            <div v-if="flash.success">
                <useSweetAlert :data="flash.datos_array" />
            </div>
            <editaralerta title="¡Registro editado correctamente!" />
        </div>

        <div
            class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border p-6">
            <div v-if="flash.success" class="mb-4">
                <useSweetAlert :data="flash.datos_array" />
            </div>

            <p class="leading-normal uppercase text-gray-800 dark:text-white text-sm">Información del Usuario</p>


            <form @submit.prevent="submitForm" class="mt-4">
                <div class="flex flex-wrap -mx-3">
                    <!-- Información Básica -->
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="name"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Primer Nombre <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" id="name" placeholder="Ingrese primer nombre" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                     />
                            <validacioens :message="erroresrecepcion?.value?.name" />
                        </div>
                    </div>
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="name2"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Segundo Nombre</label>
                            <input v-model="form.name2" type="text" id="name2" placeholder="Ingrese segundo nombre(Opcional)" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all" />      
                            <validacioens :message="erroresrecepcion?.value?.name2" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="primer_apellido"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Apellido Paterno<span class="text-red-500">*</span></label>
                            <input v-model="form.primer_apellido" type="text" id="primer_apellido" placeholder="Ingrese apellido paterno" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.primer_apellido" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="segundo_apellido"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Apellido Materno<span class="text-red-500">*</span></label>
                            <input v-model="form.segundo_apellido" type="text" id="segundo_apellido" placeholder="Ingrese apellido materno" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.segundo_apellido" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="ci"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Cédula de Identidad <span class="text-red-500">*</span></label>
                            <input v-model="form.ci" type="number" id="ci" placeholder="Ingrese cédula de identidad" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.ci" />
                        </div>
                    </div>
                </div>

                <hr
                    class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

                    <p class="leading-normal uppercase text-gray-800 dark:text-white text-sm">Información Institucional
                </p>
               

                <div class="flex flex-wrap -mx-3">
                      <!-- Distrito -->
  <!-- Distrito -->
  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
    <div class="mb-4">
      <label for="distrito_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Distrito <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.distrito_id"
        :options="distritosOptions"
        placeholder="Buscar distrito..."
        
      />
      <validacioens :message="erroresrecepcion?.value?.distrito_id" />
    </div>
  </div>
  
  <!-- Código SIE -->
  <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
    <div class="mb-4">
      <label for="codigo_sie_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Código SIE <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.codigo_sie_id"
        :options="codigosSieOptions"
        placeholder="Buscar código SIE..."
      />
      <validacioens :message="erroresrecepcion?.value?.codigo_sie_id" />
    </div>
  </div>
  
  <!-- Institución -->
  <div class=" w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
    <div class="mb-4">
      <label for="institucion_id" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
        Institución <span class="text-red-500">*</span>
      </label>
      <SearchableSelect
        v-model="form.institucion_id"
        :options="institucionesOptions"
        placeholder="Buscar institución..."
        
      />
      <validacioens :message="erroresrecepcion?.value?.institucion_id" />
    </div>
  </div>
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="id_curso" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                                Cursos <span class="text-red-500">*</span></label>
                            <select v-model="form.id_curso" id="id_curso"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
                                <option disabled value="">Selecciona un curso</option>
                                <option v-for="curso in cursos" :key="curso.id_curso" :value="curso.id_curso">
                                    {{ curso.nombre }}
                                </option>
                            </select>
                            <validacioens :message="erroresrecepcion?.value?.id_curso" />
                        </div>
                    </div>
                </div>

                <hr
                    class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

                    <p class="leading-normal uppercase text-gray-800 dark:text-white text-sm">Información Adicional </p>

                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="rda"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">RDA <span class="text-red-500">*</span></label>
                            <input v-model="form.rda" type="number" id="rda"placeholder="Ingrese número de RDA" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.rda" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="item"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Ítem <span class="text-red-500">*</span></label>
                            <input v-model="form.item" type="number" id="item"placeholder="Ingrese número de ítem" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.item" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="cargo"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Cargo <span class="text-red-500">*</span></label>
                            <input v-model="form.cargo" type="number" id="cargo"placeholder="Ingrese número de cargo" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.cargo" />
                        </div>
                    </div>

                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="horas"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Horas<span class="text-red-500">*</span></label>
                            <input v-model="form.horas" type="number" id="horas"placeholder="Ingrese número de horas" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 
                     dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"/>
                            <validacioens :message="erroresrecepcion?.value?.horas" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="closeModal"
                        class=" px-4 py-2 bg-blue-500  hover:bg-blue-600 text-white  dark:bg-violet-800  dark:hover:bg-violet-600 dark:text-white rounded-md transition duration-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                        Guardar Datos
                    </button>
                </div>
            </form>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodelete.update"
                title="¿Eliminar este registro?" />
        </div>
    </AppLayout>
</template>
