<template>
    <AppLayout title="Estadísticas">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Estadísticas Generales
        </h2>
      </template>
  
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Tarjetas de métricas -->
        <MetricCard title="Usuarios" :value="totalUsuarios" icon="ni ni-single-02" />
        <MetricCard title="Distritos" :value="totalDistritos" icon="ni ni-world-2 text-red-500" />
        <MetricCard title="Cursos" :value="totalCursos" icon="ni ni-app text-cyan-500" />
        <MetricCard title="Inscripciones" :value="totalInscripciones" icon="ni ni-credit-card text-emerald-500" />
        <MetricCard title="Usuarios Inscritos" :value="totalUsuariosInscritos" icon="ni ni-hat-3 text-blue-500" />
        <MetricCard title="Certificados" :value="totalCertificados" icon="ni ni-single-copy-04 text-yellow-500" />
      </div>
  
      <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfica de cursos por tipo -->
        <div class="bg-white dark:bg-slate-850 shadow rounded-lg p-4">
          <h3 class="text-lg font-bold mb-4">Cursos por Tipo</h3>
          <canvas ref="cursosChart"></canvas>
        </div>
  
        <!-- Gráfica de inscripciones por tipo -->
        <div class="bg-white dark:bg-slate-850 shadow rounded-lg p-4">
          <h3 class="text-lg font-bold mb-4">Inscripciones por Tipo</h3>
          <canvas ref="inscripcionesChart"></canvas>
        </div>
      </div>
    </AppLayout>
  </template>
  
  <script setup>
  import { onMounted, ref } from 'vue';
  import { Chart, registerables } from 'chart.js';
  import AppLayout from '@/Layouts/MainLayout.vue';
  import MetricCard from '@/Components/MetricCard.vue';
  
  // Registrar componentes Chart.js
  Chart.register(...registerables);
  
  const props = defineProps({
    totalUsuarios: Number,
    totalDistritos: Number,
    totalCursos: Number,
    totalInscripciones: Number,
    totalUsuariosInscritos: Number,
    totalCertificados: Number,
    cursosPorTipo: Array,
    inscripcionesPorTipo: Array,
  });
  
  const cursosChart = ref(null);
  const inscripcionesChart = ref(null);
  
  onMounted(() => {
    // Datos para Cursos por tipo
    const cursosLabels = props.cursosPorTipo.map(item => item.tipo.toUpperCase());
    const cursosData = props.cursosPorTipo.map(item => item.total);
  
    new Chart(cursosChart.value.getContext('2d'), {
      type: 'pie',
      data: {
        labels: cursosLabels,
        datasets: [{
          data: cursosData,
          backgroundColor: [
            '#34D399', '#60A5FA', '#FBBF24', '#F87171', '#A78BFA'
          ],
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  
    // Datos para Inscripciones por tipo
    const inscLabels = props.inscripcionesPorTipo.map(item => item.tipo.toUpperCase());
    const inscData = props.inscripcionesPorTipo.map(item => item.total);
  
    new Chart(inscripcionesChart.value.getContext('2d'), {
      type: 'bar',
      data: {
        labels: inscLabels,
        datasets: [{
          label: 'Inscripciones',
          data: inscData,
        }]
      },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
      }
    });
  });
  </script>
  