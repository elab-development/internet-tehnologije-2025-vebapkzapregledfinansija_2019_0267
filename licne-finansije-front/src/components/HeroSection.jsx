import React from 'react'
import SummaryCard from './SummaryCard';
import '../pages/Pocetna.css';
import { useNavigate } from 'react-router-dom';

const HeroSection = () => {

    const navigate = useNavigate();
  return (
    <section className="hero">
        <div className="hero-text">
            <h1>Preuzmi kontrolu nad svojim finansijama</h1>
            <p>
                Jednostavan i siguran nacin da pratis prihode, troskove i
                finansijske ciljeve na jednom mestu.
            </p>
            <div className="hero-actions">
                <button className="btn primary" onClick={() => navigate("/login")}>Prijava</button>
                <button className="btn secondary">Kreiraj nalog</button>
            </div>
        </div>


        <SummaryCard />
    </section>
  )
}

export default HeroSection
