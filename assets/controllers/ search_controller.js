import { Controller } from "@hotwired/stimulus";
import axios from "axios";

export default class extends Controller {
    static targets = ["list", "spinner"];

    search(event) {
        const query = event.target.value;
        this.spinnerTarget.classList.remove("d-none");

        axios.get(`/blog/search?q=${query}`)
            .then(response => {
                this.listTarget.innerHTML = response.data;
                this.spinnerTarget.classList.add("d-none");
            })
            .catch(() => {
                console.log("Erreur AJAX");
            });
    }
}
