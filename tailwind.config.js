module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            animation: {
                'float': 'float 6s ease-in-out infinite',
            }
        }
    },
    plugins: [],
}