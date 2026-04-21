import React from 'react'
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../api/api';
import './LoginPage.css';
import PasswordInput from '../../components/PasswordInput';

const LoginPage = () => {

  const navigate = useNavigate();

  const [email, setEmail] = useState('vasilijegogic29@gmail.com');
  const [password, setPassword] = useState('vaske123');

  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [info, setInfo] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setInfo('');
    setLoading(true);

    try {
      const response = await api.post("/login", { email, password });
      const { token, user, message } = response.data;

      //UPISIVANJE TOKENA I KORISNICKIH PODATAKA U LOCAL STORAGE
      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      setInfo(message || "Uspešno logovanje");
      setLoading(false);
      setTimeout(() => {
        navigate("/moj-profil");
      }, 1000);
    } catch (err) {
      setLoading(false);
      console.log(err);
      if (err.response.status === 401) {
        setError("Neispravni kredencijali");
      } else if (err.response.status === 422) {
        setError("Neispravni podaci");
      } else {
        setError("Došlo je do greške. Pokušajte ponovo.");
      }
    }

  }

  return (
    <div className="login-container">
      <form className="login-form" onSubmit={handleSubmit}>
        <h2>Login</h2>
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)} //svaki put kada se promeni input, azurira se stanje emaila; kreira se event objekat koji sadrzi informacije o promeni, 
          // a zatim se koristi setEmail funkcija da se azurira stanje emaila sa novom vrednoscu iz inputa; target je ono na sta smo kliknuli, a .value je nova vrednost tog polja; 
          //  ovo omogucava da se stanje emaila u komponenti odrzi sinhronizovanim sa vrednoscu unetom u input polje
          required
        />
        {/* <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
        /> */}
        <PasswordInput
          placeholder="Lozinka"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />

        {info && <div className="info">{info}</div>}
        {error && <div className="error-popup">{error}</div>}

        <button type="submit" disabled={loading}>
          {loading ? "Prijavljivanje..." : "Prijavi se"}
        </button>
      </form>
    </div>
  )
}

export default LoginPage
