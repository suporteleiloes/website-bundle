/* eslint-disable */
import Cookie from '../utils/cookie'
const lotesFavoritosCookieName = 'lotesFavoritos'
const leiloesFavoritosCookieName = 'leiloesFavoritos'
const bensFavoritosCookieName = 'bensFavoritos'

export default {
    data () {
        return {
            favoritos: [],
            leiloesFavoritos: [],
            bensFavoritos: []
        }
    },
    computed: {
    },
    beforeMount() {
        if (typeof TOKEN !== "undefined" && TOKEN) {
            this.comunicatorClass.http.defaults.headers.common.Authorization = 'Bearer ' + TOKEN
        }
        this.getFavoritos()
        this.getFavoritos('leiloes')
        this.getFavoritos('bens')
    },
    methods: {
        needLogged (showAlert = true) {
            if (typeof TOKEN !== "undefined" && TOKEN) {
                return true
            }
            //showAlert && alert('Você precisa estar logado');
            showAlert && this.$dialog.new({title: 'Atenção', message: 'Você precisa estar logado'})
            throw new Error('Não logado.');
        },
        http () {
            return this.comunicatorClass.http
        },
        getFavoritos (tipo = 'lotes') {
            try {
                let cookieName = lotesFavoritosCookieName
                let endpoint = 'lotes'
                let dataKey = 'favoritos'
                let findCallback = l => l.lote.id
                if (tipo === 'leiloes') {
                    cookieName = leiloesFavoritosCookieName
                    endpoint = 'leiloes'
                    dataKey = 'leiloesFavoritos'
                    findCallback = l => l.leilao.id
                }
                if (tipo === 'bens') {
                    cookieName = bensFavoritosCookieName
                    endpoint = 'bens'
                    dataKey = 'bensFavoritos'
                    findCallback = l => l.bem.id
                }
                this.needLogged(false)
                if (!Cookie.get(cookieName)) {
                    this.http().get(`api/arrematantes/${endpoint}/favoritos`)
                        .then(response => {
                            const ids = response.data.map(findCallback)
                            Cookie.add(cookieName, JSON.stringify(ids), (3600*10))
                            this[dataKey] = ids
                        })
                        .catch(error => {
                            this.alertApiError(error)
                        })
                    return []
                } else {
                    return this[dataKey] = JSON.parse(Cookie.get(cookieName))
                }
            } catch (e) {}
        },
        adicionarFavorito (event, id, tipo = 'lote') {
            event.preventDefault()
            event.stopPropagation()
            this.needLogged()
            return new Promise(resolve => {
                let cookieName = lotesFavoritosCookieName
                let endpoint = 'lotes'
                let dataKey = 'favoritos'
                if (tipo === 'leilao') {
                    cookieName = leiloesFavoritosCookieName
                    endpoint = 'leiloes'
                    dataKey = 'leiloesFavoritos'
                }
                if (tipo === 'bem') {
                    cookieName = bensFavoritosCookieName
                    endpoint = 'bens'
                    dataKey = 'bensFavoritos'
                }
                let action = 'add'
                let m = this.http().post
                if (this[dataKey].includes(id)) {
                    m = this.http().delete
                    action = 'delete'
                }
                m(`/api/arrematantes/${endpoint}/${id}/favorito`)
                    .then(response => {
                        console.log(response)
                        let f = this[dataKey]
                        if (action === 'add') {
                            if (!f.includes(id)) {
                                f.push(id)
                            }
                        } else {
                            if (f.includes(id)) {
                                f.splice(f.indexOf(id), 1)
                            }
                        }
                        Cookie.add(cookieName, JSON.stringify(f), (3600*10))
                        resolve()
                    })
                    .catch(error => {
                        resolve()
                        this.alertApiError(error)
                    })
            })

        }
    }
}