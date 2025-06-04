import axios from 'axios';

const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  console.log(`setting csrf token for axios headers`);
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
  console.log(`no csrf-token set in meta tag`);
}
// axios.defaults.withCredentials = true;

// axios.get('/sanctum/csrf-cookie'); // This sets XSRF-TOKEN cookie used by Laravel

import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
