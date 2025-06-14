<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
  cursos: {
    type: Array,
    required: true,
  },
  misCursosIds: {
    type: Array,
    default: () => [],
  },
});


const misCursos = ref([...props.misCursosIds.map(id => Number(id))]);
const loadingId = ref(null);

const hoy = new Date();

const cursosFiltrados = computed(() => {
  const cursosList = Array.isArray(props.cursos)
    ? props.cursos
    : props.cursos?.data || [];

  return cursosList.filter((curso) => {
    const inicio = new Date(curso.fecha_inicio_inscripcion);
    inicio.setHours(0, 0, 0, 0); // forzar 00:00:00

    const fin = new Date(curso.fecha_fin_inscripcion);
    fin.setHours(23, 59, 59, 999); // forzar 23:59:59.999

    return hoy >= inicio && hoy <= fin;
  });
});


const estaInscrito = (cursoId) => {
  return misCursos.value.includes(Number(cursoId));
};



const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  const date = new Date(dateString);
  return date.toLocaleDateString("es-ES");
};

const tomarmateria = (uuidCurso) => {
  if (!uuidCurso) return console.warn("UUID del curso no proporcionado.");
  loadingId.value = uuidCurso;

  router.put(`/estudianteseditar/${uuidCurso}`, {}, {
    onSuccess: async () => {
      const curso = props.cursos.find(c => c.uuid_curso === uuidCurso);
      if (curso && !misCursos.value.includes(curso.id_curso)) {
        misCursos.value.push(curso.id_curso); // fuerza reactividad
      }

      await nextTick(); // espera a que Vue actualice el DOM

      Swal.fire({
  iconHtml: `
    <div class="flex items-center justify-center">
      <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" fill="transparent"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4 -4" stroke="white" stroke-width="2"/>
      </svg>
    </div>
  `,
  title: '¡Ya estás inscrito!',
  text: 'Tu inscripción se ha registrado correctamente.',
  background: '#34d399', // verde esmeralda claro
  color: '#fff',
  customClass: {
    popup: 'rounded-2xl shadow-lg px-8 py-6',
    title: 'text-2xl font-bold',
    confirmButton: 'bg-white text-emerald-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100'
  },
  showConfirmButton: true,
  confirmButtonText: 'Aceptar',
});

    },
    onError: (errors) => {
      console.error("❌ Error al inscribirse:", errors);

      Swal.fire({
  iconHtml: `
    <div class="flex items-center justify-center">
      <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2" fill="transparent"/>
        <line x1="9" y1="9" x2="15" y2="15" stroke="white" stroke-width="2" stroke-linecap="round"/>
        <line x1="15" y1="9" x2="9" y2="15" stroke="white" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </div>
  `,
  title: '¡Error!',
  text: 'Hubo un problema al inscribirse. Intenta nuevamente.',
  background: '#ef4444', // rojo tailwind (red-500)
  color: '#fff',
  customClass: {
    popup: 'rounded-2xl shadow-lg px-8 py-6',
    title: 'text-2xl font-bold',
    confirmButton: 'bg-white text-red-600 font-semibold px-6 py-2 rounded-lg hover:bg-gray-100'
  },
  showConfirmButton: true,
  confirmButtonText: 'Cerrar',
});

    },
    onFinish: () => {
      loadingId.value = null;
    },
    preserveScroll: true,
    preserveState: false,
  });
};

onMounted(() => {
  console.log("misCursosIds:", props.misCursosIds);
  document.documentElement.classList.add("dark");
});

</script>

<template>
  <div class="mb-12">
    <div v-if="!cursos || cursos.length === 0"
      class="text-center py-12 bg-gray-50 dark:bg-red-800 text-black dark:text-white rounded-lg">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
      </svg>
      <h3 class="mt-4 text-lg font-medium">No hay actividades disponibles</h3>
    </div>

    <div v-else  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 auto-rows-fr">
      <div v-for="curso in cursosFiltrados" :key="curso.id_curso"
         class="flex flex-col rounded-2xl border-2 border-transparent transform hover:scale-[1.02] transition-transform duration-300 shadow-lg hover:shadow-xl bg-gradient-to-br from-sky-700 to-sky-500">

        <!-- Imagen -->
<!-- Imagen con bordes superiores redondeados correctos -->
<div class="w-full p-4 overflow-hidden rounded-t-2xl bg-white/20 flex items-center justify-center">
  <img
    v-if="curso.img_curso"
    :src="curso.img_curso"
    alt="Curso"
    class="max-w-full max-h-40 object-contain"
    style="aspect-ratio: 16/9;" />

  <div v-else class="w-full h-44 flex items-center justify-center bg-white/10 text-gray-300">
    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
  </div>
</div>


        <!-- Contenido -->
     <div class="bg-white/90 dark:bg-gray-800/90 rounded-b-2xl p-6 flex flex-col justify-between h-full">

          <div class="space-y-2 text-gray-600 dark:text-gray-400 text-sm">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
              {{ curso.nombre }}
            </h3>
           <p class="text-gray-600 dark:text-gray-300 mb-2 text-sm leading-snug">
  {{ curso.descripcion }}
</p>

            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="font-medium">Inscripción:</span>
                <span>{{ formatDate(curso.fecha_inicio_inscripcion) }} – {{ formatDate(curso.fecha_fin_inscripcion) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Desarrollo:</span>
                <span>{{ formatDate(curso.fecha_inicio) }} – {{ formatDate(curso.fecha_fin) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Carga Horaria:</span>
                <span>{{ curso.carga_horaria }} hrs</span>
              </div>
            </div>
          </div>

          <!-- Botón -->
          <div class="mt-6">
            <button v-if="estaInscrito(curso.id_curso)"
              class="w-full py-3 bg-gray-400 text-white font-semibold rounded-full shadow-inner cursor-not-allowed"
              disabled>
              Inscrito

            </button>

            <button v-else :disabled="loadingId === curso.uuid_curso"
              @click="tomarmateria(curso.uuid_curso)"
              class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
              text-white font-semibold rounded-full shadow-inner transition-colors duration-200 flex items-center justify-center gap-2 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="loadingId !== curso.uuid_curso">Inscribirme</span>
              <span v-else class="animate-pulse">Procesando...</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
