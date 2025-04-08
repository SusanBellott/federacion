<template>
  <nav
    class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start"
    navbar-main navbar-scroll="false"
  >
    <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
      <nav>
        <!-- breadcrumb -->
        <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
          <li class="text-sm leading-normal">
            <a :class="isDarkMode ? 'text-white opacity-50' : 'text-slate-700 opacity-50'" href="javascript:;">Pages</a>
          </li>
          <li
            :class="isDarkMode ? 'text-sm pl-2 capitalize leading-normal text-white before:float-left before:pr-2 before:text-white before:content-[\'/\']' : 'text-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-slate-700 before:content-[\'/\']'"
            aria-current="page"
          >{{ currentRoute }}</li>
        </ol>
        <h6 :class="isDarkMode ? 'mb-0 font-bold text-white capitalize' : 'mb-0 font-bold text-slate-700 capitalize'">{{ currentRoute }}</h6>
      </nav>

      <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
        <div class="flex items-center md:ml-auto md:pr-4">
          <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease">
            <span :class="isDarkMode ? 'text-sm ease leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-white transition-all' : 'text-sm ease leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all'">
              <i class="fas fa-search"></i>
            </span>
            <input 
              type="text" 
              :class="isDarkMode ? 'pl-9 text-sm focus:shadow-primary-outline ease w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-700 bg-slate-850 text-white bg-clip-padding py-2 pr-3 transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none focus:transition-shadow' : 'pl-9 text-sm focus:shadow-primary-outline ease w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none focus:transition-shadow'"
              placeholder="Type here..." 
            />
          </div>
        </div>
        
        <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
          <!-- User dropdown -->
          <li class="relative flex items-center">
            <a 
              @click="toggleUserMenu"
              href="javascript:;" 
              :class="isDarkMode ? 'flex items-center px-0 py-2 text-sm font-semibold text-white transition-all ease-nav-brand' : 'flex items-center px-0 py-2 text-sm font-semibold text-slate-700 transition-all ease-nav-brand'"
            >
              <span class="inline-block w-5 h-5 text-center">👤</span>
              <span class="hidden sm:inline ml-1">{{ userName }}</span>
              <span class="ml-1 text-xs">▼</span>
            </a>
            
            <!-- User dropdown menu -->
            <ul 
              v-if="userMenuOpen"
              class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease lg:shadow-3xl duration-250 min-w-44 before:sm:right-8 before:text-5.5 pointer-events-none absolute right-0 top-0 z-50 origin-top list-none rounded-lg border-0 border-solid border-transparent dark:shadow-dark-xl dark:bg-slate-850 bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-2 before:left-auto before:top-0 before:z-50 before:inline-block before:font-normal before:text-white before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-12 lg:block lg:cursor-pointer"
              :class="{'pointer-events-auto opacity-100': userMenuOpen}"
            >
              <li class="relative mb-2">
                <Link class="dark:hover:bg-slate-900 ease py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 lg:transition-colors"
                  href="/user/profile">
                  <div class="flex py-1">
                    <div class="flex flex-col justify-center">
                      <h6 class="mb-1 text-sm font-normal leading-normal dark:text-white">
                        <span class="inline-block w-4 h-4 mr-1 text-center">👤</span> My Profile
                      </h6>
                    </div>
                  </div>
                </Link>
              </li>
              <li class="relative mb-2">
                <a class="dark:hover:bg-slate-900 ease py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 lg:transition-colors"
                  href="/settings">
                  <div class="flex py-1">
                    <div class="flex flex-col justify-center">
                      <h6 class="mb-1 text-sm font-normal leading-normal dark:text-white">
                        <span class="inline-block w-4 h-4 mr-1 text-center">⚙️</span> Account Settings
                      </h6>
                    </div>
                  </div>
                </a>
              </li>
              <li class="relative">
                <a 
                  @click="logout"
                  class="dark:hover:bg-slate-900 ease py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-red-500 lg:transition-colors"
                  href="javascript:;">
                  <div class="flex py-1">
                    <div class="flex flex-col justify-center">
                      <h6 class="mb-1 text-sm font-normal leading-normal dark:text-white">
                        <span class="inline-block w-4 h-4 mr-1 text-center">🚪</span> Logout
                      </h6>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Mobile menu button - Hamburger -->
          <li class="flex items-center pl-4 xl:hidden">
            <a @click="toggleSidebar" class="block p-0 text-sm transition-all ease-nav-brand cursor-pointer" :class="isDarkMode ? 'text-white' : 'text-slate-700'" href="javascript:;">
              <div class="w-4.5 overflow-hidden">
                <i :class="isDarkMode ? 'ease mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all' : 'ease mb-0.75 relative block h-0.5 rounded-sm bg-slate-700 transition-all'"></i>
                <i :class="isDarkMode ? 'ease mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all' : 'ease mb-0.75 relative block h-0.5 rounded-sm bg-slate-700 transition-all'"></i>
                <i :class="isDarkMode ? 'ease relative block h-0.5 rounded-sm bg-white transition-all' : 'ease relative block h-0.5 rounded-sm bg-slate-700 transition-all'"></i>
              </div>
            </a>
          </li>
          
          <!-- Settings button -->
          <li class="flex items-center px-4">
            <a @click="toggleSettings" href="javascript:;" class="p-0 text-sm transition-all ease-nav-brand" :class="isDarkMode ? 'text-white' : 'text-slate-700'">
              <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog"></i>
            </a>
          </li>

          <!-- Notifications -->
          <li class="relative flex items-center pr-2">
            <p class="hidden transform-dropdown-show"></p>
            <a 
              @click="toggleNotifications" 
              href="javascript:;" 
              class="block p-0 text-sm transition-all ease-nav-brand"
              :class="isDarkMode ? 'text-white' : 'text-slate-700'"
            >
              <i class="cursor-pointer fa fa-bell"></i>
            </a>
            
            <!-- Notifications dropdown -->
            <ul 
              v-if="notificationsOpen"
              class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease lg:shadow-3xl duration-250 min-w-44 before:sm:right-8 before:text-5.5 pointer-events-none absolute right-0 top-0 z-50 origin-top list-none rounded-lg border-0 border-solid border-transparent dark:shadow-dark-xl dark:bg-slate-850 bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-2 before:left-auto before:top-0 before:z-50 before:inline-block before:font-normal before:text-white before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-2 lg:block lg:cursor-pointer"
              :class="{'pointer-events-auto opacity-100': notificationsOpen}"
            >
              <li class="relative mb-2" v-for="(notification, index) in notifications" :key="index">
                <a class="dark:hover:bg-slate-900 ease py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 lg:transition-colors"
                  href="javascript:;">
                  <div class="flex py-1">
                    <div v-if="notification.image" class="my-auto">
                      <img :src="notification.image" class="inline-flex items-center justify-center mr-4 text-sm text-white h-9 w-9 max-w-none rounded-xl" />
                    </div>
                    <div v-else-if="notification.icon === '🎵'" class="my-auto">
                      <div class="inline-flex items-center justify-center mr-4 text-sm text-white bg-gradient-to-tl from-zinc-800 to-zinc-700 dark:bg-gradient-to-tl dark:from-slate-750 dark:to-gray-850 h-9 w-9 max-w-none rounded-xl">
                        <span class="text-lg">{{ notification.icon }}</span>
                      </div>
                    </div>
                    <div v-else-if="notification.icon === '💳'" class="my-auto">
                      <div class="inline-flex items-center justify-center my-auto mr-4 text-sm text-white transition-all duration-200 ease-nav-brand bg-gradient-to-tl from-slate-600 to-slate-300 h-9 w-9 rounded-xl">
                        <span class="text-lg">{{ notification.icon }}</span>
                      </div>
                    </div>
                    <div v-else class="my-auto">
                      <div class="flex items-center justify-center mr-4 text-sm bg-gray-200 h-9 w-9 max-w-none rounded-xl">
                        <span class="text-lg">{{ notification.icon || '📩' }}</span>
                      </div>
                    </div>
                    <div class="flex flex-col justify-center">
                      <h6 class="mb-1 text-sm font-normal leading-normal dark:text-white">
                        <span class="font-semibold">{{ notification.title }}</span> {{ notification.message }}
                      </h6>
                      <p class="mb-0 text-xs leading-tight text-slate-400 dark:text-white/80">
                        <i class="mr-1 fa fa-clock"></i>
                        {{ notification.time }}
                      </p>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

