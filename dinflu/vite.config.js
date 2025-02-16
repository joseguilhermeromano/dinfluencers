import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react'; // Adicione o plugin do React

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'], // Altere para .tsx
            refresh: true,
        }),
        react(), // Adicione o plugin do React
    ],
    resolve: {
        alias: {
            '@': '/resources/js', // Alias para importações
        },
    },
});
