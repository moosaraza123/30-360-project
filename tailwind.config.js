import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './Modules/**/resources/views/**/*.blade.php',
        './Modules/**/resources/assets/js/**/*.js',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                // Hisabi design tokens — single source of truth
                ink: {
                    DEFAULT: '#0B1526', // headings / primary text
                    soft: '#334155',    // body text
                    faint: '#64748B',   // secondary text
                },
                brand: {
                    DEFAULT: '#0E7C66', // deep teal-emerald — primary actions
                    dark: '#0A5F4E',
                    light: '#E6F4F1',   // tinted backgrounds
                },
                gold: {
                    DEFAULT: '#C9A227', // brand accent only — use sparingly
                    dark: '#A07D18',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted: '#F7F9FB',
                },
                line: '#E5EAF0', // hairline borders
            },
            fontFamily: {
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                arabic: ['"IBM Plex Sans Arabic"', '"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                card: '12px',
            },
            boxShadow: {
                card: '0 1px 3px rgba(11, 21, 38, 0.06), 0 4px 16px rgba(11, 21, 38, 0.05)',
                'card-hover': '0 2px 6px rgba(11, 21, 38, 0.08), 0 10px 28px rgba(11, 21, 38, 0.09)',
            },
            maxWidth: {
                content: '72rem',
            },
        },
    },

    plugins: [forms],
};
