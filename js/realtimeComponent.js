import $ from 'jquery'
import Vue from 'vue'
window.Vue = Vue
import createComunicator from './comunicator'
import Mixin from './realtimeMixin'
import UserMixin from './vue/mixins/userMixin'
import StoreMixin from './vue/mixins/storeMixin'
import Utils from './vue/plugins/utils'
import Dialog from './vue/plugins/dialog'
import Modal from './vue/plugins/modal'
window.Vue.use(Utils)
window.Vue.use(Dialog)
window.Vue.use(Modal)

window.Vue.component(
    'lance',
    // A dynamic import returns a Promise.
    () => import('./vue/components/lote/Lance')
)

import '../css/app.scss'

export const app = new window.Vue({
    el: '#app',
    provide: function () {
        return {
            app: this
        }
    },
    mixins: [Mixin, UserMixin, StoreMixin],
    components: {},
    data() {
        return {
            testVue: 'VUE is OK!',
            alerts: [],
            currentView: null
        }
    },
    beforeCreate () {
        createComunicator.call(this)
        this.comunicator.on('com/connect', (env) => {
            // this.$refs.fixAlert.showCenter(false)
        })
        this.comunicator.on('com/disconnect', (env) => {
            // this.$refs.fixAlert && this.$refs.fixAlert.showCenter(true, 'Conexão com Realtime perdida, aguarde ou comunique o administrador')
        })
    },
    mounted () {
        document.body.classList.add('app-loaded')
        setTimeout(() => {
            document.body.classList.add('app-anim')
        }, 1)
    },
    watch: {
    },
    methods: {
    },
    delimiters: ["<%", "%>"]
});