<template>
  <div id="lote-lance" class="app-lance prevent-app-load">
    <div class="col1">
      <h2 class="h2-default"><strong>Últimos lances</strong></h2>
      <table class="table-lote-lance-historico">
        <thead>
        <tr>
          <th>Usuário</th>
          <th>Data</th>
          <th>Valor</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td colspan="3" style="text-align: left; font-weight: normal;">Nenhum lance registrado para este
            lote.
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="col2">
      <div class="avaliacao"><span>Avaliação</span> R$ {{ lote.valorAvaliacao|moeda }}</div>
      <div class="lance-minimo"><span>Lance Mínimo</span> R$ {{ lote.valorAtual|moeda }}</div>
      <div class="incremento"><span>Incremento</span> R$ {{ lote.valorIncremento|moeda }}</div>
<!--      {% if lote.leilao.sistemaTaxa %}
      <div class="taxas">
        {% for taxa in lote.leilao.sistemaTaxa.taxas %}
        <div>{{ taxa.nome }}: {% if taxa.tipo == 1 %}{{ taxa.valor }}%{% else %}{% endif %}</div>
        {% endfor %}
      </div>
      {% endif %}-->
    </div>
  </div>
</template>

<script>
import LeilaoMixin from 'comunicator/src/integrations/vue/mixins/leilao.mixin'
export default {
  name: "Lance",
  mixins: [LeilaoMixin],
  data () {
    return {
      lote: lote, // passado na página do lote pelo loteJson|raw
      leilao: leilao // passado na página do lote pelo leilaoJson|raw
    }
  }
}
</script>
