import Trick from './Trick';
const openModal = require('../modal.js')
class TrickList extends HTMLElement
{
    
    constructor(){
        super();
        this.loading = false
        this.className = "cards"
        this.tricks =  [];
        this.asset = this.getAttribute('asset')
        this.connect = this.getAttribute('connect')
        this.total = 0;
        this.offset = 0;
        this.loading = true;
        this.currentOffset = 0
        // this.parentNode.appendChild = this.loader()
        this.innerHTML = "Chargement des tricks"
    }
    
    loader()
    {
        const div = document.createElement('div')
        const span = document.createElement('span')
        span.className = "loader"
        div.className = "loader__head"
        div.appendChild(span)
        return div
    }
    connectedCallback()
    {
        this.getTricks()
        var _this = this;
        window.addEventListener('scroll',async function(evt){
            if ((window.innerHeight + window.pageYOffset >= document.body.offsetHeight) && (_this.offset < _this.total) ){
                
                var x=window.scrollX;
                var y=window.scrollY;
                window.onscroll=function(){window.scrollTo(x, y);};
                _this.loading = true
                    _this.appendChild(_this.loader())
                // var loader = document.querySelector('.loader')
                // if (loader.classList.contains('hide'))
                //     loader.classList.remove('hide')
                // _this.innerHTML = 'Chargement en cours'
                const raw = await fetch("/api/gettricks/"+ _this.offset,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                // if (!loader.classList.contains('hide'))
                //     loader.classList.add('hide')
                const content = await raw.json()
                _this.tricks = [..._this.tricks,...content.tricks]
                _this.offset += content.tricks.length
                _this.loading = false
                _this.innerHTML = _this.showTrick()
                _this.currentOffset = _this.offset
                openModal.all()
                window.onscroll=function(){};
            } else if ((window.innerHeight + window.pageYOffset >= document.body.offsetHeight) && (_this.offset >=  _this.total) && _this.total > 0 ) {
                if (document.querySelector('.card__end') === null) {
                    const div = document.createElement('div')
                    div.innerHTML = "Fin des tricks"
                    div.className = "card__end"
                    _this.appendChild(div)
                }
            }
        })
    }

    disconnectedCallback()
    {
        this.loading = false
        this.appendChild(this.loader())
        this.tricks =  [];
        this.asset = null
        this.total = 0;
        this.offset = 0;
        this.loading = true;
        this.connect = null;
    }

    async getTricks()
    {
        const raw = await fetch("/api/gettricks",{
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        })
        const content = await raw.json()
        this.tricks = content.tricks
        this.total = content.total
        this.offset = content.tricks.length
        this.loading = false
        this.innerHTML = this.showTrick()
        this.currentOffset = this.offset
        openModal.all()
        if (document.querySelector('.card__end') === null && content.tricks.length === 0) {
            const div = document.createElement('div')
            div.innerHTML = "Aucun tricks"
            div.className = "card__end"
            _this.appendChild(div)
        }
    }

    showTrick()
    {
        var cards = Object.entries(this.tricks).map((trick)=>{
            var date = new Date(trick[1].createdAt)
            return `<trick-card connected="${this.connect}" id="${trick[1].id}" createdAt="${date.getDay()+'/'+date.getMonth()+'/'+date.getFullYear()}" userName="${trick[1].user.name}" name="${trick[1].name}" description="${trick[1].description}" mainImage="${this.asset + '/tricks/' + trick[1].mainImage}"></trick-card>`
        })
        return cards.join('');
    }
}

customElements.define('trick-list',TrickList)