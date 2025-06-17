<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { reactive, ref, computed, watch } from "vue";
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
const isEditing = ref(false);

// Estado del modal
const showModal = ref(false);
const erroresrecepcion = reactive({});

// Options para los selectores cascada
const institucionesOptions = ref([]);
const codigosSieOptions = ref([]);

// Datos del formulario
const form = ref({
    distrito_id: "",
    codigo_sie_id: "",
    institucion_id: "",
    ci: "",
    complemento_ci: "", // valor por defecto
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

// Watch para generar email automáticamente
watch([() => form.value.ci, () => form.value.name], ([newCi, newName]) => {
    const initial = newName ? newName.charAt(0).toUpperCase() : "";
    if (newCi && newCi.toString().length >= 7) {
        form.value.email = `${initial}_${newCi}@fdteulp.com`;
    } else {
        form.value.email = "";
    }
});

// Watch para cargar instituciones cuando cambia el distrito
watch(
    () => form.value.distrito_id,
    async (nuevoDistrito) => {
        console.log("Distrito seleccionado:", nuevoDistrito);

        // Limpiar selecciones dependientes
        form.value.institucion_id = "";
        form.value.codigo_sie_id = "";
        institucionesOptions.value = [];
        codigosSieOptions.value = [];

        if (nuevoDistrito) {
            try {
                const response = await axios.get(
                    `/api/distritos/${nuevoDistrito}/instituciones`
                );
                console.log("Instituciones cargadas:", response.data);

                institucionesOptions.value = response.data.map(
                    (institucion) => ({
                        label: institucion.nivel, // o el campo que quieras mostrar
                        value: institucion.id_institucion,
                    })
                );
            } catch (error) {
                console.error("Error al cargar instituciones:", error);
                alert(
                    "No se pudieron cargar las instituciones del distrito seleccionado."
                );
            }
        }
    }
);

// Watch para cargar códigos SIE cuando cambia la institución
watch(
    () => form.value.institucion_id,
    async (nuevaInstitucion) => {
        console.log("Institución seleccionada:", nuevaInstitucion);

        // Limpiar código SIE
        form.value.codigo_sie_id = "";
        codigosSieOptions.value = [];

        if (nuevaInstitucion) {
            try {
                const response = await axios.get(
                    `/api/instituciones/${nuevaInstitucion}/codigos-sie`
                );
                console.log("Códigos SIE cargados:", response.data);

                codigosSieOptions.value = response.data.map((codigoSie) => ({
                    label: codigoSie.unidad_educativa, // o el campo que quieras mostrar
                    value: codigoSie.id_codigo_sie,
                }));
            } catch (error) {
                console.error("Error al cargar códigos SIE:", error);
                alert(
                    "No se pudieron cargar los códigos SIE de la institución seleccionada."
                );
            }
        }
    }
);
onError: (errors) => {
    console.log("Errores recibidos desde el backend:", errors);
    Object.assign(erroresrecepcion, errors);
};

const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};

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

    // Cargar datos relacionados para edición
    cargarDatosParaEdicion(distrito_id, institucion_id);
};

const cargarDatosParaEdicion = async (distrito_id, institucion_id) => {
    try {
        // Primero cargar instituciones del distrito
        if (distrito_id) {
            const responseInstituciones = await axios.get(
                `/api/distritos/${distrito_id}/instituciones`
            );
            institucionesOptions.value = responseInstituciones.data.map(
                (institucion) => ({
                    label: institucion.nivel,
                    value: institucion.id_institucion,
                })
            );
        }

        // Luego cargar códigos SIE de la institución
        if (institucion_id) {
            const responseCodigosSie = await axios.get(
                `/api/instituciones/${institucion_id}/codigos-sie`
            );
            codigosSieOptions.value = responseCodigosSie.data.map(
                (codigoSie) => ({
                    label: codigoSie.unidad_educativa,
                    value: codigoSie.id_codigo_sie,
                })
            );
        }
    } catch (error) {
        console.error("Error cargando datos para edición:", error);
    }
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
        complemento_ci: "LP",
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
    };
    id_user.value = null;

    // Limpiar options
    institucionesOptions.value = [];
    codigosSieOptions.value = [];
};

const submitForm = () => {
    if (!id_user.value) {
        router.post("/recepciones", form.value, {
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                Object.assign(erroresrecepcion, errors);
            },
            preserveScroll: true,
        });
    } else {
        router.put(`/institucioneseditar/${id_user.value}`, form.value, {
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                Object.assign(erroresrecepcion, errors);
            },
            preserveScroll: true,
        });
    }
};

const cursosAbiertos = computed(() => {
    return props.cursos.filter((c) => c.estado_curso === "abierto");
});
</script>

