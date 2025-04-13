<template>
  <footer class="pt-4">
    <div class="w-full px-6 mx-auto">
      <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
        <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
          <div class="text-sm leading-normal text-center text-slate-500 lg:text-left">
            © {{ currentYear }}, Desarrolado por <i class="fa fa-heart"></i> by
            <a href="#" class="font-semibold text-slate-700 dark:text-white"
              target="_blank">Susan Bellot</a>
              por una mejor web.
          </div>
        </div>
        <div class="w-full max-w-full px-3 mt-0 shrink-0 lg:w-1/2 lg:flex-none">
          <ul class="flex flex-wrap justify-center pl-0 mb-0 list-none lg:justify-end">
            <li class="nav-item" v-for="(link, index) in footerLinks" :key="index">
              <a :href="link.url"
                class="block px-4 pt-0 pb-1 text-sm font-normal transition-colors ease-in-out text-slate-500"
                target="_blank">{{ link.text }}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed } from 'vue';

const currentYear = computed(() => {
  return new Date().getFullYear();
});

const footerLinks = ref([
  { text: 'f', url: 'https://www.creative-tim.com' },
  { text: 'About Us', url: 'https://www.creative-tim.com/presentation' },
  { text: 'Blog', url: 'https://creative-tim.com/blog' },
  { text: 'License', url: 'https://www.creative-tim.com/license' }
]);
</script>