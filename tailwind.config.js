/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#2563eb',
                secondary: '#0ea5e9',
                accent: '#f59e0b',
            },
        },
    },
    plugins: [],
};
