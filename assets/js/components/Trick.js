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
        this.innerHTML = `
        <div class="card">
            <div class="card__header">
                <img src="${this.mainImage}" alt="${this.name}">
            </div>
            <div class="card__body">
                <p class="card__body__title">${this.name}</p>
                <div class="card__body__subtitle">
                    <p class="card__body__date">${this.createdAt}</p>
                    <p class="card__body__author">${this.userName}</p>
                </div>
                <p class="card__body__description">${this.description}</p>
                <a href="#" class="btn btn__primary">Voir plus</a>
            </div>
        </div>`
    }
}

customElements.define('trick-card',Trick)