import React from 'react'
import api from '../api/api'
import './Pocetna.css';
import SummaryCard from '../components/SummaryCard';
import { useState, useEffect } from 'react';
import { Chart } from 'react-google-charts';


const AdminStats = () => {
  
  const [stats, setStats] = useState(null);

  const [info, setInfo] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        setLoading(true);
        const res = await api.get("/stats/users");
        console.log("Fetched admin stats data:", res.data);
        setStats(res.data);
      } catch (err) {
        console.error("Error fetching stats:", err);
        setError("Greska pri ucitavanju statistike");
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

  const pieChartData =stats? [
    ["Uloga", "Broj korisnika"],
    ...stats.by_role.map(role => [role.uloga, role.count])
  ]:["Uloga", "Broj korisnika"];

  const barChartData = stats?[
    ["Period","Korisnici"],
    ["Poslednjih sedam dana", stats.users_last_7_days],
    ["Poslednjih mesec dana", stats.users_last_30_days],
    ["Poslednjih godinu dana", stats.users_last_365_days]
  ]: [["Period", "Korisnici"]];

  const dailyLineChartData = stats?[
    ["Datum", "Novi korisnici"],
    ...stats.daily_users.map(d => [d.date, d.count])
  ]: [["Datum", "Novi korisnici"]];

  const avgPerDay = stats? (stats.users_last_30_days / 30).toFixed(2) : 0;



  



  return (
    <div className="page">
      <div className="metrics-section">
        <h2>Statistika korisnika</h2>
        {loading && <p>Učitavanje statistike...</p>}
        {error && <p className="error">{error}</p>}
        {stats && (
        <>
        <div className="metrics-grid">
          {/* kartice */}
          <div className="metric-card">
            <h3>Ukupno korisnika</h3>
            <p className="balance">{stats.total_users + stats.deleted_users}</p>
          </div>
          <div className="metric-card">
            <h3>Ukupno aktivnih korisnika</h3>
            <p className="balance">{stats.total_users}</p>
          </div>
          <div className="metric-card">
            <h3>Ukupno obrisanih korisnika</h3>
            <p className="balance">{stats.deleted_users}</p>
          </div>
          <div className="metric-card">
            <h3>Ukupno premium korisnika</h3>
            <p className="balance">{stats.by_role.find(r => r.uloga === "premium")?.count || 0}</p>
          </div>
          <div className="metric-card">
            <h3>Prosecan broj novih korisnika dnevno</h3>
            <p className="balance">{avgPerDay}</p>
          </div>
        </div>
        <h2>Grafikoni</h2>
        <div className="metrics-grid">
          
          {/* chartovi */}
            <div className="metric-card">
              <h3>Korisnici po ulozi</h3>

              <Chart
                chartType="PieChart"
                width="100%"
                height="250px"
                data={pieChartData}
                options={{
                  pieHole: 0.4,
                  backgroundColor: "transparent",
                  legend: { position: "bottom" }
                }}
              />
            </div>
            <div className="metric-card">
                <h3>Trend registracije novih korisnika</h3>

                <Chart
                  chartType="ColumnChart"
                  width="100%"
                  height="300px"
                  data={barChartData}
                  options={{
                    title: "Rast korisnika",
                    legend: { position: "none" },
                    colors: ["#059669"]
                  }}
                />
            </div>
            <div className="metric-card">
                <h3>Novi korisnici po danima</h3>

                <Chart
                  chartType="LineChart"
                  width="100%"
                  height="300px"
                  data={dailyLineChartData}
                  options={{
                    title: "Novi korisnici po danima",
                    legend: { position: "none" },
                    colors: ["#3B82F6"]
                  }}
                />  
            </div>
          </div>
          </>
        )}
      </div>
    </div>
  )
}

export default AdminStats