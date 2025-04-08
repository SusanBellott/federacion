<script setup>
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    routeName: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        default: "¿Estás seguro de realizar esta acción?",
    },
    method: {
        type: String,
        default: "put",
    },
});

const show = (id, cod, texto) => {
    Swal.fire({
        title: texto,
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, confirmar",
        cancelButtonText: "Cancelar",
        customClass: {
            popup: "bg-gradient-to-r from-red-500 to-yellow-500 shadow-xl rounded-3xl p-8",
            title: "text-2xl font-bold text-white ",
            text: "text-lg text-gray-200 mt-2",
            confirmButton:
                "bg-white text-red-500 hover:bg-red-100 font-semibold py-2 px-6 rounded-full",
            cancelButton:
                "bg-white text-gray-500 hover:bg-gray-100 font-semibold py-2 px-6 rounded-full",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Enviar como parámetro en la URL para PUT
            router[props.method](route(props.routeName, { id: id, cod: cod }));
        }
    });
};

defineExpose({ show });
</script>
<template></template>
