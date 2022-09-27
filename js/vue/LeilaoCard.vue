<template>
  <article>
    <div class="r1">
      <ul class="cont-datas">
        <li>
          <span v-if="leilao.status === 2">Finalizado</span>
          <span v-else>Início dia {{ leilao.data|formatDate }}</span>
        </li>
      </ul>

      <div class="cont-foto">
        <a :href="loteUrl">
          <img :src="thumb">
        </a>
      </div>
    </div>

    <div class="cont-infos">
      <h3>
        <a :href="loteUrl">
          {{ leilao.produto.nome }}
        </a>
      </h3>

      <div class="show-cronometro" :class="bindCronometroClass"> <!--{# {% if index == 3 %} orange{% elseif index == 1 %} red{% endif %} #}-->
        <span><strong>{{String(timerPregao).substr(0, 1)}}</strong></span>
        <span><strong>{{String(timerPregao).substr(1, 1)}}</strong></span>
      </div>
<!--      <div v-else class="show-cronometro">
        <br>
        <br>
      </div>-->

      <div class="show-lances">
        <strong style="text-align: center !important;" v-if="ultimoLance">
          <span v-if="leilao.tipo !== 2">{{ ultimoLance.autor.username }}</span>
          <span v-else-if="!isFechado"><small>Último lance enviado por <br></small>{{ ultimoLance.autor.username }}</span>
          <span v-else>{{ leilao.vencedor.autor.username }} com o lance {{leilao.vencedor.valor|moeda}}</span>
        </strong>
        <strong v-else>-</strong>
        <div v-if="leilao.tipo === 2" style="font-size: 10px; padding: 10px; text-align: center; line-height: 1">* O último lance não significa que será o vencedor. O vencedor será o que enviar o menor lance e único, que será verificado no término do leilão.</div>
        <p v-if="leilao.tipo !== 2">R$ <span>{{ valorAtual|moeda }}</span></p>
        <div v-else>
          <input v-if="isPermitidoLance" class="valorLance" placeholder="Seu lance" v-model="valorLance" v-money="money" />
        </div>
      </div>


      <div class="cont-btn">
        <a v-if="isPermitidoLance" class="cursor-pointer" @click="lance">
          <span v-if="!isLancando">Lance</span>
          <span v-else>Lançando</span>
        </a>
        <div v-else-if="isFechado">
          <div class="aviso-arremate" v-if="ultimoLance">
            <div v-if="leilao.tipo === 2">ENCERRADO!</div>
            <div v-else>ARREMATADO!</div>
          </div>
          <div v-else>
            SEM LANCES.
          </div>
        </div>
        <div v-else>
          <br>
        </div>
      </div>
    </div>
  </article>
</template>

<script>
import LeilaoMixin from "./mixins/leilao.mixin"
export default {
  name: "LeilaoCard",
  mixins: [LeilaoMixin]
}
</script>
