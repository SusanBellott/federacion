<template>
    <MainLayout>
        <Head title="Dashboard" />
        <section
            class="relative bg-transparent text-gray-800 dark:text-white py-8"
        >
            <div class="max-w-4xl mx-auto px-4 space-y-4 text-center">
                <p class="text-2xl font-light">
                    Bienvenido al sistema de emisión de certificados
                </p>
                <div class="w-20 h-1 bg-gray-800 dark:bg-white mx-auto"></div>
                <h1 class="text-5xl sm:text-7xl font-extrabold uppercase">
                    FDTEULP
                </h1>
                <p class="text-xl sm:text-1xl font-medium">
                    Federación de Trabajadores de Educación Urbana La Paz
                </p>
            </div>
        </section>

        <!-- Contenido para estudiantes: Mostrar cursos disponibles -->
        <section v-if="isStudent" class="bg-white dark:bg-slate-900 py-8">
            <div class="max-w-7xl mx-auto px-4">
                <div class="mb-8">
                    <h2
                        class="text-3xl font-bold text-gray-800 dark:text-white"
                    >
                        Actividades Disponibles
                    </h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Explora y inscríbete en nuestras actividades de
                        formación profesional
                    </p>
                </div>
                <!-- Cursos de pago -->
                <div>
                    <div class="mb-12">
                        <h2
                            class="text-2xl font-bold mb-4 text-slate-800 dark:text-white"
                        >
                            Información de Cursos
                        </h2>
                        <CursosPago
                            :cursos_pago="cursos_pago"
                            :mis-cursos-ids="misCursosIds"
                        />
                    </div>
                    <h2
                        class="text-2xl font-bold mb-4 text-slate-800 dark:text-white"
                    >
                        Cursos Disponibles
                    </h2>
                    <!-- Componente de cursos disponibles -->
                    <CursosDisponibles
                        :cursos="cursos || []"
                        :mis-cursos-ids="misCursosIds || []"
                    />

                </div>
            </div>
        </section>

        <!-- Contenido condicional basado en permisos -->
        <div v-if="canSeeStats">
            <!-- Si tiene permiso mostramos las estadísticas -->
            <div
                class="max-w-7xl mx-auto px-4 py-12"
                :class="{ '-mt-12': !isStudent }"
            >
                <!-- Cards de estadísticas generales -->
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12"
                >
                    <StatCard
                        title="Usuarios"
                        :value="stats.totalUsers"
                        icon="users"
                        class="bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200"
                    />

                    <StatCard
                        title="Participantes"
                        :value="stats.totalEstudiantes"
                        icon="graduation-cap"
                        class="bg-gradient-to-br from-pink-100 to-pink-200 border-pink-300"
                    />

                    <StatCard
                        title="Encargados"
                        :value="stats.totalEncargados"
                        icon="user-cog"
                        class="bg-gradient-to-br from-yellow-100 to-yellow-200 border-yellow-300"
                    />

                    <StatCard
                        title="Administradores"
                        :value="stats.totalAdministradores"
                        icon="shield-check"
                        class="bg-gradient-to-br from-gray-100 to-gray-200 border-gray-300"
                    />

                    <StatCard
                        title="Tipos de actividad"
                        :value="stats.totalActivityTypes"
                        icon="collection"
                        class="bg-gradient-to-br from-teal-50 to-teal-100 border-teal-200"
                    />

                    <StatCard
                        title="Actividades"
                        :value="stats.totalCourses"
                        icon="book-open"
                        class="bg-gradient-to-br from-indigo-50 to-indigo-100 border-indigo-200"
                    />
                    <StatCard
                        title="Certificados"
                        :value="stats.totalCertificates"
                        icon="badge-check"
                        class="bg-gradient-to-br from-green-50 to-green-100 border-green-200"
                    />

                    <StatCard
                        title="Inscripciones"
                        :value="stats.inscripcionesActivas"
                        icon="clipboard-list"
                        class="bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200"
                    />
                </div>
            </div>

            <!-- Componente de gráficos -->
            <GraficoActividades :stats="stats" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <GraficoCursos :stats="stats" />
                <GraficoCertificados :stats="stats" />
            </div>
            <!-- Componente padre de aqui sacaremos los graficos -->
        </div>

        <!-- Si NO tiene permiso y NO es estudiante, mostrar acceso limitado -->
        <div
            v-if="!canSeeStats && !isStudent"
            class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12"
        ></div>
    </MainLayout>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import MainLayout from "@/Layouts/MainLayout.vue";
import StatCard from "@/Components/StatCard.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
/* Componentes para cursos */
import CursosDisponibles from "@/Components/m_CursosGP/CursosDisponibles.vue";
import CursosPago from "@/Components/m_CursosGP/CursosPago.vue";

/* Componentes para gráficos */

import GraficoActividades from "@/Components/m_Graficos/GraficoActividades.vue";
import GraficoCursos from "@/Components/m_Graficos/GraficoCursos.vue";
import GraficoCertificados from "@/Components/m_Graficos/GraficoCertificados.vue";
// Invocamos usePage() para obtener todos los props
const page = usePage();

// Extraemos stats, isStudent y canSeeStats del objeto props
const { stats, isStudent, canSeeStats, cursos, misCursosIds, cursos_pago } =
    page.props;

//  Variable para activar/desactivar modo depuración
const debug = ref(true);

onMounted(() => {
   console.log("=== DEBUGGING DASHBOARD ===");
    console.log("isStudent:", isStudent);
    console.log("canSeeStats:", canSeeStats);

    console.log("--- CURSOS GRATUITOS ---");
    console.log("cursos:", cursos);
    if (cursos && Array.isArray(cursos)) {
   
    } else if (cursos && cursos.data) {
        console.log(
            "Cantidad de cursos gratuitos (paginados):",
            cursos.data.length
        );
    }

    console.log("--- CURSOS DE PAGO ---");
    console.log("cursosPago:", cursos_pago);
    if (cursos_pago && Array.isArray(cursos_pago)) {
        console.log("Cantidad de cursos de pago:", cursos_pago.length);
    } else if (cursos_pago && cursos_pago.data) {
        console.log(
            "Cantidad de cursos de pago (paginados):",
            cursos_pago.data.length
        );
        console.log("Primer curso de pago:", cursos_pago.data[0]);
    } else {
        console.log("❌ No hay datos de cursos de pago o formato incorrecto");
    }

    console.log("misCursosIds:", misCursosIds);
    console.log("=== FIN DEBUG ===");
});
</script>
