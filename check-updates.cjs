const { execSync } = require('child_process');

try {
    const output = execSync('npm outdated --json', { encoding: 'utf-8' });
    const outdated = JSON.parse(output);

    console.log('\n📦 Csomagok, amik nem a legfrissebbek:\n');

    for (const [pkg, info] of Object.entries(outdated)) {
        const [currentMajor] = info.current.split('.');
        const [latestMajor] = info.latest.split('.');
    
        const isBreaking = currentMajor !== latestMajor;
    
        console.log(`🔸 ${pkg}`);
        console.log(`   Current : ${info.current}`);
        console.log(`   Wanted  : ${info.wanted}`);
        console.log(`   Latest  : ${info.latest} ${isBreaking ? '⚠️  BREAKING' : ''}`);
        console.log('');
    }

    console.log('👉 Használhatod: npm install <csomag>@latest');
} catch (err) {
    if (err.stdout) {
        console.error('Nincs elavult csomag.');
    } else {
        console.error('Hiba történt:', err.message);
    }
}