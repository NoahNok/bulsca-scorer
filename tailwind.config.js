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
                astoria: "Astoria",
                ariel: "Ariel, Helvetica, sans-serif",
                greycliff: "greycliff",
            },
            colors: {
                bulsca: "var(--brand-primary)",
                bulsca_red: "var(--brand-secondary)",
                "rlss-blue": "#163072",
                "rlss-red": "#e42313",
                "rlss-yellow": "#ffd300",
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

    plugins: [require("@tailwindcss/typography")],
};
