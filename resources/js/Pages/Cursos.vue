<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorCursos from "@/Components/Buscador.vue";
import ModalMolqui from '@/Components/modalmolqui.vue';

import 'vue3-carousel/carousel.css';
import { Carousel, Slide, Navigation } from 'vue3-carousel';

const props = defineProps({
    cursos: Object,
    filters: Object,
});

const images = computed(() => {
    const cursoSeleccionado = props.cursos.data?.find(curso => curso.uuid_curso === form2.value.uuid_curso)
        || props.cursos;

    if (!cursoSeleccionado?.imagencertificados?.length) {
        return [{
            id: 0,
            url: 'https://elements-resized.envatousercontent.com/elements-cover-images/3f0e526c-d7ee-45bd-b91d-1e189a758b43?w=433&cf_fit=scale-down&q=85&format=auto&s=4e110c03679629b3d3c67a48d969e0f08224e522cdefed56f8a22d8fccd3bfd9',
            alt: 'No hay imágenes disponibles para este curso',
            isPlaceholder: true
        }];
    }

    return cursoSeleccionado.imagencertificados.map((imagen) => {
        const rutaImagen = imagen.imagenescer || '';
        const imageUrl = rutaImagen.startsWith('storage/')
            ? `/storage/${rutaImagen.split('storage/')[1]}`
            : rutaImagen;

        const altText = imagen.descripcion
            ? `Certificado: ${imagen.descripcion}`
            : `Certificado del curso ${cursoSeleccionado.nombre || ''}`;

        return {
            id: imagen.uuid_imgcer || imagen.id_imgcer,
            url: imageUrl,
            alt: altText,
            descripcion: imagen.descripcion,
            estado: imagen.estado,
            isPlaceholder: false
        };
    });
});

const config = {
    height: 400,
    itemsToShow: 2,
    gap: 5,
    autoplay: 4000,
    wrapAround: true,
    pauseAutoplayOnHover: true,
}

const showImageInfo = (imageId, index) => {
    console.log('ID de la imagen:', imageId);
    console.log('Índice:', index);
    console.log('Datos completos:', images.value[index]);
};

const page = usePage();
const flash = computed(() => page.props.flash || {});
const previousRoute = ref(null);
const deleteDialog = ref(null);
const id_curso = ref(null);
const imageError = ref(false)
const inputFile = ref(null);

const form2 = ref({
    uuid_curso: "",
    imagenes: null,
});

const imagenesPreviews = ref([]);

const resetForm2 = () => {
    form2.value = {
        uuid_curso: "",
        imagenes: null,
    };
    imagenesPreviews.value = [];
};

