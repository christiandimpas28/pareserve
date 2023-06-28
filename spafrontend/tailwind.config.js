//https://vue-tailwind-datepicker.com/installation.html
//https://github.com/tailwindlabs/tailwindcss-forms
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
    "./node_modules/flowbite/**/*.js",
    "./node_modules/vue-tailwind-datepicker/**/*.js",
  ],
  darkMode: false,
  theme: {
    colors: {
      teal:colors.teal,
      cyan: colors.cyan,
      gray: colors.gray,
      white: '#fff',
      darkMode: false
    },
    extend: {
      colors: {
        "vtd-primary": colors.cyan,
        "vtd-secondary": colors.white,
      },
    },
  },
  plugins: [
    require("flowbite/plugin"),
    require('@tailwindcss/forms'),
  ],
}