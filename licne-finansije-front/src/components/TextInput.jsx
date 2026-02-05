import React from 'react'

const TextInput = ({type="text", placeholder, value, onChange, required}) => {
  return (
    <input
      type={type}
      placeholder={placeholder}
      value={value}
      onChange={onChange}
      required={required}
    />
  )
}

export default TextInput
