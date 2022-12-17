/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {           colors: {
      bulsca: '#070660',
      bulsca_red: '#9e0d06',
  },},
  },
  plugins: [],
}