var wrapper = document.querySelector('.modal__wrapper__body__medias__choose__content')
var cancelBtn = document.querySelector('.modal__wrapper__body__medias__choose__content__cancel')
var mediaBtn = document.querySelector('#medias-btn')
var customBtn = document.querySelector('.modal__wrapper__body__medias__choose__btn')
const img = document.querySelector('.modal__wrapper__body__medias__choose__content__img')
const formImg = document.querySelector('#form__img')
const formSubmit = document.querySelector('.form__img__submit')
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
            pic.src = asset + data.file
            var edit = document.createElement('a')
            edit.href= "#"
            var del = document.createElement('a')
            del.href= "#"
            divChild.appendChild(edit,del);
            div.appendChild(pic,divChild);
            parent.appendChild(div)
            img.src = ""; 
            wrapper.classList.remove('active')
            customBtn.classList.remove('valid')
            customBtn.innerHTML = "Choisir une image"
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