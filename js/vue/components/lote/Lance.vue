<template>
  <div class="prevent-app-load">
    <div id="lote-lance" class="app-lance">
      <div class="col1">
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
          <tr v-for="(lance, k) in lote.lances" :class="{meuLance: lance.arrematante.id === ARREMATANTEID}">
            <td>{{ lance.arrematante.apelido }} <span v-if="lance.arrematante.id === ARREMATANTEID" class="label-meu-lance">você</span></td>
            <td>{{lance.data|formatDate('dd/MM/yyyy HH:mm:ss')}}</td>
            <td>R$ {{ lance.valor|moeda }}</td>
          </tr>
          <tr v-if="lote.lances.length == 0">
            <td colspan="3" style="text-align: left; font-weight: normal">Nenhum lance registrado para este lote.
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="col2 valores">
        <div class="avaliacao"><span>Avaliação:</span> R$ {{ lote.valorAvaliacao|moeda }}</div>
        <div class="lance-minimo"><span>Lance Mínimo:</span> R$ {{ lote.valorAtual|moeda }}</div>
        <div class="incremento"><span>Incremento:</span> R$ {{ lote.valorIncremento|moeda }}</div>
        <div class="taxas" v-if="leilao.sistemaTaxa && leilao.sistemaTaxa.taxas">
          <div v-for="taxa in leilao.sistemaTaxa.taxas" :key="'taxa-' + taxa.id">
            <span class="label">{{ taxa.nome }}:</span> <span class="valor" v-if="taxa.tipo === 1">{{ taxa.valor }}%</span><span class="valor" v-else>R$ {{ taxa.valor }}</span>
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
        <div class="aba-content">
          <div class="lance-options">
            <button class="btn lance" @click="iniciarLance">
              <span class="hidden-xs">Efetuar Lance</span>
            </button>

            <div class="line2">
              <button class="btn b-automatico" @click="configurarLanceAutomatico">
                <span class="hidden-xs">Lance Automático</span>
              </button>
              <button class="btn auditorio">
                Auditório
              </button>
            </div>
            <button class="btn habilitese">
              Habilite-se
            </button>
          </div>

          <div class="lote-notification-sound">
            <label for="audioNotification">
              <small>Sons de notificação</small>
              <input type="checkbox" id="audioNotification" v-model="audioNotification">
              <span class="checkmark"></span>
            </label>
          </div>
        </div>
        <div class="aba-naologado">
          <p>É necessário estar logado para participar do leilão e efetuar lances</p>

          <a href="#" class="btn login">Login</a>
          <a href="#" class="btn cadastro">Cadastre-se</a>
        </div>
        <div class="lancador">
          <div class="lance-options">
            <div v-if="!lance.confirmarLance">
            <button @click="efetuarLance('inicial')" v-if="!ultimoLance" class="btn lance">
              Efetuar Lance Mínimo
            </button>
            <button @click="efetuarLance('incremento')" v-else class="btn lance">
              Lance Incremento +R${{lanceIncremento|moeda}}
            </button>
            </div>
            <button @click="cancelarLance" class="btn login m-t-none">
              Cancelar
            </button>
            <div v-if="lance.confirmarLance">
              <button @click="confirmarLance" class="btn lance">
                Confirmar Lance de R${{lance.valorLance|moeda}}?
              </button>
            </div>
            <div class="lance-manual">
              <p>Ou digite o valor do lance que você deseja:</p>
              <div>
                <input v-money.lazy="money" v-model="lance.lanceManual" />
                <button @click="confirmarLanceManual" class="btn auditorio">
                  Confirmar Lance de {{lance.lanceManual}}
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
export default {
  name: "Lance",
  provite: function () {
    return {
      controlador: this
    }
  },
  directives: {money: VMoney},
  mixins: [LeilaoMixin],
  data () {
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
        }
      }
    }
  },
  computed: {
    lanceManualFormatado () {
      return convertRealToMoney(this.lance.lanceManual)
    }
  },
  methods: {
    iniciarLance ($event) {
      this.isLancando = true
    },
    efetuarLance (tipo, valor = null) {
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
    confirmarLanceManual () {
      this.lance.valorLance = this.lanceManualFormatado
      this.confirmarLance()
    },
    confirmarLance () {
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
    cancelarLance () {
      this.reset()
    },
    reset () {
      this.isLancando = false
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
    configurarLanceAutomatico ($event) {},
  }
}
</script>
