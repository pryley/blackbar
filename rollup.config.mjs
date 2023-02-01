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
        file: 'assets/main.js',
        format: 'iife',
        plugins: [terser()],
      },
    ],
    plugins: [
      resolve(),
      filesize(),
      babel({
        babelHelpers: 'bundled',
        presets: [
          ['@babel/preset-env', {
            include: ['@babel/plugin-proposal-optional-chaining'],
          }],
        ],
      }),
    ]
  },
  {
    input: '+/main.css',
    onwarn (warning, warn) {
      if (warning.code === 'FILE_NAME_CONFLICT') return
      warn(warning)
    },
    output: {
      file: 'assets/main.css',
    },
    plugins: [
      filesize(),
      postcss({
        extract: true,
        minimize: true,
      }),
    ]
  },
  {
    input: 'node_modules/highlight.js/styles/atom-one-dark.css',
    onwarn (warning, warn) {
      if (warning.code === 'FILE_NAME_CONFLICT') return
      warn(warning)
    },
    output: {
      file: 'assets/syntax.css',
    },
    plugins: [
      filesize(),
      postcss({
        extract: true,
        minimize: true,
      }),
    ]
  },
]
