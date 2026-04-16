import React, { use } from 'react'
import SummaryCard from '../components/SummaryCard'
import {Chart} from 'react-google-charts';
import '../pages/Pocetna.css';
import api from '../api/api';
import { useState, useEffect } from 'react';




const MojProfil = () => {

    const user=JSON.parse(localStorage.getItem("user"));

    const [info, setInfo] = React.useState(null);
    const [error, setError] = React.useState(null);
    const [loading, setLoading] = React.useState(true);
    const [korisnik, setKorisnik] = React.useState(null);

    const [transakcije, setTransakcije] = React.useState([]);
    const [ciljevi, setCiljevi] = React.useState([]);

    const [formData, setFormData] = React.useState({
        ime: user?.ime || "",
        prezime: user?.prezime || "",
        email: user?.email || "",
        // password: "",
        // password_confirmation: "",
    });

    const [edit, setEdit] = React.useState(false);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value,
        });
    }

    const handleSubmit = async (e) => {
        try {
            setLoading(true);
            console.log("Submitting profile update with data:", formData);
            const res = await api.put('/profile', formData);
            console.log("Profile update response data:", res.data);
            setInfo("Podaci su uspešno ažurirani");
            setKorisnik(prev => ({ ...prev, ...formData }));
            localStorage.setItem("user", JSON.stringify({ ...user, ...formData }));
            setEdit(false);
        }catch (error) {
            console.error("Greska pri azuriranju profila:", error);
            setError("Greska pri azuriranju profila");
        } finally {
            setLoading(false);
        }
    }

    const fetchTransakcije = async (kategorijaId=null) => {
        try {
            setLoading(true);
            let url = `/transakcije/korisnik/${user.id}`;
            if (kategorijaId) {
                url += `/kategorija/${kategorijaId}`;
            }
            const res = await api.get(url);
            console.log("Transakcije response data:", res.data);
            setTransakcije(res.data.data);
        } catch (error) {
            console.log(error.response.data);
            console.log(error.response.data.errors);
            console.error("Greska pri ucitavanju transakcija:", error);
            setError("Greska pri ucitavanju transakcija");
        } finally {
            setLoading(false);
        }
    };

    const fetchUserInfo = async () => {
        try {
            setLoading(true);
            const res = await api.get('/me');
            setKorisnik(res.data);
        } catch (error) {
            console.error("Greska pri ucitavanju informacija o korisniku:", error);
            setError("Greska pri ucitavanju informacija o korisniku");
        } finally {
            setLoading(false);
        }
    };

    const fetchCiljevi = async () => {
        try {
            setLoading(true);
            const res = await api.get(`/finansijski-ciljevi/korisnik/${user.id}`);
            console.log("Ciljevi response data:", res.data);
            setCiljevi(res.data.data);
        } catch (error) {
            console.error("Greska pri ucitavanju ciljeva:", error);
            setError("Greska pri ucitavanju ciljeva");
        }finally {
            setLoading(false);
        }
    };


    useEffect(() => {
        fetchTransakcije();
        fetchUserInfo();
        fetchCiljevi();
    }, []);

    const generatePieData = (transakcije) => {
        const counts=transakcije.reduce((acc, transakcija) => {
            const kategorija = transakcija.kategorija?.naziv || "Nepoznato";
            acc[kategorija] = (acc[kategorija] || 0) + 1;
            return acc;
        }, {});

        const data = [["Kategorija", "Broj transakcija"]];
        Object.entries(counts).forEach(([kategorija, count]) => {
            data.push([kategorija, count]);
        });

        return data;
    }

    const pieData = generatePieData(transakcije);
    

    const pieOptions = {
        title: "Transakcije po kategorijama",
        pieHole: 0.4,
        colors: ["#059669", "#ef4444", "#3b82f6", "#f59e0b"],
    };

    // Primer podataka za bar chart (ciljevi)

    const generateCiljeviData = (ciljevi) => {
        const data = [["Naziv cilja", "Trenutni iznos", "Ciljni iznos"]];
        ciljevi.forEach(cilj => {
            data.push([cilj.naziv, Number(cilj.trenutni_iznos), Number(cilj.ciljni_iznos)]);
        });

        console.log("Generated ciljevi data for chart:", data);

        return data;
    }

    const ciljeviData = generateCiljeviData(ciljevi);

    const ciljeviOptions = {
        title: "Finansijski ciljevi",
        bars: "horizontal",
        colors: ["#059669", "#ef4444"],
        bar: { groupWidth: "70%" },
        chartArea: { width: "70%" },
        isStacked: false,
    };

    

  return (
    <div>
        <div className="hero">
            {/* Leva strana: podaci o korisniku */}
            <div className="hero-text">
                <h1>Moj Profil</h1>
                {!edit ? (<div style={{marginTop: "50px"}}>
                    <p>Ime: {korisnik?.ime}</p>
                    <p>Prezime: {korisnik?.prezime}</p>
                    <p>Email: {korisnik?.email}</p>
                    <p>Bodovi: {korisnik?.poeni}</p>
                    <p>Nivo: {korisnik?.nivo}</p>
                    <p>Uloga: {korisnik?.uloga}</p>
                    <button className="btn primary" onClick={() => setEdit(true)}>
                        Izmeni podatke
                    </button>
                </div>):(
                    <div>
                        <div className="form-group">
                            <label>Ime:</label>
                            <input type="text" name="ime" value={formData.ime} onChange={handleChange} />
                        </div>
                        <div className="form-group">
                            <label>Prezime:</label>
                            <input type="text" name="prezime" value={formData.prezime} onChange={handleChange} />
                        </div>
                        <div className="form-group">
                            <label>Email:</label>
                            <input type="email" name="email" value={formData.email} onChange={handleChange} />
                        </div>
                        {info && <p className="info">{info}</p>}
                        {error && <p className="error">{error}</p>}
                        {loading && <p>Učitavanje...</p>}
                        <button className="btn primary" onClick={handleSubmit}>
                            Sačuvaj izmene
                        </button>
                        <button className="btn secondary" onClick={() => setEdit(false)}>
                            Otkaži
                        </button>
                    </div>

                )}
                
            </div>

            {/* Desna strana: SummaryCard + grafikoni */}
            <div>
            <SummaryCard />
            </div>
        </div>
        <div className="metrics-section">
            <h1 style={{ textAlign: "center", marginBottom: "40px" }}>Moje metrike</h1>
            <div className="metrics-grid">
                <div className="metric-card">
                    <Chart
                    chartType="PieChart"
                    data={pieData}
                    options={pieOptions}
                    width={"100%"}
                    height={"400px"}
                    />
                </div>
                <div className="metric-card">
                    <Chart
                    chartType="BarChart"
                    data={ciljeviData}
                    options={ciljeviOptions}
                    width={"100%"}
                    height={"500px"}
                    />
                </div>
            </div>
        </div>

    </div>
  )
}

export default MojProfil