<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <div>
        <div
            class="bg-gray-200 dark:bg-gray-800 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8"
        >
            <div>
                <p class="mt-4 text-sm">
                    <a
                        href="https://laravel.com/docs"
                        class="inline-flex items-center font-semibold text-indigo-700 dark:text-indigo-300"
                    >
                        Explore the documentation

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            class="ms-1 size-5 fill-indigo-500 dark:fill-indigo-200"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>