const notificationsOpen = ref(false);
const userMenuOpen = ref(false);
const sidebarVisible = ref(false);
const isDarkMode = ref(false);

// Improved function to close dropdowns when clicking outside
const handleClickOutside = (event) => {
  try {
    const isUserMenuButton = event.target.closest('a[href="javascript:;"]')?.textContent?.includes(userName.value);
    const isNotificationButton = event.target.closest('a[href="javascript:;"]')?.textContent?.includes('🔔') || 
                                event.target.classList?.contains('fa-bell');
    
    if (!isUserMenuButton && userMenuOpen.value) {
      userMenuOpen.value = false;
    }
    
    if (!isNotificationButton && notificationsOpen.value) {
      notificationsOpen.value = false;
    }
  } catch (error) {
    console.error('Error in handleClickOutside:', error);
  }
};

// Function to detect dark mode
const detectTheme = () => {
  try {
    if (typeof document === 'undefined') return;
    
    const hasDarkClass = document.body?.classList?.contains('dark') || 
                        document.documentElement?.classList?.contains('dark') ||
                        document.body?.getAttribute('data-theme') === 'dark' ||
                        document.documentElement?.getAttribute('data-theme') === 'dark';
    
    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)')?.matches;
    
    isDarkMode.value = hasDarkClass || (prefersDark && !document.body?.classList?.contains('light'));
  } catch (error) {
    console.error('Error detecting theme:', error);
    isDarkMode.value = false;
  }
};

