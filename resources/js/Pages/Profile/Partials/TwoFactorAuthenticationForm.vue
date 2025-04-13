<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);
const error = ref(null); // Nuevo: Para manejo de errores

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;
    error.value = null; // Resetear error

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            return Promise.all([
                showQrCode().catch(handleError),
                showSetupKey().catch(handleError),
                showRecoveryCodes().catch(handleError)
            ]).catch(err => {
                console.error('Error fetching 2FA data:', err);
                error.value = 'Failed to load 2FA data. Please try again.';
            });
        },
        onError: (errors) => {
            error.value = errors.message || 'Failed to enable 2FA';
        },
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const handleError = (err) => {
    if (err.response?.status === 423) {
        error.value = 'Please verify your account or confirm your password before enabling 2FA';
    } else {
        error.value = err.response?.data?.message || err.message || 'An error occurred';
    }
    throw err; // Re-lanzar para manejo superior
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code'), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(response => {
        qrCode.value = response.data.svg;
    }).catch(handleError);
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key'), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(response => {
        setupKey.value = response.data.secretKey;
    }).catch(handleError);
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes'), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    }).then(response => {
        recoveryCodes.value = response.data;
    }).catch(handleError);
};

const confirmTwoFactorAuthentication = () => {
    error.value = null;
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
        onError: (errors) => {
            error.value = errors.code || 'Invalid verification code';
        },
    });
};

const regenerateRecoveryCodes = () => {
    error.value = null;
    axios.post(route('two-factor.recovery-codes'), {}, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(() => showRecoveryCodes())
    .catch(handleError);
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;
    error.value = null;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
            recoveryCodes.value = [];
        },
        onError: (errors) => {
            error.value = errors.message || 'Failed to disable 2FA';
            disabling.value = false;
        },
    });
};
</script>

<template>
    <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-xl dark:bg-slate-850 dark:shadow-dark-xl rounded-2xl bg-clip-border">
      <div class="border-black/12.5 rounded-t-2xl border-b-0 border-solid p-6 pb-0">
        <div class="flex items-center">
          <p class="mb-0 text-gray-700 dark:text-white dark:opacity-80">Autenticación de dos factores</p>
        </div>
      </div>
      
      <div class="flex-auto p-6">
        <h3 v-if="twoFactorEnabled && !confirming" class="text-lg font-medium  text-slate-700 dark:text-white/80">
          Ha habilitado la autenticación de dos factores.
        </h3>

        <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium  text-slate-700 dark:text-white/80">
          Terminar de habilitar la autenticación de dos factores.
        </h3>

        <h3 v-else class="text-lg font-medium  text-slate-700 dark:text-white/80">
          No ha habilitado la autenticación de dos factores.
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600 dark:text-gray-400">
          <p>
            Cuando la autenticación de dos factores esté habilitada, se te solicitará un token aleatorio y seguro durante la autenticación. Puedes obtener este token desde la aplicación Google Authenticator de tu teléfono.
          </p>
        </div>

        <div v-if="twoFactorEnabled">
          <div v-if="qrCode">
            <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
              <p v-if="confirming" class="font-semibold">
                Para terminar de habilitar la autenticación de dos factores, escanee el siguiente código QR con la aplicación de autenticación de su teléfono o ingrese la clave de configuración y proporcione el código OTP generado.
              </p>

              <p v-else>
                La autenticación de dos factores ya está habilitada. Escanea el siguiente código QR con la aplicación de autenticación de tu teléfono o introduce la clave de configuración.
              </p>
            </div>

            <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode" />

            <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
              <p class="font-semibold">
                Clave de configuración: <span v-html="setupKey"></span>
              </p>
            </div>

            <div v-if="confirming" class="mt-4">
              <label for="code" class="inline-block mb-2 ml-1 font-bold text-xs text-slate-700 dark:text-white/80">Código de verificación</label>
              <input
                id="code"
                v-model="confirmationForm.code"
                type="text"
                name="code"
                class="focus:shadow-primary-outline dark:bg-slate-850 dark:text-white text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-blue-500 focus:outline-none"
                inputmode="numeric"
                autofocus
                autocomplete="one-time-code"
                @keyup.enter="confirmTwoFactorAuthentication"
              />
              <p v-if="confirmationForm.errors.code" class="mt-1 text-xs text-red-500">{{ confirmationForm.errors.code }}</p>
            </div>
          </div>

          <div v-if="recoveryCodes.length > 0 && !confirming">
            <div class="mt-4 max-w-xl text-sm text-gray-600 dark:text-gray-400">
              <p class="font-semibold">
                Guarda estos códigos de recuperación en un gestor de contraseñas seguro. Pueden usarse para recuperar el acceso a tu cuenta si pierdes tu dispositivo de autenticación de dos factores.
              </p>
            </div>

            <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 dark:text-gray-100 rounded-lg">
              <div v-for="code in recoveryCodes" :key="code">
                {{ code }}
              </div>
            </div>
          </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
          <div v-if="!twoFactorEnabled">
            <button
              type="button"
              @click="enableTwoFactorAuthentication"
              class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-blue-500 to-violet-500"
              :class="{ 'opacity-25': enabling }"
              :disabled="enabling"
            >
              <span v-if="enabling">Habilitando...</span>
              <span v-else>Habilitar la autenticación de dos factores</span>
            </button>
          </div>

          <div v-else class="flex flex-wrap gap-3">
            <button
              v-if="confirming"
              type="button"
              @click="confirmTwoFactorAuthentication"
              class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-blue-500 to-violet-500"
              :class="{ 'opacity-25': enabling }"
              :disabled="enabling"
            >
              Confirmar
            </button>

            <button
              v-if="recoveryCodes.length > 0 && !confirming"
              type="button"
              @click="regenerateRecoveryCodes"
              class="px-6 py-2.5 font-bold text-center text-gray-700 uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gray-200 dark:bg-gray-700 dark:text-white"
            >
            Regenerar códigos de recuperación
            </button>

            <button
              v-if="recoveryCodes.length === 0 && !confirming"
              type="button"
              @click="showRecoveryCodes"
              class="px-6 py-2.5 font-bold text-center text-gray-700 uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gray-200 dark:bg-gray-700 dark:text-white"
            >
            Mostrar códigos de recuperación
            </button>

            <button
              v-if="confirming"
              type="button"
              @click="disableTwoFactorAuthentication"
              class="px-6 py-2.5 font-bold text-center text-gray-700 uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gray-200 dark:bg-gray-700 dark:text-white"
              :class="{ 'opacity-25': disabling }"
              :disabled="disabling"
            >
              Cancelar
            </button>

            <button
              v-if="!confirming"
              type="button"
              @click="disableTwoFactorAuthentication"
              class="px-6 py-2.5 font-bold text-center text-white uppercase align-middle transition-all rounded-lg cursor-pointer hover:shadow-xs hover:-translate-y-px active:opacity-85 leading-pro text-xs ease-in tracking-tight-rem shadow-md bg-gradient-to-tl from-red-500 to-pink-500"
              :class="{ 'opacity-25': disabling }"
              :disabled="disabling"
            >
            Desactivar el doble factor
            </button>
          </div>
        </div>
      </div>
    </div>
</template>