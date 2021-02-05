module.exports = {
    purge: (process.env.NODE_ENV === "production") ? [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',] : [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
