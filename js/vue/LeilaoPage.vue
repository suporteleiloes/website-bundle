<template>
  <main class="main-lote">
    <div class="center">
      <h1 class="title-lote">{{ leilao.produto.nome }}</h1>

      <div class="view-lote">
        <div class="cont-left">
          <div class="grid-1">
            <div class="cont-img" id="lote-image-container">
              <a>
                <img class="u-img" :src="leilao.produto.image ? leilao.produto.image.full.url : SEM_FOTO" alt="Default Image" data-index="0" data-size="600x450" id="lote-image">
              </a>
            </div>

            <ul class="show-thumbs">
<!--              {% for foto in leilao.produto.fotos %}
              <li class="{{ loop.index > 5 ? 'hide' : '' }}">
                <a data-index="{{ loop.index }}" data-size="600x450" href="{{ absolute_url(asset(foto.url)) }}">
                  {% if leilao.produto.fotos|length > 5 and loop.index == 5 %}
                  <span class="more">+{{ leilao.produto.fotos|length - 5 }}</span>
                  {% endif %}
                  <img src="{{ foto.url }}" class="lote-img">
                </a>
              </li>
              {% endfor %}-->
            </ul>
          </div>

          <div class="grid-2">
            <div class="row-1 sections-infos">
              <ul>
                <li v-if="leilao.tipo !== 2">
                  <div class="c1">
                    <span>Preço:</span>
                  </div>
                  <div class="c2">
                    <strong>R$ {{ valorAtual|moeda }}</strong>
                    <small>Frete a Combinar</small>
                  </div>
                </li><!----->
                <li style="font-size: 12px; text-align: justify" v-else>
                  * Este leilão é na modalidade "Menor lance único", ou seja, os lances até o encerramento do cronômetro são ocultados, você pode efetuar quantos lances desejar, sempre informando o valor. Quando o leilão encerrar sem mais nenhum lance, ganha quem ofertou o menor lance único.
                </li>

                <li v-if="leilao.tipo === 2">
                  <div class="c1">
                    <span>Vencedor:</span>
                  </div>
                  <div class="c2">
                    <span v-if="isFechado">{{ leilao.vencedor.autor.username }} com o lance {{leilao.vencedor.valor|moeda}}</span>
                    <span v-else>Aguardando encerramento</span>
                  </div>
                </li>
                <li v-else>
                  <div class="c1">
                    <span>Usuário:</span>
                  </div>
                  <div class="c2">
                    <span v-if="ultimoLance">{{ ultimoLance.autor.username }}</span>
                    <span v-else>-</span>
                  </div>
                </li><!----->
              </ul>

              <p v-if="isPermitidoLance" class="status-leilao">Leilão em Andamento</p>
              <p v-else class="status-leilao">Leilão encerrado</p>
            </div>

            <div class="row-2 section-cronometro">
              <div class="step-item step-1">
                <div v-if="isPermitidoLance" class="show-cronometro" :class="bindCronometroClass"> <!--{# {% if index == 3 %} orange{% elseif index == 1 %} red{% endif %} #}-->
                  <span><strong>{{String(timerPregao).substr(0, 1)}}</strong></span>
                  <span><strong>{{String(timerPregao).substr(1, 1)}}</strong></span>
                </div>

                <div v-if="leilao.tipo === 2">
                  <input v-if="isPermitidoLance" class="valorLance" placeholder="Seu lance" v-model="valorLance" v-money="money" />
                </div>

                <button v-if="isPermitidoLance" @click="lance">
                  <span v-if="!isLancando">Lance</span>
                  <span v-else>Lançando</span>
                </button>
              </div>

              <div class="step-item step-2">
                <ul>
                  <li>
                    <span>Valor de Mercado</span>
                    <span>R$ {{ leilao.produto.valor|moeda }}</span>
                  </li><!---->

                  <li>
                    <span>Lances Ofertados</span>
                    <span>{{ totalLances }}</span>
                  </li><!---->

                  <li v-if="leilao.tipo !== 2">
                    <span>Preço Final</span>
                    <span>R$ {{ valorAtual|moeda }}</span>
                  </li><!---->
                </ul>
              </div>

              <div v-if="leilao.tipo !== 2" class="step-item step-3">
                <ul>
                  <li>
                    <span>Economia de:</span>
                    <span>R$ {{ (leilao.produto.valor - valorAtual)|moeda }}</span>
                  </li>
                </ul>

                <p>O valor de mercado equivale ao preço do produto em lojas de varejo</p>
              </div>
            </div>
          </div>
        </div>

        <div class="cont-right">
          <div class="row-1 section-cadastro text-center">
            <div v-if="user">
              <a href="">Você está logado</a>
              <div>Seu usuário: <strong>{{ user.user.username }}</strong></div>
            </div>
            <div v-else>
              <h4>Novo por aqui?</h4>
              <p>Cadastre-se e ganhe lances grátis!</p>
              <a href="">Cadastre-se agora</a>
            </div>
          </div>

          <div class="row-2 section-lances">
            <h5>Últimos Lances</h5>
            <ul>
              <li class="li-head">
                <span class="c1">Lance</span>
                <span>Usuário</span>
              </li><!---->

              <li v-for="(lance, key) in ultimosLances" :key="lance.id" :class="{'meu-lance': isMeuLance(lance)}">
                <span class="c1" v-if="leilao.tipo !== 2">R$ {{ (Math.abs((key-leilao.totalLances)) * 0.01)|moeda }}</span>
                <span v-else-if="isFechado || isMeuLance(lance)" class="c1">{{lance.valor|moeda}}</span>
                <span class="c1" v-else>*****</span>
                <span>{{ lance.autor.username }}</span>
              </li>
              <li v-if="!leilao.ultimosLances || !leilao.ultimosLances.length">
                <span class="c1">Nenhum lance até o momento</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <h2 class="title-lote">{{ leilao.produto.nome }}</h2>

      <div class="show-descricao">
        <div v-html="leilao.produto.descricao"></div>
      </div>
    </div><!-- END Center -->
  </main>
</template>

<script>
import LeilaoMixin from "./mixins/leilao.mixin"
export default {
  name: "LeilaoPage",
  mixins: [LeilaoMixin]
}
</script>
