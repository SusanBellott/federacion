<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Contenedor grid para gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div 
                v-for="(config, index) in chartConfigs" 
                :key="index" 
                class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
            >
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ config.title }}
                    </h2>
                </div>
                <div class="p-4">
                    <div
                        class="h-64 w-full"
                        :ref="(el) => (chartRefs[index] = el)"
                    ></div>
                    
                    <!-- Tabla de datos -->
                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            Datos detallados
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left">Categoría</th>
                                        <th
                                            v-for="serie in config.series"
                                            :key="serie.name"
                                            class="px-3 py-2 text-right"
                                        >
                                            {{ serie.name }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr
                                        v-for="(label, rowIndex) in config.x"
                                        :key="label"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">
                                            {{ label }}
                                        </td>
                                        <td
                                            v-for="serie in config.series"
                                            :key="serie.name"
                                            class="px-3 py-2 text-right"
                                        >
                                            {{ serie.data[rowIndex] }}
                                        </td>
                                    </tr>
                                    <!-- Fila de total -->
                                    <tr class="font-semibold text-gray-900 bg-gray-100">
                                        <td class="px-3 py-2">TOTAL</td>
                                        <td
                                            v-for="serie in config.series"
                                            :key="serie.name + '-total'"
                                            class="px-3 py-2 text-right"
                                        >
                                            {{ serie.data.reduce((a, b) => a + b, 0) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de resumen y botón de descarga -->
        <div class="flex flex-col md:flex-row gap-6 mt-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex-1">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Resumen General
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-600">Usuarios</span>
                            <span class="font-medium">{{ stats.totalUsers }}</span>
                        </li>
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-600">Tipos de Actividad</span>
                            <span class="font-medium">{{ stats.totalActivityTypes }}</span>
                        </li>
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-600">Actividades</span>
                            <span class="font-medium">{{ stats.totalCourses }}</span>
                        </li>
                        <li class="flex justify-between items-center border-b border-gray-100 pb-2">
                            <span class="text-gray-600">Inscripciones</span>
                            <span class="font-medium">{{ stats.totalInscriptions }}</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span class="text-gray-600">Certificados</span>
                            <span class="font-medium">{{ stats.totalCertificates }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="flex items-center justify-end md:justify-start">
                <button
                    @click="descargarPDF"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-md transition-all flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Descargar PDF
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";
import * as echarts from "echarts";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";
import { usePage } from "@inertiajs/vue3";
import autoTable from "jspdf-autotable";

const { stats } = usePage().props;
const chartRefs = ref([]);
const chartInstances = ref([]);
const resizeObserver = ref(null);

const chartConfigs = [
    /*-------------------------------- */
   /* {
        title: "Actividades por tipo",
        x: Object.keys(stats.byActivityType),
        series: [
            { name: "Inscritos", data: Object.values(stats.byActivityType) },
        ],
    },*/
    /*-------------------------------- */
    {
        title: "Inscritos vs Certificados",
        x: Object.keys(stats.byActivityType),
        series: [
            {
                name: "Inscritos",
                data: Object.values(stats.byActivityType),
            },
            {
                name: "Certificados",
                data: Object.keys(stats.byActivityType).map((tipo) => {
                    const certificados =
                        stats.certificatesByTypeAndCourse[tipo] || {};
                    return Object.values(certificados).reduce(
                        (a, b) => a + b,
                        0
                    );
                }),
            },
        ],
    },
    /*--------------------------------*/
   /* {
        title: "Actividades realizadas",
        x: Object.keys(stats.byCourseInType),
        series: [
            {
                name: "Actividades",
                data: Object.keys(stats.byCourseInType).map(
                    (tipo) => Object.keys(stats.byCourseInType[tipo]).length
                ),
            },
        ],
    },
    /*----------------------------*/ 
    {
        title: "Detalle inscritos/certificados",
        x: Object.keys(stats.byCourseInType).flatMap((tipo) =>
            Object.keys(stats.byCourseInType[tipo])
        ),
        series: [
            {
                name: "Inscritos",
                data: Object.keys(stats.byCourseInType).flatMap((tipo) => {
                    const cursos = stats.byCourseInType[tipo];
                    const activos = stats.activosByTypeAndCourse?.[tipo] || {};
                    return Object.keys(cursos).map(
                        (curso) => activos[curso] || 0
                    );
                }),
            },
            {
                name: "Certificados",
                data: Object.keys(stats.byCourseInType).flatMap((tipo) => {
                    const cursos = stats.byCourseInType[tipo];
                    const certificados =
                        stats.certificatesByTypeAndCourse?.[tipo] || {};
                    return Object.keys(cursos).map(
                        (curso) => certificados[curso] || 0
                    );
                }),
            },
        ],
    },
];

const renderCharts = () => {
    chartConfigs.forEach((config, index) => {
        if (chartInstances.value[index]) {
            chartInstances.value[index].dispose();
        }
        
        const chart = echarts.init(chartRefs.value[index]);
        chart.setOption({
            backgroundColor: 'transparent',
            tooltip: { 
                trigger: "axis",
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                borderColor: '#e2e8f0',
                borderWidth: 1,
                textStyle: {
                    color: '#1e293b'
                },
                axisPointer: {
                    type: 'shadow',
                    shadowStyle: {
                        color: 'rgba(99, 102, 241, 0.1)'
                    }
                }
            },
            legend: { 
                data: config.series.map((s) => s.name),
                bottom: 0,
                textStyle: {
                    color: '#64748b'
                }
            },
            grid: {
                top: 30,
                right: 20,
                bottom: 30,
                left: 40,
                containLabel: true
            },
            xAxis: { 
                type: "category", 
                data: config.x,
                axisLine: {
                    lineStyle: {
                        color: '#e2e8f0'
                    }
                },
                axisLabel: {
                    color: '#64748b',
                    rotate: config.x.some(label => label.length > 10) ? 30 : 0,
                    interval: 0
                },
                axisTick: {
                    alignWithLabel: true
                }
            },
            yAxis: { 
                type: "value",
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: '#e2e8f0'
                    }
                },
                axisLabel: {
                    color: '#64748b'
                },
                splitLine: {
                    lineStyle: {
                        color: '#f1f5f9',
                        type: 'dashed'
                    }
                }
            },
            series: config.series.map((s, i) => ({
                name: s.name,
                type: "bar",
                barGap: 0,
                barWidth: config.series.length > 1 ? '40%' : '60%',
                itemStyle: {
                    borderRadius: [4, 4, 0, 0],
                    color: i === 0 ? 
                        new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#6366f1' },
                            { offset: 1, color: '#8b5cf6' }
                        ]) : 
                        new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#10b981' },
                            { offset: 1, color: '#34d399' }
                        ])
                },
                emphasis: {
                    itemStyle: {
                        color: i === 0 ? '#4f46e5' : '#059669'
                    }
                },
                data: s.data,
                label: {
                    show: true,
                    position: 'top',
                    color: '#475569',
                    fontSize: 10
                }
            })),
        });
        chartInstances.value[index] = chart;
    });
};

