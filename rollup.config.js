const terser = require("@rollup/plugin-terser");

module.exports = {
  input: "assets/js/main.js",
  output: {
    file: "dist/es-colour-pairings/assets/js/main.min.js",
    format: "iife",
    sourcemap: true
  },
  plugins: [terser()]
};