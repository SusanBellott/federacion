<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <Head title="Sign In" />
  
  <section>
    <div class="relative flex items-center min-h-screen p-0 overflow-hidden bg-center bg-cover">
      <div class="container z-1">
        <div class="flex flex-wrap -mx-3">
          <!-- Form Column -->
          <div class="flex flex-col w-full max-w-full px-3 mx-auto lg:mx-0 shrink-0 md:flex-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
            <div class="relative flex flex-col min-w-0 break-words bg-transparent border-0 shadow-none lg:py4 dark:bg-gray-950 rounded-2xl bg-clip-border">
              <div class="p-6 pb-0 mb-0">
                <h4 class="font-bold">Bienvenidos Al sistema de Emicion de Certificados</h4>
                <p class="mb-0">Introduce tu email y contraseña para iniciar sesión</p>
                
                <!-- Status Message -->
                <div v-if="status" class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                  {{ status }}
                </div>
              </div>
              
              <div class="flex-auto p-6">
                <form role="form" @submit.prevent="submit">
                  <!-- Email Field -->
                  <div class="mb-4">
                    <input 
                      id="email"
                      v-model="form.email"
                      type="email" 
                      placeholder="Email" 
                      required
                      autofocus
                      autocomplete="username"
                      class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                      :class="{ 'border-red-500': form.errors.email }"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                  </div>
                  
                  <!-- Password Field -->
                  <div class="mb-4">
                    <input 
                      id="password"
                      v-model="form.password"
                      type="password" 
                      placeholder="Password" 
                      required
                      autocomplete="current-password"
                      class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"
                      :class="{ 'border-red-500': form.errors.password }"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                  </div>
                  
                  <!-- Remember Me -->
                  <div class="flex items-center pl-12 mb-0.5 text-left min-h-6">
                    <input 
                      id="rememberMe" 
                      v-model="form.remember"
                      class="mt-0.5 rounded-10 duration-250 ease-in-out after:rounded-circle after:shadow-2xl after:duration-250 checked:after:translate-x-5.3 h-5 relative float-left -ml-12 w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-zinc-700/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-blue-500/95 checked:bg-blue-500/95 checked:bg-none checked:bg-right" 
                      type="checkbox" 
                    />
                    <label class="ml-2 font-normal cursor-pointer select-none text-sm text-slate-700" for="rememberMe">Recordar</label>
                  </div>
                  
                  <!-- Forgot Password Link -->
                  <div v-if="canResetPassword" class="text-right mb-4">
                    <Link 
                      :href="route('password.request')" 
                      class="text-xs font-semibold text-transparent bg-clip-text bg-gradient-to-tl from-blue-500 to-violet-500 hover:underline"
                    >
                    ¿Has olvidado tu contraseña?
                    </Link>
                  </div>
                  
                  <!-- Submit Button -->
                  <div class="text-center">
                    <button 
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
              
              <!-- Sign Up Link -->
              <div class="border-black/12.5 rounded-b-2xl border-t-0 border-solid p-6 text-center pt-0 px-1 sm:px-6">
                <p class="mx-auto mb-6 leading-normal text-sm">
                  ¿No tienes una cuenta?
                  <Link :href="route('register')" class="font-semibold text-transparent bg-clip-text bg-gradient-to-tl from-blue-500 to-violet-500 hover:underline">
                    Sign up
                  </Link>
                </p>
              </div>
            </div>
          </div>
          
          <!-- Right Side Image Column -->
          <div class="absolute top-0 right-0 flex-col justify-center hidden w-6/12 h-full max-w-full px-3 pr-0 my-auto text-center flex-0 lg:flex">
            <div 
  class="relative flex flex-col justify-center h-full bg-cover px-24 m-4 overflow-hidden bg-[url('/assets/img/logo-login.png')] rounded-xl">
              <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-blue-500 to-violet-500 opacity-20"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>