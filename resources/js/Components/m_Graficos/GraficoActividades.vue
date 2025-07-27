<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Contenedor grid para gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
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
                        class="w-full"
                        :class="{
                            'h-64': !isMobile,
                            'h-[420px]': isMobile,
                        }"
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

const { stats, temasPorTipo } = usePage().props;
const tipos = Object.keys(stats.byActivityType);
const chartRefs = ref([]);
const chartInstances = ref([]);
const resizeObserver = ref(null);
const chartType = ref("pie"); // o 'bar'

const chartConfigs = [
    {
        title: "Cursos realizados por tipo de actividad",
        x: Object.keys(temasPorTipo),
        series: [
            {
                name: "Cursos realizados",
                data: Object.values(temasPorTipo),
            },
        ],
    },
    {
        title: "Inscritos por tipo de actividad",
        x: Object.keys(stats.byActivityType),
        series: [
            {
                name: "Inscritos",
                data: Object.values(stats.byActivityType),
            },
        ],
    },
];

const isMobile = ref(window.innerWidth < 768);

const updateIsMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

window.addEventListener("resize", updateIsMobile);
onBeforeUnmount(() => {
    window.removeEventListener("resize", updateIsMobile);
});

const renderCharts = () => {
    chartConfigs.forEach((config, index) => {
        if (chartInstances.value[index]) {
            chartInstances.value[index].dispose();
        }

        const chart = echarts.init(chartRefs.value[index]);

        const pieData = config.x.map((label, i) => ({
            name: label,
            value: config.series[0].data[i],
        }));

        chart.setOption({
            tooltip: {
                trigger: "item",
                formatter: "{b}: {c} ({d}%)",
            },
            legend: getLegendOptions(),

            series: [
                {
                    name: config.series[0].name,
                    type: "pie",
                    center: ["45%", "50%"], // mueve el gráfico a la izquierda
                    radius: ["40%", "65%"], // tamaño razonable

                    data: pieData,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: "rgba(0, 0, 0, 0.2)",
                        },
                    },
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

                    itemStyle: {
                        borderRadius: 5,
                        borderColor: "#fff",
                        borderWidth: 2,
                    },
                },
            ],
        });

        chartInstances.value[index] = chart;
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

watch(chartType, () => {
    nextTick(() => renderCharts());
});

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
