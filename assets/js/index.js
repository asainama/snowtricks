window.addEventListener('scroll',function(evt){
    if (window.innerHeight + window.pageYOffset >= document.body.offsetHeight){
        alert('Bottom page')
    }
})