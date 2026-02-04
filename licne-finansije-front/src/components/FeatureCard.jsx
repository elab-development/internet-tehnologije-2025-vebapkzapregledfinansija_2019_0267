import React from 'react'

const FeatureCard = ({ feature }) => {
  return (
    <div className="feature-card">
        <h3>{feature.title}</h3>
        <ul>
            {feature.bullets.map((bullet, index) => (
                <li key={index}>{bullet}</li>
            ))}
        </ul>
    </div>
  )
}

export default FeatureCard
