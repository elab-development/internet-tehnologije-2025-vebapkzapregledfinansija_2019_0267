import React from 'react'

const SelectInput = ({value, onChange, options}) => {
  return (
    <select value={value} onChange={onChange} required>
        <option value="">Izaberite...</option>
        {options.map((option, index) => (
            <option key={index} value={option.value}>{option.label}</option>
        ))}
    </select>
  )
}

export default SelectInput
