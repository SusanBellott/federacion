<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'; // Añadido onMounted
import { driver } from "driver.js"; // Importamos driver.js
import "driver.js/dist/driver.css"; // Importamos los estilos de driver.js

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Estado para controlar la visibilidad de la contraseña
const showPassword = ref(false);
// Estado para controlar si el tour ya se ha mostrado
const tourShown = ref(false);

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Configuración del tour con Driver.js
const startTour = () => {
    if (tourShown.value) return;
    
    const driverObj = driver({
        showProgress: true,
        animate: true,
        stagePadding: 10,
        showButtons: ['next', 'previous', 'close'],
        steps: [
            {
                element: '#login-form',
                popover: {
                    title: 'Bienvenido al Sistema de Certificación de la Federación Departamental de Trabajadores de Educación Urbana de La Paz (F.D.T.E.U.L.P.)',
                    description: ' Para ingresar al sistema, por favor sigue los siguientes pasos:',
                    side: 'bottom',
                    align: 'center'
                }
            },
            {
                element: '#email',
                popover: {
                    title: 'Correo electrónico institucional',
                    description: 'Ingresa el correo electrónico institucional asignado. Por ejemplo: Federacion_RDA@fdteulp.com. F_0036054@fdteulp.com',
                    side: 'bottom',
                    align: 'start'
                }
            },
            {
                element: '#password',
                popover: {
                    title: 'Contraseña',
                    description: 'Ingresa tu contraseña, que corresponde a tu número de carnet de identidad. Puedes mostrar u ocultar la contraseña haciendo clic en el ícono del ojo.',
                    side: 'bottom',
                    align: 'start'
                }
            },
            
            {
                element: '#submitButton',
                popover: {
                    title: 'Iniciar sesión',
                    description: 'Haz clic para ingresar al sistema con tus credenciales',
                    side: 'top',
                    align: 'center'
                }
            }
        ]
    });
    
    driverObj.drive();
    tourShown.value = true;
};

// Botón para reiniciar el tour manualmente
const restartTour = () => {
    tourShown.value = false;
    startTour();
};

// Iniciar el tour automáticamente cuando se monte el componente
onMounted(() => {
    // Pequeño retraso para asegurar que todos los elementos estén renderizados
   setTimeout(() => {
        //startTour();
    }, 500);
});
</script>

<template>
  <Head title="Sign In" />
  
  <section class="min-h-screen 
  bg-gradient-to-b from-gray-700 to-gray-800
         text-gray-900 dark:text-gray-100" >
    <div class="relative flex items-center min-h-screen p-0 overflow-hidden bg-center bg-cover">
      <div class="container z-1">
        <div class="flex flex-wrap -mx-3">
          <!-- Form Column -->
          <div class="flex flex-col w-full max-w-full px-3 mx-auto lg:mx-0 shrink-0
            md:flex-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
            <div class="relative flex flex-col min-w-0 break-words 
            bg-transparent 
            border-[3px] border-[#b03a48] dark:border-[#a7363e]
            shadow-md p-6 rounded-2xl backdrop-blur-sm">





    <div class="p-6 pb-0 mb-0">
      <!-- Título claro y un poco más grande -->
      <h4 class="font-bold text-2xl text-gray-300">
        Bienvenidos al sistema de Emisión de Certificados
      </h4>
      <!-- Párrafo de subtítulo en gris más suave -->
      <p class="mb-4 text-gray-300">
        Introduce tu email y contraseña para iniciar sesión
      </p>
                <!-- Tour Button -->
                <button 
                  @click="restartTour" 
                  class="mt-2 px-3 py-1 text-xs text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none"
                >
                  Ver tutorial
                </button>
                
                <!-- Status Message -->
                <div v-if="status" class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                  {{ status }}
                </div>
              </div>
              
              <div class="flex-auto p-6">
                <form id="login-form" role="form" @submit.prevent="submit">
                  <!-- Email Field -->
                  <div class="mb-4">
                    <input 
                      id="email"
                      v-model="form.email"
                      type="email" 
                      placeholder="F_0006050@fdteulp.com" 
                      required
                      autofocus
                      autocomplete="username"
                      class="focus:shadow-primary-outline  dark:bg-gray-950  placeholder-gray-500 dark:placeholder-gray-400 text-gray-700 dark:text-gray-200 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal outline-none transition-all focus:border-fuchsia-300 focus:outline-none"
                      :class="{ 'border-red-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                  </div>
                  
                  <!-- Password Field -->
                  <div class="mb-4 relative">
                    <input 
                      id="password"
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'" 
                      placeholder="Ingrese número de carnet" 
                      required
                      autocomplete="current-password"
                      class="focus:shadow-primary-outline dark:bg-gray-950 placeholder-gray-500 dark:placeholder-gray-400  text-gray-700    dark:text-gray-200 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal outline-none transition-all focus:border-fuchsia-300 focus:outline-none"
                      :class="{ 'border-red-500': form.errors.password }"
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                    >
                      <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                  </div>
              
                  
                  <!-- Submit Button -->
                  <div class="text-center">
                    <button 
                      id="submitButton"
                      type="submit" 
                      class="inline-block w-full px-16 py-3.5 mt-6 mb-0 font-bold leading-normal text-center text-white align-middle transition-all bg-blue-500 border-0 rounded-lg cursor-pointer hover:-translate-y-px active:opacity-85 hover:shadow-xs text-sm ease-in tracking-tight-rem shadow-md bg-150 bg-x-25"
                      :disabled="form.processing"
                      :class="{ 'opacity-70': form.processing }"
                    >
                      <span v-if="form.processing">Iniciando sesión...</span>
                      <span v-else>Iniciar sesión</span>
                    </button>
                  </div>
                </form>
              </div>

            </div>
          </div>
          
          <!-- Right Side Image Column -->
          <div class="absolute top-0 right-0 flex-col justify-center hidden w-6/12 h-full max-w-full px-3 pr-0 my-auto text-center flex-0 lg:flex">
            <div 
              class="relative flex flex-col justify-center h-full bg-cover px-24 m-4 overflow-hidden bg-[url('/assets/img/logo-login.png')] rounded-xl"
            >
              <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-blue-500 to-violet-500 opacity-20"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>