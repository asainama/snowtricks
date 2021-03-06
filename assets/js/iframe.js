
function checkIframes ()
{
    var iframes = document.querySelectorAll('.form__group__iframe')
    
    if (iframes !== null){
        iframes.forEach(function(item){
            item.addEventListener('change', function(event){
                checkIsIframe(item)
            })
        })
    }
}
function checkIsIframe(item)
{
    if (item.value.indexOf('iframe') === -1) {
        var btnSubmit = document.querySelector('#tricks_Valider')
        btnSubmit.disabled = true
        var error = document.querySelector('#'+item.parentNode.parentNode.getAttribute('id') +' > .form__group__error__format');
        if (!error.classList.contains('invalid')){
            error.classList.add('invalid')
        }
        error.innerText = "Le iframe de la video est incorrect"
    } else {
        var btnSubmit = document.querySelector('#tricks_Valider')
        btnSubmit.disabled = false
        var regex = 'src\s*=\s*"(.+?)"'
        var url = item.value.match(regex)[1]
        item.value = url
        var error = document.querySelector('#'+item.parentNode.parentNode.getAttribute('id') +' > .form__group__error__format');
        error.innerText = ""
        if (error.classList.contains('invalid')){
            error.classList.remove('invalid')
        }
        console.log('L\'url' + url)
    }
    checkErrorIsExists()
}

function checkErrorIsExists()
{
    var errors = document.querySelectorAll('.form__group__error__format.invalid')
    console.log(errors.length)
    var form = document.querySelector('form')
    var btnSubmit = form.querySelector('button[type="submit"]')
    if (errors.length > 0) {
        if (!btnSubmit.classList.contains('btn__hide')){
            btnSubmit.classList.add('btn__hide')
        }
    } else {
        if (btnSubmit.classList.contains('btn__hide')){
            btnSubmit.classList.remove('btn__hide')
        }
    }
}

module.exports = {
    checkIframes
}