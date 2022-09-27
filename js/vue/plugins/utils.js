import {number_format as numberFormat} from '../utils/phpjs'
import { format, parseISO } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import {errorToString, systemError} from "../utils/printError"

/* eslint-disable */
export default {
  install (Vue, options) {
    Vue.mixin({
      filters: {
        moeda: function (valor) {
          return numberFormat(valor, 2, ',', '.')
        },
        moedaSCS: function (valor) {
          return numberFormat(valor, 0, ',', '.')
        },
        moedaToNumber: function (valor) {
          return Number((String(valor).replace(/[^0-9,]/g, '')).replace(',', '.'))
        },
        number: function (valor) {
          return numberFormat(valor, null, ',', '.')
        },
        formataCpf: function (cpf) {
          return String(cpf).replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/g, '$1.$2.$3-$4')
        },
        formataCnpj (cnpj) {
          return String(cnpj).replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, '$1.$2.$3/$4-$5')
        },
        formataCep (cep) {
          cep = '' + cep
          return cep.trim().replace(/(\d{2})(\d{3})(\d{3})/g, '$1.$2-$3')
        },
        formataTelefone (telefone) {
          if (typeof telefone === 'undefined') {
            return
          }
          if (String(telefone).match(/\D/)){
            return telefone
          }
          return String(telefone).length === 11 ? String(telefone).replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3') : String(telefone).length === 10 ? String(telefone).replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3') : null
        },
        formatDate(d, f = 'dd/MM/yyyy HH:mm') {
          if (!d) {
            return '-'
          }
          if (d && d.date) {
            d = d.date
          }
          let result
          try {
            result = format(parseISO(d), f, {
              locale: ptBR
            })
            return result
          } catch (e) {
            console.error(e)
            return '-'
          }
        }
      },
      methods: {
        systemError: systemError,
        errorsToString: errorToString,
        alertApiError: function (response, title = null, color = null, message = null) {
          alert(message || this.errorsToString(response))
        },
      }
    })
  }
}
