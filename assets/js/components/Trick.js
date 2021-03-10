export default class Trick extends HTMLElement
{
    constructor(){
        super();
        this.loading = false
        this.name = this.getAttribute('name')
        this.mainImage = this.getAttribute('mainImage')
        this.createdAt = this.getAttribute('createdAt')
        this.userName = this.getAttribute('userName')
        this.description = this.getAttribute('description')
        this.connect = (this.getAttribute('connected') === "1") ? "card__body__infos__actions__connected" : ""
        this.innerHTML = `
        <div class="card">
            <div class="card__header">
                <img src="${this.mainImage}" alt="${this.name}">
            </div>
            <div class="card__body">
                <div class="card__body__infos">
                    <p class="card__body__title">${this.name}</p>
                    <div class="card__body__infos__actions ${this.connect}">
                        <a href="/ajax/${this.getAttribute('id')}" class="js-modal">M</a>
                        <a href="#">D</a>
                    </div>
                </div>
            </div>
        </div>`
    }
}

customElements.define('trick-card',Trick)

{/* <div class="card__body__subtitle">
<p class="card__body__date">${this.createdAt}</p>
<p class="card__body__author">${this.userName}</p>
</div>
<p class="card__body__description">${this.description}</p>
<a href="#" class="btn btn__primary">Voir plus</a> */}