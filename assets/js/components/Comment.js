export default class Comment extends HTMLElement
{
    constructor(){
        super();
        this.loading = false
        this.name = this.getAttribute('name')
        this.userImage = this.getAttribute('userImage')
        this.createdAt = this.getAttribute('createdAt')
        this.userName = this.getAttribute('userName')
        this.content = this.getAttribute('content')
        this.connect = (this.getAttribute('connected') === "1") ? "card__body__infos__actions__connected" : ""
        this.innerHTML = `
        <div class="modal__wrapper__body__comments__item">
            <div class="modal__wrapper__body__comments__item__img">
                <img src="${this.userImage}" alt="${this.userName}">
            </div>
            <div class="modal__wrapper__body__comments__item__body">
                <span>${this.createdAt}</span>
                <p>${this.content}</p>
            </div>
        </div>`
    }
}

customElements.define('comment-card',Comment)
