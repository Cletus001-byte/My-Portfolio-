const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const optDir = 'C:/xampp/htdocs/cletusportfolio/wp-content/plugins/cletus-portfolio-core/assets/images/optimized';

console.log('Optimized folder files:');
fs.readdirSync(optDir).forEach(file => {
    const stat = fs.statSync(path.join(optDir, file));
    console.log(` - ${file}: ${Math.round(stat.size / 1024)} KB`);
});
