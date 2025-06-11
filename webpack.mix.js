const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.ts("resources/admin/ts/app.js", "public/admin-public/js")
    .ts("resources/admin/ts/body.js", "public/admin-public/js")
    .ts("resources/admin/ts/defer.js", "public/admin-public/js")
    .sass("resources/admin/sass/app.scss", "public/admin-public/css")
    .sass("resources/admin/sass/carHistory/invoice.scss", "public/admin-public/css/history/invoice.css")
    .sass("resources/admin/sass/invoice/detail.scss", "public/admin-public/css/invoice/detail.css")
    .sass("resources/admin/sass/invoice/receipt.scss", "public/admin-public/css/invoice/receiptII.css")
    .js("resources/admin/ts/select_geo.js", "public/admin-public/js");

mix.copyDirectory("vendor/tinymce/tinymce", "public/admin-public/js/tinymce");
// mix.ts("resources/admin/ts/app.ts", "public/admin-public/js").sass(
//     "resources/admin/sass/app.scss",
//     "public/admin-public/css"
// );
mix.browserSync("127.0.0.1:8000");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Frontend
 |--------------------------------------------------------------------------
*/

// mix.ts("resources/website/ts/app.js", "public/website/styles/js").sass(
//     "resources/website/sass/app.scss",
//     "public/website/styles/css"
// );