const handleImagenesChange = (event) => {
    const files = event.target.files;
    form2.value.imagenes = [];
    if (files && files.length > 0) {
        imagenesPreviews.value = [];
        Array.from(files).forEach(file => {
            if (file.type.match('image.*')) {
                form2.value.imagenes.push(file);
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
    form2.value.imagenes.splice(index, 1);
    if (form2.value.imagenes.length === 0) {
        form2.value.imagenes = null;
    }
};

const handleClickEditarCertificado = (uuid_curso) => {
    const currentUrl = window.location.href;
    const encoded = btoa(currentUrl);
    router.visit(`/certificados/${uuid_curso}?from=${encoded}`);
};

const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};

const showModal = ref(false);
const showModalimagen = ref(false);
const showModalcertificado = ref(false);

const form = ref({
    nombre: "",
    descripcion: "",
    fecha_inicio: "",
    fecha_fin: "",
    carga_horaria: "",
    img_curso: "",
    imagen: null,
    encargado: "",
    grado_academico: "",
    estado_curso: "",
});

const imagenPreview = ref(null);

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.imagen = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagenPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleClick = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
    imagenPreview.value = null;
};

const Abrirmmodaliamgenes = (uuid) => {
    showModalimagen.value = true;
    form2.value.uuid_curso = uuid;
    previousRoute.value = window.location.href;
};

const closemodaliamgenes = () => {
    showModalimagen.value = false;
    resetForm2();
};

const Abrirmmodalcertificados = () => {
    showModalcertificado.value = true;
};

const closemodalcertificados = () => {
    showModalcertificado.value = false;
};

const handleClickEditar = (
    uuid,
    nombre,
    descripcion,
    fecha_inicio,
    fecha_fin,
    img_curso,
    carga_horaria,
    encargado,
    grado_academico,
    estado_curso
) => {
    showModal.value = true;
    id_curso.value = uuid;
    form.value.nombre = nombre;
    form.value.descripcion = descripcion;
    form.value.fecha_inicio = fecha_inicio;
    form.value.fecha_fin = fecha_fin;
    form.value.img_curso = img_curso;
    form.value.carga_horaria = carga_horaria;
    form.value.encargado = encargado;
    form.value.grado_academico = grado_academico;
    form.value.estado_curso = estado_curso;
};

const resetForm = () => {
    form.value = {
        nombre: "",
        descripcion: "",
        fecha_inicio: "",
        fecha_fin: "",
        carga_horaria: "",
        img_curso: "",
        encargado: "",
        grado_academico: "",
        estado_curso: "",
    };
    id_curso.value = null;
};

const submitForm = () => {
    const formData = new FormData();
    for (const [key, value] of Object.entries(form.value)) {
        formData.append(key, value);
    }
    if (form.value.imagen) {
        formData.append('imagen', form.value.imagen);
    }
    const config = {
        headers: {
            'Content-Type': 'multipart/form-data'
        },
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            console.log("Respuesta recibida - Mensajes flash:", {
                success: flash.value.success,
                datos_array: flash.value.datos_array,
            });
        },
        onError: (errors) => {
            console.error("Errores de validación:", errors);
        }
    };

    if (!id_curso.value) {
        router.post("/cursos", formData, config);
    } else {
        router.post(`/cursoseditar/${id_curso.value}`, formData, {
            ...config,
            data: formData,
            method: 'post',
            headers: {
                ...config.headers,
                'X-HTTP-Method-Override': 'PUT'
            }
        });
    }
};

const enviarfotos = () => {
    if (inputFile.value.value && form2.value.imagenes) {
        const formData = new FormData();

        for (const [key, value] of Object.entries(form2.value)) {
            if (key !== 'imagenes') {
                formData.append(key, value);
            }
        }

        if (form2.value.imagenes) {
            if (Array.isArray(form2.value.imagenes)) {
                form2.value.imagenes.forEach((imagen, index) => {
                    formData.append(`imagenes[${index}]`, imagen);
                });
            } else {
                formData.append('imagenes[0]', form2.value.imagenes);
            }
        }

        router.post("/cursosimagenes", formData, {
            onSuccess: () => {
                closemodaliamgenes();
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
        console.log("Se están enviando los archivos...");
    } else {
        console.log("No existen archivos para enviar.");
        resetForm2();
        inputFile.value.value = null;
    }
};

</script>

<template>
    <AppLayout title="Crusos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Cursos Molquino
            </h2>
        </template>

        <!-- Modal con Formulario -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <form @submit.prevent="submitForm">
                <div class="p-6">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                        {{ id_curso ? 'Editar Curso' : 'Agregar Nuevo Curso' }}
                    </h2>

                    <div class="space-y-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Campo Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nombre
                            </label>
                            <input v-model="form.nombre" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción
                            </label>
                            <input v-model="form.descripcion" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Fecha de Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fecha de Inicio
                            </label>
                            <input v-model="form.fecha_inicio" type="date" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Fecha de Culminación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Fecha de Culminación
                            </label>
                            <input v-model="form.fecha_fin" type="date" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Carga Horaria -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Carga Horaria
                            </label>
                            <input v-model="form.carga_horaria" type="number" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Imagen del Curso -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen del curso</label>
                            <input type="file" @change="handleImageChange" accept="image/*"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white text-gray-800">

                            <!-- Vista previa -->
                            <div v-if="imagenPreview" class="mt-2">
                                <img :src="imagenPreview" class="h-40 w-auto object-cover rounded">
                            </div>

                            <!-- Mostrar imagen existente al editar -->
                            <div v-else-if="form.img_curso" class="mt-2">
                                <img :src="form.img_curso" class="h-40 w-auto object-cover rounded">
                            </div>
                        </div>

                        <!-- Campo Encargado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Encargado
                            </label>
                            <input v-model="form.encargado" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Grado Académico -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Grado Académico
                            </label>
                            <input v-model="form.grado_academico" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>

                        <!-- Campo Estado del Curso -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Estado del Curso
                            </label>
                            <input v-model="form.estado_curso" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-gray-800" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                            {{ id_curso ? 'Actualizar' : 'Guardar' }} curso
                        </button>
                    </div>
                </div>
            </form>
        </Modal>

        <!-- Modal para imágenes -->
        <Modal :show="showModalimagen" @close="closemodaliamgenes" max-width="2xl">
            <div class="p-6">
                <div class="mt-2 flex items-start gap-0 group">
                    <div class="flex-grow">
                        <label class="block text-sm font-medium text-pink-700">Añadir imagenes</label>
                        <div class="flex items-center space-x-4">
                            <input ref="inputFile" type="file" @change="handleImagenesChange" accept="image/*" multiple
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                            <button v-if="imagenesPreviews && imagenesPreviews.length > 0" type="button"
                                @click="enviarfotos()"
                                class="px-4 py-2 bg-gray-950 hover:bg-gray-600 text-white rounded-md transition duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                </svg>
                            </button>
                        </div>

                        <!-- Contenedor de miniaturas -->
                        <div class="mt-2 flex flex-wrap gap-2">
                            <div v-for="(preview, index) in imagenesPreviews" :key="'new-' + index" class="relative">
                                <img :src="preview" class="h-20 w-20 object-cover rounded border border-gray-200">
                                <button type="button" @click="removePreview(index)"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0 pl-4">
                        <button type="button" @click="closemodaliamgenes"
                            class="px-4 py-2 bg-red-700 hover:bg-pink-600 text-white rounded-md transition duration-200">
                            x
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-200">
                    Todas las imagenes
                </h2>
                <Carousel v-bind="config" class="carousel">
                    <Slide v-for="(image, index) in images" :key="image.id">
                        <div class="slide-container">
                            <img :src="image.url" :alt="image.alt" />
                            <button @click="showImageInfo(image.id, index)" class="info-button"
                                aria-label="Mostrar información de la imagen">
                                ℹ️
                            </button>
                        </div>
                    </Slide>

                    <template #addons>
                        <Navigation />
                    </template>
                </Carousel>
            </div>
        </Modal>

        <div class="flex-none w-full max-w-full px-3">
            <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                    <h6 class="text-gray-700 dark:text-white">Cursos</h6>

                    <div class="flex items-center space-x-4">
                        <BuscadorCursos :filters="filters" ruta="cursos.index" />

                        <button v-if="$page.props.permissions.includes('cursos.create')"
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
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Nombre</th>
                                    <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Descripción</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Fechas</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Carga Horaria</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Imagen</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Certificados</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Encargado</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodeletecursos.update')" class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-700 opacity-70">Estado</th>
                                    <th v-if="$page.props.permissions.includes('editarestadodeletecursos.update') && $page.props.permissions.includes('cursoseditar.update')" class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-collapse border-solid shadow-none dark:border-white/40 dark:text-white tracking-none whitespace-nowrap text-slate-700 opacity-70">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="curso in cursos.data" :key="curso.id" class="border-b dark:border-white/40">
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">{{ curso.nombre }}</p>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <p class="mb-0 text-xs leading-tight text-gray-600 dark:text-white dark:opacity-80">{{ curso.descripcion }}</p>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold leading-tight text-gray-600 dark:text-white dark:opacity-80">Inicio: {{ curso.fecha_inicio }}</span>
                                            <span class="text-xs font-semibold leading-tight text-gray-600 dark:text-white dark:opacity-80">Fin: {{ curso.fecha_fin }}</span>
                                        </div>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">{{ curso.carga_horaria }} hrs</span>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <img v-if="curso.img_curso" :src="curso.img_curso" class="h-16 w-auto object-cover rounded mx-auto">
                                        <span v-else class="text-xs italic text-gray-500 dark:text-gray-400">Sin imagen</span>
                                    </td>
                                    <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <button v-if="curso.estado != 'eliminado' && curso.estado != 'inactivo'"
                                            @click="Abrirmmodaliamgenes(curso.uuid_curso)"
                                            class="p-2 text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex flex-col">
                                            <h6 class="mb-0 text-sm leading-normal text-gray-700 dark:text-white">{{ curso.encargado }}</h6>
                                            <p class="mb-0 text-xs leading-tight text-gray-600 dark:text-white dark:opacity-80">{{ curso.grado_academico }}</p>
                                        </div>
                                    </td>
                                    <td v-if="$page.props.permissions.includes('editarestadodeletecursos.update')" class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <span v-if="curso.estado == 'activo'"
                                            class="bg-gradient-to-tl from-emerald-500 to-teal-400 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(curso.uuid_curso, 2, '¿Estás seguro de que deseas deshabilitar este registro?')">
                                            Activo
                                        </span>
                                        <span v-else-if="curso.estado == 'inactivo'"
                                            class="bg-gradient-to-tl from-slate-600 to-slate-300 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(curso.uuid_curso, 1, '¿Estás seguro de que deseas activar este registro?')">
                                            Inactivo
                                        </span>
                                        <span v-else
                                            class="bg-gradient-to-tl from-gray-400 to-gray-600 px-2.5 text-xs rounded-1.8 py-1.4 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white"
                                            @click="handleDelete(curso.uuid_curso, 1, '¿Estás seguro de que deseas registrar nuevamente?')">
                                            Registrar
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                        <div class="flex justify-center space-x-2">
                                            <button v-if="curso.estado != 'eliminado' && curso.estado != 'inactivo' && $page.props.permissions.includes('cursoseditar.update')"
                                                @click="handleClickEditar(
                                                    curso.uuid_curso,
                                                    curso.nombre,
                                                    curso.descripcion,
                                                    curso.fecha_inicio,
                                                    curso.fecha_fin,
                                                    curso.img_curso,
                                                    curso.carga_horaria,
                                                    curso.encargado,
                                                    curso.grado_academico,
                                                    curso.estado_curso
                                                )"
                                                class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </button>
                                            <button v-if="curso.estado != 'eliminado' && curso.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeletecursos.update')"
                                                @click="handleDelete(curso.uuid_curso, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
                                                class="p-2 text-red-500 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                            <button v-if="curso.estado != 'eliminado' && curso.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeletecursos.update')"
                                                @click="handleClickEditarCertificado(curso.uuid_curso)"
                                                class="p-2 text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
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
                        <Pagination :pagination="cursos" />
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeletecursos.update"
            title="¿Eliminar este registro?" />
    </AppLayout>
</template>

<!-- <style>
:root {
    background-color: #242424;
}

.carousel {
    --vc-nav-background: rgba(255, 255, 255, 0.7);
    --vc-nav-border-radius: 100%;
}

img {
    border-radius: 8px;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slide-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.info-button {
    position: absolute;
    bottom: 16px;
    right: 16px;
    width: 28px;
    height: 28px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.3s ease;
    backdrop-filter: blur(2px);
}

.info-button:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.1);
}

.info-button:active {
    transform: scale(0.95);
}
</style> -->
