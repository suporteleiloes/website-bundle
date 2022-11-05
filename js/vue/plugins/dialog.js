import DialogContainer from '../components/dialog/DialogContainer'
import Dialog from '../components/dialog/Dialog'

function uid() {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(8)
    );
}


/* eslint no-unused-vars: off */
let plugin
const instances = []
const listeners = []

function createVM (Vue, Container, wid, config, slot) {
    return new Vue({
        provide: {
            container: Container
        },
        data () {
            return {
                wid: wid
            }
        },
        methods: {},
        render: h => h(Dialog, {
            ref: `dialog_${wid}`,
            props: {
                wid: wid,
                ...config
            }
        }, slot.isSimpleNode ? slot.default : [h(slot.default, {
            props: config.props || {}
        })]),
        mounted () {
            this.$refs[`dialog_${wid}`].show()
        }
    })
}

function init ({Vue}) {
    this.container = new Vue(DialogContainer)
    document.body.appendChild(this.container.$mount().$el)
}

function dialogPlugin (Component, Vue, Container) {
    return {
        /**
         * Create an new Dialog Window
         * @param config
         * @return Promise
         */
        'new' (config, slot) {
            config = config || {}
            return new Promise((resolve, reject) => {
                let content, reason
                let wid = config.wid ? config.wid : uid()
                if (typeof instances[wid] !== 'undefined') {
                    if (instances[wid].pending) {
                        let reason = new Error('Window is opening, wait...')
                    }
                    reject(reason || new Error(`Window *${wid}* already exists`))
                    return
                }
                instances[wid] = {pending: true}
                if (typeof slot === 'function') {
                    content = slot
                }
                else {
                    content = () => new Promise((resolve, reject) => {
                        resolve({'default': slot, 'isSimpleNode': typeof slot !== 'object'})
                    })
                }
                content().then((c) => {
                    const vm = createVM.call(this, Vue, Container, wid, config, c)
                    Container.$el.appendChild(vm.$mount().$el)
                    instances[wid] = vm
                    listeners[wid] = {}
                    resolve(wid)
                })
            })
        },

        /**
         * Get an window instance by wid
         * @param wid
         * @return UPrintView
         */
        get (wid) {
            return instances[wid]
        },

        /**
         * Close an window by wid
         * @param wid
         * @return boolean
         */
        close (wid) {
            console.log(`Destroy Window with WID ${wid}`)
            if (typeof instances[wid] === 'undefined') {
                throw new Error(`Window with WID ${wid} not found`)
            }
            let vm = instances[wid]
            vm.$destroy()
            if (typeof listeners[wid] !== 'undefined') {
                delete listeners[wid]
            }
            delete instances[wid]
            console.log(`Destroy successfuly`)
        },

        /**
         * Add event listeners to window by wid
         * @param wid
         * @param events
         * @return wid
         */
        listen (wid, events) {
            if (!(events instanceof Object)) {
                throw new Error('**events** need a object.')
            }
            if (typeof listeners[wid] === 'undefined') {
                listeners[wid] = {}
            }
            Object.keys(events).map((event, index) => {
                if (typeof listeners[wid][event] === 'undefined') {
                    listeners[wid][event] = []
                }
                listeners[wid][event].push(events[event])
            })
        },

        /**
         * Turn off an window event listener
         * @param wid
         * @param event
         * @param listener
         */
        turnOffListener (wid, event, listener) {
            if (typeof instances[wid] === 'undefined') {
                throw new Error(`Window with WID ${wid} not found`)
            }
            if (typeof listeners[wid][event] !== 'undefined' && listeners[wid][event] instanceof Array) {
                var _listeners = listeners[wid][event]
                for (var i = 0, len = _listeners.length; i < len; i++) {
                    if (_listeners[i] === listener) {
                        _listeners.splice(i, 1)
                        break
                    }
                }
            }
        },

        /**
         * Emit window events listeners by wid and event name
         * @param wid
         * @param event
         * @param data
         */
        emit (wid, event, data) {
            if (typeof instances[wid] === 'undefined') {
                throw new Error(`Fail to emit event ${event.type ? event.type : event}. Window with WID ${wid} not exits`)
            }

            if (typeof event === 'string') {
                event = {type: event, data: data}
            }

            if (!event.type) {
                throw new Error('Event object missing *type* property.')
            }

            if (typeof listeners[wid][event.type] !== 'undefined' && listeners[wid][event.type] instanceof Array) {
                var _listeners = listeners[wid][event.type]
                for (var i = 0, len = _listeners.length; i < len; i++) {
                    _listeners[i](wid, event.data ? event.data : null)
                }
            }
            event.type === 'close' && this.close(wid)
        }
    }
}

export default {
    install (Vue, options) {
        init.call(this, {Vue})
        this.create = Vue.prototype.$dialog = dialogPlugin(Dialog, Vue, this.container)
    }
}
