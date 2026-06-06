import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["content"]

  select(event) {
    fetch(`/emails/${event.params.id}`)
      .then(response => response.text())
      .then(html => {
        this.contentTarget.innerHTML = html
      })
  }
}
