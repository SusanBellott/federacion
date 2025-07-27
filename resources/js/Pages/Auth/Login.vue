<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted } from "vue"; // Añadido onMounted
import { driver } from "driver.js"; // Importamos driver.js
import "driver.js/dist/driver.css"; // Importamos los estilos de driver.js

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
      captcha: '',
    remember: false,
});

// Estado para controlar la visibilidad de la contraseña
const showPassword = ref(false);
// Estado para controlar si el tour ya se ha mostrado
const tourShown = ref(false);

const captchaLoaded = ref(true) // controlamos el estado de carga
// Captcha
const captchaSrc = ref('/captcha?' + Date.now());

const reloadCaptcha = () => {
      captchaLoaded.value = false
  captchaSrc.value = '/custom-captcha?' + Date.now();

};



const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route('login'), {
    onFinish: () => form.reset('password'),
    onError: () => {
  reloadCaptcha();
  form.captcha = '';
},
    });
};

// Configuración del tour con Driver.js
const startTour = () => {
    if (tourShown.value) return;

    const driverObj = driver({
        showProgress: true,
        animate: true,
        stagePadding: 10,
        showButtons: ["next", "previous", "close"],
        steps: [
            {
                element: "#login-form",
                popover: {
                    title: "Bienvenido al Sistema de Certificación de la Federación Departamental de Trabajadores de Educación Urbana de La Paz (F.D.T.E.U.L.P.)",
                    description:
                        " Para ingresar al sistema, por favor sigue los siguientes pasos:",
                    side: "bottom",
                    align: "center",
                },
            },
            {
                element: "#email",
                popover: {
                    title: "Correo electrónico institucional",
                    description:
                        "Ingresa el correo electrónico institucional asignado. Por ejemplo: Federacion_RDA@fdteulp.com. F_0036054@fdteulp.com",
                    side: "bottom",
                    align: "start",
                },
            },
            {
                element: "#password",
                popover: {
                    title: "Contraseña",
                    description:
                        "Ingresa tu contraseña, que corresponde a tu número de carnet de identidad. Puedes mostrar u ocultar la contraseña haciendo clic en el ícono del ojo.",
                    side: "bottom",
                    align: "start",
                },
            },

            {
                element: "#submitButton",
                popover: {
                    title: "Iniciar sesión",
                    description:
                        "Haz clic para ingresar al sistema con tus credenciales",
                    side: "top",
                    align: "center",
                },
            },
        ],
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
     reloadCaptcha();
    // Pequeño retraso para asegurar que todos los elementos estén renderizados
    setTimeout(() => {
        //startTour();
    }, 500);
});
</script>

<template>
    <Head title="Sign In" />

    <section
        class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white relative overflow-hidden"
    >
        <!-- Elementos decorativos de fondo -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width=%2260%22%20height=%2260%22%20viewBox=%220%200%2060%2060%22%20xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg%20fill=%22none%22%20fill-rule=%22evenodd%22%3E%3Cg%20fill=%22%23ffffff%22%20fill-opacity=%220.03%22%3E%3Cpath%20d=%22M30%2030c0-11.046-8.954-20-20-20s-20%208.954-20%2020%208.954%2020%2020%2020%2020-8.954%2020-20zm0%200c0%2011.046%208.954%2020%2020%2020s20-8.954%2020-20-8.954-20-20-20-20%208.954-20%2020z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>

        
        <!-- Círculos decorativos -->
        <div class="absolute top-20 left-20 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-pulse"></div>

        <!-- Contenedor principal centrado -->
 <div class="relative flex items-center justify-center p-6 min-h-[600px]">

            <div class="w-full max-w-md mx-auto">
                <!-- Formulario de login centrado -->
                <div class="relative flex flex-col min-w-0 break-words bg-white/10 backdrop-blur-lg border border-white/20 shadow-2xl p-6 rounded-3xl">
                    <div class="pb-4 mb-4 border-b border-white/10">
                        <!-- Bloque del logo (arriba del título) -->
                        <div class="flex justify-center items-center gap-10 mb-2">
                            <img
                                src="/assets/img/logo_instituto.png"
                                alt="Logo"
                                class="h-20"
                            />
                        </div>

                        <!-- Título del formulario -->
                        <h4 class="font-bold text-xl text-white text-center mb-4">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Inicio de sesión
                            </span>
                        </h4>

                        <!-- Tour Button reducido -->
                        <div class="text-center">
                            <button
                                @click="restartTour"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-blue-500 to-purple-500 rounded-full hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-1 focus:ring-blue-500/50 transform hover:scale-105 transition-all duration-200 shadow-md"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Ver tutorial
                            </button>
                        </div>

                        <!-- Status Message mejorado -->
                        <div
                            v-if="status"
                            class="mt-4 p-3 bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 text-green-300 rounded-xl text-sm backdrop-blur-sm"
                        >
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ status }}
                            </div>
                        </div>
                    </div>

                    <div class="flex-auto">
                        <form
                            id="login-form"
                            role="form"
                            @submit.prevent="submit"
                            class="space-y-4"
                        >
                            <!-- Email Field mejorado -->
                            <div class="group">
                                <label for="email" class="block mb-2 text-sm font-semibold text-gray-200">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Introduce tu correo institucional
                                    </span>
                                </label>
                                <div class="relative">
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="F_0006050@fdteulp.com"
                                        required
                                        autofocus
                                        autocomplete="username"
                                        class="w-full px-4 py-2.5 text-white placeholder-gray-400 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-200 hover:bg-white/15"
                                        :class="{
                                            'border-red-500 focus:ring-red-500/50': form.errors.email,
                                        }"
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fi fi-sr-at text-blue-400 text-lg"></i>
                                    </div>
                                </div>
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 text-sm text-red-400 flex items-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Password Field mejorado -->
                            <div class="group">
                                <label for="password" class="block mb-2 text-sm font-semibold text-gray-200">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Introduce tu contraseña
                                    </span>
                                </label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        placeholder="Ingrese número de carnet"
                                        required
                                        autocomplete="current-password"
                                        class="w-full px-4 py-2.5 pr-12 text-white placeholder-gray-400 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-200 hover:bg-white/15"
                                        :class="{
                                            'border-red-500 focus:ring-red-500/50': form.errors.password,
                                        }"
                                    />
                                    <!-- Botón con íconos Flaticon -->
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white focus:outline-none transition-colors duration-200"
                                    >
                                        <i
                                            :class="showPassword 
                                                ? 'fi fi-sr-eye text-blue-400 text-lg' 
                                                : 'fi fi-sr-eye-crossed text-blue-400 text-lg'"
                                        ></i>
                                    </button>
                                </div>
                                <p
                                    v-if="form.errors.password"
                                    class="mt-1 text-sm text-red-400 flex items-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Remember Me checkbox mejorado -->
                            <div class="flex items-center group">
                                <div class="relative">
                                    <input 
                                        v-model="form.remember" 
                                        type="checkbox" 
                                        id="remember" 
                                        class="w-4 h-4 text-blue-500 bg-white/10 border-white/20 rounded focus:ring-blue-500/50 focus:ring-2 transition-all duration-200"
                                    />
                                </div>
                                <label for="remember" class="ml-3 text-sm text-gray-200 cursor-pointer group-hover:text-white transition-colors duration-200">
                                    Recuérdame
                                </label>
                            </div>

                            <!-- Captcha section mejorada -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-200">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-6l-3 3m-3-3l3 3m-3-3v6"/>
                                        </svg>
                                        Verificación de seguridad
                                    </span>
                                </label>
                                
                                <div class="flex items-center gap-3">
                                    <div class="bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl p-2 flex items-center justify-center overflow-hidden shadow-lg">
                                        <img 
                                            :src="captchaSrc" 
                                            alt="captcha"  
                                            class="w-[120px] h-[35px] object-contain transition-opacity duration-200" 
                                            @load="captchaLoaded = true" 
                                            @error="captchaLoaded = false"  
                                        />
                                    </div>
                                    
                                    <button
                                        type="button"
                                        @click="reloadCaptcha"
                                        class="p-2 w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500/50"
                                    >
                                        <i class="fi fi-sr-refresh text-white text-sm"></i>
                                    </button>
                                </div>

                                <input
                                    v-model="form.captcha"
                                    type="text"
                                    maxlength="6"
                                    placeholder="Ingrese el código"
                                    autocomplete="off"
                                    class="w-full px-4 py-2.5 text-white placeholder-gray-400 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all duration-200 hover:bg-white/15"
                                    @input="form.captcha = form.captcha.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6)"
                                />
                                
                                <p 
                                    v-if="form.errors.captcha" 
                                    class="text-sm text-red-400 flex items-center gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ form.errors.captcha }}
                                </p>
                            </div>

                            <!-- Submit Button mejorado -->
                            <div class="pt-2">
                                <button
                                    id="submitButton"
                                    type="submit"
                                    class="w-full py-2 px-2 font-bold text-white bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-xl hover:from-blue-600 hover:via-purple-600 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transform hover:scale-[1.02] transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing" class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Iniciando sesión...
                                    </span>
                                    <span v-else class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3-3H6a3 3 0 01-3 3v1"/>
                                        </svg>
                                        Iniciar sesión
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>