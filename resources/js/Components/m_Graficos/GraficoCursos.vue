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
                    class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between"
                >
                    <!-- Título a la izquierda -->
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ config.title }}
                    </h2>

                    <!-- Hamburguesa a la izquierda -->
                    <div class="relative">
                        <button
                            id="hamburger-button"
                            @click="showMenu = !showMenu"
                            class="p-2 rounded-md text-gray-700 hover:bg-blue-100 transition"
                            title="Filtrar por mes"
                        >
                            <!-- Ícono hamburguesa -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>

                        <!-- Menú desplegable -->
                        <div
                            v-if="showMenu"
                            id="dropdown-menu"
                            class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg z-10"
                        >
                            <ul class="py-1 text-sm text-gray-700">
                                <li
                                    v-for="mes in availableMonths"
                                    :key="mes"
                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                                    @click="
                                        selectedMonth = mes;
                                        showMenu = false;
                                    "
                                >
                                    {{ mes }}
                                </li>
                            </ul>
                        </div>
                    </div>
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

const { stats } = usePage().props;
const chartRefs = ref([]);
const chartInstances = ref([]);
const resizeObserver = ref(null);
const isMobile = ref(window.innerWidth < 768);

const availableMonths = Object.keys(stats.byCourseInTypePorMes);
const selectedMonth = ref(availableMonths[0]);

const showMenu = ref(false);

const updateIsMobile = () => {
    isMobile.value = window.innerWidth < 768;
};

window.addEventListener("resize", updateIsMobile);
onBeforeUnmount(() => {
    window.removeEventListener("resize", updateIsMobile);
});

const chartConfigs = ref([
    {
        title: "Inscritos por curso",
        type: "pie",
        data: [], // se llenará dinámicamente
    },
]);

const renderCharts = () => {
    chartConfigs.value.forEach((config, index) => {
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

const handleClickOutside = (event) => {
    const menu = document.getElementById("dropdown-menu");
    const button = document.getElementById("hamburger-button");
    if (
        showMenu.value &&
        menu &&
        !menu.contains(event.target) &&
        button &&
        !button.contains(event.target)
    ) {
        showMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
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

function updateChartData() {
    const dataDelMes = stats.byCourseInTypePorMes[selectedMonth.value];

    chartConfigs.value[0].data = Object.entries(dataDelMes).map(
        ([nombreCompletoCurso, cantidad]) => {
            const [tipo, codigo] = nombreCompletoCurso.split(" - ");
            return {
                name: `${codigo} (${tipo})`,
                value: cantidad,
            };
        }
    );

    renderCharts();
}

watch(selectedMonth, () => {
    updateChartData();
});

onMounted(() => {
    updateChartData(); // <- IMPORTANTE

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
