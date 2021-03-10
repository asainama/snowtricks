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

var alerts =  document.querySelectorAll('.alert.show')

if (alerts !== null){
    alerts.forEach(function(item){
        setTimeout(function(){
            item.classList.remove('show')
            item.classList.add('hide')
        },3500)
    })
}

let modal = null
const focusableSelector = 'button, a, input,textarea'
let focusables = []
let previouslyFocusedElement = null

const openModal = async function(e) {
    e.preventDefault()
    const target = e.target.getAttribute('href')
    if (target.startsWith('#')) {
        modal = document.querySelector(target)
    } else {
        modal = await loadModal(target)
    }
    focusables = Array.from(modal.querySelectorAll(focusableSelector))
    previouslyFocusedElement = document.querySelector(':focus')
    modal.style.display = null;
    focusables[0].focus()
    modal.removeAttribute('aria-hidden')
    modal.setAttribute('aria-modal', 'true')
    modal.addEventListener('click', closeModal)
    modal.querySelector('.js-modal-close').addEventListener('click', closeModal)
    modal.querySelector('.js-modal-stop').addEventListener('click', stopPropagation)
}

const closeModal = function(e) {
    if (modal === null) return
    if (previouslyFocusedElement !== null) previouslyFocusedElement.focus()
    e.preventDefault()
    modal.setAttribute('aria-hidden','true')
    modal.removeAttribute('aria-modal')
    modal.removeEventListener('click', closeModal)
    modal.querySelector('.js-modal-close').removeEventListener('click',closeModal)
    modal.querySelector('.js-modal-stop').removeEventListener('click',stopPropagation)
    modal.addEventListener('animationend', hideModal)
}

const hideModal = function(){
    modal.style.display = "none";
    modal = null
}

const stopPropagation = function (e) {
    e.stopPropagation()
}

const loadModal = async function(url){
    // Afficher un loader
    // const existingModal = document.querySelector('#modal1')
    // if (existingModal !== null) return existingModal
    const html = await fetch(url).then(response => response.text())
    console.log(html)
    const element = document.createRange().createContextualFragment(html).querySelector('#modal1')
    if (element === null) throw `L'élément #modal1 n'a pas été trouvé dans la page ${url}`
    document.body.append(element)
    return element
}

const focusInModal = function(e){
    e.preventDefault()
    let index = focusables.findIndex(f => f === modal.querySelector(':focus'))
    if (e.shiftKey === true){
        index--
    } else {
        index++
    }
    if (index >= focusables.length){
        index = 0
    }
    if (index < 0){
        index.focusables.length - 1
    }
    focusables[index].focus()
}

document.querySelectorAll('.js-modal').forEach(a => {
    a.addEventListener('click',openModal)
})

window.addEventListener('keydown',function(e){
    if (e.key === "Escape" || e.key === "Esc"){
        closeModal(e)
    }
    if (e.key === 'Tab' && modal !== null){
        focusInModal(e)
    }
})