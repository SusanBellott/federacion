<script setup>
import Swal from "sweetalert2";
import { defineProps, watchEffect, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();
const flash = computed(() => page.props.flash || {});
const props = defineProps({
    title: {
        type: String,
        default: "¡Todo fue un éxito!",
    },
    timer: {
        type: Number,
        default: 2000,
    },
    showButton: {
        type: Boolean,
        default: false,
    },
    buttonText: {
        type: String,
        default: "OK",
    },
});

const showSuccessAlert = () => {
    Swal.fire({
        title: props.title,
        icon: "success",
        showConfirmButton: props.showButton,
        confirmButtonText: props.buttonText,
        timer: props.timer,
        customClass: {
            popup: "bg-gradient-to-r from-emerald-600 to-teal-400 shadow-xl rounded-3xl p-8",
            title: "text-2xl font-bold text-white",
            htmlContainer: "text-lg text-gray-100 mt-2",
            icon: "text-white",
            confirmButton:
                "bg-white text-teal-600 px-4 py-2 rounded-lg hover:bg-gray-100",
        },
        showClass: {
            popup: "animate__animated animate__fadeInDown",
        },
        hideClass: {
            popup: "animate__animated animate__fadeOutUp",
        },
    });
};

// Exponer la función para usarla desde fuera
defineExpose({ showSuccessAlert });
watchEffect(() => {
    if (flash.value.editado) {
        showSuccessAlert();
    }

    /*  showSuccessAlert(); */
});
</script>

<template>
    <!-- Componente sin UI, solo lógica -->
</template>
