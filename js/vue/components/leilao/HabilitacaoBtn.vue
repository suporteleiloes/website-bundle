<template>
  <div>
  <button v-if="requerHabilitacao && !existeSolicitacaoHabilitacao" class="btn habilitese" @click="$refs.habilitacao.show()">
    <span v-if="leilao.habilitacao < 3">Habilite-se para efetuar lances</span>
    <span v-else>Solicitar Habilitação</span>
  </button>
  <button v-if="!ocultarHabilitado && existeSolicitacaoHabilitacao && requerHabilitacao && isHabilitado" disabled class="btn lance">Você está habilitado para este leilão</button>
  <button v-if="existeSolicitacaoHabilitacao && requerHabilitacao && !isHabilitado" disabled class="btn auditorio">Habilitação em análise, aguarde</button>
    <habilitacao @habilitacao="onHabilitar" :leilao="leilao" ref="habilitacao" />
  </div>
</template>

<script>
import Cookie from '../../utils/cookie'
import Habilitacao from './Habilitacao'
export default {
  name: 'HabilitacaoBtn',
  inject: ['controlador'],
  props: {
    ocultarHabilitado: {
      type: Boolean,
      default: true
    }
  },
  components: {
    Habilitacao
  },
  computed: {
    cookieName () {
      return `leilao_${this.leilao.id}_habilitacao`
    },
    leilao () {
      return this.controlador.leilao
    },
    requerHabilitacao () {
      return this.leilao.habilitacao
    },
    existeSolicitacaoHabilitacao () {
      return this.leilao.habilitacaoArrematante || this.isHabilitado
    },
    isHabilitado () {
      return Cookie.get(this.cookieName) || this.controlador.habilitado
    }
  },
  methods: {
    onHabilitar (data) {
      this.leilao.habilitacaoArrematante = data
      if (data.status === 1) {
        Cookie.add(this.cookieName, '1', (86400*100))
        this.controlador.habilitado = true
      }
    }
  }
}
</script>
