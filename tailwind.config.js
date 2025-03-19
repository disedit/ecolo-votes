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
                dark: '#078656',
                pine: '#3b2d3d',
                neon: '#00f57c',
                DEFAULT: '#09b876'
            },
            'gray': {
                50: '#fafafa',
                100: '#f5f5f5',
                200: '#efefef',
                300: '#e6e6e6',
                400: '#cbd5e0',
                500: '#cecece',
                600: '#cecece',
                700: '#aaa',
                800: '#aaa',
                900: '#868686',
                950: '#38313a',
            },
            'white': '#fff',
            'black': '#3b2d3d',
            'yellow': '#FFDC2E',
            'pink': '#ffdbe5',
            'purple': '#704572',
            'red': '#DC2628',
            'orange': '#feae37',
            'blue': '#4659d1',
            'sand': '#f8f6f2'
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
                sans: ['Klima', ...defaultTheme.fontFamily.sans],
                headline: ['NewSpirit', 'sans-serif'],
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
