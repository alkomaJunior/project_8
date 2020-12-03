/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.css';
import 'jquery/dist/jquery.slim.min.js';
import 'popper.js/dist/umd/popper.min.js';
import 'bootstrap/dist/js/bootstrap.js';
import 'glyphicons-only-bootstrap/fonts/glyphicons-halflings-regular.eot';
import 'glyphicons-only-bootstrap/fonts/glyphicons-halflings-regular.svg';
import 'glyphicons-only-bootstrap/fonts/glyphicons-halflings-regular.ttf';
import 'glyphicons-only-bootstrap/fonts/glyphicons-halflings-regular.woff';
import 'glyphicons-only-bootstrap/fonts/glyphicons-halflings-regular.woff2';


// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
//import $ from 'jquery';
// create global $ and jQuery variables
//


//console.log('Hello Webpack Encore! Edit me in assets/app.js');

/*close dropdown menu after mouse leaves*/
/*const $dropdown = $('.dropdown');
const $dropdownToggle = $('.dropdown-toggle');
const $dropdownMenu = $('.dropdown-menu');
const showClass = 'show';
$(window).on('load resize', function () {
    if (this.matchMedia('(min-width: 768px)').matches) {
        $dropdown.hover(
            function () {
                const $this = $(this);
                $this.addClass(showClass);
                $this.find($dropdownToggle).attr('aria-expanded', 'true');
                $this.find($dropdownMenu).addClass(showClass);
            },
            function () {
                const $this = $(this);
                $this.removeClass(showClass);
                $this.find($dropdownToggle).attr('aria-expanded', 'false');
                $this.find($dropdownMenu).removeClass(showClass);
            }
        );
    } else {
        $dropdown.off('mouseenter mouseleave');
    }
});*/

