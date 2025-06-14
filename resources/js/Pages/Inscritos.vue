<template>
    <AppLayout title="Inscritos">

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Inscritos
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
                <h6 class="text-gray-800 dark:text-white">Inscritos</h6>
                <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
                    <div class="p-6 pb-0 mb-0 border-b-0 border-b-solid rounded-t-2xl border-b-transparent flex justify-between items-center">
                        <!-- Search & Add Btton Row -->
                        <div class="flex items-center space-x-4">
                            <!-- Buscador (Search) -->
                            <div class="relative">
                                <BuscadorDistritos :filters="filters" ruta="inscritos.index" />
                            </div>

                        </div>
                                                    <!-- Add Button -->
                            <button v-if="$page.props.permissions.includes('inscritos.create')"
                                class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all bg-blue-500 rounded-lg cursor-pointer leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md"
                                @click="handleClick">
                                <span class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Agregar Nuevo Inscrito
                                </span>
                            </button>
                    </div>

                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-0 overflow-x-auto">
                            <table
                                class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                                <thead class="align-bottom">
                                    <tr>
                                        <th class="w-[50px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words" >Nro</th> 
                                        <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Usuario</th>
                                        <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Título de actividad</th>
                                            <th class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
    Costo
</th>

                                        <th
                                        class="w-[100px] px-3 py-3 text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            Fecha de Inscripción</th>
                                            
                                            <th v-if="$page.props.permissions.includes('editarestadodeleteinscritos.update') || $page.props.permissions.includes('inscritoeditar.update') || $page.props.permissions.includes('curso.inscrito.reporte')"
                                            class="px-3 py-3 w-[150px] max-w-[200px] text-[11px] font-bold text-center uppercase align-middle bg-transparent border-b border-gray-300 text-gray-700 dark:border-white/40 dark:text-white dark:opacity-80 whitespace-nowrap">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(inscrito, index) in inscritos.data" :key="inscrito.id_inscripcion" class="border-b dark:border-white/40">
                                        <td class="w-[100px] p-2 text-center align-middle bg-transparent border-b dark:border-white/40 text-[11px] font-semibold text-gray-700 dark:text-white dark:opacity-80 whitespace-normal break-words">
                                            {{ (currentPage - 1) * perPage + index + 1 }}</td>
                                        <td
                                            class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <div class="flex px-2 py-1">
                                                <div>
                                                    <!-- User Initial Circle -->
                                                    <div
                                                        class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 ease-in-out text-sm h-9 w-9 rounded-xl bg-slate-700 dark:bg-slate-600">
                                                        {{ inscrito.user.name.charAt(0) }}
                                                    </div>
                                                </div>
                                                <div class="flex flex-col justify-center">
                                                    <h6
                                                        class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        {{ inscrito.user.name }}</h6>
                                                    <p
                                                        class="mb-0 text-xs leading-tight dark:text-white dark:opacity-80 text-slate-400">
                                                        {{ inscrito.user.primer_apellido }} {{
                                                        inscrito.user.segundo_apellido }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <p
                                                class="mb-0 text-xs font-semibold leading-tight dark:text-white dark:opacity-80">
                                                <span
                                                    class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80"
                                                    v-if="inscrito.id_curso === null">No asignado</span>
                                                <span
                                                    class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80"
                                                    v-else>{{ inscrito.curso.nombre }}</span>
                                            </p>
                                        </td>
                                        
                                     <td class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap text-xs font-semibold text-gray-700 dark:text-white dark:opacity-80">
  <span
  :class="{
    'text-green-500 font-semibold': inscrito.curso?.tipo_pago === 'gratuito',
    'text-yellow-400 font-semibold': inscrito.curso?.tipo_pago === 'pago'
  }"
>
 {{ inscrito.curso?.precio ? 'Bs. ' + inscrito.curso.precio : 'Gratuito' }}
</span>
                                        
</td>

                                        <td
                                            class="p-2 text-center align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                            <span
                                                class="text-xs font-semibold leading-tight dark:text-white dark:opacity-80 text-slate-400">{{
                                                inscrito.fecha_inscripcion }}</span>
                                        </td>
                                        <td
                                             class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent w-[200px] max-w-[200px]">
                                            <div class="flex space-x-3">
                                                <!-- Certificado (PDF) -->
                                                <a
  v-if="mostrarCertificado(inscrito.curso.fecha_fin) && $page.props.permissions.includes('curso.inscrito.reporte')"
  :href="route('curso.inscrito.reporte', inscrito.uuid_inscripcion)"
  target="_blank"
  class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700 transition"
