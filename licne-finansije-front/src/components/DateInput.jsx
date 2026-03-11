import React from 'react'
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";

const DateInput = ({ value, onChange }) => {
  return (
    <DatePicker
      selected={value || null}
      onChange={onChange}
      dateFormat="yyyy-MM-dd"
      placeholderText="Izaberite datum"
      showMonthDropdown
      showYearDropdown
      dropdownMode="select"
      

      className="date-input"
    />
  )
}

export default DateInput
