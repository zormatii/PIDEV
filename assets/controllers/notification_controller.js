import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["container"];

    connect() {
        console.log("Notification controller connecté ✅");
        document.addEventListener('notification:show', (event) => {
            this.show(event);
        });
    }

    show(event) {
        console.log("Notification reçue 🚨");

        const message = event.detail.message;
        const notif = document.createElement('div');
        notif.innerHTML = message;
        notif.className = 'alert alert-success mt-2';

        this.containerTarget.appendChild(notif);

        setTimeout(() => {
            notif.remove();
        }, 3000);
    }
}
