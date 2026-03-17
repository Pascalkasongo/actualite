/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.html.twig",  // fichiers Twig
    "./assets/**/*.js",            // fichiers JS
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),       // si tu utilises des formulaires
    require('@tailwindcss/typography'),  // si tu utilises prose pour articles
  ],
}