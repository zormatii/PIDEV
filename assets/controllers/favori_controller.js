import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["button"];

    toggle(event) {
        event.preventDefault();

        const commentId = this.data.get("commentId");
        const url = `/commentaire/${commentId}/favorite`;
        const method = "POST";

        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.updateButton(data.isFavorited);
                }
            })
            .catch((error) => console.error("Erreur :", error));
    }

    updateButton(isFavorited) {
        if (isFavorited) {
            this.buttonTarget.textContent = "Retirer des favoris";
            this.buttonTarget.classList.remove("btn-primary");
            this.buttonTarget.classList.add("btn-danger");
        } else {
            this.buttonTarget.textContent = "Ajouter aux favoris";
            this.buttonTarget.classList.remove("btn-danger");
            this.buttonTarget.classList.add("btn-primary");
        }
    }
}