function css (element, css) {
  const style = element.style

  Object.keys(css).forEach(prop => {
    style[prop] = css[prop]
  })
}

export default {
  name: 'AppModalContainer',
  data () {
    return {
      zindex: 799
    }
  },
  created () {
    // this.instances = []
  },
  methods: {
  },
  mounted () {
    const body = document.body
    body.appendChild(this.$el)
    css(this.$el, {'z-index': this.zindex})
  },
  beforeDestroy () {
    if (this.$el.parentNode) {
      this.$el.parentNode.removeChild(this.$el)
    }
  },
  render (h) {
    const components = []
    return h('div', {
      staticClass: 'app-modal-container'
    }, [
      ...components,
      this.$slots.default
    ])
  }
}
