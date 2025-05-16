import './bootstrap';
import '../css/app.css';
// import '../css/nucleo-icons.css';
// import '../css/nucleo-svg.css';
import '../css/argon-dashboard-tailwind.css?v=1.0.1';
// import '@fortawesome/fontawesome-free/css/all.min.css';


import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import route from './ziggy'; // Este es el archivo generado con artisan
import { ZiggyVue } from 'ziggy-js'; // Este viene desde npm (y sí exporta ZiggyVue)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

document.addEventListener('DOMContentLoaded', () => {
    // Carga los scripts externos de la plantilla
    const scripts = [
        // 'assets/js/plugins/chartjs.min.js',
        '/assets/js/plugins/perfect-scrollbar.min.js',
        '/assets/js/argon-dashboard-tailwind.js?v=1.0.1',
        '/assets/js/navbar-sticky.js'
       
    ];
    
    scripts.forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        document.body.appendChild(script);
    });
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, { Ziggy: route }) 
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
