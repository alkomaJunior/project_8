
/**
 * ToDoAndCo Project
 * Copyright (c) 2020 BigBoss 2020.  BigBoss Oualid
 * mailto: <bigboss@it-bigboss.de>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 * Inc., Munich, Germany.
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


const inputUsername = document.getElementById('username');
if (inputUsername && inputUsername.value !== '') {
    inputUsername.classList.add('input-value');
}


