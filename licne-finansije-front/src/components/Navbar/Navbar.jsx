import React, { use } from 'react'
import './Navbar.css';
import { useLocation, useNavigate } from 'react-router-dom';
import { useState, useEffect } from 'react';
import PrimaryButton from '../PrimaryButton';
import api from '../../api/api';

const Navbar = () => {

  const location=useLocation();
  const [isAuth, setIsAuth] = useState(false);
  const [isAdmin, setIsAdmin] = useState(false);

  useEffect(() => {
    const token = localStorage.getItem("token");
    const user= JSON.parse(localStorage.getItem("user"));
    setIsAuth(!!token);
    setIsAdmin(user && user.uloga === "admin");
    console.log("location changed:", location.pathname);
    console.log("isAuth:", isAuth);
    console.log("isAdmin:", isAdmin);
    console.log("token:", token);
  }, [location]);

  const navigate = useNavigate();

  const handleLogout = async() => {
    try {
      await api.post("/logout");
    } catch (err) {
      console.error("Logout error:", err);
    }finally {
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      setIsAuth(false);
      navigate("/login");
    }
  }

  return (
    <nav className="navbar">
        <div className="navbar-container">
            <span className="logo" onClick={() => navigate("/")} style={{cursor: "pointer"}}>Kićanović.</span>
            <div className="nav-actions">

              {isAuth ? (
                <>
                  {/* <button 
                  className="btn secondary"
                  //ZAMENITI ODGOVARAJUCOM STRANICOM KADA SE NAPRAVI
                  onClick={() => navigate("/proba")}>
                    Proba
                  </button> */}

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/budzet")}>
                    Budžet
                  </button>

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/finansijski-cilj")}>
                    Finansijski cilj
                  </button>

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/podsetnik")}>
                    Podsetnik
                  </button>

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/kategorija")}>
                    Kategorija
                  </button>

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/transakcija")}>
                    Transakcija
                  </button>

                  {isAdmin && (
                    <button 
                    className="btn primary"
                    onClick={() => navigate("/admin-dashboard")}>
                      Admin dashboard
                    </button>
                  )}
                  {isAdmin && (
                    <button 
                    className="btn primary"
                    onClick={() => navigate("/admin-stats")}>
                      Statistika
                    </button>
                  )}

                  <button 
                  className="btn secondary"
                  onClick={() => navigate("/moj-profil")}>
                    Moj profil
                  </button>


                  <button
                    className="btn primary"
                    onClick={handleLogout}>Odjava</button>    

                </>
              ): (
                <>
                <button
                  className="btn secondary"
                  onClick={() => navigate("/login")}
                >
                    Prijava
                </button>
                <button 
                  className="btn primary"
                  onClick={() => navigate("/register")}
                >
                    Kreiraj nalog
                </button>
                </>
              )}
              
            </div>
        </div>
    </nav>
  )
}

export default Navbar
