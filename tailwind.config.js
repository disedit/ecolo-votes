import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        colors: {
            'transparent': 'transparent',
            'green': {
                dark: '#0F8A54',
                pine: '#0B3323',
                DEFAULT: '#47B972'
            },
            'gray': {
                50: '#fafafa',
                100: '#f4f2f5',
                200: '#e9e6ea',
                300: '#dbd6dc',
                400: '#c3bbc5',
                500: '#aa9fad',
                600: '#938796',
                700: '#7d717f',
                800: '#685f6a',
                900: '#544d56',
                950: '#38313a',
            },
            'white': '#fff',
            'black': '#282828',
            'yellow': '#FFDC2E',
            'pink': '#F6ADCD',
            'purple': '#6950A1',
            'red': '#fc574a',
            'orange': '#feae37',
            'blue': '#00a5ec'
        },
        fontSize: {
            'xs': 'var(--text-xs)',
            'sm': 'var(--text-sm)',
            'rbase': '1rem',
            'base': 'var(--text-base)',
            'md': 'var(--text-md)',
            'lg': 'var(--text-lg)',
            'xl': 'var(--text-xl)',
            '2xl': 'var(--text-2xl)',
            '3xl': 'var(--text-3xl)',
            '4xl': 'var(--text-4xl)',
            '5xl': 'var(--text-5xl)',
        },
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                headline: ['Doumbar', 'sans-serif'],
                mono: ['Space Mono', 'monospace']
            },
            height: {
                'safe-area': 'var(--navbar-safe-area)',
            },
            minHeight: {
                fill: ['var(--fill-space)', 'var(--fill-space-svh)'],
                'safe-area': 'var(--navbar-safe-area)',
                'full-minus-nav': ['calc(100vh - var(--navbar-safe-area))', 'calc(100svh - var(--navbar-safe-area))'],
            },
            spacing: {
                'safe-area': 'var(--navbar-safe-area)',
                site: 'var(--site-padding)'
            },
            inset: {
                navbar: 'var(--navbar-safe-area)'
            },
            boxShadow: {
                'window': '.75rem .75rem 0 0 rgba(125, 113, 127, 0.5)',
                'mario': '.5rem .5rem 0 0 var(--egp-green-pine)',
            },
            lineHeight: {
                min: '1.1'
            }
        },
    },

    plugins: [forms],
};
