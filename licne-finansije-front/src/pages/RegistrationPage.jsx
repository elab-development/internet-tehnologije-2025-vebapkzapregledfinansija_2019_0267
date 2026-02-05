import React from 'react'
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import TextInput from '../components/TextInput';
import PasswordInput from '../components/PasswordInput';
import PrimaryButton from '../components/PrimaryButton';
import api from '../api/api';
import './Login/LoginPage.css';

const RegistrationPage = () => {

    const navigate = useNavigate();

    const [ime, setIme] = useState("");
    const [prezime, setPrezime] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const[passwordConfirm, setPasswordConfirm] = useState("");

    const [error, setError] = useState('');
    const [info, setInfo] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setInfo('');
        setLoading(true);

        try {
            const response = await api.post("/register", { 
                ime, 
                prezime, 
                email, 
                password, 
                password_confirmation:passwordConfirm 
            });

            console.log(response.data);
            setInfo(response.data.message || "Uspešna registracija");
            setLoading(false);

            setTimeout(() => {
                navigate("/login");
            }, 1000);
        } 
        catch (err) {
            setLoading(false);
            setError(err.response?.data?.message || "Greška prilikom registracije");
        
        }
    }
  return (
    <div className="login-container">
      <form className="login-form" onSubmit={handleSubmit}>
        <h2>Registracija</h2>


        <TextInput
          placeholder="Ime"
          value={ime}
          onChange={(e) => setIme(e.target.value)}
          required
        />

        <TextInput
          placeholder="Prezime"
          value={prezime}
          onChange={(e) => setPrezime(e.target.value)}
          required
        />

        <TextInput
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />

        <PasswordInput
          placeholder="Lozinka"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
        />

        <PasswordInput
          placeholder="Potvrda lozinke"
          value={passwordConfirm}
          onChange={(e) => setPasswordConfirm(e.target.value)}
        />

        {info && <div className="info">{info}</div>}
        {error && <div className="error-popup">{error}</div>}

        <PrimaryButton text="Registruj se" loading={loading} />
      </form>
    </div>
  )
}

export default RegistrationPage
