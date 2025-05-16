<template>
  <MainLayout>
    <Head title="Dashboard" />
    <section class="relative bg-transparent text-gray-800 dark:text-white py-24">
      <div class="max-w-4xl mx-auto px-6 space-y-6 text-center">
        <p class="text-4xl font-light">
          Bienvenido al sistema de emisión de certificados
        </p>
        <div class="w-24 h-1 bg-gray-800 dark:bg-white mx-auto"></div>
        <h1 class="text-8xl sm:text-9xl font-extrabold uppercase">
          FDTEULP
        </h1>
        <p class="text-xl sm:text-2xl font-medium">
          Federación de Trabajadores de Educación Urbana La Paz
        </p>
      </div>
    </section>

    <!-- Contenido para estudiantes: Mostrar cursos disponibles -->
    <section v-if="isStudent" class="bg-white dark:bg-slate-900 py-8">
      <div class="max-w-7xl mx-auto px-4">
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-gray-800 dark:text-white">Actividades Disponibles</h2>
          <p class="mt-2 text-gray-600 dark:text-gray-300">
            Explora y inscríbete en nuestras actividades de formación profesional
          </p>
        </div>
        

        <!-- Componente de cursos disponibles -->
        <CursosDisponibles :cursos="cursos || []" />
      </div>
    </section>

    <!-- Contenido condicional basado en permisos -->
    <div v-if="canSeeStats">
      <!-- Si tiene permiso mostramos las estadísticas -->
      <div class="max-w-7xl mx-auto px-4 py-12" :class="{ '-mt-12': !isStudent }">
        <!-- Cards de estadísticas generales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-12">
          <StatCard title="Usuarios" :value="stats.totalUsers" icon="users" class="bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200" />
          <StatCard title="Cursos" :value="stats.totalCourses" icon="book-open" class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-indigo-200" />
          <StatCard title="Inscripciones" :value="stats.totalInscriptions" icon="clipboard-list" class="bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200" />
          <StatCard title="Certificados" :value="stats.totalCertificates" icon="badge-check" class="bg-gradient-to-br from-green-50 to-green-100 border-green-200" />
          <StatCard title="Tipos de actividad" :value="stats.totalActivityTypes" icon="collection" class="bg-gradient-to-br from-teal-50 to-teal-100 border-teal-200" />
        </div>
      </div>
      <!-- Componente de gráficos -->
      <DashboardCharts :stats="stats" />
    </div>
    
<!-- Si NO tiene permiso y NO es estudiante, mostrar acceso limitado -->
<div v-if="!canSeeStats && !isStudent" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">

</div>

  </MainLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import StatCard from '@/Components/StatCard.vue'
import DashboardCharts from '@/Components/DashboardCharts.vue'
import { Head } from '@inertiajs/vue3';
import CursosDisponibles from "@/Components/CursosDisponibles.vue";
import { ref, onMounted } from 'vue';

// Invocamos usePage() para obtener todos los props
const page = usePage()

// Extraemos stats, isStudent y canSeeStats del objeto props
const { stats, isStudent, canSeeStats,cursos  } = page.props

// Variable para activar/desactivar modo depuración
const debug = ref(true); // Cambiar a false en producción

// Depuración - Para verificar que los datos lleguen correctamente
onMounted(() => {
  console.log("isStudent:", isStudent);
  console.log("Datos de cursos:", cursos);
  if (cursos && cursos.length > 0) {
    console.log("Primer curso:", cursos[0]);
  } else {
    console.log("No hay cursos disponibles");
  }
});
</script>

