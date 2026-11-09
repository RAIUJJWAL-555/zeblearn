/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      colors: {
        brand: '#FB6221',
        'brand-dark': '#E0521A',
        canvas: '#E4E6E9',
        soft: '#D3D6DA',
        ink: '#0A0A0A',
        muted: '#5F6368',
      },
      fontFamily: {
        display: ['Anton', 'Impact', 'sans-serif'],
        sans: ['Bricolage Grotesque', 'Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: { card: '24px' },
    },
  },
  plugins: [],
};
