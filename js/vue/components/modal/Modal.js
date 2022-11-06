export default {
  provide: function () {
    return {
      modal: this
    }
  },
  props: {
    overlay: {
      type: Boolean,
      default: true
    },
    fullscreen: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      opened: false
    }
  },
  created () {
  },
  methods: {
    open () {
      this.opened = true
      this.$emit('open')
      setTimeout(() => {
        this.$refs.modal.classList.add('after-modal-open')
        document.body.classList.add('app-has-modal')
      }, 10)
    },
    close () {
      this.$refs.modal && this.$refs.modal.classList.remove('after-modal-open')
      document.body.classList.remove('app-has-modal')
      setTimeout(() => {
        this.opened = false
        this.$emit('close')
      }, 130)
    }
  },
  mounted () {
    const container = document.querySelector('.app-modal-container')
    container.appendChild(this.$el)
  },
  beforeDestroy () {
    this.close()
    if (this.$el.parentNode) {
      this.$el.parentNode.removeChild(this.$el)
    }
  },
  computed: {
    classes () {
      const css = []
      // this.opened && css.push('modal-opened')
      this.fullscreen && css.push('modal-fullscreen')
      return css
    }
  },
  render (h) {
    if (!this.opened) {
      return null
    }
    const components = []
    this.overlay && components.push(h('div', {
      staticClass: 'modal-overlay',
      on: {
        click: this.close
      }
    }))
    return h('div', {
      ref: 'modal',
      staticClass: 'app-modal',
      class: this.classes
    }, [
      ...components,
      h('div', {
        staticClass: 'app-modal-content'
      }, this.$slots.default)
    ])
  }
}
