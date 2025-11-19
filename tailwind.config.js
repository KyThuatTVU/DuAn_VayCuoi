/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    "./assets/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#7ec8e3',
        accent: '#5ab8d9',
      },
    },
  },
  plugins: [],
}
