<template>
  <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start">
    <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
      <nav>
        <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
          <li class="text-sm leading-normal">
            <a class="dark:text-white text-slate-800 opacity-50" href="javascript:;">Pagina</a>
          </li>
          <li class="text-sm pl-2 capitalize leading-normal dark:text-white text-slate-800 before:float-left before:pr-2 before:dark:text-white before:text-slate-800 before:content-['/']" aria-current="page">
            {{ currentPage }}
          </li>
        </ol>
        <h6 class="mb-0 font-bold dark:text-white text-slate-800 capitalize">{{ currentPage }}</h6>
      </nav>

      <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
        <!-- Barra de búsqueda -->
        <div class="flex items-center md:ml-auto md:pr-4">

        </div>

        <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
          <!-- Menú de usuario -->
          <li class="relative flex items-center pr-2">
            <div class="dropdown">
              <a href="javascript:;" class="block px-0 py-2 text-sm font-semibold dark:text-white text-slate-800 transition-all ease-nav-brand" @click="toggleUserMenu">
                <i class="fa fa-user sm:mr-1"></i>
                <span class="hidden sm:inline">{{ user.name }}</span>
                <i class="ml-1 fa fa-caret-down"></i>
              </a>
              <ul v-show="userMenuOpen" class="absolute right-0 z-50 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg dark:bg-slate-850">
                <li>
                  <Link href="/user/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-slate-900">
                    <i class="mr-2 fa fa-user"></i> Profile
                  </Link>
                </li>
                <li>
                  <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-white dark:hover:bg-slate-900">
                    <i class="mr-2 fa fa-sign-out"></i> Logout
                  </Link>
                </li>
              </ul>
            </div>
          </li>

          <!-- Botón para móvil -->
          <li class="flex items-center pl-4 xl:hidden">
            <button @click="$emit('toggle-sidebar')" class="block p-0 text-sm text-white transition-all ease-nav-brand">
              <div class="w-4.5 overflow-hidden">
                <i class="ease mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                <i class="ease mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                <i class="ease relative block h-0.5 rounded-sm bg-white transition-all"></i>
              </div>
            </button>
          </li>

          <!-- Configuración -->
          <li class="flex items-center px-4">
            <a href="javascript:;" class="p-0 text-sm dark:text-white text-slate-800 transition-all ease-nav-brand" @click="toggleSettings">
              <i class="cursor-pointer fa fa-cog"></i>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  currentPage: {
    type: String,
    default: 'Dashboard'
  },
  user: {
    type: Object,
    default: () => ({})
  }
});

const searchQuery = ref('');
const userMenuOpen = ref(false);
const notificationsOpen = ref(false);
const settingsOpen = ref(false);

const notifications = ref([
  {
    title: 'New message from Laur',
    time: '13 minutes ago',
    image: '/assets/img/team-2.jpg',
    read: false
  },
  {
    title: 'New album by Travis Scott',
    time: '1 day ago',
    icon: 'fab fa-spotify',
    iconBg: 'bg-gradient-to-tl from-zinc-800 to-zinc-700',
    read: false
  },
  {
    title: 'Payment successfully completed',
    time: '2 days ago',
    icon: 'fas fa-credit-card',
    iconBg: 'bg-gradient-to-tl from-slate-600 to-slate-300',
    read: true
  }
]);

const unreadNotifications = computed(() => {
  return notifications.value.filter(n => !n.read).length;
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
    notifications.value = notifications.value.map(n => ({ ...n, read: true }));
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
  if (!event.target.closest('.dropdown')) {
    userMenuOpen.value = false;
  }
  if (!event.target.closest('[dropdown-trigger]')) {
    notificationsOpen.value = false;
  }
};

document.addEventListener('click', onClickOutside);
</script>