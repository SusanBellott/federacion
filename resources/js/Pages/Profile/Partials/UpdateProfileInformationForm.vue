<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    // Asegurarse de que el archivo se adjunte correctamente
    if (photoInput.value?.files[0]) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => {
            clearPhotoFileInput();
            // Resetear la vista previa después de guardar
            photoPreview.value = null;
        },
        // Forzar el envío como multipart/form-data
        forceFormData: true,
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value?.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>

    <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
      <!-- Cover Image -->
      <img class="w-full rounded-t-2xl" src="/assets/img/bg-profile.jpg" alt="profile cover image">
      
      <!-- Profile Photo Section -->
      <div class="flex flex-wrap justify-center -mx-3">
        <div class="w-4/12 max-w-full px-3 flex-0">
          <div class="mb-6 -mt-6 lg:mb-0 lg:-mt-16">
            <!-- Profile Photo Input (hidden) -->
            <input
              id="photo"
              ref="photoInput"
              type="file"
              class="hidden"
              @change="updatePhotoPreview"
            >
            
            <!-- Current Profile Photo -->
            <a href="javascript:;" @click.prevent="selectNewPhoto">
              <img 
                v-if="!photoPreview"
                :src="user.profile_photo_url" 
                :alt="user.name" 
                class="h-auto max-w-full border-2 border-white border-solid rounded-circle"
              >
              
              <!-- New Profile Photo Preview -->
              <img
                v-else
                :src="photoPreview"
                :alt="user.name"
                class="h-auto max-w-full border-2 border-white border-solid rounded-circle"
              >
            </a>
            
            <!-- Error Message -->
            <p v-if="form.errors.photo" class="mt-2 text-xs text-red-500 text-center">
              {{ form.errors.photo }}
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="border-black/12.5 rounded-t-2xl p-6 text-center pt-0 pb-6 lg:pt-2 lg:pb-4">
        <div class="flex justify-between">
          <button 
            type="button" 
            class="hidden px-8 py-2 font-bold leading-normal text-center text-white align-middle transition-all ease-in border-0 rounded-lg shadow-md cursor-pointer text-xs bg-cyan-500 lg:block tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85"
            @click="updateProfileInformation"
            :disabled="form.processing"
          >
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Guardar Cambios</span>
          </button>
          
          <button 
            type="button" 
            class="block px-8 py-2 font-bold leading-normal text-center text-white align-middle transition-all ease-in border-0 rounded-lg shadow-md cursor-pointer text-xs bg-cyan-500 lg:hidden tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85"
            @click="updateProfileInformation"
            :disabled="form.processing"
          >
            <i class="ni ni-check-bold text-2.8"></i>
          </button>
          
          <button 
            v-if="user.profile_photo_path"
            type="button" 
            class="hidden px-8 py-2 font-bold leading-normal text-center text-white align-middle transition-all ease-in border-0 rounded-lg shadow-md cursor-pointer text-xs bg-slate-700 lg:block tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85"
            @click.prevent="deletePhoto"
          >
            Eliminar Foto
          </button>
          
          <button 
            v-if="user.profile_photo_path"
            type="button" 
            class="block px-8 py-2 font-bold leading-normal text-center text-white align-middle transition-all ease-in border-0 rounded-lg shadow-md cursor-pointer text-xs bg-slate-700 lg:hidden tracking-tight-rem hover:shadow-xs hover:-translate-y-px active:opacity-85"
            @click.prevent="deletePhoto"
          >
            <i class="ni ni-fat-remove text-2.8"></i>
          </button>
        </div>
      </div>

      <!-- Profile Stats -->
      <div class="flex-auto p-6 pt-0">
        <!-- Profile Information Form -->
        <div class="mt-6">
          <!-- Name Field -->
          <div class="mb-4">
            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Nombre</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
              required
              autocomplete="name"
              :class="{ 'border-red-500': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
          </div>

          <!-- Email Field -->
          <div class="mb-4">
            <label class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Correo</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
              required
              autocomplete="email"
              :class="{ 'border-red-500': form.errors.email }"
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>

            <!-- Email Verification -->
            <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null" class="mt-2">
              <p class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">
                Su dirección de correo electrónico no está verificada.
                <Link
                  :href="route('verification.send')"
                  method="post"
                  as="button"
                  class="text-indigo-600 dark:text-indigo-400 hover:underline"
                  @click.prevent="sendEmailVerification"
                >
                Haga clic aquí para volver a enviar el correo electrónico de verificación.
                </Link>
              </p>
              <p v-show="verificationLinkSent" class="mt-1 text-sm text-green-600 dark:text-green-400">
                Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.
              </p>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="form.recentlySuccessful" class="mt-2 text-sm text-green-600 dark:text-green-400">
            Profile updated successfully.
          </div>
        </div>
      </div>
    </div>
</template>