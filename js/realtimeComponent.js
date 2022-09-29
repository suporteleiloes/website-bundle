import $ from 'jquery'
import Vue from 'vue'
import createComunicator from './comunicator'
import Mixin from './realtimeMixin'
import UserMixin from './vue/mixins/userMixin'
import Utils from './vue/plugins/utils'
Vue.use(Utils)

new Vue({
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
    },
    watch: {
    },
    methods: {
    },
    delimiters: ["<%", "%>"]
});