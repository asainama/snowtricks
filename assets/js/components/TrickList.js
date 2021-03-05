import Trick from './Trick';

class TrickList extends HTMLElement
{
    
    constructor(){
        super();
        this.loading = false
        this.innerHTML = 'Chargement des tricks'
        this.tricks =  [];
        this.asset = this.getAttribute('asset')
        this.total = 0;
        this.offset = 0;
        this.loading = true;
    }

    connectedCallback()
    {
        this.getTricks()
        var _this = this;
        window.addEventListener('scroll',async function(evt){
            if ((window.innerHeight + window.pageYOffset >= document.body.offsetHeight) && _this.offset < _this.total){
                console.log('Bottom page ' + _this.offset)
                _this.loading = true
                _this.innerHTML = 'Chargement en cours'
                const raw = await fetch("/api/gettricks/"+ _this.offset,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                const content = await raw.json()
                _this.tricks = [..._this.tricks,...content.tricks]
                _this.offset += content.tricks.length
                _this.loading = false
                _this.innerHTML = _this.showTrick()
            }
        })
    }

    disconnectedCallback()
    {
        this.loading = false
        this.innerHTML = 'Chargement des tricks'
        this.tricks =  [];
        this.asset = null
        this.total = 0;
        this.offset = 0;
        this.loading = true;
    }

    attributeChangedCallback(name, oldValue, newValue)
    {

    }

    async getTricks()
    {
        const raw = await fetch("/api/gettricks",{
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            // body: JSON.stringify({})
        })
        const content = await raw.json()
        this.tricks = content.tricks
        this.total = content.total
        this.offset = content.tricks.length
        this.loading = false
        this.innerHTML = this.showTrick()
    }

    showTrick()
    {
        var cards = Object.entries(this.tricks).map((trick)=>{
            var date = new Date(trick[1].createdAt)
            return `<trick-card createdAt="${date.getDay()+'/'+date.getMonth()+'/'+date.getFullYear()}" userName="${trick[1].user.name}" name="${trick[1].name}" description="${trick[1].description}" mainImage="${this.asset + '/tricks/' + trick[1].mainImage}"></trick-card>`
        })
        return cards.join();
    }
}

customElements.define('trick-list',TrickList)