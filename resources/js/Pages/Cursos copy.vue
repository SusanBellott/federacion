<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorCursos from "@/Components/Buscador.vue";

const props = defineProps({
    cursos: Object,
    filters: Object,
});
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});

const deleteDialog = ref(null);
const id_curso = ref(null);
const handleDelete = (id, cod, texto) => {
    //console.log("hola este es el id ", id);
    deleteDialog.value?.show(id, cod, texto);
};
// Estado del modal
const showModal = ref(false);
const imagenesPreviews = ref([]); // Array para las vistas previas

// Datos del formulario
const form = ref({
    nombre: "",
    descripcion: "",
    fecha_inicio: "",
    fecha_fin: "",
    carga_horaria: "",
    img_curso: [],
    img_firma: null,
    encargado: "",
    grado_academico: "",
    estado_curso: "",
});
const handleImageChange = (event) => {
    const files = event.target.files; // Obtener todos los archivos seleccionados

    if (files && files.length > 0) {
        // Limpiar arrays si es necesario
        form.value.imagenes = [];
        imagenesPreviews.value = [];

        // Procesar cada archivo
        Array.from(files).forEach(file => {
            if (file.type.match('image.*')) {
                // Agregar al array de imágenes
                form.value.imagenes.push(file);

                // Crear vista previa
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagenesPreviews.value.push(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });
    }
};
const removePreview = (index) => {
    imagenesPreviews.value.splice(index, 1);
    form.value.imagenes.splice(index, 1);
};

const removeExistingImage = (index) => {
    // Marcar imagen para eliminación en el backend
    if (!form.value.imagenes_a_eliminar) {
        form.value.imagenes_a_eliminar = [];
    }
    form.value.imagenes_a_eliminar.push(form.value.imagenes_url[index].id);
    form.value.imagenes_url.splice(index, 1);
};
const handleClick = () => {
    showModal.value = true;
};
const handleClickEditar = (
    uuid,
    nombre,
    descripcion,
    fecha_inicio,
    fecha_fin,
    carga_horaria,
    encargado,
    grado_academico,
    estado_curso
) => {
    //console.log(id_distrito, "  ", subsistema);
    showModal.value = true;
    id_curso.value = uuid;
    // Aquí modificamos el objeto dentro de ref()
    form.value.nombre = nombre;
    form.value.descripcion = descripcion;
    form.value.fecha_inicio = fecha_inicio;
    form.value.fecha_fin = fecha_fin;
    form.value.carga_horaria = carga_horaria;
    form.value.encargado = encargado;
    form.value.grado_academico = grado_academico;
    form.value.estado_curso = estado_curso;
};
const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    form.value = {
        nombre: "",
        descripcion: "",
        fecha_inicio: "",
        fecha_fin: "",
        carga_horaria: "",
        img_curso: "",
        img_firma: "",
        encargado: "",
        grado_academico: "",
        estado_curso: "",
    };
    id_curso.value = null;
};
const submitForm = () => {
    // 1. Log de datos enviados
    if (!id_curso.value) {
        router.post("/cursos", form.value, {
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
        router.put(`/cursoseditar/${id_curso.value}`, form.value, {
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

// Función para transformar FormData en un objeto
const formDataToObject = (formData) => {
    const obj = {};
    formData.forEach((value, key) => {
        obj[key] = value;
    });
    return obj;
};
</script>

<template>
    <AppLayout title="Crusos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Cursos Molquino
            </h2>
        </template>

        <div class="row flex justify-center">
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
                    Agregar Nuevo Curso
                </span>
            </button>
            <div v-if="flash.success">
                <useSweetAlert :data="flash.datos_array" />
            </div>

            <editaralerta title="¡Registro editado correctamente!" />
        </div>

        <!-- Modal con Formulario -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                        Agregar Nuevo Curso
                    </h2>

                    <div class="space-y-4">
                        <!-- Campo Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                nombre
                            </label>
                            <input v-model="form.nombre" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <!-- Campo Dirección -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                descripcion
                            </label>
                            <input v-model="form.descripcion" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>

                        <!-- Campo Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                fecha de inicio
                            </label>
                            <input v-model="form.fecha_inicio" type="date" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                fecha de culminacion
                            </label>
                            <input v-model="form.fecha_fin" type="date" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                carga horaria
                            </label>
                            <input v-model="form.carga_horaria" type="number" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Imágenes (Múltiples)</label>
                            <input type="file" @change="handleImageChange" accept="image/*" multiple
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                            <!-- Contenedor de miniaturas -->
                            <div class="mt-2 flex flex-wrap gap-2">
                                <!-- Vistas previas de nuevas imágenes -->
                                <div v-for="(preview, index) in imagenesPreviews" :key="'new-' + index"
                                    class="relative">
                                    <img :src="preview" class="h-20 w-20 object-cover rounded border border-gray-200">
                                    <button @click="removePreview(index)"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                        ×
                                    </button>
                                </div>

                                <!-- Mostrar imágenes existentes si estamos editando -->
                                <div v-for="(imagen, index) in form.imagenes_url" :key="'existing-' + index"
                                    class="relative">
                                    <img :src="imagen.url"
                                        class="h-20 w-20 object-cover rounded border border-gray-200">
                                    <button @click="removeExistingImage(index)"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="id_curso == null">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                imgen de la firma digital
                            </label>
                            <input v-model="form.img_firma" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                encargado
                            </label>
                            <input v-model="form.encargado" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                grado academico
                            </label>
                            <input v-model="form.grado_academico" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                estado del curso
                            </label>
                            <input v-model="form.estado_curso" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                            Guardar curso
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <BuscadorCursos :filters="filters" ruta="cursos.index" />

            <!-- Tabla con scroll vertical -->
            <div class="overflow-y-auto" style="max-height: 70vh">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                nombre
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                descripcion
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                fecha de inicio
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                fecha de culminacion
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                carga horaria
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                imagen curso
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                iamgen firma
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                encargado
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                grado academico
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                estado curso
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="curso in cursos.data" :key="cursos.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.nombre }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.descripcion }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.fecha_inicio }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.fecha_fin }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.carga_horaria }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.img_curso }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.img_firma }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.encargado }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.grado_academico }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ curso.estado_curso }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                <button v-if="curso.estado == 'activo'" @click="
                                    handleDelete(
                                        curso.uuid_curso,
                                        2,
                                        '¿Estás seguro de que deseas deshabilitar este registro?'
                                    )
                                    "
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors">
                                    Activo
                                </button>

                                <button v-else-if="curso.estado == 'inactivo'" @click="
                                    handleDelete(
                                        curso.uuid_curso,
                                        1,
                                        '¿Estás seguro de que deseas activar este registro?'
                                    )
                                    "
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">
                                    Inactivo
                                </button>

                                <button v-else @click="
                                    handleDelete(
                                        curso.uuid_curso,
                                        1,
                                        '¿Estás seguro de que deseas registrar nuevamente?'
                                    )
                                    "
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors">
                                    registrar
                                </button>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                <button v-if="
                                    curso.estado != 'eliminado' &&
                                    curso.estado != 'inactivo'
                                " @click="
                                    handleDelete(
                                        curso.uuid_curso,
                                        3,
                                        '¿Estás seguro de que deseas eliminar este registro de forma permanente?'
                                    )
                                    " class="bg-red-500 text-white px-4 py-2 rounded mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>

                                <button v-if="
                                    curso.estado != 'eliminado' &&
                                    curso.estado != 'inactivo'
                                " @click="
                                    handleClickEditar(
                                        curso.uuid_curso,
                                        curso.nombre,
                                        curso.descripcion,
                                        curso.fecha_inicio,
                                        curso.fecha_fin,
                                        curso.carga_horaria,
                                        curso.encargado,
                                        curso.grado_academico,
                                        curso.estado_curso
                                    )
                                    " class="bg-yellow-500 text-white px-4 py-2 rounded ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Paginación -->
                <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <Pagination :pagination="cursos" />
                </div>
            </div>

            <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeletecursos.update"
                title="¿Eliminar este registro?" />
        </div>
    </AppLayout>
</template>