const handleResize = debounce(() => {
    chartInstances.value.forEach(chart => {
        if (chart) {
            chart.resize();
        }
    });
}, 100);

function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

let escudoBase64 = null;

const cargarEscudo = async () => {
    const response = await fetch("/assets/img/logo_instituto.png");
    const blob = await response.blob();
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.readAsDataURL(blob);
    });
};

const agregarEncabezado = (pdf) => {
    const pageWidth = pdf.internal.pageSize.getWidth();

    if (escudoBase64) {
        pdf.addImage(escudoBase64, "PNG", pageWidth / 2 - 12, 10, 24, 24);
    }

    pdf.setFontSize(11);
    pdf.setFont("helvetica", "bold");
    pdf.text(
        "FEDERACIÓN DEPARTAMENTAL DE TRABAJADORES DE EDUCACIÓN URBANA DE LA PAZ ",
        pageWidth / 2,
        40,
        { align: "center" }
    );
    pdf.setFontSize(10);
    pdf.text("F.D.T.E.U.L.P.", pageWidth / 2, 48, { align: "center" });

    pdf.setFontSize(12);
    pdf.text("Reporte de estadísticas generales", pageWidth / 2, 55, {
        align: "center",
    });

    return 60;
};

const descargarPDF = async () => {
    escudoBase64 = await cargarEscudo();
    const pdf = new jsPDF("p", "mm", "a4");
    let y = agregarEncabezado(pdf);

    // Agrupar gráficos de 2 en 2 para el PDF
    for (let i = 0; i < chartConfigs.length; i += 2) {
        if (i > 0) {
            pdf.addPage();
            y = agregarEncabezado(pdf);
        }

        // Procesar dos gráficos por página
        for (let j = i; j < Math.min(i + 2, chartConfigs.length); j++) {
            const chartRef = chartRefs.value[j];
            const config = chartConfigs[j];
            
            await new Promise(resolve => setTimeout(resolve, 300));
            const canvas = await html2canvas(chartRef);
            const imgData = canvas.toDataURL("image/png");
            const pageWidth = pdf.internal.pageSize.getWidth();
            const imgWidth = 80;
            const xPos = j % 2 === 0 ? 20 : 110;

            pdf.addImage(imgData, "PNG", xPos, y, imgWidth, 50);

            // Solo agregar espacio si es el primero de la fila
            if (j % 2 === 0) {
                y += 60;
            }

            // Tabla debajo del gráfico
            const bodyRows = config.x.map((label, row) => [
                label,
                ...config.series.map((s) => s.data[row] ?? "—"),
            ]);
            bodyRows.push([
                "TOTAL",
                ...config.series.map((s) => s.data.reduce((a, b) => a + b, 0)),
            ]);

            autoTable(pdf, {
                startY: y,
                head: [["Categoría", ...config.series.map((s) => s.name)]],
                body: bodyRows,
                startX: xPos,
                tableWidth: imgWidth,
                styles: { 
                    fontSize: 7,
                    cellPadding: 2,
                    lineColor: [241, 245, 249],
                    textColor: [71, 85, 105],
                    fillColor: [255, 255, 255]
                },
                headStyles: {
                    fillColor: [79, 70, 229],
                    textColor: 255,
                    fontStyle: "bold",
                },
                alternateRowStyles: {
                    fillColor: [248, 250, 252]
                }
            });

            y = pdf.lastAutoTable.finalY + 10;
        }
    }

    // Página final con resumen
    pdf.addPage();
    y = agregarEncabezado(pdf);
    autoTable(pdf, {
        startY: y + 10,
        head: [["Indicador", "Cantidad"]],
        body: [
            ["Usuarios", stats.totalUsers],
            ["Cursos", stats.totalCourses],
            ["Inscripciones", stats.totalInscriptions],
            ["Certificados", stats.totalCertificates],
            ["Tipos de Actividad", stats.totalActivityTypes],
        ],
        headStyles: { 
            fillColor: [16, 185, 129],
            textColor: 255,
            fontStyle: "bold"
        },
        styles: {
            fontSize: 9,
            cellPadding: 4,
            textColor: [71, 85, 105]
        },
        theme: "grid",
        alternateRowStyles: {
            fillColor: [248, 250, 252]
        }
    });

    // Pie de página
    const totalPages = pdf.getNumberOfPages();
    const fechaHora = new Date().toLocaleString("es-BO", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });

    for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        pdf.setFontSize(8);
        pdf.setTextColor(100);
        pdf.text(`Emitido el: ${fechaHora}`, 105, 287, { align: "center" });
    }

    pdf.save(`estadisticas_dashboard_${Date.now()}.pdf`);
};

onMounted(() => {
    renderCharts();
    
    resizeObserver.value = new ResizeObserver(() => {
        handleResize();
    });
    
    window.addEventListener('resize', handleResize);
    
    nextTick(() => {
        chartRefs.value.forEach(el => {
            if (el) {
                resizeObserver.value.observe(el);
            }
        });
    });
});

onBeforeUnmount(() => {
    if (resizeObserver.value) {
        resizeObserver.value.disconnect();
    }
    window.removeEventListener('resize', handleResize);
    
    chartInstances.value.forEach(chart => {
        if (chart) {
            chart.dispose();
        }
    });
});
</script>

<style scoped>
/* Estilos para mejorar la legibilidad en móviles */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .text-lg {
        font-size: 1rem;
    }
    
    .text-sm {
        font-size: 0.875rem;
    }
    
    .text-xs {
        font-size: 0.75rem;
    }
}
</style>