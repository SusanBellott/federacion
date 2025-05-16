<template>
    <div class="max-w-7xl mx-auto px-4 py-12">
    <div v-for="(config, index) in chartConfigs" :key="index" class="mb-12">
      <!-- Título y gráfico -->
      <div class="bg-white shadow-md rounded-lg p-6 mb-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">{{ config.title }}</h2>
        <div class="h-96" :ref="el => chartRefs[index] = el"></div>
      </div>

      <!-- Tabla de datos -->
      <div class="overflow-x-auto bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Datos detallados</h3>
        <table class="min-w-full text-sm text-left text-gray-600">
          <thead class="bg-gray-100 text-gray-700 uppercase">
            <tr>
              <th scope="col" class="px-4 py-2">Categoría</th>
              <th v-for="serie in config.series" :key="serie.name" class="px-4 py-2">{{ serie.name }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(label, rowIndex) in config.x" :key="label" class="border-b">
              <td class="px-4 py-2 font-medium text-gray-900">{{ label }}</td>
              <td v-for="serie in config.series" :key="serie.name" class="px-4 py-2">
                {{ serie.data[rowIndex] }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  
      <div class="mt-8 flex justify-end">
        
      <button @click="descargarPDF" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm">
        Descargar todo en PDF
      </button>
    </div>
      <div class="mt-12 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Resumen General</h3>
        <ul class="space-y-2 text-gray-600">
          <li>Usuarios: {{ stats.totalUsers }}</li>
          <li>Actividades: {{ stats.totalCourses }}</li>
          <li>Inscripciones: {{ stats.totalInscriptions }}</li>
          <li>Certificados: {{ stats.totalCertificates }}</li>
          <li>Tipos de Actividad: {{ stats.totalActivityTypes }}</li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import * as echarts from 'echarts';
  import jsPDF from 'jspdf';
  import html2canvas from 'html2canvas';
  import StatCard from '@/Components/StatCard.vue';
  import { usePage } from '@inertiajs/vue3';
  import autoTable from 'jspdf-autotable';

  const { stats } = usePage().props;
  const chartRefs = ref([]);
  const chartInstances = [];
  
  const chartConfigs = [
    {
      title: 'Gráfico general de actividades',
      x: Object.keys(stats.byActivityType),
      series: [{ name: 'Inscritos', data: Object.values(stats.byActivityType) }]
    },
    {
      title: 'Gráfico general de inscritos y certificados',
      x: Object.keys(stats.byActivityType),
      series: [
        {
          name: 'Inscritos',
          data: Object.keys(stats.byActivityType).map(tipo => stats.byActivityType[tipo])
        },
        {
          name: 'Certificados',
          data: Object.keys(stats.certificatesByTypeAndCourse).map(tipo => {
            const cursos = stats.certificatesByTypeAndCourse[tipo] || {};
            return Object.values(cursos).reduce((a, b) => a + b, 0);
          })
        }
      ]
    },
    {
      title: 'Gráfico de cantidad de actividades realizadas',
      x: Object.keys(stats.byCourseInType),
      series: [
        {
          name: 'Actividad',
          data: Object.keys(stats.byCourseInType).map(tipo => Object.keys(stats.byCourseInType[tipo]).length)
        }
      ]
    },
    {
      title: 'Gráfico detallado de inscritos y certificados generados',
      x: Object.keys(stats.byCourseInType).flatMap(tipo => Object.keys(stats.byCourseInType[tipo])),
      series: [
        {
          name: 'Inscritos',
          data: Object.keys(stats.byCourseInType).flatMap(tipo => Object.values(stats.byCourseInType[tipo]))
        },
        {
          name: 'Certificados',
          data: Object.keys(stats.certificatesByTypeAndCourse).flatMap(tipo => {
            const cursos = stats.certificatesByTypeAndCourse[tipo] || {};
            return Object.keys(stats.byCourseInType[tipo] || {}).map(curso => cursos[curso] || 0);
          })
        }
      ]
    }
  ];
  
  const renderCharts = () => {
    chartConfigs.forEach((config, index) => {
      const chart = echarts.init(chartRefs.value[index]);
      chart.setOption({
        tooltip: { trigger: 'axis' },
        legend: { data: config.series.map(s => s.name) },
        xAxis: { type: 'category', data: config.x },
        yAxis: { type: 'value' },
        series: config.series.map(s => ({
          name: s.name,
          type: 'bar',
          barGap: 0,
          emphasis: { focus: 'series' },
          data: s.data
        }))
      });
      chartInstances.push(chart);
    });
  };
  let escudoBase64 = null;

const cargarEscudo = async () => {
  const response = await fetch('/assets/img/logo_instituto.png');
  const blob = await response.blob();
  return new Promise(resolve => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result);
    reader.readAsDataURL(blob);
  });
};
const agregarEncabezado = (pdf) => {
  const pageWidth = pdf.internal.pageSize.getWidth();

  // Escudo
  if (escudoBase64) {
    pdf.addImage(escudoBase64, 'PNG', pageWidth / 2 - 12, 10, 24, 24); // centrado
  }

  // Texto centrado
  pdf.setFontSize(11);
  pdf.setFont('helvetica', 'bold');
  pdf.text('FEDERACIÓN DEPARTAMENTAL DE TRABAJADORES DE EDUCACIÓN URBANA DE LA PAZ ', pageWidth / 2, 40, { align: 'center' });
  pdf.setFontSize(10);
  pdf.text('F.D.T.E.U.L.P.', pageWidth / 2, 48, { align: 'center' });

  // Subtítulo
  pdf.setFontSize(12);
  pdf.text('Reporte de estadísticas generales', pageWidth / 2, 55, { align: 'center' });

  return 60; // devuelve la posición vertical para el siguiente contenido
};

