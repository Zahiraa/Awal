/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.{js,jsx,ts,tsx}",
    "./templates/**/*.{html,twig}",
    "./src/**/*.{php,html}",
    "./templates/**/*.twig",
  ],
  safelist: [
    "hidden",
    "md:block",
    "md:hidden",
    "sm:px-6",
    "lg:px-8",
    "md:flex",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
