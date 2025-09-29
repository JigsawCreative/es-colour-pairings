const terser = require("@rollup/plugin-terser");

module.exports = {
  input: "assets/js/main.js",
  output: {
    file: "dist/plugin-starter/assets/js/main.min.js",
    format: "iife",
    sourcemap: true
  },
  plugins: [terser()]
};