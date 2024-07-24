const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Nunito", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                bulsca: "var(--brand-primary)",
                bulsca_red: "var(--brand-secondary)",
            },
            screens: {
                "3xl": "1600px",
                "4xl": "1920px",
            },
            transitionProperty: {
                width: "width",
            },
        },
    },

    plugins: [],
};
