<template>
  <modal class="document-modal" ref="modal">
    <div class="modal-nav"><button @click="modal().close()" class="btn text-blue-grey-5">Fechar</button></div>
    <div class="pad">
      <div class="mb-3 font-bold">Habilite-se para participar deste leilão</div>

      <div v-if="loading" class="text-center"><i class="fa fa-spin fa-spinner" /></div>
      <div class="app-leilao-abilitacao-texto" v-else v-html="document"></div>

      <div class="text-right mt-3">
        <div class="termos-input-aceite"><input type="checkbox" v-model="aceite" class="mr-2" /> Declaro que li e aceito as condições do leilão</div>
        <button class="btn btn-info" @click="modal().close()">Cancelar</button>
        <button class="btn btn-success" @click="confirm">Habilitar</button>
      </div>
    </div>
  </modal>
</template>

<script>
import {getTextoHabilitacaoLeilao, leilaoHabilitar} from '../../../domain/services'
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
      visible: false,
      aceite: false,
      document: '',
      loading: false
    }
  },
  methods: {
    show () {
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
      leilaoHabilitar(this.leilao.id)
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
