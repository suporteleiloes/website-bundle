<template>
  <div class="prevent-app-load">
    <div id="lote-lance" class="app-lance">
      <div class="col1" v-if="showHistoricoLances">
        <h2 class="al-title"><strong>Últimos lances</strong></h2>

        <table class="app-lance-history">
          <thead>
          <tr>
            <th>Usuário</th>
            <th>Data</th>
            <th>Valor</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(lance, k) in lancesLimited" :class="{meuLance: lance.arrematante.id === ARREMATANTEID}">
            <td>{{ lance.arrematante.apelido }} <span v-if="lance.arrematante.id === ARREMATANTEID"
                                                      class="label-meu-lance">você</span></td>
            <td>{{ lance.data|formatDate('dd/MM/yyyy HH:mm:ss') }}</td>
            <td>R$ {{ lance.valor|moeda }}</td>
          </tr>
          <tr v-if="lote.lances.length === 0">
            <td colspan="3" style="text-align: left; font-weight: normal">Nenhum lance registrado para este lote.
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="col2 valores" v-if="showTaxas">
        <div class="avaliacao"><span>Avaliação:</span> R$ {{ lote.valorAvaliacao|moeda }}</div>
        <div class="lance-minimo"><span>Lance Mínimo:</span> R$ {{ lote.valorAtual|moeda }}</div>
        <div class="incremento"><span>Incremento:</span> R$ {{ lote.valorIncremento|moeda }}</div>
        <div class="taxas" v-if="leilao.sistemaTaxa && leilao.sistemaTaxa.taxas">
          <div v-for="taxa in leilao.sistemaTaxa.taxas" :key="'taxa-' + taxa.id">
            <span class="label">{{ taxa.nome }}:</span> <span class="valor" v-if="taxa.tipo === 1">{{
              taxa.valor
            }}%</span><span class="valor" v-else>R$ {{ taxa.valor }}</span>
          </div>
        </div>
        <!--      {% if lote.leilao.sistemaTaxa %}
              <div class="taxas">
                {% for taxa in lote.leilao.sistemaTaxa.taxas %}
                <div>{{ taxa.nome }}: {% if taxa.tipo == 1 %}{{ taxa.valor }}%{% else %}{% endif %}</div>
                {% endfor %}
              </div>
              {% endif %}-->
      </div>
      <div class="col3" :class="{naologado: !ARREMATANTEID, isLancando: isLancando}">
        <h2 class="al-title"><strong>Tem interesse? Dê seu lance</strong></h2>
        <div v-if="isLancando && leilao.permitirParcelamento && lote.permitirParcelamento" class="lancando-tipoLance">
          <div>
            <label>Tipo do lance</label>
            <select class="seletor-parcelas" v-model="lance.parcelamento.lanceParcelado">
              <option v-for="(tipo, index) in [{label: 'À Vista', value: false}, {label: 'Parcelado', value: true}]" :value="tipo.value" :key="'lance-tipo-' + index">{{tipo.label}}</option>
            </select>
          </div>
          <div v-if="lance.parcelamento.lanceParcelado" class="lancador-btns">
            <div>
              <label>Parcelar em</label>
              <select class="seletor-parcelas" v-model="tmpParcelas">
                <option disabled>Selecione</option>
                <option v-for="(p, index) in parcelas" :value="p.value" :key="'lance-parcela-' + index">{{p.label}}</option>
              </select>
              <div class="lance-manual-tab">
                <label>Entrada (Mínimo: {{lanceParceladoEntradaMinima}}%)</label>
                <input type="number" class="input input-lance-parcelado-porcentagem" autofocus v-model="lance.parcelamento.entrada" />
              </div>
            </div>
          </div>
        </div>
        <div class="aba-content" v-if="!analisandoLote && !lance.configurarLanceAutomaticoLayout">
          <div class="lance-options">
            <button class="btn lance" @click="iniciarLance">
              <span>Efetuar Lance</span>
            </button>

            <div class="line2">
              <button class="btn b-automatico" @click="configurarLanceAutomatico">
                <span class="hidden-xs mr-1">Lance</span> Automático
              </button>
              <button @click="gotoAuditorio" class="btn auditorio">
                Auditório
              </button>
            </div>
            <!--            <button class="btn habilitese">
                          Habilite-se
                        </button>-->
            <habilitacao-btn/>
          </div>

          <div class="lote-notification-sound">
            <label for="audioNotification">
              <small>Sons de notificação</small>
              <input type="checkbox" id="audioNotification" v-model="audioNotification">
              <span class="checkmark"></span>
            </label>
          </div>
        </div>
        <div class="aba-content" v-else-if="lance.configurarLanceAutomaticoLayout && analiseLote">
          <div class="timer-container">
            <div class="lance-automatico-status">
              <div class="on" v-if="analiseLote.lanceAutomatico && analiseLote.lanceAutomatico.active">Lance Automático Ligado</div>
              <div class="off" v-else>Lance Automático&nbsp;<strong>Desligado</strong></div>
            </div>
          </div>
          <div class="lance-auto-tab">
            <div class="lance-auto-label">
              <div class="lance-configurado-alert" v-if="analiseLote.lanceAutomatico && analiseLote.lanceAutomatico.active">
                Você já configurou um lance automático para este lote, caso queira alterar o valor basta modificá-lo abaixo.
                <button @click="cancelarLanceAutomatico" class="btn auditorio bg-danger mt-2 mb-2">Cancelar meu lance automático</button>
              </div>
              <span v-else>Informe o valor máximo que podemos lançar em seu nome para este lote</span>
            </div>
            <input class="input-valor-lance-auto input" autofocus v-model="lance.valorLanceAutomatico" v-money.lazy="money" />
          </div>
          <div v-if="configurarLanceAutomatico" class="lancador-btns lance-options"> <!-- AQUI POR CAUSA DA ORDEM O BTN -->
            <div class="lance-auto-tab">
              <button @click="confirmarLanceAutomatico" class="btn lance bg-positive lance-confirmar-manual">
                <span v-if="analiseLote.lanceAutomatico && analiseLote.lanceAutomatico.active">Confirmar e Alterar Meu Lance Automático</span>
                <span v-else>Confirmar e Salvar Lance Automático</span>
              </button>
              <button @click="cancelarLance" class="btn login m-t-none">
                <span>Cancelar</span>
              </button>
            </div>
          </div>
        </div>
        <div class="aba-content mt-3" v-else>
          <i class="fa fa-spin fa-spinner mr-3" /> Analisando informações do lote...
        </div>
        <div class="aba-naologado">
          <p>É necessário estar logado para participar do leilão e efetuar lances</p>

          <a href="/login" class="btn login">Login</a>
          <a href="/cadastro" class="btn cadastro">Cadastre-se</a>
        </div>
        <div class="lancador">
          <div class="lance-options">
            <div v-if="!lance.confirmarLance">
              <button @click="efetuarLance('inicial')" v-if="!ultimoLance" class="btn lance">
                Efetuar Lance Mínimo
              </button>
              <button @click="efetuarLance('incremento')" v-else class="btn lance">
                Lance Incremento +R${{ lanceIncremento|moeda }}
              </button>
            </div>
            <button @click="cancelarLance" class="btn login m-t-none">
              Cancelar
            </button>
            <div v-if="lance.confirmarLance">
              <button @click="confirmarLance" class="btn lance">
                Confirmar Lance de R${{ lance.valorLance|moeda }}?
              </button>
            </div>
            <div class="lance-manual">
              <p>Ou digite o valor do lance que você deseja:</p>
              <div>
                <input v-money.lazy="money" v-model="lance.lanceManual" class="input" />
                <button @click="confirmarLanceManual" v-if="lance.lanceManual > 0" class="btn auditorio">
                  Confirmar Lance de {{ lance.lanceManual }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LeilaoMixin from 'comunicator/src/integrations/vue/mixins/leilao.mixin'
import {convertRealToMoney, REAL_BRL} from "../../utils/money"
import {VMoney} from 'v-money'
import HabilitacaoBtn from "../leilao/HabilitacaoBtn"
import {analisadorLote, cancelarLanceAutomaticoLote, registrarLanceAutomaticoLote} from "../../../domain/services";

export default {
  name: "Lance",
  props: {
    showHistoricoLances: {
      type: Boolean,
      default: true
    },
    showTaxas: {
      type: Boolean,
      default: true
    }
  },
  components: {HabilitacaoBtn},
  provide: function () {
    return {
      controlador: this
    }
  },
  directives: {money: VMoney},
  mixins: [LeilaoMixin],
  data() {
    const _lote = lote
    _lote.id = _lote.aid
    const _leilao = leilao
    _leilao.id = _leilao.aid
    return {
      money: REAL_BRL,
      seguirLeilao: false,
      ARREMATANTEID: __ARREMATANTEID__,
      lote: _lote, // passado na página do lote pelo loteJson|raw
      leilao: _leilao, // passado na página do lote pelo leilaoJson|raw
      lance: {
        valorLance: 0,
        confirmarLance: false,
        isLancandoManual: false,
        lanceManual: 0,
        valorLanceAutomatico: 0,
        parcelamento: {
          lanceParcelado: false,
          parcelas: 1,
          entrada: 0,
          indice: null
        },
        configurarLanceAutomaticoLayout: false
      },
      analisandoLote: false,
      analiseLote: null,
      habilitado: false,
      tmpParcelas: null
    }
  },
  watch: {
    tmpParcelas (v) {
      this.lance.parcelamento.parcelas = v
    },
    'lance.parcelamento.lanceParcelado' (v) {
      if (!v) {
        this.lance.parcelamento.indice = null
        this.lance.parcelamento.parcelas = null
        this.lance.parcelamento.entrada = null
      }
    }
  },
  computed: {
    lancesLimited () {
      if (this.lote.lances && this.lote.lances.length) {
        return this.lote.lances.slice(0, 5)
      }
      return this.lote.lances
    },
    lanceManualFormatado() {
      return convertRealToMoney(this.lance.lanceManual)
    },
    maximoParcelas () {
      if (!this.hasParcelamentoLote) {
        return this.leilao.parcelamentoQtdParcelas
      }
      return this.lote.parcelamentoQtdParcelas
    },
    parcelas () {
      const parcelas = parseInt(this.maximoParcelas)
      if (!parcelas || isNaN(parcelas)) {
        return [{label: '1 vez', value: 1}]
      }
      const p = []
      for (let i = 2; i <= parseInt(parcelas); i++) {
        p.push({label: i + ' vezes', value: Number(i)})
        //p.push(i)
      }
      return p
    },
    lanceParceladoError () {
      return Number(this.lance.parcelamento.entrada) < this.lanceParceladoEntradaMinima
    },
    lanceParceladoEntradaMinima () {
      if (this.lote.parcelamentoMinimoEntrada && this.lote.parcelamentoMinimoEntrada !== this.leilao.parcelamentoMinimoEntrada) {
        return this.lote.parcelamentoMinimoEntrada
      }
      return this.leilao.parcelamentoMinimoEntrada
    }
  },
  methods: {
    iniciarLance($event) {
      this.isLancando = true
    },
    efetuarLance(tipo, valor = null) {
      this.lance.confirmarLance = true
      if (tipo === 'incremento') {
        this.lance.valorLance = this.valorAtual + this.lanceIncremento
        return
      }
      if (tipo === 'inicial') {
        this.lance.valorLance = this.valorAtual
        return
      }
    },
    confirmarLanceManual() {
      this.lance.valorLance = this.lanceManualFormatado
      this.confirmarLance()
    },
    confirmarLance() {
      this.valorLance = this.lance.valorLance
      this.parcelamento = this.lance.parcelamento
      const callbackSucess = (data) => {
        console.log(data)
        this.$dialog.new({
          message: 'Parabéns! Seu lance foi aceito.',
          title: 'Sucesso!'
        })
        // Add lance to list
        this.reset()
      }
      this.__efetuarLance(true)
          .then(({data}) => {
            callbackSucess(data)
          })
          .catch(error => {
            console.log(error)
            this.alertApiError(error)
          })
    },
    cancelarLance() {
      this.reset()
    },
    reset() {
      this.isLancando = false
      this.analisandoLote = false
      this.analiseLote = null
      this.lance.configurarLanceAutomaticoLayout = false
      this.valorLance = null
      this.parcelamento = {}
      this.parcelamento.lanceParcelado = false
      this.parcelamento.parcelas = 1
      this.parcelamento.indice = null
      this.lance.confirmarLance = false
      this.lance.valorLance = null
      this.lance.lanceManual = 0
      this.lance.isLancandoManual = false
    },
    configurarLanceAutomatico() {
      this.setTipoLance('automatico')
    },
    setTipoLance(tipo) {
      if (tipo === 'parcelado') {
        this.lance.parcelamento.lanceParcelado = true
        this.lance.parcelamento.entrada = this.lanceParceladoEntradaMinima
        this.analisaLote()
      }
      if (tipo === 'automatico') {
        this.lance.configurarLanceAutomaticoLayout = true
        this.analisaLote()
      }
    },
    analisaLote() {
      this.analisandoLote = true
      this.preventOpen = true
      analisadorLote(this.lote.id)
          .then(({data}) => {
            this.analiseLote = data
            this.$nextTick(() => {
              this.analisandoLote = false
              if (this.analiseLote.lanceAutomatico && this.analiseLote.lanceAutomatico.active) {
                this.lance.valorLanceAutomatico = this.$options.filters.moeda(this.analiseLote.lanceAutomatico.valorLimite)
                this.lance.parcelamento.lanceParcelado = this.analiseLote.lanceAutomatico.parcelado
                this.tmpParcelas = this.lance.parcelamento.parcelas = this.analiseLote.lanceAutomatico.parcelas
                this.lance.parcelamento.entrada = this.analiseLote.lanceAutomatico.entrada
              }
              setTimeout(() => {
              }, 100)
            })
            console.log(data)
          })
          .catch(error => {
            this.analisandoLote = false
            this.alertApiError(error)
            console.log(error)
          })
    },
    confirmarLanceAutomatico() {
      this.$dialog.new({
        title: 'Confirmar Lance Aautomático',
        message: 'Você tem certeza que deseja salvar seu lance automático? <br><strong>Valor limite de ' + this.lance.valorLanceAutomatico + ' </strong>',
        cancelBtn: true,
        html: true,
        ok: 'Sim',
        cancel: 'Não',
        color: 'positive'
      }).then(wid => {
        this.$dialog.listen(wid, {
          clickOk: (wid) => {
            this.salvarLanceAutomatico()
          }
        })
      })
    },
    cancelarLanceAutomatico() {
      this.$dialog.new({
        title: 'Cancelar lance automático',
        message: 'Você tem certeza que deseja cancelar seu lance automático ?<br><br>Esta ação somente irá parar de efetuar novos lances, seus lances já efetuados não podem ser cancelados.',
        html: true,
        cancelBtn: true,
        ok: 'Sim',
        cancel: 'Não',
        color: 'positive'
      }).then(wid => {
        this.$dialog.listen(wid, {
          clickOk: (wid) => {
            cancelarLanceAutomaticoLote(this.lote.id)
                .then(({data}) => {
                  // this.$q.loading.hide()
                  this.$dialog.new({
                    message: 'Seu lance automático foi cancelado.',
                    color: 'positive'
                  })
                  this.reset()
                })
                .catch(error => {
                  // this.$q.loading.hide()
                  this.alertApiError(error)
                })
          }
        })
      })
    },
    salvarLanceAutomatico() {
      const valor = convertRealToMoney(String(this.lance.valorLanceAutomatico))
      if (valor <= 0) {
        this.$dialog.new({
          message: 'Você precisa especificar o valor máximo para seus lances automáticos',
          color: 'danger'
        })
        return
      }
      // this.$q.loading.show({message: 'Salvando seu lance automático'})
      const parcelamento = this.lance.parcelamento
      registrarLanceAutomaticoLote(this.lote.id, valor, parcelamento)
          .then(({data}) => {
            console.log(data)
            this.analiseLote = data
            // this.$q.loading.hide()
            this.$dialog.new({
              message: data.lanceAtual ? 'Parabéns! Seu lance automático foi aceito. Registramos o primeiro lance no valor de R$ ' + this.$options.filters.moeda(data.lanceAtual.valor) : 'Parabéns! Seu lance automático foi aceito.',
              // caption: 'Se alguem ',
              color: 'positive'
            })
            this.reset()
          })
          .catch(error => {
            // this.$q.loading.hide()
            this.alertApiError(error)
          })
    },
    gotoAuditorio () {
      window.location = PAINEL_URL + `/#/auditorio/${this.leilao.id}`
    }
  }
}
</script>