>
  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
  </svg>
  Descargar certificado
</a>



                                                <!-- Editar -->
                                                 
                                                <button
                                                    v-if="inscrito.estado != 'eliminado' && inscrito.estado != 'inactivo' && $page.props.permissions.includes('inscritoeditar.update')"
                                                    @click="handleClickEditar(inscrito.uuid_inscripcion, inscrito.id_user, inscrito.user.name, inscrito.user.primer_apellido, inscrito.user.segundo_apellido, inscrito.id_curso, inscrito.curso.nombre)"
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
                                                    v-if="inscrito.estado != 'eliminado' && inscrito.estado != 'inactivo' && $page.props.permissions.includes('editarestadodeleteinscritos.update')"
                                                    @click="handleDelete(inscrito.uuid_inscripcion, 3, '¿Estás seguro de que deseas eliminar este registro de forma permanente?')"
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
                        
                            <Pagination :pagination="inscritos" :filters="filters" />

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal with Form -->
            <Modal :show="showModal" @close="closeModal" max-width="2xl">
                <form @submit.prevent="submitForm">
                    <div class="p-6 ">
                        <h2 class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-collapse shadow-none dark:border-white/40 dark:text-white text-base border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">
    Agregar Nuevo Inscrito
</h2>

                        <div class="space-y-4">
                            <!-- Campo Nombre -->
                            <div>
                                <label class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                    Usuario a Inscribir
                                </label>
                                <Buscadoruser v-if="valorbusqueda" ref="inscritosComponent" :filters2="filters2"
                                    ruta="inscritos.index" />
                                <div v-if="user_nombre != '' && valorbusqueda">
                                    <!-- Contenedor con scroll cuando hay más de 5 registros -->
                                    <div class="p-0 overflow-x-auto">
                                        <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
                                            <tbody>
                                                <tr v-for="usuario in usuarios.data" :key="usuario.id"
                                                class="border-b dark:border-white/40">
                                                    <td
                                                    class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                                        {{ usuario.name }} {{ usuario.primer_apellido }} {{
                                                            usuario.segundo_apellido }}
                                                    </td>
                                                    <td
                                                    class="p-2 align-middle bg-transparent border-b dark:border-white/40 whitespace-nowrap shadow-transparent">
                                                        <button type="button"
                                                            @click="getSearchTerm(usuario.name, usuario.primer_apellido, usuario.segundo_apellido, usuario.id)"
                                                            class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white rounded-md transition duration-200">
                                                            +
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-if="!valorbusqueda" class="flex items-center space-x-4">
                                    <!-- Botón rojo para borrar (degradado rojo) -->
                                    <button type="button" @click="limipiarnombre"
                                        class="px-4 py-2 bg-gradient-to-r from-red-400 to-red-600 hover:from-red-500 hover:to-red-700 text-white rounded-md transition duration-200">
                                        -
                                    </button>

                                    <!-- Párrafo con color sky hermoso -->
                                    <p class="text-xl text-sky-500 dark:text-sky-400">
                                        {{ user_nombre_completo }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <!-- Campo Nombre -->
                            <div>
                                <label class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                                    Seleccionar Curso
                                </label>
  <Buscadorcurso
    v-if="valorbusquedacurso"
    ref="buscadorCursoRef"
    :filters="filters3"
    :data="props.cursos"
    ruta="inscritos.index"
  />

<!-- Resultado seleccionado - SOLO UNA VEZ -->
<div v-if="!valorbusquedacurso" class="flex items-center space-x-4 mt-2">
  <button
    type="button"
    @click="limipiarcurso"
    class="px-4 py-2 bg-gradient-to-r from-red-400 to-red-600 hover:from-red-500 hover:to-red-700 text-white rounded-md transition duration-200"
  >
    -
  </button>
  <p class="text-xl text-sky-500 dark:text-sky-400">
    {{ curso_nombre }}
  </p>
  <span
    v-if="curso_nombre"
    :class="{
      'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900': getTipoCurso() === 'gratuito',
      'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900': getTipoCurso() === 'pago'
    }"
    class="text-xs font-semibold px-2.5 py-0.5 rounded-full border"
  >
    {{ getTipoCurso() === 'gratuito' ? 'Gratuito' : `Con costo (Bs. ${getPrecioCurso()})` }}
  </span>
  <p v-if="errors.id_curso"
     class="mt-1 text-red-500 text-sm dark:text-red-400">
     {{ errors.id_curso }}
  </p>
