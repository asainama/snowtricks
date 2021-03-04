const checkIframes = require('./iframe')

var file = document.querySelector('.attachment')
if(file !== null){
    file.addEventListener('change',function(e){
        e.preventDefault()
        const preview = document.querySelector('#preview')
        preview.src = URL.createObjectURL(e.target.files[0])
    })
}

var alertClose = document.querySelector('.alert__close')
if (alertClose !== null) {
    alertClose.addEventListener('click',function(e){
        e.preventDefault()
        var alertMessage = document.querySelector('.alert')
        if (alertMessage.classList.contains('show')) {
            alertMessage.classList.remove('show')
            alertMessage.classList.add('hide')
        }
    })
}


// Videos

var addVideo = document.querySelectorAll('.form__group__video__add')
var deleteVideo = document.querySelectorAll('.form__group__video__delete')
var addImage = document.querySelectorAll('.form__group__image__add')
var deleteImage = document.querySelectorAll('.form__group__image__delete')
var parentDivVideo = document.querySelector('#video__list')
var parentDivImage = document.querySelector('#image__list')

if(parentDivImage !== null){
    if (document.querySelectorAll('.form__group__image__item').length === 0)
        addForm(parentDivImage,'image')
}

if(parentDivImage !== null){
    if (document.querySelectorAll('.form__group__video__item').length === 0)
        addForm(parentDivVideo,'video')
}

if (addVideo !== null) {
    addVideo.forEach(video => {
        video.addEventListener('click', function(e){
            e.preventDefault()
            addForm(parentDivVideo,'video')
        })
    });
}

if (deleteVideo !== null) {
    deleteVideo.forEach(video => {
        video.addEventListener('click', function(e){
            e.preventDefault()
            deleteForm(video,'video')
        })
    });
}

if (addImage !== null) {
    addImage.forEach(image => {
        image.addEventListener('click', function(e){
            e.preventDefault()
            addForm(parentDivImage,'image')
        })
    });
}

if (addImage !== null) {
    deleteImage.forEach(image => {
        image.addEventListener('click', function(e){
            e.preventDefault()
            deleteForm(image,'image')
        })
    });
}


function deleteForm(item,type){
    var div = document.querySelector('div[data-delete="'+item.getAttribute('data-id')+'"]')
    document.querySelector('#'+type+'__list').removeChild(div)
    checkLength(type)
}

function addForm(parentDiv,type) {
    checkLength(type)
    var index = document.querySelectorAll('.form__group__'+type+'__item').length
    var label = parentDiv.getAttribute('data-label')
    var id = parentDiv.getAttribute('data-id')
    id = id.replace(/__name__/g,index);
    label = label.replace(/__name__/g,index)
    var input = parentDiv.getAttribute('data-input')
    input = input.replace(/__name__/g,index)
    var divError = document.createElement('div')
    divError.className = "form__group__error__format"
    var div = document.createElement('div');
    div.className = 'form__group__'+type+'__item'
    div.setAttribute('data-delete', id)
    div.innerHTML = label
    div.id = id
    var divBody = document.createElement('div')
    divBody.className = "form__group__action"
    divBody.innerHTML = input
    var a = document.createElement('a')
    a.className = 'form__group__'+type+'__delete'
    a.setAttribute('data-id',id)
    a.innerText= 'X'
    a.onclick = function(){
        deleteForm(this,type)
    }
    divBody.appendChild(a)
    div.appendChild(divBody)
    div.appendChild(divError)
    var btnAdd = document.querySelector('.form__group__'+type+'__add')
    var divBefore = btnAdd.parentNode
    parentDiv.insertBefore(div,divBefore)
    checkIframes.checkIframes()
}
function checkLength(type) {
    var add = document.querySelector('.form__group__'+type+'__add')
    var countDiv = document.querySelectorAll('.form__group__'+type+'__item').length
    if (countDiv === 3) {
        if (!add.classList.contains('btn__hide')){
            add.classList.add('btn__hide')
        } else {
            add.classList.remove('btn__hide')
        }
    }
}