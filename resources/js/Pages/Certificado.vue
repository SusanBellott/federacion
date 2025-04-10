<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import { ref, computed } from "vue";
import { router, usePage, useForm } from "@inertiajs/vue3";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import 'vue3-carousel/carousel.css';
import { Carousel, Slide, Navigation } from 'vue3-carousel';

const props = defineProps({
    cursos: Object,
    uuid_curso: String,
    rutaanterior: String,
    imagenes: Object,
    imagenes2: Object,
});
const baseUrl = ref(window.location.origin);
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});
//console.log("todo curso ", props.cursos)
const images = computed(() => {
    // 1. Depuración: Verifica la estructura real de props.imagenes
    //console.log('Estructura de props.imagenes:', props.imagenes);

    // 2. Obtener las imágenes directamente (ajusta según lo que muestre el console.log)
    const imagenesData = props.imagenes?.data || props.imagenes;

    // 3. Verificar si tenemos datos válidos
    if (!imagenesData || !Array.isArray(imagenesData)) {
        console.error('Datos de imágenes no válidos:', imagenesData);
        return [{
            id: 0,
            url: 'https://via.placeholder.com/300', // Imagen placeholder
            alt: 'No hay imágenes disponibles',
            isPlaceholder: true
        }];
    }

    // 4. Procesar las imágenes
    return imagenesData.map((imagen) => {
        // Verificar estructura de cada imagen
        if (!imagen) {
            console.warn('Imagen inválida encontrada:', imagen);
            return null;
        }

        // Obtener ruta de la imagen (ajusta según tu estructura real)
        const rutaImagen = imagen.imagenescer || imagen.ruta || imagen.url || '';

        // Construir URL correcta
        let imageUrl;
        if (rutaImagen.startsWith('http')) {
            imageUrl = rutaImagen;
        } else if (rutaImagen.startsWith('storage/')) {
            imageUrl = `/${rutaImagen}`;
        } else if (rutaImagen) {
            imageUrl = `/storage/${rutaImagen}`;
        } else {
            imageUrl = 'https://via.placeholder.com/300';
        }

        // Texto alternativo
        const altText = imagen.descripcion
            ? `Certificado: ${imagen.descripcion}`
            : `Certificado del curso`;

        return {
            id: imagen.uuid_imgcer || imagen.id || Date.now(),
            url: imageUrl,
            alt: altText,
            descripcion: imagen.descripcion || '',
            estado: imagen.estado || 'activo',
            originalData: imagen,
            isPlaceholder: !rutaImagen
        };
    }).filter(img => img !== null); // Filtrar imágenes inválidas
});

const showImageInfo = (imageId, index) => {
    //console.log('ID de la imagen:', imageId);
    //console.log('Índice:', index);
    //console.log('Datos completos:', images.value[index]);
    router.put(`/estuditarimagencertificado/${props.cursos.uuid_curso}`, {
        uuid_imgcer: imageId,
        img_cer: imageId
    });
};
const guardarCambios = () => {
    console.log("Comentario guardado:", props.cursos.titulocer);
    const $titulocertificado = props.cursos.titulocer;
    router.put(`/estuditarcursocerti/${props.cursos.uuid_curso}`, {
        titulocer: $titulocertificado
    });
};
const guardarCambioscontenido = () => {
    console.log("Comentario guardado:", props.cursos.contenidocer);
    const $textocertificado = props.cursos.contenidocer;
    router.put(`/estuditarcursocerti/${props.cursos.uuid_curso}`, {
        contenidocer: $textocertificado
    });
};
const eliminarimagenbaner = (id_iamgen) => {
    console.log("id de la imagen:", id_iamgen);
    router.put(`/imagencertificadodelete/${props.cursos.uuid_curso}`, {
        img_cer: '',
        uuid_imgcer: id_iamgen
    });

};
//----------------------------------------------sello 1
const gudarsello1 = (imageId, index) => {
    router.put(`/estuditarimagencertificado/${props.cursos.uuid_curso}`, {
        uuid_imgcer: imageId,
        img_sello: imageId
    });
};
const eliminarsello1 = (id_iamgen) => {
    router.put(`/imagencertificadodelete/${props.cursos.uuid_curso}`, {
        img_sello: '',
        uuid_imgcer: id_iamgen
    });

};
const gudarfirma = (firmaKey, imageId, index) => {

    const campox = 'img_firma' + firmaKey;
    //console.log(`Guardando :`, campox, imageId);
    router.put(`/estuditarimagencertificado/${props.cursos.uuid_curso}`, {
        uuid_imgcer: imageId,
        [campox]: imageId
    });
};

