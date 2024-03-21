import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = ['modal'];
    openModal(event) {
        console.log('Modal controller');
        const modal = new Modal(this.modalTarget);
        modal.show();
    }
}
