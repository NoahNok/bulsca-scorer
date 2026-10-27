const { join } = require('path');

// Keep Chrome inside the project so PHP-FPM (a different user to the one
// running npm) can find it when rendering PDF Packs.
module.exports = {
    cacheDirectory: join(__dirname, '.cache', 'puppeteer'),
};
