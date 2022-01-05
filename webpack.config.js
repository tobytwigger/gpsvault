const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            'ui': path.resolve('resources/js/ui'),
        },
    },
};
