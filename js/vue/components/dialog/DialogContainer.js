function css (element, css) {
  const style = element.style

  Object.keys(css).forEach(prop => {
    style[prop] = css[prop]
  })
}

export default {
  name: 'AppDialogContainer',
  data () {
    return {
      zindex: 1000
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
      staticClass: 'app-dialog-container'
    }, [
      ...components,
      this.$slots.default
    ])
  }
}
