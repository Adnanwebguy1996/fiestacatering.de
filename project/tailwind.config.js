/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                'fiesta-blue': '#164FA1',
                'fiesta-orange': '#F57C00',
                'fiesta-dark': '#1A130C',
                'fiesta-light': '#FFFFFF',
                'fiesta-grey': '#625C56',
                'fiesta-bg-light': '#F5F5F5',
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                display: ['Outfit', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
