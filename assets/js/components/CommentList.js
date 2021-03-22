import Comment from './Comment';
class CommentList extends HTMLElement
{
    
    constructor(){
        super();
        this.loading = false
        this.comments =  [];
        this.asset = this.getAttribute('asset')
        this.id = this.getAttribute('id')
        this.total = 0;
        this.offset = 0;
        this.loading = true;
        this.currentOffset = 0
        this.loader = this.loader()
        this.loadMore = this.loadMore()
        // this.parentNode.appendChild = this.loader()
        this.innerHTML = "Chargement des commentaires"
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
        this.getComments()
    }

    disconnectedCallback()
    {
        this.loading = false
        // this.parentElement.appendChild(this.loader())
        this.comments =  [];
        this.asset = null
        this.total = 0;
        this.offset = 0;
        this.loading = true;
        this.connect = null;
    }

    async getComments()
    {
        const raw = await fetch("/api/getcomments/" +  this.id,
        {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        })
        const content = await raw.json()
        this.comments = content.comments
        this.total = content.total
        this.offset = content.comments.length
        this.loading = false
        this.innerHTML = this.showComment()
        this.currentOffset = this.offset
        if (!(document.querySelector('.card__end') === null && content.comments.length === 0) && this.total !== this.offset){
            this.parentElement.appendChild(this.loadMore)
        }else {
            if (document.querySelector('.card__end') === null) {
                const div = document.createElement('div')
                div.innerHTML = "Fin des commentaires"
                div.className = "card__end"
                this.appendChild(div)
            }
        }
    }

    async btnClick(ev){
        ev.preventDefault()
        var _this = ev.target.myparam
        if (_this.total > _this.offset) {
            _this.loading = true
            _this.appendChild(_this.loader)
            ev.target.style.display= "none"
            const raw = await fetch("/api/getcomments/" + _this.id + _this.offset,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                const content = await raw.json()
                _this.comments = [..._this.comments,...content.comments]
                _this.offset += content.comments.length
                _this.loading = false
                _this.innerHTML = _this.showComment()
                _this.currentOffset = _this.offset
                if (_this.loader !== null && !_this.loader.classList.contains('hide')){
                    _this.loader.classList.add('hide')
                }
                if (_this.total > _this.offset) {
                    const div = document.createElement('div')
                    _this.parentElement.appendChild(_this.loadMore)
                    ev.target.style.display= "block"
                } else {
                    if (document.querySelector('.card__end') === null) {
                        const div = document.createElement('div')
                        div.innerHTML = "Fin des commentaires"
                        div.className = "card__end"
                        _this.appendChild(div)
                    }
                }
        } 
    }

    showComment()
    {
        var cards = Object.entries(this.comments).map((comment)=>{
            var date = new Date(comment[1].createdAt)
            var day = ("0" + date.getDate()).slice(-2)
            var month = ("0" + (date.getMonth() + 1)).slice(-2)
            return `<comment-card content="${comment[1].content}" id="${comment[1].id}" createdAt="${day+'/'+month+'/'+date.getFullYear()}" userName="${comment[1].user.name}" userImage="${this.asset + '/users/' + comment[1].user.attachment}"></comment-card>`
        })
        return cards.join('');
    }
}

customElements.define('comment-list',CommentList)