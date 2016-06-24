/**
 * Created by fisa on 10/16/15.
 */
'use strict';

let path = require('path');
let fs = require('fs');

const CONTROLLERS_DIR = path.join(__dirname, '../app/Controllers');
const OUTPUT_FILE = path.join(__dirname, '../app/controllers.js');

function loadDir(dirName, outputFile) {
    return fs.readdirSync(dirName).map(function(file) {
        // exclude non `.js` files
        if (path.extname(file).toLowerCase() !== '.js') return;

        return {
            name: file.substring(0, file.indexOf('.js')),
            path: './' + path.relative(path.dirname(outputFile), path.join(dirName, file))
        };
    });
}

console.log('Building Controllers...');

let controllers = loadDir(CONTROLLERS_DIR, OUTPUT_FILE);
// console.log(controllers);
// process.exit();

fs.open(path.join(__dirname, '../app/controllers.js'),'w+', function(err, fd){
    if(err){
        console.error('ERROR', err);
        return false;
    }
    //Controllers import
    let controllersImports = new Array(controllers.length);
    let controllersInArray = new Array(controllers.length);

    for(let index=0; index<controllers.length; index++){
        controllersImports[index] = ['import', controllers[index].name, 'from \"' + controllers[index].path + '\";\n'].join(' ');
        controllersInArray[index] = ['\"', controllers[index].name, '\":', controllers[index].name, ''].join('');
    }

    let buffer = controllersImports.join(''),
        arrayBuffer = 'var controllers = {' + controllersInArray.join(',') + '};',
        offset = 0;

    try {
        // write use strict
        let useStrictBuffer = '\'use strict\';\n';
        offset += fs.writeSync(fd, useStrictBuffer, offset, useStrictBuffer.length);
        // write imports
        offset += fs.writeSync(fd, buffer, offset, buffer.length);
        //Create array
        offset += fs.writeSync(fd, arrayBuffer, offset, arrayBuffer.length);
    } catch(e){
        console.error('Write to file failed while writing imports, error:', e);
        return false;
    }

    //Export
    let exportLine = '\nexport default controllers;';
    try {
        offset += fs.writeSync(fd,exportLine,offset,exportLine.length);
    } catch(e){
        console.error('ERROR while writing to file - executing exportLine, error:', e);
        return false;
    }
    try {
        fs.closeSync(fd);
        console.log('done');
    } catch(e){
        console.error('ERROR while closing file')
    }
});




