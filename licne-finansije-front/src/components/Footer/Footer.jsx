import React from 'react'
import './Footer.css';

const Footer = () => {
  return (
    <footer className="footer">
        <div className="footer-container">
            <p>© {new Date().getFullYear()} FinTrack. Sva prava zadrzana.</p>
            <div className="footer-links">
                <a href="#">Politika privatnosti</a>
                <a href="#">Uslovi koriscenja</a>
                <a href="#">Kontakt</a>
            </div>
        </div>
    </footer>
  )
}

export default Footer
