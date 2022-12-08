let http

export const setHttp = (h) => {
    http = h
}

export const getPublicConfig = (configs) => {
    const url = '/api/public/globalconfigs'
    let method = http.get
    if (typeof configs !== 'undefined') {
        method = (url) => http.post(url, configs)
    }
    return method(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const getPublicDocument = (code) => {
    const url = `/api/public/documentos/${code}`
    return http.get(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const leilaoHabilitar = (id, extra = {}) => {
    const url = `/api/public/arrematantes/service/leiloes/${id}/habilitar`
    return http.post(url, extra)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const getTextoHabilitacaoLeilao = (id) => {
    const url = `/api/public/arrematantes/service/leiloes/${id}/habilitar`
    return http.get(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const getLotesMinimo = (id) => {
    const url = `/api/public/arrematantes/service/leiloes/${id}/lotes-min`
    return http.get(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const analisadorLote = (id) => {
    const url = `/api/arrematantes/service/lotes/${id}/analisador`
    return http.get(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}


export const registrarLanceAutomaticoLote = (loteId, valorLimite, parcelamento) => {
    const url = `/api/arrematantes/service/lotes/${loteId}/registrarLanceAutomatico`
    return http.post(url, {
        valorLimite: valorLimite,
        ...parcelamento
    })
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}

export const cancelarLanceAutomaticoLote = (loteId) => {
    const url = `/api/arrematantes/service/lotes/${loteId}/cancelarLanceAutomatico`
    return http.delete(url)
        .then(response => {
            return Promise.resolve(response)
        })
        .catch(({response}) => {
            return Promise.reject(response)
        })
}