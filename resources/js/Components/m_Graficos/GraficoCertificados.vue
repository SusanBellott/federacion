<template>
    <div class="w-full">
        <!-- Contenedor grid para gráficos -->
        <div class="grid grid-cols-1 gap-6">
            <div
                v-for="(config, index) in chartConfigs"
                :key="index"
                class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden"
            >
                <div
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100"
                >
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ config.title }}
                    </h2>
                </div>
                <div class="p-4">
                    <div
                        class="h-64 w-full"
                        :ref="(el) => (chartRefs[index] = el)"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from "vue";
import * as echarts from "echarts";
import { usePage } from "@inertiajs/vue3";

const { stats } = usePage().props;
const chartRefs = ref([]);
const chartInstances = ref([]);
const resizeObserver = ref(null);

const props = defineProps({
    cursos: {
        type: Array,
        default: () => [],
    },
});

const permissions = usePage().props.permissions;
const cursos = props.cursos; //
watch(
    () => props.cursos,
    (newVal) => {
        cursos.value = [...newVal];
    }
);
const chartConfigs = [
    {
        title: "Certificados por tipo de actividad",
        type: "pie",
        data: Object.entries(stats.certificatesByTypeAndCourse).map(
            ([tipo, cursos]) => ({
                name: tipo,
                value: Object.values(cursos).reduce((a, b) => a + b, 0),
            })
        ),
    },
];

const renderCharts = () => {
    chartConfigs.forEach((config, index) => {
        // Siempre inicializa el chart
        const chart = echarts.init(chartRefs.value[index]);

        // Si es gráfico de pastel
        if (config.type === "pie") {
            chart.setOption({
                tooltip: {
                    trigger: "item",
                },
                legend: getLegendOptions(),

                series: [
                    {
                        name: config.title,
                        type: "pie",
                        center: ["45%", "50%"], // mueve el gráfico a la izquierda
                        radius: ["40%", "65%"], // tamaño razonable
                        // Deja espacio para la leyenda a la derecha
                        avoidLabelOverlap: false,
                        label: {
                            formatter: "{b|{b}}\n{c} ({d}%)",
                            rich: {
                                b: {
                                    fontSize: 10,
                                    color: "#334155",
                                    fontWeight: "bold",
                                },
                            },
                            lineHeight: 16,
                        },

                        labelLine: {
                            length: 15,
                            length2: 10,
                            smooth: true,
                        },
                        data: config.data,
                    },
                ],
            });

            chartInstances.value[index] = chart;
            return;
        }
    });
};

const getLegendOptions = () => {
    const isMobile = window.innerWidth < 768;

    return {
        orient: isMobile ? "horizontal" : "vertical",
        left: isMobile ? "center" : "right",
        top: isMobile ? "bottom" : "middle",
        icon: "roundRect",
        itemWidth: 14,
        itemHeight: 14,
        itemGap: 10,
        textStyle: {
            color: "#334155",
            fontSize: 10,
            fontWeight: "normal",
        },
    };
};

const handleResize = debounce(() => {
    chartInstances.value.forEach((chart, index) => {
        if (chart) {
            chart.resize();

            // Vuelve a establecer solo la leyenda en caso de cambio de tamaño
            chart.setOption({
                legend: getLegendOptions(),
            });
        }
    });
}, 100);

function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

onMounted(() => {
    renderCharts();

    resizeObserver.value = new ResizeObserver(() => {
        handleResize();
    });

    window.addEventListener("resize", handleResize);

    nextTick(() => {
        chartRefs.value.forEach((el) => {
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
    window.removeEventListener("resize", handleResize);

    chartInstances.value.forEach((chart) => {
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
