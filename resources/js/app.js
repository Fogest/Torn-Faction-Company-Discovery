require('./bootstrap');
let moment = require('moment');

let test = moment();
// console.log(test.tz('Europe/London').format('ha z'));
console.log(test.utc().format());
