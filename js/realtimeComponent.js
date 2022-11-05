import $ from 'jquery'
import Vue from 'vue'
import createComunicator from './comunicator'
import Mixin from './realtimeMixin'
import UserMixin from './vue/mixins/userMixin'
import Utils from './vue/plugins/utils'
import Dialog from './vue/plugins/dialog'
Vue.use(Utils)
Vue.use(Dialog)

Vue.component(
    'lance',
    // A dynamic import returns a Promise.
    () => import('./vue/components/lote/Lance')
)

import '../css/app.scss'

const app = new Vue({
    el: '#app',
    mixins: [Mixin, UserMixin],
    components: {},
    data() {
        return {
            testVue: 'VUE is OK!',
            alerts: []
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