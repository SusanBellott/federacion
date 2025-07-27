<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const showCurrentPassword = ref(false);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

// Verificar si el tema es oscuro (esto es un ejemplo, ajústalo según cómo manejas tu tema)
const isDarkMode = computed(() => {
    // Puedes usar el localStorage, una store de Pinia/Vuex, o verificar una clase en el HTML
    // Esta es una forma básica de detectarlo por una clase en el documento
    return document.documentElement.classList.contains("dark");
});

const showSweetAlert = (options) => {
    // Configuración base para SweetAlert2
    const baseConfig = {
        confirmButtonColor: "#3085d6",
        customClass: {
            // Aplicar clases para modo oscuro si es necesario
            popup: isDarkMode.value ? "swal-dark-theme" : "",
            title: isDarkMode.value ? "swal-dark-title" : "",
            content: isDarkMode.value ? "swal-dark-content" : "",
            confirmButton: "swal-confirm-button",
        },
    };

    // Combinar con las opciones específicas
    Swal.fire({
        ...baseConfig,
        ...options,
    });
};

const updatePassword = () => {
    form.put(route("user-password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Mostrar SweetAlert2 cuando la actualización sea exitosa
            showSweetAlert({
                title: "¡Éxito!",
                text: "Tu contraseña ha sido actualizada correctamente",
                icon: "success",
                confirmButtonText: "Aceptar",
                timer: 3000,
                timerProgressBar: true,
            });
        },
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();

                // Mostrar SweetAlert2 para error de contraseña actual
                showSweetAlert({
                    title: "¡Error!",
                    text: form.errors.current_password,
                    icon: "error",
                    confirmButtonText: "Intentar de nuevo",
                    confirmButtonColor: "#d33",
                });
            }
        },
    });
};
</script>

<template>
    <div
        class="relative flex flex-col min-w-0 break-words bg-gradient-to-br from-violet-900 to-indigo-900 border-0 shadow-xl dark:shadow-dark-xl rounded-2xl bg-clip-border"
    >
        <div class="flex-auto p-6">
            <hr
                class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent"
            />

            <!-- Sección de Actualización de Contraseña -->
            <p
                class="leading-normal uppercase text-gray-700 dark:text-white dark:opacity-80 text-sm"
            >
                Actualizar contraseña
            </p>
            <div class="flex flex-wrap -mx-3">
                <div
                    class="w-full max-w-full px-3 shrink-0 md:w-full md:flex-0"
                >
                    <form @submit.prevent="updatePassword">
                        <!-- Contraseña Actual -->
                        <div class="mb-4 relative">
                            <label
                                for="current_password"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Contraseña Actual
                            </label>
                            <div class="relative">
                                <input
                                    id="current_password"
                                    ref="currentPasswordInput"
                                    v-model="form.current_password"
                                    :type="
                                        showCurrentPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    autocomplete="current-password"
                                    class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none pr-10"
                                    :class="{
                                        'border-red-500':
                                            form.errors.current_password,
                                    }"
                                />

                                <!-- Icono mostrar/ocultar -->
                                <span
                                    class="absolute right-3 top-2.5 cursor-pointer"
                                    @click="
                                        showCurrentPassword =
                                            !showCurrentPassword
                                    "
                                >
                                    <i
                                        :class="
                                            showCurrentPassword
                                                ? 'fi fi-sr-eye text-blue-400 text-lg'
                                                : 'fi fi-sr-eye-crossed text-blue-400 text-lg'
                                        "
                                    ></i>
                                </span>
                            </div>
                            <p
                                v-if="form.errors.current_password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.current_password }}
                            </p>
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="mb-4 relative">
                            <label
                                for="password"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Nueva Contraseña
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    ref="passwordInput"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
                                    class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none pr-10"
                                    :class="{
                                        'border-red-500': form.errors.password,
                                    }"
                                />

                                <!-- Icono mostrar/ocultar -->
                                <span
                                    class="absolute right-3 top-2.5 cursor-pointer"
                                    @click="showPassword = !showPassword"
                                >
                                    <i
                                        :class="
                                            showPassword
                                                ? 'fi fi-sr-eye text-blue-400 text-lg'
                                                : 'fi fi-sr-eye-crossed text-blue-400 text-lg'
                                        "
                                    ></i>
                                </span>
                            </div>
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="mb-4 relative">
                            <label
                                for="password_confirmation"
                                class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80"
                            >
                                Confirmar Contraseña
                            </label>
                            <div class="relative">
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :type="
                                        showPasswordConfirm
                                            ? 'text'
                                            : 'password'
                                    "
                                    autocomplete="new-password"
                                    class="focus:shadow-primary-outline bg-violet-950 text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-violet-700 bg-clip-padding px-3 py-2 font-normal outline-none transition-all placeholder:text-violet-300 focus:border-blue-400 focus:outline-none pr-10"
                                    :class="{
                                        'border-red-500':
                                            form.errors.password_confirmation,
                                    }"
                                />

                                <!-- Icono mostrar/ocultar -->
                                <span
                                    class="absolute right-3 top-2.5 cursor-pointer"
                                    @click="
                                        showPasswordConfirm =
                                            !showPasswordConfirm
                                    "
                                >
                                    <i
                                        :class="
                                            showPasswordConfirm
                                                ? 'fi fi-sr-eye text-blue-400 text-lg'
                                                : 'fi fi-sr-eye-crossed text-blue-400 text-lg'
                                        "
                                    ></i>
                                </span>
                            </div>
                            <p
                                v-if="form.errors.password_confirmation"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password_confirmation }}
                            </p>
                        </div>

                        <!-- Botón de Guardar -->
                        <div class="text-right">
                            <button
                                type="submit"
                                class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-blue-500 to-violet-500"
                                :disabled="form.processing"
                                :class="{ 'opacity-50': form.processing }"
                            >
                                <span v-if="form.processing"
                                    >Actualizando...</span
                                >
                                <span v-else>Actualizar contraseña</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Estilos para SweetAlert2 en modo oscuro */
.swal-dark-theme {
    background-color: #1e293b !important; /* Similar a slate-850 */
    color: white !important;
}

.swal-dark-title {
    color: white !important;
}

.swal-dark-content {
    color: rgba(255, 255, 255, 0.8) !important;
}

/*.swal-confirm-button {
    /* Estilos personalizados para el botón si los necesitas 
}*/
</style>
