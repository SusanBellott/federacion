<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Desactivamos la herencia automática de atributos
defineOptions({
  inheritAttrs: false,
});

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
  form.delete(route('current-user.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.reset();
};
</script>

<template>
  <!-- Contenedor raíz que heredará los atributos (como class) -->
  <div v-bind="$attrs">
    <!-- Contenido principal -->
    <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
      <!-- Header -->
      <div class="p-6 px-4 pb-0 mb-0 border-b-0 rounded-t-2xl">
        <h6 class="mb-0 text-slate-700 dark:text-white/80">Eliminar cuenta</h6>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Eliminar permanentemente su cuenta.</p>
      </div>

      <!-- Content -->
      <div class="flex-auto p-4 pt-6">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
          Una vez eliminada su cuenta, todos sus recursos y datos se eliminarán permanentemente.
Antes de eliminar su cuenta, descargue cualquier dato o información que desee conservar.
        </div>

        <!-- Delete Button -->
        <div class="mt-5">
          <button
            @click="confirmUserDeletion"
            class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-red-600 to-orange-600"
          >
          Eliminar cuenta
          </button>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal content -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-850 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-slate-850 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg font-medium text-slate-700 dark:text-white/80 mb-4">Eliminar cuenta</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              ¿Seguro que quieres eliminar tu cuenta? Una vez eliminada, todos sus recursos y datos se eliminarán permanentemente. Introduce tu contraseña para confirmar que deseas eliminar tu cuenta permanentemente.
            </p>
            
            <div class="mt-4">
              <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Contraseña</label>
              <input
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                placeholder="Enter your password"
                autocomplete="current-password"
                @keyup.enter="deleteUser"
              />
              <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
            </div>
          </div>
          
          <div class="bg-gray-50 dark:bg-slate-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="deleteUser"
              class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-red-600 to-orange-600"
              :class="{ 'opacity-50': form.processing }"
              :disabled="form.processing"
            >
            Eliminar cuenta
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
  </div>
</template>