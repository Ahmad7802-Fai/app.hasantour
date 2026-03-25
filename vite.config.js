import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST ?? '127.0.0.1',
        port: Number(process.env.VITE_PORT ?? 5173),
        strictPort: true,
    },

    plugins: [
        laravel({
            input: [
                // =====================
                // GLOBAL
                // =====================
                'resources/scss/app.scss',

                // =====================
                // WEBSITE (🔥 LANDING PAGE)
                // =====================
                'resources/scss/website/website.scss',

                // =====================
                // JAMAAH
                // =====================
                'resources/scss/jamaah/jamaah.scss',

                // =====================
                // BACKOFFICE
                // =====================
                'resources/scss/cabang.scss',
                'resources/scss/agent.scss',

                // =====================
                // JS
                // =====================
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});

// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     server: {
//         host: process.env.VITE_HOST ?? '127.0.0.1',
//         port: Number(process.env.VITE_PORT ?? 5173),
//         strictPort: true,
//     },

//     plugins: [
//         laravel({
//             input: [
//                 // DEFAULT
//                 'resources/scss/app.scss',

//                 // ADMIN
//                 // 'resources/scss/admin-f4.scss',

//                 // JAMAAH (🔥 WAJIB)
//                 'resources/scss/jamaah/jamaah.scss',
//                 // CABANG
//                 'resources/scss/cabang.scss',
//                 'resources/scss/agent.scss',
//                 // JS
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
// });
