/* eslint-disable */
const Events = require('./bindEventListeners')
const Cronometro = require('./ext/cronometro.mixin')
const {REAL_BRL, convertRealToMoney} = require("../utils/money")
const VMoney = require("v-money")

const Component = {
  props: ['leilaoData', 'loteUrl', 'thumb', 'userData'],
  directives: {money: VMoney.VMoney},
  mixins: [Events, Cronometro],
  data () {
    return {
      money: REAL_BRL,
      leilao: JSON.parse(this.leilaoData),
      user: this.userData ? JSON.parse(this.userData) : null,
      hasNovoLance: false,
      isLancando: false,
      valorLance: 0.00,
      audioNotification: true
    }
  },
  mounted () {
    console.log(document.getElementById('c-bonus'))
  },
  computed: {
    bindCronometroClass () {
      const css = []
      const secondsLeft = Number(this.timerPregao)
      if (secondsLeft < 13 && secondsLeft > 5) {
        css.push('orange')
      }
      if (secondsLeft < 6) {
        css.push('red')
      }
      return css
    },
    isFechado () {
      return Number(this.leilao.status) !== 1
    },
    isPermitidoLance () {
      return Number(this.leilao.status) === 1 && this.timeLeft > 0
    },
    ultimosLances () {
      if (this.leilao.ultimosLances && Array.isArray(this.leilao.ultimosLances) && this.leilao.ultimosLances.length) {
        return this.leilao.ultimosLances.slice(0,10)
      }
      return []
    },
    ultimoLance() {
      if (this.ultimosLances.length) {
        return this.ultimosLances[0]
      }
      return null
    },
    valorAtual () {
      if (this.ultimoLance) {
        return this.leilao.totalLances * 0.01
      }
      return 0
    },
    totalLances () {
      return this.leilao.totalLances
    }
  },
  methods: {
    /**
     * Verifica se a comunicação recebida do realtime é relacionada ao leilão renderizado em tela
     * @param data
     * @returns {boolean}
     */
    isLeilaoComunication (data) {
      console.log('XXX,', data)
      let _data = data
      if (!_data || !_data.leilao || !_data.leilao.id) return false
      if (!this.leilao) return false
      return _data.leilao.id === this.leilao.id
    },
    lance () {
      this.isLancando = true
      const valor = convertRealToMoney(this.valorLance)
      this.comunicatorClass.lance(this.leilao.id, valor)
          .then(response => {
            this.isLancando = false
            document.getElementById('c-bonus').innerText = Number(response.saldo.bonus)
            document.getElementById('c-saldo').innerText = Number(response.saldo.valor)
            console.log(response)
            this.valorLance = 0.00
            this.ativaTimer()
          })
          .catch(error => {
            this.isLancando = false
            this.alertApiError(error)
          })
    },
    __parseLance (data) {
      if (!this.isLeilaoComunication(data)) return
      this.leilao.ultimosLances.unshift(data)
      this.leilao.totalLances = this.leilao.totalLances + 1
    },
    __encerrarLeilao (data) {
      if (!this.isLeilaoComunication(data)) return
      console.log('Encerramento Leilão', data)
      this.leilao.status = 2
      this.desativaTimer()
    },
    isMeuLance (lance) {
      if(!this.user || !this.user.user || !this.user.user.id) return false
      return this.user.user.id === lance.autor.id
    }
  }
}

module.exports = Component
