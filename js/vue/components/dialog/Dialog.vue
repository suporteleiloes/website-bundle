<template>
  <div class="dialog-window" :class="classes">
    <div @click="() => closeClickoutside && close()" class="dialog-overlay" v-if="overlay"></div>
    <slot>
      <div class="dialog-body">
        <div class="dialog-title">{{title}}</div>
        <div class="dialog-message">{{message}}</div>
        <div class="dialog-footer">
          <button v-if="okBtn" @click="clickOk" class="okBtn">Ok</button>
          <button v-if="cancelBtn" @click="clickCancel" class="cancelBtn">Cancelar</button>
        </div>
      </div>
    </slot>
  </div>
</template>
<script>
/* eslint-disable */
export default {
  props: {
    overlay: {
      type: Boolean,
      default: true
    },
    closeClickoutside: {
      type: Boolean,
      default: true
    },
    okBtn: {
      type: Boolean,
      default: true
    },
    cancelBtn: {
      type: Boolean,
      default: false
    },
    title: {required: false},
    message: {required: false},
    wid: {required: true}
  },
  data() {
    return {
      opened: false
    }
  },
  beforeDestroy() {
    this.$el.parentNode.removeChild(this.$el);
  },
  computed: {
    classes() {
      const css = []
      this.opened && css.push('opened')
      return css
    }
  },
  methods: {
    show() {
      this.opened = true
    },
    clickOk () {
      this.$dialog.emit(this.wid, 'clickOk')
      this.close()
    },
    clickCancel () {
      this.$dialog.emit(this.wid, 'clickCancel')
      this.close()
    },
    close() {
      this.opened = false
      setTimeout(() => {
        this.$dialog.emit(this.wid, 'close')
      }, 1)
    }
  }
}
</script>
