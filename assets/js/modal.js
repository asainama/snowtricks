
let modal = null
const focusableSelector = 'button, a, input,textarea'
let focusables = []
let previouslyFocusedElement = null

const openModal = async function(e) {
    e.preventDefault()
    console.log(e)
    const target = e.target.getAttribute('href')
    console.log(target)
    if (target.startsWith('#')) {
        modal = document.querySelector(target)
    } else {
        if (modal !== null)
        {
            modal.remove()
        }
        modal = await loadModal(target)
        if (modal.classList.contains('edit')){
            runAll()
        } else {
            const aEdit = document.querySelector('.modal__wrapper__img__edit.js-edit')
            if (aEdit !== null)
                aEdit.addEventListener('click',function(e){
                    if (confirm("Vous êtes sur de modifier le trick ?"))
                        openModal(e)
                })
        }
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
    const hideModal = function(){
        modal.style.display = "none";
        modal.removeEventListener('animationend',hideModal)
        modal = null
    }
    modal.addEventListener('animationend', hideModal)
    if (document.querySelector('#modal2')){
        console.log('modal supprimée')
        document.body.removeChild(document.querySelector('#modal2'))
    }
}


const stopPropagation = function (e) {
    e.stopPropagation()
}

const loadModal = async function(url){
    // Afficher un loader
    // const existingModal = document.querySelector('#modal2')
    // if (existingModal !== null) return existingModal
    const html = await fetch(url).then(response => response.text())
    const element = document.createRange().createContextualFragment(html).querySelector('#modal2')
    if (element === null) throw `L'élément #modal2 n'a pas été trouvé dans la page ${url}`
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

const all = function (){
    document.querySelectorAll('.js-modal').forEach(a => {
        a.addEventListener('click',openModal)
    })
    del()
}

const del = function(){
    var deletes = document.querySelectorAll('.delete__img')
    if (deletes !== null){
        deletes.forEach(function(item){
            item.addEventListener('click',function(event){
                event.preventDefault();
                event.stopPropagation()
                if(confirm("Voulez-vous supprimer ce trick ?")) {
                    var formData = new FormData();
                    formData.append("_token" , document.querySelector('#home').getAttribute('data-token'))
                    fetch(this.getAttribute('href'), {
                        method : 'DELETE',
                        headers: {
                            'X-Request-With' : 'XMLHttpRequest',
                            'Content-Type' : 'application/json'
                        },
                        body: JSON.stringify({"_token": document.querySelector('#home').getAttribute('data-token')})
                    }).then(
                        response => response.json()
                    ).then(data => {
                        if (data.success) {
                            document.querySelector('trick-card[id="'+ item.getAttribute('data-id') +'"]').remove()
                        } else {
                            console.log(data.error)
                        }
                    }).catch(e=> console.log(e))
                }
            })
        })
    }
}

window.addEventListener('keydown',function(e){
    if (e.key === "Escape" || e.key === "Esc"){
        closeModal(e)
    }
    if (e.key === 'Tab' && modal !== null){
        focusInModal(e)
    }
})

const runAll = () => {

var wrapper = document.querySelector('.modal__wrapper__body__medias__choose__content')
var cancelBtn = document.querySelector('.modal__wrapper__body__medias__choose__content__cancel')
var mediaBtn = document.querySelector('#medias-btn')
var customBtn = document.querySelector('.modal__wrapper__body__medias__choose__btn')
const img = document.querySelector('.modal__wrapper__body__medias__choose__content__img')
const formImg = document.querySelector('#form__img')
const formSubmit = document.querySelector('#form__img > .form__img__submit')
const fileName = document.querySelector('.modal__wrapper__body__medias__choose__content__file')
let regExp = /[0-9a-zA-Z\^\&\'\@\{\}\[\]\,\$\=\!\-\#\(\)\.\%\+\~\_ ]+$/;

const btnClick = (e) =>{
    if (!e.target.classList.contains('valid')){
        mediaBtn.click()
    } else {
        if(confirm("Voulez-vous ajouter cette image ?")) {
            formSubmit.click()
        }
    }

}

customBtn.addEventListener('click',btnClick)

formImg.addEventListener('submit',function(e){
    e.preventDefault()
    e.stopPropagation()
    var formData = new FormData(this);
    fetch("/admin/create/image/" + mediaBtn.getAttribute('data-id'), {
        method : "POST",
        // headers: {
        //     'X-Request-With' : 'XMLHttpRequest',
        //     'Content-Type' : 'application/json'
        // },
        body: formData
    }).then(
        response => response.json()
    ).then(data => {
        if (data.success) {
            var parent = document.querySelector('.modal__wrapper__body__medias__images')
            var div = document.createElement('div')
            var divChild = document.createElement('div')
            divChild.className = "modal__wrapper__body__medias__actions"
            var asset = mediaBtn.getAttribute('data-asset')
            var pic = document.createElement('img')
            console.log(asset + data.file)
            pic.src = asset + data.file
            var edit = document.createElement('a')
            edit.href= "#"
            edit.className="btn__modal__edit"
            var del = document.createElement('a')
            del.href= "#"
            del.className="btn__modal__delete delete__img"
            divChild.appendChild(edit);
            divChild.appendChild(del);
            div.appendChild(pic);
            div.appendChild(divChild);
            parent.appendChild(div)
            img.src = ""; 
            wrapper.classList.remove('active')
            customBtn.classList.remove('valid')
            customBtn.innerHTML = "Choisir une image"
            delModal()
        } else {
            console.log(data.error)
        }
    }).catch(e=> console.log(e))
    return false;
})

mediaBtn.addEventListener('change',function(e){
    const file = this.files[0]
    if (file){
        const reader = new FileReader()
        reader.onload = function(){
            const result = reader.result
            img.src = result
            wrapper.classList.add('active')
            customBtn.innerHTML = "Valider"
            customBtn.classList.add('valid')
        }
        cancelBtn.addEventListener("click",function(){
            img.src = ""; 
            wrapper.classList.remove('active')
            customBtn.classList.remove('valid')
            customBtn.innerHTML = "Choisir une image"
        })
        reader.readAsDataURL(file)
    }
    if (this.value){
        let valueStore = this.value.match(regExp)
        fileName.textContent = valueStore
    }
})

const btnMainImage = document.querySelector('.modal__wrapper__img__edit__mainImage')

btnMainImage.addEventListener('click',function(e){
    e.preventDefault()
    e.stopPropagation()
    const href = e.target.getAttribute('href')
    if (href !== null) {
        const id = e.target.getAttribute('data-id')
        const input = document.querySelector('#trick-image-btn-mainImage-' + id)
        const form = document.querySelector('#formtricks-img-mainImage-' + id)
        if(confirm("Vous êtes sur de vouloir modifier cette image ?")){
            input.click()
            input.addEventListener('change',function(ev){
                var formData = new FormData(form)
                formData.append('file',input.files[0])
                fetch(href, {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                .then((data)=>{
                    if (data.success) {
                        var img = document.querySelector('.modal__wrapper__img > img')
                        console.log(img)
                        img.src = input.getAttribute('data-asset') + data.file
                    }
                }).catch(e=> console.log(e))
            })
        }
    }
})

const AllEdit = document.querySelectorAll('.edit__img')
AllEdit.forEach(edit =>{
    edit.addEventListener('click',function(e){
        e.preventDefault()
        e.stopPropagation()
        const href = e.target.getAttribute('href')
        if (href !== null) {
            const id = e.target.getAttribute('data-id')
            const input = document.querySelector('#trick-image-btn-' + id)
            const form = document.querySelector('#formtricks-img-' + id)
            if(confirm("Vous êtes sur de vouloir modifier cette image ?")){
                input.click()
                input.addEventListener('change',function(ev){
                    var formData = new FormData(form)
                    formData.append('file',input.files[0])
                    fetch(href, {
                        method: 'POST',
                        body: formData
                    }).then(response => response.json())
                    .then((data)=>{
                        if (data.success) {
                            var img = document.querySelector('.form__img__trick__submit__' + id + ' img')
                            console.log(img)
                            img.src = input.getAttribute('data-asset') + data.file
                        }
                    }).catch(e=> console.log(e))
                })
            }
        }
    })
})
 delModal()
}


const delModal = () =>{
    var deletes = document.querySelectorAll('.modal__wrapper__body__medias__actions > .delete__img')
console.log(deletes)
    if (deletes !== null){
        deletes.forEach(function(item){
            item.addEventListener('click',function(event){
                event.preventDefault();
                event.stopPropagation()
                if(confirm("Voulez-vous supprimer cette image ?")) {
                    var formData = new FormData();
                    formData.append("_token" , item.getAttribute('data-token'))
                    fetch(this.getAttribute('href'), {
                        method : 'DELETE',
                        headers: {
                            'X-Request-With' : 'XMLHttpRequest',
                            'Content-Type' : 'application/json'
                        },
                        body: JSON.stringify({"_token": item.getAttribute('data-token')})
                    }).then(
                        response => response.json()
                    ).then(data => {
                        if (data.success) {
                            item.parentElement.parentElement.remove()
                        } else {
                            console.log(data.error)
                        }
                    }).catch(e=> console.log(e))
                }
            })
        })
    }
}

module.exports = {
    openModal,
    focusInModal,
    closeModal,
    all
}