const toggleNotifications = (event) => {
  try {
    event?.stopPropagation();
    notificationsOpen.value = !notificationsOpen.value;
    if (notificationsOpen.value) {
      userMenuOpen.value = false;
    }
  } catch (error) {
    console.error('Error toggling notifications:', error);
  }
};

const toggleUserMenu = (event) => {
  try {
    event?.stopPropagation();
    userMenuOpen.value = !userMenuOpen.value;
    if (userMenuOpen.value) {
      notificationsOpen.value = false;
    }
  } catch (error) {
    console.error('Error toggling user menu:', error);
  }
};

const toggleSettings = () => {
  try {
    document.dispatchEvent(new CustomEvent('toggle-settings'));
  } catch (error) {
    console.error('Error toggling settings:', error);
  }
};

const toggleSidebar = () => {
  try {
    if (typeof document === 'undefined') return;
    
    sidebarVisible.value = !sidebarVisible.value;
    
    if (document.body) {
      document.body.classList.toggle('g-sidenav-show', sidebarVisible.value);
      document.body.classList.toggle('g-sidenav-hidden', !sidebarVisible.value);
    }
    
    document.dispatchEvent(new CustomEvent('toggle-sidebar', { 
      detail: { visible: sidebarVisible.value } 
    }));
  } catch (error) {
    console.error('Error toggling sidebar:', error);
  }
};

const handleResize = () => {
  try {
    if (typeof window === 'undefined') return;
    
    if (window.innerWidth >= 1200) {
      if (document.body) {
        document.body.classList.add('g-sidenav-show');
        document.body.classList.remove('g-sidenav-hidden');
      }
      sidebarVisible.value = true;
    } else {
      if (!sidebarVisible.value && document.body) {
        document.body.classList.remove('g-sidenav-show');
        document.body.classList.add('g-sidenav-hidden');
      }
    }
  } catch (error) {
    console.error('Error handling resize:', error);
  }
};

const handleThemeChange = () => {
  detectTheme();
};

onMounted(() => {
  try {
    requestAnimationFrame(() => {
      document.addEventListener('click', handleClickOutside);
      window.addEventListener('resize', handleResize);
      window.addEventListener('themechange', handleThemeChange);
      
      // Initialize theme and sidebar
      setTimeout(detectTheme, 100);
      setTimeout(handleResize, 100);
    });
  } catch (error) {
    console.error('Error in onMounted:', error);
  }
});

onUnmounted(() => {
  try {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', handleResize);
    window.removeEventListener('themechange', handleThemeChange);
  } catch (error) {
    console.error('Error in onUnmounted:', error);
  }
});

const logout = () => {
  router.post('/logout', {}, {
    onError: (errors) => {
      console.error('Logout failed:', errors);
    }
  });
};

const userName = computed(() => {
  return usePage().props.auth?.user?.name || 'User';
});

const currentRoute = computed(() => {
  const routePath = usePage().url;
  return routePath.split('/').pop() || 'Dashboard';
});

const notifications = ref([
  {
    title: 'New message',
    message: 'from Laur',
    time: '13 minutes ago',
    icon: '📩',
    image: '../assets/img/team-2.jpg'
  },
  {
    title: 'New album',
    message: 'by Travis Scott',
    time: '1 day',
    icon: '🎵'
  },
  {
    title: 'Payment completed',
    message: '',
    time: '2 days',
    icon: '💳'
  }
]);

watch(isDarkMode, (newValue) => {
  if (import.meta.env.DEV) {
    console.log('Dark mode changed:', newValue);
  }
});
</script>