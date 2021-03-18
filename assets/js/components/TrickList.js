import Trick from './Trick';
const openModal = require('../modal.js')

class DomUtils {
    // left: 37, up: 38, right: 39, down: 40,
    // spacebar: 32, pageup: 33, pagedown: 34, end: 35, home: 36
    static keys = { 37: 1, 38: 1, 39: 1, 40: 1 };
  
    static preventDefault(e) {
      e = e || window.event;
      if (e.preventDefault) e.preventDefault();
      e.returnValue = false;
    }
  
    static preventDefaultForScrollKeys(e) {
      if (DomUtils.keys[e.keyCode]) {
        DomUtils.preventDefault(e);
        return false;
      }
    }
  
    static disableScroll() {
      document.addEventListener('wheel', DomUtils.preventDefault, {
        passive: false,
      }); // Disable scrolling in Chrome
      document.addEventListener('keydown', DomUtils.preventDefaultForScrollKeys, {
        passive: false,
      });
    }
  
    static enableScroll() {
      document.removeEventListener('wheel', DomUtils.preventDefault, {
        passive: false,
      }); // Enable scrolling in Chrome
      document.removeEventListener(
        'keydown',
        DomUtils.preventDefaultForScrollKeys,
        {
          passive: false,
        }
      ); // Enable scrolling in Chrome
    }
  }
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
        this.loader = this.loader()
        this.loadMore = this.loadMore()
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

    loadMore()
    {
        const div = document.createElement('div')
        div.className ="btn btn__primary load__more"
        div.onclick = div.addEventListener('click',this.btnClick)
        div.myparam = this
        div.innerHTML = "Load More"
        return div
    }
    connectedCallback()
    {
        this.getTricks()
    }

    disconnectedCallback()
    {
        this.loading = false
        this.parentElement.appendChild(this.loader())
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
        // if (document.querySelector('.card__end') === null && content.tricks.length === 0) {
        //     const div = document.createElement('div')
        //     div.innerHTML = "Aucun tricks"
        //     div.className = "card__end"
        //     _this.appendChild(div)
        // } 
        if (!(document.querySelector('.card__end') === null && content.tricks.length === 0)){
            this.parentElement.parentElement.appendChild(this.loadMore)
        }
    }

    async btnClick(ev){
      ev.preventDefault()
      var _this = ev.target.myparam
        if (_this.total > _this.offset) {
          _this.loading = true
          _this.appendChild(_this.loader)
          ev.target.style.display= "none"
            const raw = await fetch("/api/gettricks/"+ _this.offset,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                const content = await raw.json()
                console.log(content)
                _this.tricks = [..._this.tricks,...content.tricks]
                _this.offset += content.tricks.length
                _this.loading = false
                _this.innerHTML = _this.showTrick()
                _this.currentOffset = _this.offset
                if (_this.loader !== null && !_this.loader.classList.contains('hide')){
                    _this.loader.classList.add('hide')
                }
                openModal.all()
                if (_this.total > _this.offset) {
                  const div = document.createElement('div')
                  this.parentElement.parentElement.appendChild(_this.loadMore)
                } else {
                  if (document.querySelector('.card__end') === null) {
                      const div = document.createElement('div')
                      div.innerHTML = "Fin des tricks"
                      div.className = "card__end"
                      _this.appendChild(div)
                  }
              }
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