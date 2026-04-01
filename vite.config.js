import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'Modules/DayCountCalculator/resources/assets/sass/app.scss',
                'Modules/DayCountCalculator/resources/assets/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
