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


    useEffect(() => {
        fetchTransakcije();
        fetchUserInfo();
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

    // Primer podataka za gauge chart (ciljevi)
    const gaugeData = [
        ["Label", "Value"],
        ["Štednja", 70],
        ["Putovanje", 40],
        ["Investicije", 55],
    ];

    const gaugeOptions = {
        width: 400,
        height: 120,
        redFrom: 0,
        redTo: 30,
        yellowFrom: 30,
        yellowTo: 70,
        greenFrom: 70,
        greenTo: 100,
        minorTicks: 5,
    };

  return (
    <div>
        <div className="hero">
            {/* Leva strana: podaci o korisniku */}
            <div className="hero-text">
            <h1>Moj Profil</h1>
            <p>Ime: {korisnik?.ime}</p>
            <p>Prezime: {korisnik?.prezime}</p>
            <p>Email: {korisnik?.email}</p>
            <button className="btn primary">Izmeni podatke</button>
            </div>

            {/* Desna strana: SummaryCard + grafikoni */}
            <div>
            <SummaryCard />
            </div>
        </div>
        <div className="metrics-section">
            <h2>Moje metrike</h2>
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
                    chartType="Gauge"
                    data={gaugeData}
                    options={gaugeOptions}
                    width={"100%"}
                    height={"200px"}
                    />
                </div>
            </div>
        </div>

    </div>
  )
}

export default MojProfil