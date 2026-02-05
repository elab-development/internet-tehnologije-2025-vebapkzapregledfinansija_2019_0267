import React from 'react'

const PrimaryButton = ({ text, loading }) => {
  return (
    <button type="submit" disabled={loading}>
      {loading ? "Obrada..." : text}
    </button>
  )
}

export default PrimaryButton
