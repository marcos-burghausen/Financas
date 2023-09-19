module.exports = {
  env: {
    browser: true,
    es2021: true,
    node: true,
  },
  extends: [
    "eslint:recommended",
    "plugin:@typescript-eslint/recommended",
    "plugin:vue/vue3-recommended",
  ],
  overrides: [
    {
      env: {
        node: true,
      },
      files: [".eslintrc.{js,cjs}"],
      parserOptions: {
        sourceType: "script",
      },
    },
  ],
  parserOptions: {
    ecmaVersion: "latest",
    parser: "@typescript-eslint/parser",
    sourceType: "module",
  },
  plugins: ["@typescript-eslint", "vue"],
  rules: {
    indent: ["error", 4],
    semi: ["error", "always"],
    quotes: ["error", "double"],
    "vue/multi-word-component-names": "off",
    "comma-spacing": ["error", { before: false, after: true }],
    "@typescript-eslint/ban-ts-comment": false,
  },
};



// module.exports = {
//   root: true,
//   env: {
//     node: true,
//   },
//   extends: ["plugin:vue/vue3-essential", "eslint:recommended"],
//   Plugins: ["vue"],
//   rules: {
//     'vue/multi-word-component-names': 'off',
//     quotes: ["error", "single"],
//     indent: ["error", 1],
//     "comma-spacing": ["error", { before: false, after: true }],
//     "vue/no-multi-spaces": 0,
//     'vue/comment-directive': 'off'
//   },
// };