</div>



                                    <div v-if="curso_nombre !== '' && valorbusquedacurso">
      <div class="max-h-60 overflow-y-auto">
        <table class="items-center w-full mb-0 align-top border-collapse dark:border-white/40 text-slate-500">
          <tbody>
            <template v-if="cursos?.data?.length">
          <tr v-for="curso in cursos.data" :key="curso.id_curso" class="border-b dark:border-white/40">
            <td class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
              {{ curso.nombre }} {{ curso.descripcion }}
            </td>
              <td class="text-xs font-semibold leading-tight text-gray-700 dark:text-white dark:opacity-80">
                <button
                type="button"
                @click="getSearchTerm2(curso.nombre, curso.descripcion, curso.id_curso)"
                class="px-4 py-2 bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white rounded-md transition duration-200"
              >
                +
              </button>
              </td>
            </tr>
        </template>
            <tr v-else>
          <td colspan="2" class="text-center text-sm text-gray-500 dark:text-white/70 py-3">
            No hay cursos disponibles actualmente para inscripción.
          </td>
        </tr>
          </tbody>
        </table>
      </div>
    </div>

                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" @click="closeModal"
                            class="inline-block px-6 py-3 mr-3 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer bg-slate-500 leading-normal text-xs ease-in tracking-tight-rem shadow-xs bg-150 bg-x-25 hover:-translate-y-px active:opacity-85 hover:shadow-md">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
                                Guardar Inscripcion
                            </button>
                        </div>
                    </div>
                </form>
            </Modal>

            <ConfirmDelete ref="deleteDialog" :method="'patch'" route-name="editarestadodeleteinscritos.update"
                title="¿Eliminar este registro?" @confirm="onConfirmDelete" />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/MainLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ref, computed, watchEffect } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import ConfirmDelete from "@/Components/ConfirmDelete.vue";
import Pagination from "@/Components/Pagination.vue";
import useSweetAlert from "@/Components/SweetAlert.vue";
import editaralerta from "@/Components/AlertaEditada.vue";
import BuscadorDistritos from "@/Components/Buscador.vue";
import Buscadoruser from "@/Components/Buscadoruser.vue";
import Buscadorcurso from "@/Components/Buscadorcurso.vue";

const props = defineProps({
    inscritos: Object,
    usuarios: Object,
    cursos: Object,
    filters: Object,
    filters2: Object,
    filters3: Object,
});

const currentPage = computed(() => props.inscritos.current_page)
const perPage     = computed(() => props.inscritos.per_page)
const errors = computed(() => page.props.errors || {});
// Acceso a flashes
const page = usePage();
const flash = computed(() => page.props.flash || {});
const id_inscripcion = ref(null);
//const inscritosList = ref(
  //props.inscritos.data.filter(i => i.estado === 'activo')
//)

//---------------------------busqueda de ususarios -----------------
const inscritosComponent = ref("");
const user_nombre = ref('');
const user_nombre_completo = ref('');
const id_usuariof = ref(null);
const valorbusqueda = ref(true);
//---------------------------busqueda de cursos -----------------
const buscadorCursoRef = ref("");

const curso_nombre = ref('');
const id_cursof = ref(null);
const valorbusquedacurso = ref(true);
const cursos = computed(() => {
  const lista = props.cursos?.data || [];
  return {
    data: lista.filter(c => c.estado_curso === 'abierto')
  };
});


// Función para acceder a searchTerm2 del componente hijo
const getSearchTerm = (nombre, mater, pater, id) => {
    if (inscritosComponent.value) {
        inscritosComponent.value.searchTerm2 = nombre;
        user_nombre_completo.value = [
  nombre,
  mater || '',
  pater || ''
].join(' ').replace(/\s+/g, ' ').trim();

        valorbusqueda.value = false;
        id_usuariof.value = id;
    }
}
const getSearchTerm2 = (nom, des, id) => {
    if (buscadorCursoRef.value) {
        buscadorCursoRef.value.searchTerm3 = nom;
        valorbusquedacurso.value = false;
        id_cursof.value = id;
    }
}

