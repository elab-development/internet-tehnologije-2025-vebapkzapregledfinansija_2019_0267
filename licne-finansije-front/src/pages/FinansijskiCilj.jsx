import React from 'react'
import { useState, useEffect } from 'react';
import './Pocetna.css';
import api from '../api/api';
import TextInput from '../components/TextInput';
import DateInput from '../components/DateInput';
import PrimaryButton from '../components/PrimaryButton';
import { FiEdit, FiTrash2 } from 'react-icons/fi';

const FinansijskiCilj = () => {
    const user= JSON.parse(localStorage.getItem("user"));

    const [error, setError] = useState('');
    const [info, setInfo] = useState('');
    const [loading, setLoading] = useState(false);

    const [ciljevi, setCiljevi] = useState([]);
    const [naziv, setNaziv] = useState('');
    const [ciljIznos, setCiljIznos] = useState('');
    const [trenutniIznos, setTrenutniIznos] = useState('');
    const [rok, setRok] = useState(new Date());
    const [editId, setEditId] = useState(null);


    const fetchCiljevi = async () => {
        try {
            setLoading(true);
            const res = await api.get(`/finansijski-ciljevi/korisnik/${user.id}`);
            console.log("Ciljevi response data:", res.data);
            setCiljevi(res.data.data);

            console.log("CEO RESPONSE:", res);
            console.log("DATA:", res.data);
            console.log("CILJEVI:", res.data.data);
        } catch (error) {
            console.error("Greska pri ucitavanju ciljeva:", error);
            setError("Greska pri ucitavanju ciljeva");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCiljevi();
    }, []);

    const handleSubmit = async (e) => {
        e.preventDefault();

        const payload = {
            idKorisnik: user.id,
            naziv,
            ciljni_iznos: ciljIznos,
            trenutni_iznos: trenutniIznos,
            rok
        };

        setError('');
        setInfo('');
        setLoading(true);

        if (editId) {
            try {
                const res = await api.put(`/finansijski-ciljevi/${editId}`, payload);
                setCiljevi(ciljevi.map(c => c.idCilj === editId ? res.data : c));
                setInfo("Cilj uspešno izmenjen");
                setEditId(null);
            } catch (error) {
                console.error("Greska pri izmeni cilja:", error);
                setError("Greska pri izmeni cilja");
            } finally {
                setLoading(false);
            }
        }else {
            try{
                const res = await api.post("/finansijski-ciljevi", payload);
                const newCilj = res.data.data;
                setCiljevi([...ciljevi, newCilj]);
            
                setInfo("Cilj uspešno dodat");
                await fetchCiljevi();
            }catch (error) {
                console.error("Greska pri dodavanju cilja:", error);
                setError("Greska pri dodavanju cilja");
            } finally {
                setLoading(false);
            }
        }

        setNaziv('');
        setCiljIznos('');
        setTrenutniIznos('');
        setRok('');
    }   

    const handleDelete = async (id) => {
        if (!window.confirm("Da li ste sigurni da želite da obrišete ovaj cilj?")) {
            return;
        }
        try {
            await api.delete(`/finansijski-ciljevi/${id}`);
            // setCiljevi(ciljevi.filter(c => c.idCilj !== id));
            setInfo("Cilj uspešno obrisan");
            await fetchCiljevi();
        } catch (error) {
            console.error("Greska pri brisanju cilja:", error);
            setError("Greska pri brisanju cilja");
        }
    }

    const handleEdit = (cilj) => {
        setNaziv(cilj.naziv);
        setCiljIznos(cilj.ciljni_iznos);
        setTrenutniIznos(cilj.trenutni_iznos);
        setRok(cilj.rok);
        setEditId(cilj.idCilj);
    }



  return (
    <div className="page">
      <div className="hero">
        {/* FORMA */}
        <div className="summary-card">
          <h2>{editId ? "Izmena cilja" : "Novi cilj"}</h2>

          <form onSubmit={handleSubmit}>
            <TextInput
              placeholder="Naziv cilja"
              value={naziv}
              onChange={(e) => setNaziv(e.target.value)}
            />

            <TextInput
              placeholder="Ciljni iznos"
              value={ciljIznos}
              onChange={(e) => setCiljIznos(e.target.value)}
            />

            <TextInput
              placeholder="Trenutni iznos"
              value={trenutniIznos}
              onChange={(e) => setTrenutniIznos(e.target.value)}
            />

            <DateInput
              
              value={rok}
              onChange={(date) => setRok(date)}
            />
            {info && <div className="info">{info}</div>}
            {error && <div className="error-popup">{error}</div>}
            {loading && <p>Učitavanje...</p>}
            <PrimaryButton text={editId ? "Sačuvaj izmene" : "Kreiraj cilj"} />
          </form>
        </div>

        {/* LISTA */}
        <div className="features-grid">
          {ciljevi.filter((c) => c && c.rok !== undefined).map((c) => (
            <div className="feature-card" key={c.idCilj}>
              <h3>{c.naziv}</h3>
              <p>Ciljni iznos: {c.ciljni_iznos}</p>
              <p>Trenutni iznos: {c.trenutni_iznos}</p>
              <p>Rok: {c.rok}</p>

              <div className="hero-actions">
                <FiEdit onClick={() => handleEdit(c)} cursor="pointer" />
                <FiTrash2 onClick={() => handleDelete(c.idCilj)} cursor="pointer" />
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default FinansijskiCilj
