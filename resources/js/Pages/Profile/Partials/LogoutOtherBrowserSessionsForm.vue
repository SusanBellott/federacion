<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    sessions: {
        type: Array,
        required: true,
        default: () => []
    },
    class: {
        type: String,
        default: 'mt-10 sm:mt-0'
    }
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const containerClasses = computed(() => {
    return `relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border ${props.class}`;
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    setTimeout(() => passwordInput.value?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};

const getDeviceIcon = (session) => {
    if (session.agent.is_desktop) {
        return {
            icon: '🖥️',
            type: 'Desktop',
            class: 'text-blue-500'
        };
    } else if (session.agent.is_mobile) {
        return {
            icon: '📱',
            type: 'Mobile',
            class: 'text-green-500'
        };
    } else if (session.agent.is_tablet) {
        return {
            icon: '💻',
            type: 'Tablet',
            class: 'text-purple-500'
        };
    } else {
        return {
            icon: '❓',
            type: 'Unknown',
            class: 'text-gray-500'
        };
    }
};

const getBrowserIcon = (browser) => {
    const browserLower = browser?.toLowerCase() || '';
    
    if (browserLower.includes('chrome')) return '🌐';
    if (browserLower.includes('firefox')) return '🦊';
    if (browserLower.includes('safari')) return '🍏';
    if (browserLower.includes('edge')) return '🔵';
    if (browserLower.includes('opera')) return '🔴';
    if (browserLower.includes('ie')) return '🏢';
    return '🌐';
};
</script>

<template>
    <div :class="containerClasses">
      <!-- Header -->
      <div class="p-6 px-4 pb-0 mb-0 border-b-0 rounded-t-2xl">
        <h6 class="mb-0 text-slate-700 dark:text-white/80">Sesiones Activas</h6>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Administra y cierra sesión en otros navegadores y dispositivos.</p>
      </div>

      <!-- Content -->
      <div class="flex-auto p-4 pt-6">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          Si es necesario, puedes cerrar sesión en todos tus otros navegadores en todos tus dispositivos.
          Algunas de tus sesiones recientes se enumeran a continuación. Si crees que tu cuenta ha sido comprometida,
          también deberías cambiar tu contraseña.
        </div>

        <!-- Sessions List -->
        <ul class="flex flex-col pl-0 mb-0 rounded-lg" v-if="sessions.length > 0">
          <li v-for="(session, i) in sessions" :key="i" 
              class="relative flex p-6 mb-2 border-0 rounded-xl bg-gray-50 dark:bg-slate-850">
            <div class="flex items-center w-full">
              <!-- Device Icon -->
              <div class="mr-4 text-3xl" :class="getDeviceIcon(session).class">
                {{ getDeviceIcon(session).icon }}
              </div>

              <!-- Session Info -->
              <div class="flex-grow">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ session.agent.platform ? session.agent.platform : 'Sistema desconocido' }}
                      <span class="text-xs ml-2 px-2 py-1 rounded-full bg-gray-200 dark:bg-gray-700">
                        {{ getDeviceIcon(session).type }}
                      </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                      <span class="mr-2">{{ getBrowserIcon(session.agent.browser) }} {{ session.agent.browser ? session.agent.browser : 'Navegador desconocido' }}</span>
                      • {{ session.ip_address }}
                    </div>
                  </div>
                  <div v-if="session.is_current_device" class="text-xs px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                    Este dispositivo
                  </div>
                </div>
                
                <div v-if="session.last_active" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                  Última actividad: {{ session.last_active }}
                </div>
              </div>
            </div>
          </li>
        </ul>

        <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
          No hay otras sesiones activas registradas.
        </div>

        <!-- Logout Button -->
        <div class="mt-5">
          <button
            @click="confirmLogout"
            class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-red-500 to-pink-500"
          >
            Cerrar Otras Sesiones
          </button>
          
          <span v-if="form.recentlySuccessful" class="ml-3 text-sm text-green-500">
            Listo.
          </span>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="confirmingLogout" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="closeModal">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal content -->
        <div 
          role="document"
          aria-labelledby="modal-title"
          aria-describedby="modal-description"
          class="inline-block align-bottom bg-white dark:bg-slate-850 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white dark:bg-slate-850 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 id="modal-title" class="text-lg font-medium text-slate-700 dark:text-white/80 mb-4">Cerrar Otras Sesiones</h3>
            <p id="modal-description" class="text-sm text-gray-600 dark:text-gray-400">
              Por favor ingresa tu contraseña para confirmar que deseas cerrar sesión en tus otros navegadores en todos tus dispositivos.
            </p>
            
            <div class="mt-4">
              <label for="password" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Contraseña</label>
              <input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                placeholder="Ingresa tu contraseña"
                autocomplete="current-password"
                @keyup.enter="logoutOtherBrowserSessions"
              />
              <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
            </div>
          </div>
          
          <div class="bg-gray-50 dark:bg-slate-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="logoutOtherBrowserSessions"
              class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-red-500 to-pink-500"
              :class="{ 'opacity-50': form.processing }"
              :disabled="form.processing"
            >
              Cerrar Otras Sesiones
            </button>
            <button
              @click="closeModal"
              class="px-6 py-2.5 mr-3 font-bold text-center text-gray-700 uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gray-200 dark:bg-gray-700 dark:text-white"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
</template>