const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/filament/**/*.blade.php'
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                gray: colors.slate,
                danger: colors.rose,
                primary: colors.sky,
                success: colors.emerald,
                warning: colors.orange,
            },
            fontFamily: {
                sans: [ 'Inter', ...defaultTheme.fontFamily.sans ]
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ]
};
