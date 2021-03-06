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
import './components/Trick.js'
import './components/TrickList.js'
import './components/Comment.js'
import './components/CommentList.js'

var alerts =  document.querySelectorAll('.alert.show')

if (alerts !== null){
    alerts.forEach(function(item){
        setTimeout(function(){
            item.classList.remove('show')
            item.classList.add('hide')
        },3500)
    })
}

var btnMobile = document.querySelector('.navbar__header__mobile')

if (btnMobile !== null){
    btnMobile.addEventListener('click',function(e){
        e.preventDefault()
        e.stopPropagation()
        var subNav = document.querySelector('.navbar__header__subnav')
        if (subNav.classList.contains('mobile')){
            subNav.classList.remove('mobile')
        } else {
            subNav.classList.add('mobile')
        }
    })
}
