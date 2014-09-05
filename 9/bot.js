var page = require('webpage').create();
var system = require('system');
var address;
var nbpage = 0;

if (system.args.length === 1) {
  console.log('Usage: '+system.args[0]+' <some URL>');
  phantom.exit();
}

address = system.args[1];

phantom.addCookie({
  'name': 'phantomjs-cheat',
  'value': '60afe57f665abca1a54cc83955cf3adf0a7db9e5abc8334bf77d4cc1a6fb599a',
  'domain': system.args[2]
});

page.open(address, function(status) {
    console.log("open "+address);    
});

page.onLoadFinished = function(status) {
    console.log('Status: ' + status);
    nbpage+=1;
    if(nbpage==2){
        phantom.exit();
    }
};