<template>
  <modal class="document-modal modal-habilitacao" ref="modal">
    <div class="modal-nav"><button @click="modal().close()" class="btn text-blue-grey-5">Fechar</button></div>
    <div class="pad">
<!--      <div class="mb-3 font-bold">Habilite-se para participar deste leilão</div>-->

      <div v-if="loading" class="text-center"><i class="fa fa-spin fa-spinner" /></div>
      <div class="app-leilao-abilitacao-texto" v-else v-html="document"></div>
      <div> <!--  v-if="leilao.direitoPreferencia" -->
        <div class="termos-input-direito-preferencia"><input type="checkbox" v-model="direitoPreferencia" /><span>Exercer direito de preferência</span></div>
        <div class="box-direito-preferencia" v-if="direitoPreferencia">
          <div class="text-justify">
            O direito de preferência permite você igualar o valor do lote e, em caso de empate, ter preferência na disputa.
            <br><br>
            Selecione os lotes que deseja ter preferência. De acordo com regras judiciais seu exercimento será analisada, e caso aprovado você verá uma opção para igualar o valor na disputa de um lote na tela de lances.
            <br><br>
            Caso esteja tudo certo com sua habilitação mas você não ter direito de exercer preferência, sua solicitação será aprovada, porém o direito de preferência, não.
            <br><br>
            <strong>SELECIONE OS LOTES QUE DESEJA TER PREFERÊNCIA:</strong>
            <br><br>
            <div v-if="loadingLotes"><i class="fa fa-spin fa-spinner" /> Carregando lotes, aguarde</div>
            <div v-else>
              <div class="box-dp-lote" v-for="lote in lotesPreferencia" :key="lote.id"><input type="checkbox" color="black" size="xs" v-model="lote.checked" /> {{lote.numeroString || lote.numero}} - {{lote.siteTitulo}}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-right mt-3 footer-btns">
        <div class="termos-input-aceite"><input type="checkbox" v-model="aceite" /><span>Declaro que li e aceito as condições do leilão</span></div>
        <button class="btn btn-success" @click="confirm">Concordo e solicito a habilitação</button>
        <button class="btn btn-negative" @click="modal().close()">Não concordo</button>
      </div>
    </div>
  </modal>
</template>

<script>
import {getLotesMinimo, getTextoHabilitacaoLeilao, leilaoHabilitar} from '../../../domain/services'
import Modal from "../modal/Modal"
import ModalMixin from "../modal/ModalMixin"

export default {
  name: 'Habilitacao',
  components: {Modal},
  mixins: [ModalMixin],
  props: {
    leilao: {
      required: true
    }
  },
  data () {
    return {
      document: '',
      loading: false,
      visible: false,
      aceite: false,
      direitoPreferencia: false,
      texto: '',
      loadingTexto: false,
      loadingLotes: true,
      lotesPreferencia: []
    }
  },
  watch: {
    direitoPreferencia (v) {
      if (v && !this.lotesPreferencia.length) {
        this.loadLotes()
      }
    }
  },
  methods: {
    show () {
      this.$root.needLogged()
      this.$refs.modal.open()
      this.loading = true
      this.carregaTexto()
    },
    hide () {
      this.$refs.modal.close()
    },
    carregaTexto () {
      this.loading = true
      getTextoHabilitacaoLeilao(this.leilao.id)
        .then(({data}) => {
          this.document = data.texto
          this.loading = false
        })
        .catch((error) => {
          console.log(error)
          this.loading = false
          this.alertApiError(error)
        })
    },
    loadLotes () {
      this.loadingLotes = true
      getLotesMinimo(this.leilao.id)
          .then(({data}) => {
            this.lotesPreferencia = data ? data.map(d => {
              return {...d, checked: false}
            }) : []
            this.loadingLotes = false
          })
          .catch((error) => {
            console.log(error)
            this.loadingLotes = true
            this.alertApiError(error)
          })
    },
    confirm () {
      if (!this.aceite) {
        this.$dialog.new({
          title: 'Atenção!',
          message: 'Você precisa ler e concordar com as condições deste leilão'
        })
        setTimeout(() => {
          this.show()
        }, 10)
        return
      }
      console.log('Confirm')
      const extra = {
        direitoPreferencia: this.direitoPreferencia,
        lotesPreferencia: this.lotesPreferencia
      }
      leilaoHabilitar(this.leilao.id, extra)
        .then(({data}) => {
          this.$emit('habilitacao', data)
          if (data.status) {
            this.$dialog.new({
              title: 'Habilitação realizada!',
              message: 'Você está habilitado para participar deste leilão.'
            })
          } else {
            this.$dialog.new({
              title: 'Habilitação em análise!',
              message: 'Aguarde nossa análise do seu cadastro para habilitar para participar deste leilão.'
            })
          }
        })
        .catch((error) => {
          console.log(error)
          this.alertApiError(error)
        })
      this.hide()
    }
  }
}
</script>
