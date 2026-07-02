import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        addLabel: String,
        deleteLabel: String,
    };

    connect() {
        this.index = this.element.childElementCount;

        const btn = document.createElement("button");
        btn.setAttribute("class", "btn btn-secondary");
        btn.setAttribute("type", "button");
        btn.textContent = this.addLabelValue || "Ajouter un ingrédient";
        btn.addEventListener("click", this.addElement);
        this.element.childNodes.forEach(this.addDeleteElement);
        this.element.appendChild(btn);
    }

    /**
     *
     * @param {MouseEvent} e
     */
    addElement = (e) => {
        e.preventDefault();
        const element = document
            .createRange()
            .createContextualFragment(
                this.element.dataset["prototype"].replaceAll(
                    /__name__/g,
                    this.index,
                ),
            ).firstElementChild;
        this.addDeleteElement(element);
        this.index++;
        e.currentTarget.insertAdjacentElement("beforebegin", element);
    };

    /**
     *
     * @param {HTMLElement} item
     */
    addDeleteElement = (item) => {
        const btn = document.createElement("button");
        btn.setAttribute("class", "btn btn-secondary");
        btn.setAttribute("type", "button");
        btn.textContent = this.deleteLabelValue || "Supprimer un ingrédient";
        item.appendChild(btn);
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            item.remove();
        });
    };
}
