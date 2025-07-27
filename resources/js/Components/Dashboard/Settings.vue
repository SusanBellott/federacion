<template>
 <!-- 
  <div>
    <a @click.stop="toggleSettings"
      class="fixed px-4 py-2 text-xl bg-white shadow-lg cursor-pointer bottom-8 right-8 z-990 rounded-circle text-slate-700">
      <i class="py-2 pointer-events-none fa fa-cog"></i>
    </a>

    <div fixed-plugin-card
      class="z-sticky backdrop-blur-2xl backdrop-saturate-200 dark:bg-slate-850/80 shadow-3xl w-90 ease fixed top-0 flex h-full min-w-0 flex-col break-words rounded-none border-0 bg-white/80 bg-clip-border px-2.5 duration-200"
      :class="{ '-right-90': !isOpen, 'right-0': isOpen }"
      ref="settingsPanel"
    >
    <div class="px-6 pt-4 pb-0 mb-0 border-b-0 rounded-t-2xl">
  
      <div class="float-left">
    <h5 class="mt-4 mb-0 dark:text-white text-slate-800">Configurar Tema de panel</h5>
    <p class="dark:text-white text-slate-800 dark:opacity-80 opacity-80">Active la opcion</p>
  </div>

  <div class="float-right mt-6">
    <button @click.stop="toggleSettings"
      class="inline-block p-0 mb-4 text-sm font-bold leading-normal text-center uppercase align-middle transition-all ease-in bg-transparent border-0 rounded-lg shadow-none cursor-pointer hover:-translate-y-px tracking-tight-rem bg-150 bg-x-25 active:opacity-85 dark:text-white text-slate-800">
      <i class="fa fa-close"></i>
    </button>
  </div>
</div>

      

      <div class="flex-auto p-6 pt-0 overflow-auto sm:pt-4">


     
         
        <hr
          class="h-px my-6 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
          <div class="flex mt-2 mb-12">
            <h6 class="mb-0 dark:text-white text-slate-800">Light / Dark</h6>
          <div class="block pl-0 ml-auto min-h-6">
            <input v-model="darkMode"
              dark-toggle
              class="rounded-10 duration-250 ease-in-out after:rounded-circle after:shadow-2xl after:duration-250 checked:after:translate-x-5.3 h-5 relative float-left mt-1 ml-auto w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-blue-500/95 checked:bg-blue-500/95 checked:bg-none checked:bg-right"
              type="checkbox" />
          </div>
        </div>
      </div>
    </div>
  </div>-->
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const isOpen = ref(false);
const darkMode = ref(true);
const navbarFixed = ref(true);
const sidenavType = ref('white');
const currentSidebarColor = ref('blue');
const settingsPanel = ref(null);

const sidebarColors = ref([
  { value: 'blue', gradient: 'bg-gradient-to-tl from-blue-500 to-violet-500' },
  { value: 'gray', gradient: 'bg-gradient-to-tl from-zinc-800 to-zinc-700 dark:bg-gradient-to-tl dark:from-slate-750 dark:to-gray-850' },
  { value: 'cyan', gradient: 'bg-gradient-to-tl from-blue-700 to-cyan-500' },
  { value: 'emerald', gradient: 'bg-gradient-to-tl from-emerald-500 to-teal-400' },
  { value: 'orange', gradient: 'bg-gradient-to-tl from-orange-500 to-yellow-500' },
  { value: 'red', gradient: 'bg-gradient-to-tl from-red-600 to-orange-600' }
]);

const toggleSettings = () => {
  isOpen.value = !isOpen.value;
};

const changeSidebarColor = (color) => {
  currentSidebarColor.value = color;
  // Save sidebar color preference
  localStorage.setItem('sidebarColor', color);
  // Emit event for parent components
  emitSettingsChange();
};

const changeSidenavType = (type) => {
  sidenavType.value = type;
  // Save sidenav type preference
  localStorage.setItem('sidenavType', type);
  // Emit event for parent components
  emitSettingsChange();
};

// Function to emit settings changes to parent components
const emitSettingsChange = () => {
  const event = new CustomEvent('settings-changed', {
    detail: {
      darkMode: darkMode.value,
      navbarFixed: navbarFixed.value,
      sidenavType: sidenavType.value,
      sidebarColor: currentSidebarColor.value
    }
  });
  document.dispatchEvent(event);
};

// Function to close panel when clicking outside
const handleClickOutside = (event) => {
  if (isOpen.value && settingsPanel.value && !settingsPanel.value.contains(event.target) && 
      !event.target.classList.contains('fa-cog')) {
    isOpen.value = false;
  }
};

watch(darkMode, (newValue) => {
  // Toggle dark mode
  if (newValue) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
  
  // Save preference in localStorage
  localStorage.setItem('darkMode', newValue.toString());
  // Emit event for parent components
  emitSettingsChange();
});

watch(navbarFixed, (newValue) => {
  // Save navbar fixed preference
  localStorage.setItem('navbarFixed', newValue.toString());
  // Emit event for parent components
  emitSettingsChange();
});

onMounted(() => {
  // Listen for toggle-settings event from other components
  document.addEventListener('toggle-settings', toggleSettings);
  
  // Add event listener to detect clicks outside the component
  document.addEventListener('click', handleClickOutside);
  
  // Retrieve saved preferences
  const savedDarkMode = localStorage.getItem('darkMode');
  const savedNavbarFixed = localStorage.getItem('navbarFixed');
  const savedSidenavType = localStorage.getItem('sidenavType');
  const savedSidebarColor = localStorage.getItem('sidebarColor');
  
  // Apply saved preferences if they exist
  if (savedDarkMode !== null) {
    darkMode.value = savedDarkMode === 'true';
  } else {
    // Use light mode by default
    darkMode.value = false;
  }
  
  if (savedNavbarFixed !== null) {
    navbarFixed.value = savedNavbarFixed === 'true';
  }
  
  if (savedSidenavType !== null) {
    sidenavType.value = savedSidenavType;
  }
  
  if (savedSidebarColor !== null) {
    currentSidebarColor.value = savedSidebarColor;
  }
  
  // Apply mode to DOM
  if (darkMode.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
});

onUnmounted(() => {
  // Remove event listeners when unmounting the component
  document.removeEventListener('toggle-settings', toggleSettings);
  document.removeEventListener('click', handleClickOutside);
});
</script>