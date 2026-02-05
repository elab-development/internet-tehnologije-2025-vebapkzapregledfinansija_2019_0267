import React from 'react'
import './Navbar.css';
import { useNavigate } from 'react-router-dom';

const Navbar = () => {
  const navigate = useNavigate();

  return (
    <nav className="navbar">
        <div className="navbar-container">
            <span className="logo">FinTrack</span>
            <div className="nav-actions">
            <button
              className="btn secondary"
              onClick={() => navigate("/login")}
            >
              Prijava
            </button>
            <button className="btn primary">Kreiraj nalog</button>
            </div>
        </div>
    </nav>
  )
}

export default Navbar
