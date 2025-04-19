const fs = require('fs');
const path = require('path');

const configFile = process.argv[2] || './crud.config.cjs';
const config = require(path.resolve(configFile));

// Laravel-sakai belső mappa – ehhez igazítjuk a generálást
const baseOutput = path.resolve('./resources/js/Pages');

//
const vueOutputDir = path.resolve('./resources/js/Pages', ...config.namespace.split('/'));
const serviceOutputDir = path.resolve('./resources/js/service', ...config.namespace.split('/'));
const controllerOutputDir = path.resolve('./app/Http/Controllers', ...config.namespace.split('/'));
const requestOutputDir = path.resolve('./app/Http/Requests');
const resourceOutputDir = path.resolve('./app/Http/Resources');
const factoryOutputDir = path.resolve('./database/factories');
const seederOutputDir = path.resolve('./database/seeders');


const migrationOutputDir = path.resolve('./database/migrations');

const modelOutputDir = path.resolve('./app/Models');

const routeOutputDir = path.resolve('./routes');


const toKebab = str => str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
const toSnake = str => str.replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase();
const lcFirst = str => str.charAt(0).toLowerCase() + str.slice(1);

const replacements = {
    '{{entity}}': config.entity,
    '{{entityPlural}}': config.entityPlural,
    '{{entityLower}}': lcFirst(config.entity),
    '{{entityPluralLower}}': lcFirst(config.entityPlural),
    '{{namespace}}': config.namespace,
    '{{apiRoute}}': config.apiRoute
};

// Pl. 'Calendars/Calendar' => ['Calendars', 'Calendar']
const targetSubdirs = config.namespace.split('/');
const targetDir = path.join(baseOutput, ...targetSubdirs);

const isForce = process.argv.includes('--force');

if (!fs.existsSync(targetDir)) {
    fs.mkdirSync(targetDir, { recursive: true });
}

const generateFromStub = (stubFile, outFile, targetDirectory) => {
    const stubPath = path.resolve(__dirname, 'stubs', stubFile);
    const outputPath = path.join(targetDirectory, outFile);

    if (fs.existsSync(outputPath) && !isForce) {
        console.log(`⚠️  Skipped (already exists): ${outputPath}`);
        return;
    }

    let content = fs.readFileSync(stubPath, 'utf-8');
    for (const [key, value] of Object.entries(replacements)) {
        content = content.replaceAll(key, value);
    }

    fs.mkdirSync(targetDirectory, { recursive: true });
    fs.writeFileSync(outputPath, content, 'utf-8');
    console.log(`✅ Generated: ${outputPath}`);
};

const appendRouteIfMissing = (routeFilePath, stubFileName) => {
    const stubPath = path.resolve(__dirname, 'stubs', stubFileName);
    const routePath = path.resolve(routeFilePath);

    if (!fs.existsSync(routePath)) {
        console.log(`❌ Route file not found: ${routePath}`);
        return;
    }

    let routeFileContent = fs.readFileSync(routePath, 'utf-8');
    let stubContent = fs.readFileSync(stubPath, 'utf-8');

    for (const [key, value] of Object.entries(replacements)) {
        stubContent = stubContent.replaceAll(key, value);
    }

    if (!routeFileContent.includes(stubContent.trim())) {
        fs.appendFileSync(routePath, '\n\n' + stubContent.trim());
        console.log(`✅ Route added to ${routePath}`);
    } else {
        console.log(`⚠️  Route already exists in ${routePath}`);
    }
};


// Példa: csak az Index.vue generálása
generateFromStub('Index.vue.stub', 'Index.vue', vueOutputDir);
generateFromStub('Create.vue.stub', 'Create.vue', vueOutputDir);
generateFromStub('Edit.vue.stub', 'Edit.vue', vueOutputDir);
generateFromStub('Delete.vue.stub', 'Delete.vue', vueOutputDir);

generateFromStub('CalendarService.js.stub', 'CalendarService.js', serviceOutputDir);

generateFromStub('CalendarController.php.stub', 'CalendarController.php', controllerOutputDir);

generateFromStub('StoreCalendarRequest.php.stub', 'StoreCalendarRequest.php', requestOutputDir);
generateFromStub('UpdateCalendarRequest.php.stub', 'UpdateCalendarRequest.php', requestOutputDir);
generateFromStub('IndexCalendarRequest.php.stub', 'IndexCalendarRequest.php', requestOutputDir);
generateFromStub('DeleteCalendarRequest.php.stub', 'DeleteCalendarRequest.php', requestOutputDir);

generateFromStub('CalendarResource.php.stub', 'CalendarResource.php', resourceOutputDir);

generateFromStub('CalendarFactory.php.stub', 'CalendarFactory.php', factoryOutputDir);
generateFromStub('CalendarSeeder.php.stub', 'CalendarSeeder.php', seederOutputDir);


// migrációs fájl létrehozása időbélyeggel
const now = new Date();
const pad = n => n.toString().padStart(2, '0');
const timestamp = [
    now.getFullYear(),
    pad(now.getMonth() + 1),
    pad(now.getDate()),
    pad(now.getHours()) + pad(now.getMinutes()) + pad(now.getSeconds())
].join('_');

const migrationFileName = `${timestamp}_create_${toSnake(config.entityPlural)}_table.php`;

generateFromStub('CreateCalendarsTable.php.stub', migrationFileName, migrationOutputDir);


generateFromStub('Calendar.php.stub', 'Calendar.php', modelOutputDir);

//appendRouteIfMissing('./routes/api.php', 'api.php.stub',);
//appendRouteIfMissing('./routes/web.php', 'web.php.stub',); // ha web route-ot is használsz
