var dir_root = '/LVDI';
var dir_api = '/LVDI/api';



// function formatLocalDate() {
//     var now = new Date(),
//         tzo = -now.getTimezoneOffset(),
//         dif = tzo >= 0 ? '+' : '-',
//         pad = function(num) {
//             norm = Math.abs(Math.floor(num));
//             return (norm < 10 ? '0' : '') + norm;
//         };
//     return now.getFullYear() 
//         + '-' + pad(now.getMonth()+1)
//         + '-' + pad(now.getDate())
//         + 'T' + pad(now.getHours())
//         + ':' + pad(now.getMinutes()) 
//         + ':' + pad(now.getSeconds()) 
//         + dif + pad(tzo / 60) 
//         + ':' + pad(tzo % 60);
// }