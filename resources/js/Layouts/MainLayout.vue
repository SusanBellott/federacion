<template>
    <div>
        <Sidebar
            :activeRoute="$page.url"
            :sidebarOpen="sidebarOpen"
            @toggle-sidebar="toggleSidebar"
        />
        <main
            class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl"
        >
            <Navbar
                :currentPage="title"
                :user="user"
                @toggle-sidebar="toggleSidebar"
            />
            <div class="w-full p-6 mx-auto">
                <slot></slot>
            </div>
            <Footer :activeRoute="$page.url" />
        </main>
 <!-- Aquí se renderiza la página 
        <div class="w-full px-6 py-6 mx-auto">
            <ThemeSettings />
        </div>-->
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Footer from "@/Components/Dashboard/Footer.vue";
import Navbar from "@/Components/Dashboard/Navbar.vue";
import Sidebar from "@/Components/Dashboard/Sidebar.vue";
import ThemeSettings from "@/Components/Dashboard/Settings.vue";

const props = defineProps({
    title: {
        type: String,
        default: "Dashboard",
    },
    user: {
        type: Object,
        default: () => ({}),
    },
});

const sidebarOpen = ref(false);

// Inicializar sidebar según tamaño de pantalla
onMounted(() => {
    sidebarOpen.value = window.innerWidth >= 1280;

    window.addEventListener("resize", () => {
        sidebarOpen.value = window.innerWidth >= 1280;
    });
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>
