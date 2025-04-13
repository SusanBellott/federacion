<template>
  <div>
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>

    <aside
      class="fixed inset-y-0 flex-wrap items-center justify-between block w-full p-0 my-4 overflow-y-auto antialiased transition-transform duration-200 -translate-x-full bg-white border-0 shadow-xl dark:shadow-none dark:bg-slate-850 max-w-64 ease-nav-brand z-990 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0 sidebar-no-scrollbar"
      :class="{ 'translate-x-0': props.sidebarOpen }" aria-expanded="false">

      <div class="h-32">
        <i class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer fas fa-times dark:text-white text-slate-400 xl:hidden"
          @click="toggleSidebar"></i>
        <a class="flex items-center px-8 py-6 m-0 text-base whitespace-nowrap dark:text-white text-slate-700" href="/"
          target="_blank">
          <img src="/assets/img/logo_instituto.png"
            class="inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-24"
            alt="main_logo" />
          <img src="/assets/img/logo_instituto.png"
            class="hidden h-full max-w-full transition-all duration-200 dark:inline ease-nav-brand max-h-24"
            alt="main_logo" />
          <span class="ml-3 text-xl font-semibold transition-all duration-200 ease-nav-brand">SISEMCE</span>
        </a>
      </div>

      <hr
        class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

      <div class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full sidebar-no-scrollbar">
        <ul class="flex flex-col pl-0 mb-0">
          <li v-for="(item, index) in menuItems.filter(item => hasPermission(item.permissions))" :key="index">
            <InertiaLink :href="item.link"
              class="py-2.7 text-sm ease-nav-brand my-0 mx-2 flex items-center whitespace-nowrap px-4 transition-colors dark:text-white text-slate-800"
              :class="activeClass(item.link)">
              <div
                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                <i :class="`relative top-0 text-sm leading-normal ${item.icon}`"></i>
              </div>
              <span class="ml-1 duration-300 opacity-100 pointer-events-none ease">{{ item.name }}</span>
            </InertiaLink>
          </li>
        </ul>
      </div>
    </aside>

    <div class="fixed bottom-8 right-8 xl:hidden z-990">
      <button @click="toggleSidebar" class="p-3 bg-blue-500 rounded-full text-white shadow-lg">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link as InertiaLink, usePage } from '@inertiajs/vue3';

const props = defineProps({
  activeRoute: {
    type: String,
    required: true
  },
  sidebarOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['toggle-sidebar']);
const page = usePage();

const toggleSidebar = () => {
  emit('toggle-sidebar');
};

const activeClass = (link) => {
  return props.activeRoute === link ?
    'bg-blue-500/13 font-semibold text-slate-700 dark:text-white dark:opacity-80' :
    'dark:text-white dark:opacity-80';
};

const hasPermission = (itemPermissions) => {
  if (!itemPermissions || itemPermissions.length === 0) return true;
  return itemPermissions.some(permission => page.props.permissions.includes(permission));
};

const menuItems = [
  {
    name: 'Inicio',
    icon: 'text-blue-500 ni ni-tv-2',
    link: '/dashboard',
    permissions: ['dashboard']
  },
  {
    name: 'Recepción',
    icon: 'text-orange-500 ni ni-calendar-grid-58',
    link: '/recepciones',
    permissions: ['recepciones.index']
  },
  {
    name: 'Inscritos',
    icon: 'text-emerald-500 ni ni-credit-card',
    link: '/inscritos',
    permissions: ['inscritos.index']
  },
  {
    name: 'Cursos',
    icon: 'text-cyan-500 ni ni-app',
    link: '/cursos',
    permissions: ['cursos.index']
  },
  {
    name: 'Institucion',
    icon: 'text-cyan-500 ni ni-app',
    link: '/instituciones',
    permissions: ['instituciones.index']
  },
  {
    name: 'Distritos',
    icon: 'text-red-600 ni ni-world-2',
    link: '/distritos',
    permissions: ['distritos.index']
  },
  {
    name: 'Mis Cursos',
    icon: 'text-blue-500 ni ni-collection',
    link: '/estudiantes',
    permissions: ['estudiantes.index']
  },
  {
    name: 'Usuarios',
    icon: 'text-slate-700 ni ni-single-02',
    link: '/usuarios',
    permissions: ['usuarios.index']
  }
];
</script>

<style scoped>
.sidebar-no-scrollbar::-webkit-scrollbar {
  display: none;
}

.sidebar-no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>