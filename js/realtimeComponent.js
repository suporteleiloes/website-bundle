import Vue from 'vue'
import createComunicator from './comunicator'
import Mixin from './realtimeMixin'
import UserMixin from './vue/mixins/userMixin'
import StoreMixin from './vue/mixins/storeMixin'
import Utils from './vue/plugins/utils'
import Dialog from './vue/plugins/dialog'
import Modal from './vue/plugins/modal'
import {REAL_BRL} from "./vue/utils/money"
import {VMoney} from 'v-money'

import '../css/app.scss'

let app

const createApp = () => {
    Vue.use(Utils)
    Vue.use(Dialog)
    Vue.use(Modal)
    Vue.component(
        'lance',
        // A dynamic import returns a Promise.
        () => import('./vue/components/lote/Lance')
    )
    return app = new Vue({
        el: '#app',
        provide: function () {
            return {
                app: this
            }
        },
        directives: {money: VMoney},
        mixins: [Mixin, UserMixin, StoreMixin],
        components: {},
        data() {
            return {
                testVue: 'VUE is OK!',
                money: REAL_BRL,
                alerts: [],
                currentView: null
            }
        },
        beforeCreate() {
            createComunicator.call(this)
            this.comunicator.on('com/connect', (env) => {
                // this.$refs.fixAlert.showCenter(false)
            })
            this.comunicator.on('com/disconnect', (env) => {
                // this.$refs.fixAlert && this.$refs.fixAlert.showCenter(true, 'Conexão com Realtime perdida, aguarde ou comunique o administrador')
            })
        },
        mounted() {
            document.body.classList.add('app-loaded')
            setTimeout(() => {
                document.body.classList.add('app-anim')
            }, 1)
        },
        watch: {},
        methods: {},
        delimiters: ["<%", "%>"]
    })
};

export {
    Vue,
    app,
    createApp
}