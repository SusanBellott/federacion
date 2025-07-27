<template>
    <div class="flex flex-col">
        <!-- contenido principal -->
        <main class="flex-grow">
            <!-- aquí va tu contenido dinámico -->
            <router-view />
        </main>

        <!-- footer -->
        <footer class="pt-4 bg-transparent mb-[1cm]">
            <div class="w-full px-6 mx-auto">
                <div class="text-sm text-center text-slate-500">
                    © {{ currentYear }} | F.D.T.E.U.L.P. – Web Developer - Tahere Susan
                    Bellott Huaranca – Todos los Derechos Reservados
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { computed } from "vue";

const currentYear = computed(() => new Date().getFullYear());
</script>
