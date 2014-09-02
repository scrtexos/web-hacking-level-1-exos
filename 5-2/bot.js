var page = require('webpage').create();
var system = require('system');
var address;

if (system.args.length === 1) {
  console.log('Usage: '+system.args[0]+' <some URL>');
  phantom.exit();
}

address = system.args[1];

phantom.addCookie({
  'name': 'Admin-cookie',
  'value': 'Congratz ! You steal this cookie.',
  'domain': '127.0.0.1'
});


page.open(address, function(status) {
    phantom.exit();
});