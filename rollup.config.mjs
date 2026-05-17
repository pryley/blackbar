import babel from '@rollup/plugin-babel';
import filesize from 'rollup-plugin-filesize';
import postcss from 'rollup-plugin-postcss'
import resolve from '@rollup/plugin-node-resolve';
import terser from '@rollup/plugin-terser';

export default [
  {
    input: '+/main.js',
    output: [
      {
        file: 'assets/blackbar.js',
        format: 'iife',
        plugins: [
          terser(),
        ],
      },
    ],
    plugins: [
      resolve(),
      filesize(),
      babel({
        babelHelpers: 'runtime',
        plugins: [
          '@babel/plugin-proposal-optional-chaining',
          '@babel/plugin-transform-runtime',
        ],
        presets: [
          '@babel/preset-env',
        ],
      }),
      terser({
        compress: {
          pure_funcs: Object.keys(console)
            .filter(key => !~['info', 'warn', 'error'].indexOf(key))
            .map(key => `console.${key}`),
        },
        format: { comments: false },
      }),
    ],
  },
  {
    input: '+/main.css',
    onwarn (warning, warn) {
      if (warning.code === 'FILE_NAME_CONFLICT') return
      warn(warning)
    },
    output: {
      file: 'assets/blackbar.css',
    },
    plugins: [
      filesize(),
      postcss({
        extract: true,
        minimize: true,
      }),
    ],
  },
]