<template>
    <AppLayout title="Instituciones">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
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
            class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border p-6"
        >
            <div v-if="flash.success" class="mb-4">
                <useSweetAlert :data="flash.datos_array" />
            </div>

            <p
                class="leading-normal uppercase text-gray-800 dark:text-white text-sm"
            >
                Información del Usuario
            </p>

            <form @submit.prevent="submitForm" class="mt-4">
                <div class="flex flex-wrap -mx-3">
                    <!-- Información Básica -->
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="name"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Primer Nombre
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                id="name"
                                placeholder="Ingrese primer nombre"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />

                            <validacioens :message="erroresrecepcion.name" />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="name2"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Segundo Nombre
                            </label>
                            <input
                                v-model="form.name2"
                                type="text"
                                id="name2"
                                placeholder="Ingrese segundo nombre (Opcional)"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="primer_apellido"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Apellido Paterno
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.primer_apellido"
                                type="text"
                                id="primer_apellido"
                                placeholder="Ingrese apellido paterno"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="segundo_apellido"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Apellido Materno
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.segundo_apellido"
                                type="text"
                                id="segundo_apellido"
                                placeholder="Ingrese apellido materno"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="ci"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Cédula de Identidad
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.ci"
                                type="number"
                                id="ci"
                                placeholder="Ingrese cédula de identidad"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <validacioens :message="erroresrecepcion.ci" />
                        </div>
                    </div>
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="complemento_ci"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Complemento CI
                                <span class="text-red-500"></span>
                            </label>
                            <input
                                list="complementos"
                                v-model="form.complemento_ci"
                                id="complemento_ci"
                                placeholder="Ingresa el complemento (Opcional)"
                                maxlength="5"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <datalist id="complementos">
                                <option value="LP" />
                                <option value="CB" />
                                <option value="SC" />
                                <option value="-1T" />
                                <option value="-2E" />
                                <option value="EXT" />
                                <option value="A" />
                                <option value="B" />
                            </datalist>

                            <validacioens
                                :message="erroresrecepcion.complemento_ci"
                            />
                        </div>
                    </div>
                </div>

                <hr
                    class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent"
                />

                <p
                    class="leading-normal uppercase text-gray-800 dark:text-white text-sm"
                >
                    Información Institucional
                </p>

                <div class="flex flex-wrap -mx-3">
                    <!-- Distrito -->
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="distrito_id"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Distrito <span class="text-red-500">*</span>
                            </label>
                            <SearchableSelect
                                v-model="form.distrito_id"
                                :options="
                                    props.distritos.map((d) => ({
                                        label: d.descripcion,
                                        value: d.id_distrito,
                                    }))
                                "
                                placeholder="Buscar distrito"
                            />
                            <validacioens
                                :message="erroresrecepcion.distrito_id"
                            />
                        </div>
                    </div>

                    <!-- Institución -->
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="institucion_id"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Nivel <span class="text-red-500">*</span>
                            </label>
                            <SearchableSelect
                                v-model="form.institucion_id"
                                :options="institucionesOptions"
                                placeholder="Selecciona un nivel"
                                :disabled="!form.distrito_id"
                            />
                            <validacioens
                                :message="erroresrecepcion.institucion_id"
                            />
                        </div>
                    </div>

                    <!-- Código SIE -->
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="codigo_sie_id"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Unidad Educativa
                                <span class="text-red-500">*</span>
                            </label>
                            <SearchableSelect
                                v-model="form.codigo_sie_id"
                                :options="codigosSieOptions"
                                placeholder="Selecciona una unidad educativa"
                                :disabled="!form.institucion_id"
                            />
                            <validacioens
                                :message="erroresrecepcion.codigo_sie_id"
                            />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-6/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="id_curso"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Cursos <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.id_curso"
                                id="id_curso"
                                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                            >
                                <option disabled value="">
                                    Selecciona un curso
                                </option>
                                <option
                                    v-for="curso in cursosAbiertos"
                                    :key="curso.id_curso"
                                    :value="curso.id_curso"
                                >
                                    {{ curso.nombre }}
                                    ({{
                                        curso.tipo_pago === "pago"
                                            ? `Con costo Bs. ${Number(
                                                  curso.precio
                                              ).toFixed(2)}`
                                            : "Gratuito"
                                    }})
                                </option>
                            </select>
                            <validacioens
                                :message="erroresrecepcion.id_curso"
                            />
                        </div>
                    </div>
                </div>

                <hr
                    class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent"
                />

                <p
                    class="leading-normal uppercase text-gray-800 dark:text-white text-sm"
                >
                    Información Adicional
                </p>

                <div class="flex flex-wrap -mx-3">
                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="rda"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                RDA <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.rda"
                                type="number"
                                id="rda"
                                placeholder="Ingrese número de RDA"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <validacioens :message="erroresrecepcion.rda" />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="item"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Ítem <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.item"
                                type="number"
                                id="item"
                                placeholder="Ingrese número de ítem"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <validacioens :message="erroresrecepcion.item" />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="cargo"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Cargo <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.cargo"
                                type="number"
                                id="cargo"
                                placeholder="Ingrese número de cargo"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <validacioens :message="erroresrecepcion.cargo" />
                        </div>
                    </div>

                    <div
                        class="w-full max-w-full px-3 shrink-0 md:w-3/12 md:flex-0"
                    >
                        <div class="mb-4">
                            <label
                                for="horas"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Horas <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.horas"
                                type="number"
                                id="horas"
                                placeholder="Ingrese número de horas"
                                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 placeholder-gray-500 dark:bg-slate-850 dark:text-white focus:border-blue-500 focus:shadow-primary-outline focus:outline-none transition-all"
                            />
                            <validacioens :message="erroresrecepcion.horas" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white rounded-md transition duration-200"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200"
                    >
                        {{ isEditing ? "Actualizar Datos" : "Guardar Datos" }}
                    </button>
                </div>
            </form>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"></div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden"
        >
            <ConfirmDelete
                ref="deleteDialog"
                :method="'patch'"
                route-name="editarestadodelete.update"
                title="¿Eliminar este registro?"
            />
        </div>
    </AppLayout>
</template>
