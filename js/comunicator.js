const Comunicator = require('comunicator/src/index')
const RealtimeInterface = require('comunicator/src/realtime-service/interface.js')
import axios from 'axios'
import Vue from 'vue'

const createComunicator = function () {
    axios.defaults.baseURL = SL_API
    Vue.prototype.comunicatorClass = new Comunicator(null, RealtimeInterface, axios)
    Vue.prototype.comunicator = this.comunicatorClass.comunicator.connect(COMUNICATOR_SERVER, {})
    this.comunicator.subscribe('all')

    // bind events
};

export default createComunicator
