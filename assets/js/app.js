/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import '../scss/style.scss';

// start the Stimulus application
// import '../bootstrap';

import './form.js'
import './category.js'
import './index.js'

var alerts =  document.querySelectorAll('.alert.show')

if (alerts !== null){
    alerts.forEach(function(item){
        setTimeout(function(){
            item.classList.remove('show')
            item.classList.add('hide')
        },3500)
    })
}