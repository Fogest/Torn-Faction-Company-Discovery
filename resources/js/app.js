require('./bootstrap');
let moment = require('moment');

let newCompanyDay = moment().utc().hour(18).minute(0).second(0);
if (newCompanyDay.isBefore(moment().utc()))
    newCompanyDay.add(1, 'd'); // Go to next day as this time already passed.
// console.log(test.tz('Europe/London').format('ha z'));
console.log(newCompanyDay.format());
console.log("Local Time: " + newCompanyDay.local().format());
console.log("Time until: " + newCompanyDay.fromNow())
