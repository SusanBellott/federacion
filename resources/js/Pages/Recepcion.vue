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
    usuarios: Object,
    instituciones: Object,
    cursos: Object,
    filters: Object,
});

// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});

const deleteDialog = ref(null);
const id_user = ref(null);
const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};

// Estado del modal
const showModal = ref(false);

// Datos del formulario
const form = ref({
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
    id_curso: "",
});

const handleClick = () => {
    showModal.value = true;
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
        id_curso: "",
    };
    id_user.value = null;
};

const submitForm = () => {
    if (!id_user.value) {
        router.post("/recepciones", form.value, {
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
    } else {
        router.put(`/institucioneseditar/${id_institucion.value}`, form.value, {
            onSuccess: () => {
                closeModal();
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
                Institucion Molquino
            </h2>
        </template>

        <div class="row flex justify-center">
            <div v-if="flash.success">
                <useSweetAlert :data="flash.datos_array" />
            </div>
            <editaralerta title="¡Registro editado correctamente!" />
        </div>

        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border p-6">
            <div v-if="flash.success" class="mb-4">
                <useSweetAlert :data="flash.datos_array" />
            </div>
            
            <p class="leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Información del Usuario</p>
            
            <form @submit.prevent="submitForm" class="mt-4">
                <div class="flex flex-wrap -mx-3">
                    <!-- Información Básica -->
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="name" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Nombre</label>
                            <input v-model="form.name" type="text" id="name" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="primer_apellido" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Primer Apellido</label>
                            <input v-model="form.primer_apellido" type="text" id="primer_apellido" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="segundo_apellido" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Segundo Apellido</label>
                            <input v-model="form.segundo_apellido" type="text" id="segundo_apellido" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="ci" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Cédula de Identidad</label>
                            <input v-model="form.ci" type="number" id="ci" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="email" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Correo Electrónico</label>
                            <input v-model="form.email" type="email" id="email" required
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                </div>
                
                <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                
                <p class="leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Información Institucional</p>
                
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
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
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0">
                        <div class="mb-4">
                            <label for="id_curso" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Cursos</label>
                            <select v-model="form.id_curso" id="id_curso"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none">
                                <option disabled value="">Selecciona un curso</option>
                                <option v-for="curso in cursos" :key="curso.id_curso" :value="curso.id_curso">
                                    {{ curso.nombre }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                
                <p class="leading-normal uppercase dark:text-white dark:opacity-60 text-sm">Información Adicional (Opcional)</p>
                
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="rda" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">RDA</label>
                            <input v-model="form.rda" type="number" id="rda"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="item" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Item</label>
                            <input v-model="form.item" type="number" id="item"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="cargo" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Cargo</label>
                            <input v-model="form.cargo" type="number" id="cargo"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                    
                    <div class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0">
                        <div class="mb-4">
                            <label for="horas" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Horas de trabajo</label>
                            <input v-model="form.horas" type="number" id="horas"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none" />
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" @click="closeModal"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
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