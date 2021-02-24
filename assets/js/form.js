var file = document.querySelector('#user_attachment')
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
const parentDiv = document.querySelector('#videos__list')

if (addVideo !== null) {
    addVideo.forEach(video => {
        video.addEventListener('click', function(e){
            e.preventDefault()
            addForm(video)
        })
    });
}

if (deleteVideo !== null) {
    deleteVideo.forEach(video => {
        video.addEventListener('click', function(e){
            e.preventDefault()
            deleteForm(video)
        })
    });
}

function deleteForm(video){
    console.log(video.getAttribute('data-id'))
    var div = document.querySelector('div[data-delete="'+video.getAttribute('data-id')+'"]')
    parentDiv.removeChild(div)
}

function addForm(video) {
    var index = document.querySelectorAll('.form__group__video__item').length
    var prototype = parentDiv.getAttribute('data-prototype');
    var label = parentDiv.getAttribute('data-label')
    label = label.replace(/__name__/g,index)
    var input = parentDiv.getAttribute('data-input')
    input = input.replace(/__name__/g,index)
    var div = document.createElement('div');
    div.className = "form__group__video__item"
    div.innerHTML = label
    var divBody = document.createElement('div')
    divBody.className = "form__group__action"
    divBody.innerHTML = input
    var a = document.createElement('a')
    a.className = 'form__group__video__delete'
    a.innerHTML= 'X'
    a.onclick = function(){
        this.deleteForm(this)
    }
    divBody.appendChild(a)
    div.appendChild(divBody)
    parentDiv.appendChild(div)
}