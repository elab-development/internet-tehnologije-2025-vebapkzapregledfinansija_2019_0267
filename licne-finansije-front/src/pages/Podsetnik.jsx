import React from 'react'
import { useState, useEffect } from 'react';
import './Pocetna.css';
import api from '../api/api'; 
import TextInput from '../components/TextInput';
import DateInput from '../components/DateInput';
import PrimaryButton from '../components/PrimaryButton';
import { FiEdit, FiTrash2 } from 'react-icons/fi';  
import { set } from 'date-fns';


const Podsetnik = () => {

    const user= JSON.parse(localStorage.getItem("user"));

    const [error, setError] = useState('');
    const [info, setInfo] = useState('');
    const [loading, setLoading] = useState(false);

    const [podsetnici, setPodsetnici] = useState([]);
    const [opis, setOpis] = useState('');
    const [datum, setDatum] = useState(new Date());
    const [status, setStatus] = useState(false);
    const [editId, setEditId] = useState(null);

    const fetchPodsetnici = async () => {
        try {
            setLoading(true);
            const res = await api.get(`/podsetnici/korisnik/${user.id}`);
            console.log("Podsetnici response data:", res.data);
            setPodsetnici(res.data.data);
        } catch (error) {
            console.error("Greska pri ucitavanju podsetnika:", error);
            setError("Greska pri ucitavanju podsetnika");
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        fetchPodsetnici();
    }, []);


    const handleSubmit = async (e) => {
        e.preventDefault();
        const payload = {
            idKorisnik: user.id,
            opis,
            datum_vreme: new Date(datum).toISOString(),
            status: status ? 1 : 0
        };

        setError('');
        setInfo('');
        setLoading(true);

        if (editId) {
           try{
                const res=await api.put(`/podsetnici/${editId}`, payload);
                const updatedPodsetnik = res.data;
                setPodsetnici(podsetnici.map(p => p.idPodsetnik === editId ? updatedPodsetnik : p));
                setInfo("Podsetnik uspešno izmenjen");
                setEditId(null);
           }catch(error){

                console.error("Greska pri izmeni podsetnika:", error);
                setError("Greska pri izmeni podsetnika");
           }finally{
                setLoading(false);
           }
        }else{
            try{
                const res = await api.post('/podsetnici', payload);
                // kako vraca podatke sa beka
                console.log("Podsetnik post response data:", res.data);
                console.log("Podsetnik post response data-data:", res.data.data);
                const newPodsetnik = res.data;
                console.log("Dodat podsetnik:", newPodsetnik);
                setPodsetnici([...podsetnici, newPodsetnik]);
                setInfo("Podsetnik uspešno dodat");
                await fetchPodsetnici();
            }catch(error){
                console.log(error.response.data);
                console.log(error.response.data.errors);
                console.error("Greska pri dodavanju podsetnika:", error);
                setError("Greska pri dodavanju podsetnika");
            }finally{
                setLoading(false);
            }
        }

        setOpis('');
        setDatum(new Date());
        setStatus(true);
    }

    const handleDelete = async (id) => {
        if (!window.confirm("Da li ste sigurni da želite da obrišete ovaj podsetnik?")) {
            return;
        }
        try{
            await api.delete(`/podsetnici/${id}`);
            setInfo("Podsetnik uspešno obrisan");
            await fetchPodsetnici();
        } catch (error) {
            console.error("Greska pri brisanju podsetnika:", error);
            setError("Greska pri brisanju podsetnika");
        }
    }

    const handleEdit = (podsetnik) => {
        setOpis(podsetnik.opis);
        setDatum(new Date(podsetnik.datum_vreme));
        setStatus(podsetnik.status);
        setEditId(podsetnik.idPodsetnik);
    }

  return (
    <div className="page">
        <div className='hero'>
            {/* FORMA */}
            <div className='summary-card'>
                <h2>{editId ? "Izmeni podsetnik" : "Dodaj novi podsetnik"}</h2>
                <form onSubmit={handleSubmit} >
                    <TextInput
                       placeholder="Opis podsetnika"
                       value={opis}
                       onChange={(e) => setOpis(e.target.value)}
                    />
                    <DateInput
                       label="Datum i vreme"
                       value={datum}
                       onChange={(date) => setDatum(date)}
                    />
                    <input
                       type="checkbox"
                       label="Status"
                       checked={status}
                       onChange={(e) => setStatus(e.target.checked)}
                    />
                    {info && <div className="info">{info}</div>}
                    {error && <div className="error-popup">{error}</div>}
                    {loading && <p>Učitavanje...</p>}
                    <PrimaryButton text={editId ? "Sačuvaj izmene" : "Dodaj podsetnik"} />
                </form>
            </div>
            {/* LISTA PODSETNIKA */}
            <div className="features-grid">
                {podsetnici.map(p => (
                    <div key={p.idPodsetnik} className="feature-card">
                        <h3>{p.opis}</h3>
                        <p>{new Date(p.datum_vreme).toLocaleString()}</p>
                        <p>Status: {p.status ? "Aktivan" : "Neaktivan"}</p>
                        
                        <div className="hero-actions">
                            <FiEdit onClick={() => handleEdit(p)} cursor="pointer" />
                            <FiTrash2 onClick={() => handleDelete(p.idPodsetnik)} cursor="pointer" />
                        </div>
                    </div>
                ))}
            </div>
        </div>
      
    </div>
  )
}

export default Podsetnik
