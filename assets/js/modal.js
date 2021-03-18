
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
            pic.src = asset + data.file
            var edit = document.createElement('a')
            edit.href= "/admin/edit/image/"+ data.id
            edit.className="btn__modal__edit"
            var del = document.createElement('a')
            del.href= "/admin/delete/image/" + + data.id
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
                            img.src = input.getAttribute('data-asset') + data.file
                        }
                    }).catch(e=> console.log(e))
                })
            }
        }
    })
})
    delModal()
    editVideo()
    editVideoDeleteOrClose()
    createVideo()
    delTrick()
}
const createVideo = () => {
    const btnAdd = document.querySelector('.modal__wrapper__body__medias__choose__btn__video')
    const input = document.querySelector('#tricks_videos_url')
    const token = document.querySelector('.modal__wrapper__body__medias__videos').getAttribute('data-token')
    btnAdd.addEventListener('click',function(e){
        e.preventDefault()
        e.stopPropagation()

        if(input.value.length > 0 && verifIframe(input.value)){
            var regex = 'src\s*=\s*"(.+?)"'
            var url = input.value.match(regex)[1]
            const id = e.target.getAttribute('data-id')
            fetch('/admin/create/video/' + id,{
                method: 'POST',
                headers: {
                    'X-Request-With' : 'XMLHttpRequest',
                    'Content-Type' : 'application/json'
                },
                body: JSON.stringify({'_token': token, 'url': url })
            }).then(response => response.json())
            .then(data =>{
                if(data.success){
                    var div = document.createElement('div')
                    div.className = "video__parent__" + data.id
                    var ifrm = document.createElement('iframe')
                    ifrm.src = data.url
                    ifrm.allow = "autoplay; encrypted-media"
                    ifrm.frameBorder ="0"
                    ifrm.allowFullscreen = true
                    var txt = document.createElement('textarea')
                    txt.className="trick__video__textarea hide"
                    txt.id="trick__video__url__" + data.id
                    txt.name = "trick__video__url"
                    var divChild = document.createElement('div')
                    divChild.className="modal__wrapper__body__medias__actions"
                    var editBtn = document.createElement('a')
                    editBtn.className="btn__modal__edit"
                    editBtn.href ="/admin/edit/video/" + data.id
                    editBtn.dataset.id = data.id
                    var delBtn = document.createElement('a')
                    delBtn.className="btn__modal__delete"
                    delBtn.href ="/admin/delete/video/" + data.id
                    delBtn.dataset.id = data.id
                    divChild.appendChild(editBtn)
                    divChild.appendChild(delBtn)
                    div.appendChild(ifrm)
                    div.appendChild(txt)
                    div.appendChild(divChild)
                    document.querySelector('.modal__wrapper__body__medias__videos').appendChild(div)
                    input.value=''
                    editVideo()
                    editVideoDeleteOrClose()
                }
            }).catch(e => console.error(e))
        } else{
            console.log("Le texte n'est pas une iframe valide")
        }
    })
}

const editVideoDeleteOrClose = () => {
    const videos = document.querySelectorAll('.btn__modal__delete')
    console.log(videos)
    videos.forEach(function(video){
        video.addEventListener('click',function(ev){
            ev.preventDefault()
            ev.stopPropagation()
            const ifrm = ev.target.parentElement.parentElement.querySelector('iframe')
            const textat = ev.target.parentElement.parentElement.querySelector('textarea')
            const id = ev.target.getAttribute('data-id')
            const parentDiv = document.querySelector('.video__parent__' + id)
            const editBtn = parentDiv.querySelector('.btn__modal__edit')
            const token = document.querySelector('.modal__wrapper__body__medias__videos').getAttribute('data-token')
            const href = ev.target.href
            if (ev.target.classList.contains('valid')){
                if(!textat.classList.contains('hide')){
                    textat.classList.add('hide')
                }
                if(ifrm.classList.contains('hide')){
                    ifrm.classList.remove('hide')
                }
                ev.target.classList.remove('valid')
                editBtn.classList.remove('valid')
                textat.value = ''
            } else {
                if(confirm("Vous êtes sur de vouloir supprimer cette video ?")) {
                    fetch(href,{
                        method: 'DELETE',
                        headers: {
                            'X-Request-With' : 'XMLHttpRequest',
                            'Content-Type' : 'application/json'
                        },
                        body: JSON.stringify({'_token': token})
                    }).then(response => response.json())
                    .then(data =>{
                        if(data.success){
                            document.querySelector('.video__parent__'+ id).remove()
                        }
                    }).catch(e => console.error(e))
                }
            }
        })
    })
}

