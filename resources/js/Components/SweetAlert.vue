<script setup>
import Swal from "sweetalert2";
import { defineProps, defineEmits, onMounted, watch } from "vue";

const props = defineProps({
    data: { type: Object, required: true },
    action: { type: String, default: "actualizado" },
    timer: { type: Number, default: 2000 },
});
const emit = defineEmits(["closed"]);

let alreadyShown = false;

const showAlert = () => {
    let mensaje = "✓ Acción completada.";

    switch (props.action) {
        case "creado":
            mensaje = "✓ Dato creado correctamente.";
            break;
        case "actualizado":
            mensaje = "✓ Datos actualizados correctamente.";
            break;
        case "eliminado":
            mensaje = "✓ Dato eliminado correctamente.";
            break;
    }

    Swal.fire({
        position: 'top-end',
        html: mensaje,
        icon: props.action === "eliminado" ? "warning" : "success",
        showConfirmButton: false,
        timer: props.timer,
        customClass: {
            popup: "bg-gradient-to-r from-emerald-600 to-teal-400 shadow-xl rounded-2xl px-6 py-4 w-auto max-w-sm",
            htmlContainer: "text-white text-left text-sm",
            icon: "text-white",
        },
        showClass: {
            popup: "animate__animated animate__fadeInRight",
        },
        hideClass: {
            popup: "animate__animated animate__fadeOutRight",
        },
    }).then(() => emit("closed"));
};

onMounted(() => {
    watch(
        () => props.data,
        (newVal) => {
            if (newVal && !alreadyShown) {
                showAlert();
                alreadyShown = true;
            }
        },
        { immediate: true }
    );
});
</script>

<template></template>
