import React from 'react'
import './Navbar.css';

const Navbar = () => {
  return (
    <nav className="navbar">
        <div className="navbar-container">
            <span className="logo">FinTrack</span>
            <div className="nav-actions">
            <button className="btn secondary">Prijava</button>
            <button className="btn primary">Kreiraj nalog</button>
            </div>
        </div>
    </nav>
  )
}

export default Navbar