const descargarPDF = async () => {
  escudoBase64 = await cargarEscudo(); // esperar a que se cargue
  const pdf = new jsPDF('p', 'mm', 'a4');
  let y = agregarEncabezado(pdf); // dibujar encabezado

  for (let i = 0; i < chartRefs.value.length; i++) {
  const chartRef = chartRefs.value[i];
  const config = chartConfigs[i];

  // Captura del gráfico
  const canvas = await html2canvas(chartRef);
  const imgData = canvas.toDataURL('image/png');
  const pageWidth = pdf.internal.pageSize.getWidth();
const imgWidth = 160; // puedes ajustar este valor si deseas un ancho específico
const xCentered = (pageWidth - imgWidth) / 2;

pdf.addImage(imgData, 'PNG', xCentered, y, imgWidth, 80);

  y += 90;

  // ✅ Insertar título de la tabla debajo del gráfico
  pdf.setFontSize(12);
  pdf.setFont('helvetica', 'bold');
  pdf.text(config.title, 105, y, { align: 'center' });
  y += 8;

  // Generar tabla
  autoTable(pdf, {
    startY: y,
    head: [[ 'Categoría', ...config.series.map(s => s.name) ]],
    body: config.x.map((label, row) => [
      label,
      ...config.series.map(s => s.data[row] ?? '—')
    ]),
    styles: {
      fontSize: 9,
      halign: 'left',
    },
    headStyles: {
      fillColor: [41, 128, 185],
      textColor: 255,
      fontStyle: 'bold'
    },
    theme: 'grid'
  });

  y = pdf.lastAutoTable.finalY + 10;

  if (i < chartRefs.value.length - 1) {
    pdf.addPage();
    y = agregarEncabezado(pdf); // reinicia encabezado
  }
}

  // Página final con resumen
  pdf.addPage();
  y = agregarEncabezado(pdf);
  autoTable(pdf, {
    startY: y + 10,
    head: [['Indicador', 'Cantidad']],
    body: [
      ['Usuarios', stats.totalUsers],
      ['Cursos', stats.totalCourses],
      ['Inscripciones', stats.totalInscriptions],
      ['Certificados', stats.totalCertificates],
      ['Tipos de Actividad', stats.totalActivityTypes]
    ],
    headStyles: { fillColor: [67, 160, 71], textColor: 255 },
    theme: 'grid'
  });

 // Agrega pie de página a todas las páginas
const totalPages = pdf.getNumberOfPages();
const fechaHora = new Date().toLocaleString('es-BO', {
  day: '2-digit', month: '2-digit', year: 'numeric',
  hour: '2-digit', minute: '2-digit', second: '2-digit'
});

for (let i = 1; i <= totalPages; i++) {
  pdf.setPage(i);
  pdf.setFontSize(8);
  pdf.setTextColor(100);
  pdf.text(`Emitido el: ${fechaHora}`, 105, 287, { align: 'center' }); // 287mm ~ 1cm desde el borde inferior
}




  pdf.save(`estadisticas_dashboard_${Date.now()}.pdf`);
};

  
  onMounted(renderCharts);
  
  </script>
  