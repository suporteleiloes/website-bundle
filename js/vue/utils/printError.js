export default function printError (err) {
  if (Array.isArray(err)) {
    if (Array.isArray(err[0])) {
      return this.printError(err[0])
    } else if (typeof err[0] === 'object') {
      /* for(var key in err[0]){
        if (err.hasOwnProperty(key)){

        }
      } */
    }
    return err[0]
  }
  return err
}

export function systemError (response) {
  if (response.response) {
    response = response.response
  }
  const data = response.responseJSON || response.data || response
  const err = data.detail || data.message || data.errors || data.error || data
  if (Array.isArray(err)) {
    return err
  }
  return [err]
}

export function errorToString (response, separator = '.\r\n ') {
  const err = systemError(response)
  const errors = valuesOfArray(err)
  console.log(errors)
  return errors.join(separator)
}

export function valuesOfArray (a) {
  let values = []
  if (Array.isArray(a)) {
    a.forEach((a2) => {
      if (typeof a2 === 'object') {
        let v = Object.values(a2)
        if (Array.isArray(v) || typeof v === 'object') {
          v = valuesOfArray(v)
        }
        values = values.concat(v)
      } else {
        const p = valuesOfArray(a2)
        values = values.concat(p)
      }
    })
  } else {
    if (typeof a === 'object') {
      values = values.concat(Object.values(a))
    } else {
      values.push(a)
    }
  }
  return values
}
