module.exports = {
  plugins: [
    require('postcss-import'),
    require('postcss-preset-env')({ stage: 0 }),
    require('postcss-hexrgba'),
    require('postcss-custom-properties'),
    require('autoprefixer'),
  ],
};
