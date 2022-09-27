import {date} from 'uloc-vue'

export const datePtToEn = function (d) {
  d = String(d).split('/')
  if (d.length < 3) {
    return
  }
  d = `${d[2]}-${d[1]}-${d[0]}`
  return d
}

export const datetimeToEn = function (d) {
  const dt = d.split(' ')
  d = dt[0].split('/')
  const time = dt[1]
  const timeArr = time.split(':')
  if (timeArr.length < 3) {
    timeArr.push('00')
  }
  d = `${d[2]}-${d[1]}-${d[0]} ${timeArr.join(':')}`
  return d
}

export const convertDate = function (varDate, splitDateTime = false) {
  if (varDate && varDate.date) {
    let _dt = varDate.date
    _dt = date.formatDate(_dt, 'DD/MM/YYYY HH:mm')
    if (splitDateTime) {
      return String(_dt).split(' ')
    }
    return _dt
  }
  return null
}
