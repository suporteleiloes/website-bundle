/**
 * Mixin responsável pelos serviços de pesquisa de bens, lotes e leilões
 */
/* eslint-disable */
// import Cookie from '../utils/cookie'
import axios from "axios"

const urls = {
    filtros: '/api/filtros/tipos'
}

let http2

export default {
    data () {
        return {
            filtros: {
                tipos: [],
                uf: [],
                cidade: [],
                bairro: []
            }
        }
    },
    computed: {
    },
    beforeMount() {
        http2 = axios.create({
            baseURL: '/'
        })
    },
    methods: {
        http2 () {
            return http2
        },
        getTiposBem (tipo = null, tipoPai = null) {
            return new Promise((resolve, reject) => {
                let extra = ''
                if (tipo) {
                    extra = extra + '&tipoBem=' + tipo;
                }
                if (tipoPai) {
                    extra = extra + '&tipoBemPai=' + tipoPai;
                }
                this.http2().get(urls.filtros + '?onlyTipoBem=1' + extra)
                    .then(response => {
                        this.filtros[t] = response.data.tiposCache
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
            })
        },
        getTipoCache (t, extra = '') {
            return new Promise((resolve, reject) => {
                this.http2().get(urls.filtros + '?tipo=' + t + extra)
                    .then(response => {
                        this.filtros[t] = response.data.tiposCache
                        if (typeof response.data.tiposBem !== 'undefined') {
                            this.filtros.tipos = response.data.tiposBem
                        }
                        resolve(response)
                    })
                    .catch((error) => {
                        reject(error)
                    })
            })
        },
        getUfs () {
            return this.getTipoCache('uf')
        },
        getCidades (uf) {
            return this.getTipoCache('cidade', uf ? '&parente=' + uf : '')
        },
        getBairros (cidade) {
            return this.getTipoCache('bairro', cidade ? '&parente=' + cidade : '')
        }
    }
}