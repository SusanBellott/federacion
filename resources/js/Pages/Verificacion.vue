<template>
    <div class="min-h-screen flex items-center justify-center bg-white px-4 py-8">
      <div class="w-full max-w-2xl border p-8 shadow-lg rounded-lg text-center">
        <!-- Logos -->
        <div class="flex justify-center items-center gap-10 mb-6">
          <img src="/images/hola.png" alt="Logo" class="h-16" />
        </div>

        <!-- Título -->
        <h1 class="text-3xl font-bold text-white bg-green-600 inline-block px-6 py-2 rounded mb-8">
          CERTIFICACIÓN
        </h1>

        <!-- Información -->
        <div class="text-left text-gray-800 space-y-3">
          <p><span class="font-bold">CEDULA DE IDENTIDAD:</span> {{ inscrito.user.ci }}</p>
          <p><span class="font-bold">NOMBRE COMPLETO:</span> {{ inscrito.user.name }}</p>
          <p><span class="font-bold">CURSO INSCRITO:</span> {{ inscrito.curso.nombre}} </p>
          <p><span class="font-bold">CARGA HORARIA:</span> {{ inscrito.curso.carga_horaria}}</p>
        </div>

        <!-- Información adicional -->
        <div class="mt-10 text-sm text-gray-600">
          <p class="font-bold">Oficina Central:</p>
          <p>Calle Bolívar 724 Esq. Indaburo</p>
          <p>Correo Electrónico: admin@admin.com</p>
          <p>Teléfono: +591 2200353</p>
          <p>La Paz - Bolivia</p>
        </div>
      </div>
    </div>
</template>
<script setup>
  const props = defineProps({
    inscrito: Object,
});
console.log(props.incrito);

</script>