const limipiarnombre = () => {
    user_nombre_completo.value = "";
    valorbusqueda.value = true;
    id_usuariof.value = null;
}
const limipiarcurso = () => {
    valorbusquedacurso.value = true;
    id_cursof.value = null
}
watchEffect(() => {
    if (inscritosComponent.value) {
        // Inicializar searchTerm con el valor de searchTerm2 del hijo
        user_nombre.value = inscritosComponent.value.searchTerm2;
    }
    if (buscadorCursoRef.value) {
        // Inicializar searchTerm con el valor de searchTerm2 del hijo
        curso_nombre.value = buscadorCursoRef.value.searchTerm3;

    }
});
const deleteDialog = ref(null);
const handleDelete = (id, cod, texto) => {
    deleteDialog.value?.show(id, cod, texto);
};
// Estado del modal
const showModal = ref(false);

// Datos del formulario
const form = ref({
    id_user: "",
    id_curso: "",
});
const previousRoute = ref(null);  // Variable reactiva para guardar la ruta anterior

const handleClick = () => {
    showModal.value = true;
    previousRoute.value = window.location.href; // Guarda la URL actual
};
const handleClickEditar = (uuid, id_user, nombre, mat, pat, id_curso, nom) => {
    previousRoute.value = window.location.href; // Guarda la URL actual
    showModal.value = true;
    id_inscripcion.value = uuid;
    // Aquí modificamos el objeto dentro de ref()
    id_usuariof.value = id_user;
    id_cursof.value = id_curso;
    user_nombre_completo.value = nombre + " " + mat + " " + pat;
    valorbusqueda.value = false;
    curso_nombre.value = nom;
    valorbusquedacurso.value = false;
};
const closeModal = () => {
    showModal.value = false;
    resetForm();
    if (previousRoute.value) {
        router.visit(previousRoute.value, {
            preserveState: true, // Mantiene el estado de la página
            replace: true, // No añade nueva entrada al historial
            preserveScroll: true // Mantiene la posición del scroll
        });
    } else {
        console.warn('No hay ruta anterior definida.');
    }
};

const resetForm = () => {
    form.value = {
        id_user: "",
        id_curso: "",
    };
    id_inscripcion.value = null;
    limipiarnombre();
    limipiarcurso();
};

const submitForm = () => {
    form.value = {
        id_user: id_usuariof.value,
        id_curso: id_cursof.value,
    };
    if (!id_inscripcion.value) {
        router.post("/inscritos", form.value, {
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                console.error("Errores de validación:", errors);
            },
            preserveScroll: true,
        });
    } else {
        router.put(`/inscritoeditar/${id_inscripcion.value}`, form.value, {
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

function getTipoCurso() {
  const curso = props.cursos.data.find(c => c.id_curso === id_cursof.value);
  return curso?.tipo_pago || 'gratuito';
}
const getPrecioCurso = () => {
  const curso = props.cursos?.data?.find(c => c.id_curso === id_cursof.value);
  return curso?.precio ? Number(curso.precio).toFixed(2) : '0.00';
};

const mostrarCertificado = (fechaFin) => {
    if (!fechaFin) return false;

    const fechaActual = new Date();
    const fechaFinCurso = new Date(fechaFin);

    // Le sumamos 2 días
    fechaFinCurso.setDate(fechaFinCurso.getDate() + 0);

    return fechaActual >= fechaFinCurso;
};
// 2️⃣ Reemplaza aquí tu antiguo handleDelete si quieres, pero lo importante es esto:
const onConfirmDelete = async (uuid, cod) => {
  await router.patch(
    route('editarestadodeleteinscritos.update', uuid),
    {},
    {
      onSuccess: () => {
        // 3️⃣ Cuando cod sea 2 (desinscribir) o 3 (borrar), filtramos
        if (cod === 2 || cod === 3) {
          inscritosList.value = inscritosList.value.filter(
            i => i.uuid_inscripcion !== uuid
          )
        }
      }
    }
  )
}


</script>
