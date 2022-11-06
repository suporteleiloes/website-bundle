import ModalContainer from '../components/modal/ModalContainer.js'
export default {
  install: function (Vue, options) {
    this.container = new Vue(ModalContainer)
    document.body.appendChild(this.container.$mount().$el)
    Vue.prototype.appModal = this.container
  }
}
