import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['"Noto Serif"', '"Times New Roman"', 'Times', 'serif'],
                chinese: ['"Noto Serif SC"', '"Source Han Serif SC"', 'STKaiti', 'KaiTi', 'serif'],
            },
        },
    },

    plugins: [forms],
};
