/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      keyframes: {
        appear: {
          "0%": {
            opacity: "0",
          },
          "100%": {
            opacity: "1",
          },
        },
      },
      animation: {
        appear: "appear 0.5s ease-in-out",
      },
      backgroundColor: {
        "blue-skye": "var(--blue-skye)",
        "blue-skye-dark": "var(--blue-skye-dark)",
      },
      colors: {
        "main-text-color": "var(--main-text-color)",
        "nav-hover-color": "var(--nav-hover-color)",
      },
      borderColor: {
        "border-color": "var(--border-color)",
      },
    },
    screens: {
      'iphonese': {
        'raw': '(min-height: 665px)',
      },
      'xs': '320px',
      '2s': '375px',
      's': '425px',
      'sm': '640px',
      'md': '768px',
      'lg': '1024px',
      'xl': '1280px',
      '2xl': '2440px',
    }
  },
  plugins: [],
}

