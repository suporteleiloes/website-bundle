export const REAL_BRL = {
  decimal: ',',
  thousands: '.',
  prefix: 'R$ ',
  suffix: '',
  precision: 2,
  masked: false
}

export const convertRealToMoney = function (v) {
  /* if(!Number.isNaN(v)){
    return v
  } */
  return Number(String(v).replace(/\D/gi, '')) / 100
}
