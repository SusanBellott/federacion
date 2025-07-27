<template>
    <nav
         class="mt-4 relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in duration-250 shadow-xl rounded-2xl lg:flex-nowrap lg:justify-start border border-transparent bg-gradient-to-br from-violet-900 to-indigo-900 text-white"
>
        <div
            class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit"
        >
            <div
                class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto"
            >
                <!-- Botón para móvil -->
                <div class="xl:hidden">
                    <button
                        @click="$emit('toggle-sidebar')"
                        class="block px-0 py-2 text-sm font-semibold dark:text-white text-slate-800 transition-all ease-nav-brand"
                    >
                        <i
                            class="fas fa-bars text-gray-800 dark:text-white"
                        ></i>
                    </button>
                </div>
                <!-- Barra de búsqueda -->
                <div class="flex items-center md:ml-auto md:pr-4"></div>

                <ul
                    class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full"
                >
                    <!-- Menú de usuario -->
                    <li class="relative flex items-center pr-2">
                        <div class="dropdown">
                            <a
                                href="javascript:;"
                                class="block px-0 py-2 text-sm font-semibold dark:text-white text-slate-800 transition-all ease-nav-brand"
                                @click="toggleUserMenu"
                            >
                                <i class="fa fa-user sm:mr-2"></i>
                                <span class="text-sm font-semibold uppercase">
                                    {{
                                        $page.props.user?.rol === "Estudiante"
                                            ? "PARTICIPANTE"
                                            : $page.props.user?.rol?.toUpperCase()
                                    }}
                                </span>

                                <i class="ml-1 fa fa-caret-down"></i>
                            </a>
                            <ul
                                v-show="userMenuOpen"
                                class="absolute right-0 z-50 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-slate-850"
                            >
                                <li>
                                    <Link
                                        href="/user/profile"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-slate-900"
                                    >
                                        <i class="mr-2 fa fa-user"></i> Perfil
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-slate-900"
                                    >
                                        <i class="mr-2 fa fa-sign-out"></i>
                                        Cerrar Sesion
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Configuración -->
                    <!-- <li class="flex items-center px-4">
            <a href="javascript:;" class="p-0 text-sm dark:text-white text-slate-800 transition-all ease-nav-brand"
              @click="toggleSettings">
              <i class="cursor-pointer fa fa-cog"></i>
            </a>
          </li> -->
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed, onUnmounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
    currentPage: {
        type: String,
        default: "Dashboard",
    },
});

// Obtener el usuario de las props globales de Inertia
const user = computed(() => usePage().props.auth.user);

const searchQuery = ref("");
const userMenuOpen = ref(false);
const notificationsOpen = ref(false);
const settingsOpen = ref(false);

const notifications = ref([
    {
        title: "New message from Laur",
        time: "13 minutes ago",
        image: "/assets/img/team-2.jpg",
        read: false,
    },
    {
        title: "New album by Travis Scott",
        time: "1 day ago",
        icon: "fab fa-spotify",
        iconBg: "bg-gradient-to-tl from-zinc-800 to-zinc-700",
        read: false,
    },
    {
        title: "Payment successfully completed",
        time: "2 days ago",
        icon: "fas fa-credit-card",
        iconBg: "bg-gradient-to-tl from-slate-600 to-slate-300",
        read: true,
    },
]);

const unreadNotifications = computed(() => {
    return notifications.value.filter((n) => !n.read).length;
});

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
    if (userMenuOpen.value) {
        notificationsOpen.value = false;
        settingsOpen.value = false;
    }
};

const toggleNotifications = () => {
    notificationsOpen.value = !notificationsOpen.value;
    if (notificationsOpen.value) {
        userMenuOpen.value = false;
        settingsOpen.value = false;
        notifications.value = notifications.value.map((n) => ({
            ...n,
            read: true,
        }));
    }
};

const toggleSettings = () => {
    settingsOpen.value = !settingsOpen.value;
    if (settingsOpen.value) {
        userMenuOpen.value = false;
        notificationsOpen.value = false;
    }
};

// Cerrar menús al hacer clic fuera
const onClickOutside = (event) => {
    if (!event.target.closest(".dropdown")) {
        userMenuOpen.value = false;
    }
    if (!event.target.closest("[dropdown-trigger]")) {
        notificationsOpen.value = false;
    }
};

document.addEventListener("click", onClickOutside);
</script>
