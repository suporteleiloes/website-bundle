const Comunicator = require('comunicator/src/index')
const RealtimeInterface = require('comunicator/src/realtime-service/interface.js')
import $ from 'jquery'
import Vue from 'vue'

const createComunicator = function () {
    Vue.prototype.comunicatorClass = new Comunicator(GATEWAY_SERVER, RealtimeInterface, $)
    Vue.prototype.comunicator = this.comunicatorClass.comunicator.connect(COMUNICATOR_SERVER, {})
    this.comunicator.subscribe('all')

    // bind events
};

export default createComunicator
