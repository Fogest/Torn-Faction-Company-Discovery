require('./bootstrap');

let newCompanyDay = moment().utc().hour(18).minute(0).second(0);
if (newCompanyDay.isBefore(moment().utc()))
    newCompanyDay.add(1, 'd'); // Go to next day as this time already passed.
// console.log(test.tz('Europe/London').format('ha z'));
console.log(newCompanyDay.format());
console.log("Local Time: " + newCompanyDay.local().format());
console.log("Time until: " + newCompanyDay.fromNow())

$(document).ready( function () {
    let directoryTable = $('#directory-table').DataTable({
        "visible": true,
        "api": true,
        "responsive": true,
        "paging": false,
        "order": [[2, 'asc'], [3, 'dsc']],
        "columnDefs": [
            {
                targets: [0,1,2,3,4],
                className: 'dt-body-center'
            }
        ],
        "rowCallback": function(row, data, index) {
            let companyName = data[1].replace(/\s+/g, '').toLowerCase(); // Strip whitespace and make lowercase
            if (companyName.includes('hiring') || companyName.includes('hire')) {
                $('td', row).css('background-color', '#ffc8008c');
            }

            let positions = data[4];
            let positionsSplit = positions.split('/');
            if(positionsSplit[0] !== positionsSplit[1]) {
                $(row).find('td:eq(4)').css('background-color', '#f75e5ead');
            }
        }
    })
        .columns.adjust()
        .responsive.recalc();

    $('#directory-table-debug').DataTable({
        "paging": false,
    });
} );
