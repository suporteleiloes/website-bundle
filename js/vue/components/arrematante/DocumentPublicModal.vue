<template>
  <modal class="document-modal" ref="modal">
    <div class="modal-nav"><button @click="modal().close()" class="text-blue-grey-5">Fechar</button></div>
    <div class="q-pa-md">
      <div v-if="loading" class="text-center"><i class="fa fa-spin fa-spinner" /></div>
      <div v-else v-html="document"></div>
    </div>
  </modal>
</template>

<script>
import Modal from '../modal/Modal'
import ModalMixin from '../modal/ModalMixin'
import {getPublicDocument} from '../../../domain/services'

export default {
  name: 'DocumentoPublicModal',
  mixins: [ModalMixin],
  data () {
    return {
      loading: true,
      documentName: null,
      document: null
    }
  },
  methods: {
    define (name) {
      this.documentName = name
      return this
    },
    open () {
      console.log('OPEN')
      this.$refs.modal.open()
      this.loading = true
      getPublicDocument(this.documentName)
          .then(response => {
            this.loading = false
            this.document = response.data.document
            console.log(response)
          })
          .catch(error => {
            this.loading = false
            this.alertApiError(error)
            console.log(error)
          })
    }
  },
  components: {Modal}
}
</script>
