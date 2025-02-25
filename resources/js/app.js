import './bootstrap';
import '../scss/main.scss';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createVfm } from 'vue-final-modal';
import Layout from './Layouts/Layout.vue';
import InputButton from './Components/Inputs/Button.vue';

const appName = import.meta.env.VITE_APP_NAME || 'EGP Congress';
const vfm = createVfm()

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"));
        page.default.layout ??= Layout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        app.use(plugin)
            .use(ZiggyVue)
            .use(vfm)
            .mixin({ components: { InputButton } })
            .mount(el);
        
        return app;
    },
    progress: {
        color: '#4B5563',
    },
});
