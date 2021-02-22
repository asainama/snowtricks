var file = document.querySelector('#user_attachment')
if(file !== null){
    file.addEventListener('change',function(e){
        const preview = document.querySelector('#preview')
        preview.src = URL.createObjectURL(e.target.files[0])
    })
}