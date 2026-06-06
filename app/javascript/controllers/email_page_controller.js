import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
  static targets = ["content"]

  select(event) {
    this.element.querySelector(".active")?.classList.remove("active")
    event.currentTarget.classList.add("active")
    
    fetch(`/emails/${event.params.id}`)
      .then(response => response.text())
      .then(html => {
        this.contentTarget.innerHTML = html
      })
  }
}
