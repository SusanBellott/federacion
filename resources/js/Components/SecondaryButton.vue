<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition duration-200">
        <slot />
    </button>
</template>
