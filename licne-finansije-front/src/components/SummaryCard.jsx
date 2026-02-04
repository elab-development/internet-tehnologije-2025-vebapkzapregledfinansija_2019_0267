import React from 'react'

const SummaryCard = () => {
  return (
    <div className="summary-card">
        <h2>Ukupno stanje</h2>
        <p className="balance">€12,450.00</p>


        <div className="summary-grid">
            <div className="income">
                <span>Prilivi</span>
                <strong>€3,200</strong>
            </div>
            <div className="expense">
                <span>Odlivi</span>
                <strong>€1,850</strong>
            </div>
        </div>
    </div>
  )
}

export default SummaryCard
