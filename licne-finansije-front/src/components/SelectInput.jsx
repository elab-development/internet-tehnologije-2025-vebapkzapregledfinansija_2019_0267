import React from 'react'

const SelectInput = ({label, value, onChange, options}) => {
  return (
    <div className="select-input-wrapper">
      {label && <label className="select-label">{label}</label>}
      <select value={value} onChange={onChange} required className="select-element">
          <option value="">Izaberite...</option>
          {options.map((option, index) => (
              <option key={index} value={option.value}>{option.label}</option>
          ))}
      </select>
    </div>
  )
}

export default SelectInput