const eliminarfirma = (firmaKey, idImagen) => {
    const campox = 'img_firma' + firmaKey;
    //console.log(`Eliminando ${firmaKey}:`, idImagen, " ", campox);
    router.put(`/imagencertificadodelete/${props.cursos.uuid_curso}`, {
        [campox]: '',
        uuid_imgcer: idImagen
    });
};
const config2 = {
    height: 200,
    itemsToShow: 3,
    gap: 5,
    autoplay: 4000,
    wrapAround: true,
    pauseAutoplayOnHover: true,
}
const config = {
    height: 200,
    itemsToShow: 1,
    gap: 5,
    autoplay: 4000,
    wrapAround: true,
    pauseAutoplayOnHover: true,
}
const atraz = () => {

    if (props.rutaanterior) {
        router.visit(props.rutaanterior, {
            preserveState: true, // Mantiene el estado de la página
            replace: true, // No añade nueva entrada al historial
            preserveScroll: true // Mantiene la posición del scroll
        });
    }
    //showModal.value = true;
};

</script>

<template>
    <AppLayout title="Instituciones">
        <template #header>
            <div class="p-4 m-4 border-2 border-gray-300 rounded-lg shadow-lg">
                <h2 class="font-semibold text-xl text-sky-400 leading-tight text-center">
                    Diseño del Certificado
                </h2>
                <!-- Contenedor flex para Nombre y Descripción -->
                <div class="flex justify-between mt-4">
                    <!-- Nombre del curso -->
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-lg">
                            <span class="text-sky-500">Nombre del Curso:</span> {{ props.cursos.nombre }}
                        </p>
                    </div>

                    <!-- Descripción del curso -->
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-lg">
                            <span class="text-sky-500">Descripción del curso:</span> {{ props.cursos.descripcion }}
                        </p>
                    </div>
                </div>
            </div>

        </template>
        <div class="relative">
            <button
                class="absolute top-2 right-2 w-10 h-10 rounded-full bg-gradient-to-r from-red-600 to-pink-500 text-white font-bold shadow-lg hover:shadow-xl transition-all duration-300 hover:from-red-500 hover:to-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-300 focus:ring-opacity-20 flex items-center justify-center"
                @click="atraz" aria-label="Cerrar o retroceder">
                &#120143;
            </button>
        </div>
        <div class="p-4 m-4 border-2 border-gray-300 rounded-lg shadow-lg">
            <br>
            <div class="p-4 m-4 border-2 border-gray-300 rounded-lg shadow-lg">
                <div v-if="!props.cursos.img_cer" class="carousel-container max-w-full mx-auto">
                    <Carousel v-if="!props.cursos.img_cer" v-bind="config2" class="carousel">
                        <Slide v-for="(image, index) in images" :key="image.id">
                            <div class="slide-container">
                                <img :src="image.url" :alt="image.alt" class="carousel-image" />
                                <button @click="showImageInfo(image.id, index)" class="info-button"
                                    aria-label="Mostrar información de la imagen">
                                    ✅
                                </button>
                            </div>
                        </Slide>
                        <template #addons>
                            <Navigation />
                        </template>
                    </Carousel>
                </div>
                <div v-if="props.cursos.img_cer" class="slide-container">
                    <h1 class="text-sky-400 text-center">IMAGENE DEL BANER</h1>
                    <div v-for="ima in props.imagenes2" :key="imagenes2.uuid_imgcer">
                        <img v-if="ima.uuid_imgcer === props.cursos.img_cer" :src="`${baseUrl}/` + ima.imagenescer"
                            class="w-full h-32 object-cover" />
                        <button v-if="ima.uuid_imgcer === props.cursos.img_cer"
                            @click="eliminarimagenbaner(ima.uuid_imgcer)" class="info-button"
                            aria-label="Mostrar información de la imagen">
                            ❌
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full max-w-lg mx-auto relative  rounded-lg shadow-lg">
                <textarea v-model="props.cursos.titulocer"
                    class="w-full px-4 py-2 border-2 border-sky-700 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 resize-y"
                    rows="2" placeholder="Escribe tu titulo del certificado..."></textarea>
                <!-- Botón para borrar -->
                <button @click="props.cursos.titulocer = ''"
                    class="absolute top-2 right-12 text-gray-500 hover:text-red-500" aria-label="Borrar comentario">
                    🗑️
                </button>
                <!-- Botón para guardar cambios -->
                <button @click="guardarCambios" class="absolute top-2 right-2 text-gray-500 hover:text-green-500"
                    aria-label="Guardar comentario">
                    ✅
                </button>
            </div>
            <div class="w-full max-w-2xl mx-auto relative  rounded-lg shadow-lg">
                <textarea v-model="props.cursos.contenidocer"
                    class="w-full px-4 py-2 border-2 border-sky-700 rounded-md focus:outline-none focus:ring-2 focus:ring-sky-500 resize-y"
                    rows="3" placeholder="Escribe tu contenido del certificado..."></textarea>
                <!-- Botón para borrar -->
                <button @click="props.cursos.contenidocer = ''"
                    class="absolute top-2 right-12 text-gray-500 hover:text-red-500" aria-label="Borrar comentario">
                    🗑️
                </button>

                <!-- Botón para guardar cambios -->
                <button @click="guardarCambioscontenido"
                    class="absolute top-2 right-2 text-gray-500 hover:text-green-500" aria-label="Guardar comentario">
                    ✅
                </button>
            </div>
            <div class="py-2 carousel-container max-w-xs mx-auto">
                <h1 class="text-sky-400 text-center">SELLO DE LA INSTITUCIÓN</h1>
                <div v-if="!props.cursos.img_sello" class="carousel-container max-w-full mx-auto">
                    <Carousel v-if="!props.cursos.img_sello" v-bind="config" class="carousel">
                        <Slide v-for="(image, index) in images" :key="image.id">
                            <div class="slide-container">
                                <img :src="image.url" :alt="image.alt"
                                    class="carousel-image w-full h-64 object-cover" />
                                <button @click="gudarsello1(image.id, index)" class="info-button"
                                    aria-label="Mostrar información de la imagen">
                                    ✅
                                </button>
                            </div>
                        </Slide>
                        <template #addons>
                            <Navigation />
                        </template>
                    </Carousel>
                </div>
                <div v-if="props.cursos.img_sello" class="slide-container">
                    <div v-for="ima in props.imagenes2" :key="imagenes2.uuid_imgcer">
                        <img v-if="ima.uuid_imgcer === props.cursos.img_sello" :src="`${baseUrl}/` + ima.imagenescer"
                            class="w-full h-64 object-cover" v-bind="config" />
                        <button v-if="ima.uuid_imgcer === props.cursos.img_sello"
                            @click="eliminarsello1(ima.uuid_imgcer)" class="info-button"
                            aria-label="Mostrar información de la imagen">
                            ❌
                        </button>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="carousel-container w-[300px] mx-auto" v-for="(firma, index) in 4" :key="index">
                    <h1 class="text-sky-400 text-center font-semibold mb-2">FIRMA {{ index + 1 }}</h1>

                    <!-- Mostrar imagen ya seleccionada -->
                    <div v-if="props.cursos[`img_firma${index + 1}`]" class="slide-container relative">
                        <div v-for="ima in props.imagenes2" :key="ima.uuid_imgcer">
                            <img v-if="ima.uuid_imgcer === props.cursos[`img_firma${index + 1}`]"
                                :src="`${baseUrl}/${ima.imagenescer}`"
                                class="w-full h-40 object-cover rounded shadow" />
                            <button v-if="ima.uuid_imgcer === props.cursos[`img_firma${index + 1}`]"
                                class="absolute top-1 right-1 bg-pink-800 text-white rounded-full px-2 py-1 text-sm shadow"
                                @click="eliminarfirma(index + 1, ima.uuid_imgcer)">
                                &#120143;
                            </button>
                        </div>
                    </div>
                    <!-- Carrusel si no hay imagen seleccionada -->
                    <div v-else>
                        <Carousel v-bind="config" class="carousel">
                            <Slide v-for="(image, idx) in images" :key="image.id">
                                <div class="slide-container relative">
                                    <img :src="image.url" :alt="image.alt"
                                        class="w-full h-40 object-cover rounded shadow" />
                                    <button
                                        class="absolute bottom-2 right-2 bg-cyan-900 text-white rounded px-2 py-1 text-sm shadow"
                                        @click="gudarfirma(index + 1, image.id, idx)">
                                        ✅
                                    </button>
                                </div>
                            </Slide>
                            <template #addons>
                                <Navigation />
                            </template>
                        </Carousel>
                    </div>
                </div>
            </div>
            <br>
            <hr>
            <br>
            <div class="row flex justify-center">
                <button
                    class="px-6 py-3 bg-gradient-to-r from-blue-800 to-sky-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:from-blue-600 hover:to-sky-400 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50"
                    @click="imprimircertificado">
                    <span class="flex items-center justify-center">
                        &#128424;
                    </span>
                </button>

                <div v-if="flash.success">
                    <useSweetAlert :data="flash.datos_array" />
                </div>
                <editaralerta title="¡Registro editado correctamente!" />
            </div>
        </div>

    </AppLayout>
</template>

<!--<style>
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

/* Nuevos estilos para el botón */
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

/* Efecto para el botón al hacer click */
.info-button:active {
    transform: scale(0.95);
}
</style>
-->
