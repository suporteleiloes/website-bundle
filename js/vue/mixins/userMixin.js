/* eslint-disable */
import Cookie from '../utils/cookie'
const lotesFavoritosCookieName = 'lotesFavoritos'

export default {
    data () {
        return {
            favoritos: []
        }
    },
    computed: {
    },
    beforeMount() {
        this.getLotesFavoritos()
    },
    methods: {
        needLogged (showAlert = true) {
            if (typeof TOKEN !== "undefined" && TOKEN) {
                return true
            }
            showAlert && alert('Você precisa estar logado');
            throw new Error('Não logado.');
        },
        http () {
            if (typeof TOKEN !== "undefined" && TOKEN) {
                this.comunicatorClass.http.defaults.headers.common.Authorization = 'Bearer ' + TOKEN
            }
            return this.comunicatorClass.http
        },
        getLotesFavoritos () {
            try {
                this.needLogged(false)
                if (!Cookie.get(lotesFavoritosCookieName)) {
                    this.http().get('api/arrematantes/lotes/favoritos')
                        .then(response => {
                            const ids = response.data.map(l => l.lote.id)
                            Cookie.add(lotesFavoritosCookieName, JSON.stringify(ids), (3600*10))
                            this.favoritos = ids
                        })
                        .catch(error => {
                            this.alertApiError(error)
                        })
                    return []
                } else {
                    return this.favoritos = JSON.parse(Cookie.get(lotesFavoritosCookieName))
                }
            } catch (e) {}
        },
        adicionarFavorito (loteId) {
            this.needLogged()
            let action = 'add'
            let m = this.http().post
            if (this.favoritos.includes(loteId)) {
                m = this.http().delete
                action = 'delete'
            }
            m(`/api/arrematantes/lotes/${loteId}/favorito`)
                .then(response => {
                    console.log(response)
                    let f = this.getLotesFavoritos()
                    if (action === 'add') {
                        if (!f.includes(loteId)) {
                            f.push(loteId)
                        }
                    } else {
                        if (f.includes(loteId)) {
                            f.splice(f.indexOf(loteId), 1)
                        }
                    }
                    Cookie.add(lotesFavoritosCookieName, JSON.stringify(f), (3600*10))
                })
                .catch(error => {
                    this.alertApiError(error)
                })
        }
    }
}