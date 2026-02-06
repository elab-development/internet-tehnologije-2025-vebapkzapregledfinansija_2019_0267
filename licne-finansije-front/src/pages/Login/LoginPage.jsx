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
            const {token, user, message} =response.data;

            //UPISIVANJE TOKENA I KORISNICKIH PODATAKA U LOCAL STORAGE
            localStorage.setItem("token", token);
            localStorage.setItem("user", JSON.stringify(user));

            setInfo(message || "Uspešno logovanje");
            setLoading(false);
            setTimeout(() => {
                navigate("/budzet");
            }, 1000);
        } catch (err) {
            setLoading(false);
            console.log(err);
           if(err.response.status === 401){
            setError("Neispravni kredencijali");
           } else if (err.response.status === 422){
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
          onChange={(e) => setEmail(e.target.value)}
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
