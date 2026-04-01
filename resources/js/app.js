import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const guestPathPatterns = [
    /^\/$/,
    /^\/login$/,
    /^\/register$/,
    /^\/forgot-password$/,
    /^\/reset-password\/[^/]+$/,
];

const isGuestPath = (pathname) => guestPathPatterns.some((pattern) => pattern.test(pathname));
const isBackForwardNavigation = () => performance.getEntriesByType('navigation')[0]?.type === 'back_forward';

const reloadGuestPagesOnHistoryNavigation = () => {
    if (isGuestPath(window.location.pathname)) {
        window.location.reload();
    }
};

window.addEventListener('pageshow', (event) => {
    if (event.persisted || isBackForwardNavigation()) {
        reloadGuestPagesOnHistoryNavigation();
    }
});

window.addEventListener('popstate', reloadGuestPagesOnHistoryNavigation);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
