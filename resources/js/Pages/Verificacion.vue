<template>
  <div class="min-h-screen flex items-center justify-center bg-white px-4 py-8">
    <div class="w-full max-w-2xl border p-8 shadow-lg rounded-lg text-center">
      <!-- Logos -->
      <div class="flex justify-center items-center gap-10 mb-6">
        <img src="/assets/img/logo_instituto.png" alt="Logo" class="h-28" />
      </div>
      <!-- Título -->
      <h1 class="text-3xl font-bold text-white bg-red-600 inline-block px-6 py-2 rounded mb-8">
        CERTIFICACIÓN
      </h1>
      <!-- Información -->
      <div class="text-left text-gray-800 space-y-3">
        <p><span class="font-bold">CÉDULA DE IDENTIDAD:</span> {{ inscrito?.user?.ci || 'N/A' }}</p>
        <p><span class="font-bold">NOMBRE COMPLETO:</span> {{ nombreCompleto }}</p>
        
        <!-- Nombre de la actividad -->
        <p>
          <span class="font-bold">TÍTULO DE ACTIVIDAD:</span>
          {{ inscrito?.curso?.nombre?.toUpperCase() || 'N/A' }}
        </p>
        <p><span class="font-bold">CARGA HORARIA:</span> {{ inscrito?.curso?.carga_horaria || 'N/A' }}</p>

        <p>
  <strong>ACTIVIDAD REALIZADA:</strong>
  {{ rangoFechas }}
</p>


      </div>
      <!-- Información adicional -->
      <div class="mt-10 text-sm text-gray-600 space-y-0">
        <p class="font-bold">Oficina Central:</p>
        <p>La Casa Social del Maestro</p>
        <p>Calle Genaro Sanjinés Nro. 607 </p>
        <p>La Casona del Magisterio Urbano paceño</p>
        <p>Calle Indaburu esquina Genaro Sanjinés Nro. 923</p>
        <p>La Paz - Bolivia</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  inscrito: Object,
});

// Computed properties for safer data handling
const nombreCompleto = computed(() => {
  const u = props.inscrito?.user
  if (!u) return 'N/A'
  return [u.name, u.primer_apellido, u.segundo_apellido]
    .filter(Boolean)
    .join(' ')
    .toUpperCase()
})

const rangoFechas = computed(() => {
  const curso = props.inscrito?.curso
  if (!curso?.fecha_inicio || !curso?.fecha_fin) {
    return 'N/A'
  }

  // Forzamos hora 00:00 para evitar desfases UTC
  const inicio = new Date(curso.fecha_inicio + 'T00:00:00')
  const fin    = new Date(curso.fecha_fin    + 'T00:00:00')

  const diaI  = inicio.getDate()
  const diaF  = fin.getDate()
  const mesI  = inicio.toLocaleDateString('es-ES', { month: 'long' })
  const mesF  = fin   .toLocaleDateString('es-ES', { month: 'long' })
  const anyoI = inicio.getFullYear()
  const anyoF = fin   .getFullYear()

  // Mismo mes y año → "del 4 al 6 de mayo de 2025"
  if (mesI === mesF && anyoI === anyoF) {
    return `en los dias ${diaI} a ${diaF} de ${mesI} de ${anyoI}`
  }

  // Mismo año, meses distintos → "del 30 de mayo al 2 de junio de 2025"
  if (anyoI === anyoF) {
    return `en los dias ${diaI} de ${mesI} a ${diaF} de ${mesF} de ${anyoI}`
  }

  // Mes y año distintos → "del 30 de diciembre de 2024 al 2 de enero de 2025"
  const strI = inicio.toLocaleDateString('es-ES', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
  const strF = fin   .toLocaleDateString('es-ES', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
  return `en los dias ${strI} a ${strF}`
})
// Correct property name for debugging
console.log('Inscrito data:', props.inscrito);
</script>