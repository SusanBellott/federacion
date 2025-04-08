<script setup>
import Swal from "sweetalert2";
import { defineProps, watchEffect } from "vue";

const props = defineProps({
    data: {
        type: [Array, Object],
        required: true,
        validator: (value) => {
            // Valida que tenga los campos mínimos requeridos
            const item = Array.isArray(value) ? value[0] : value;
            return item;
        },
    },
    title: {
        type: String,
        default: "✓ Datos Registrados o Actualizados",
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

const emit = defineEmits(["closed"]);

const showAlert = () => {
    try {
        const item = Array.isArray(props.data) ? props.data[0] : props.data;
        //console.log("los ítems-------------------", item);

        // Construcción del HTML con un foreach
        let itemsHtml = "<div class='text-left p-6'>";
        Object.entries(item).forEach(([key, value]) => {
            itemsHtml += `<p class="mb-3 text-white"><strong class="font-bold">${key}:</strong> ${value}</p>`;
        });
        itemsHtml += "</div>";

        Swal.fire({
            title: props.title,
            html: itemsHtml,
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
        }).then(() => {
            emit("closed");
        });
    } catch (error) {
        console.error("Error al mostrar alerta:", error);
        Swal.fire({
            title: "Error",
            text: "Ocurrió un error al mostrar los datos",
            icon: "error",
        });
    }
};

// Muestra automáticamente al montar (opcional)
watchEffect(() => {
    if (props.data) {
        showAlert();
        props.data.value = null;
    }
});
</script>
<template></template>
