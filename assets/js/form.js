var file = document.querySelector('#user_attachment')
if(file !== null){
    file.addEventListener('change',function(e){
        const preview = document.querySelector('#preview')
        preview.src = URL.createObjectURL(e.target.files[0])
    })
}

var alertClose = document.querySelector('.alert__close')
if (alertClose !== null) {
    alertClose.addEventListener('click',function(e){
        console.log(alertClose)
        var alertMessage = document.querySelector('.alert')
        if (alertMessage.classList.contains('show')) {
            alertMessage.classList.remove('show')
            alertMessage.classList.add('hide')
        }
    })
}