const editVideo = () =>{
    const videos = document.querySelectorAll('.btn__modal__edit')
    videos.forEach(function(video){
        video.addEventListener('click',function(ev){
            ev.preventDefault()
            ev.stopPropagation()
            const ifrm = ev.target.parentElement.parentElement.querySelector('iframe')
            const textat = ev.target.parentElement.parentElement.querySelector('textarea')
            const id = ev.target.getAttribute('data-id')
            const parentDiv = document.querySelector('.video__parent__' + id)
            const delBtn = parentDiv.querySelector('.btn__modal__delete')
            const token = document.querySelector('.modal__wrapper__body__medias__videos').getAttribute('data-token')
            if (!ev.target.classList.contains('valid')){
                if (!ifrm.classList.contains('hide')){
                    ifrm.classList.add('hide')
                }
                if (textat.classList.contains('hide')){
                    textat.classList.remove('hide')
                }
                ev.target.classList.add('valid')
                if (!delBtn.classList.contains('valid')){
                    delBtn.classList.add('valid')
                }
            } else {
                if (textat.value.length > 10 && verifIframe(textat.value)){
                    var regex = 'src\s*=\s*"(.+?)"'
                    var url = textat.value.match(regex)[1]
                    fetch(ev.target.href,{
                        method: 'POST',
                        headers: {
                            'X-Request-With' : 'XMLHttpRequest',
                            'Content-Type' : 'application/json'
                        },
                        body: JSON.stringify({'_token': token, 'url': url})
                    }).then(response => response.json())
                    .then(data => {
                        if(data.success){
                            ifrm.src = data.url
                            if (ifrm.classList.contains('hide')){
                                ifrm.classList.remove('hide')
                            }
                            if (!textat.classList.contains('hide')){
                                textat.classList.add('hide')
                            }
                            if (ev.target.classList.contains('valid')){
                                ev.target.classList.remove('valid')
                            }
                            if (delBtn.classList.contains('valid')){
                                delBtn.classList.remove('valid')
                            }
                            textat.value=''
                        }
                    }).catch(e => console.error(e))
                } else {
                    alert("Le texte est trop court ou iframe est invalide")
                }
            }
        })
    })
}

const verifIframe = (value) => {
    if(value.indexOf('iframe') === -1) {
        return false
    } else {
        return true
    }
}


const delModal = () =>{
    var deletes = document.querySelectorAll('.modal__wrapper__body__medias__actions > .delete__img')
    if (deletes !== null){
        deletes.forEach(function(item){
            item.addEventListener('click',function(event){
                event.preventDefault();
                event.stopPropagation()
                if(confirm("Voulez-vous supprimer cette image ?")) {
                    fetch(this.getAttribute('href'), {
                        method : 'DELETE',
                        headers: {
                            'X-Request-With' : 'XMLHttpRequest',
                            'Content-Type' : 'application/json'
                        },
                        body: JSON.stringify({"_token": document.querySelector('.modal__wrapper__body__medias__images').getAttribute('data-token')})
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


const delTrick = function(){
    var deletes = document.querySelectorAll('.modal__wrapper__body__actions > .delete__trick')
    console.log(deletes)
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
                            modal.querySelector('.js-modal-close').click()
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