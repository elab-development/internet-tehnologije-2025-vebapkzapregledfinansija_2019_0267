import React from 'react'
import { useState, useEffect } from 'react';
import './Pocetna.css';
import api from '../api/api';
import TextInput from '../components/TextInput';
import PrimaryButton from '../components/PrimaryButton';
import { FiEdit, FiTrash2 } from 'react-icons/fi';
import { set } from 'date-fns';


const Kategorija = () => {
  const user= JSON.parse(localStorage.getItem("user"));

  const [error, setError] = useState('');
  const [info, setInfo] = useState('');
  const [loading, setLoading] = useState(false);

  const [kategorije, setKategorije] = useState([]);
  const [naziv, setNaziv] = useState('');
  const [opis, setOpis] = useState('');
  const [editId, setEditId] = useState(null);

  const fetchKategorije = async () => {
    try {
        setLoading(true);
        const res = await api.get(`/kategorije/korisnik/${user.id}`);
        console.log("CEO RESPONSE:", res);
        console.log("DATA:", res.data);
        console.log("KATEGORIJE:", res.data.data);
        console.log("Kategorije response data:", res.data);
        setKategorije(res.data.data);
    }catch (error) {
        console.log(error.response.data);
        console.log(error.response.data.errors);
        console.error("Greska pri ucitavanju kategorija:", error);
        setError("Greska pri ucitavanju kategorija");
    } finally {
        setLoading(false);
    }
  };

  useEffect(() => {
    fetchKategorije();
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const payload = {
        idKorisnik: user.id,
        naziv,
        opis
    };

    setError('');
    setInfo('');
    setLoading(true);

    if (editId) {
        try{
            const res=await api.put(`/kategorije/${editId}`, payload);
            console.log("Update kategorija response:", res);
            console.log("Updejtovana kategorija data:", res.data);
            setKategorije(kategorije.map(k => k.idKategorija === editId ? res.data : k));
            setInfo("Kategorija uspešno ažurirana");
            setEditId(null);
        }catch(error){
            console.error("Greska pri ažuriranju kategorije:", error);
            setError("Greska pri ažuriranju kategorije");
        }finally{
            setLoading(false);
        }
    }else{
      try{
          const res = await api.post('/kategorije', payload);
          console.log("Kreiranje kategorije response:", res.data);
          console.log("Kreirana kategorija data:", res.data.data);
          const newKategorija = res.data;
          setKategorije([...kategorije, newKategorija]);
          setInfo("Kategorija uspešno kreirana");
          await fetchKategorije();
      }catch(error){
          console.log(error.response.data);
          console.log(error.response.data.errors);
          console.error("Greska pri kreiranju kategorije:", error);
          setError("Greska pri kreiranju kategorije");
      }finally{
          setLoading(false);
      }
    }

    setNaziv('');
    setOpis('');
  }

  const handleDelete = async (id) => {
    if (!window.confirm("Da li ste sigurni da želite da obrišete ovu kategoriju?")) {
      return;
    }
    try{
        await api.delete(`/kategorije/${id}`);
        setInfo("Kategorija uspešno obrisana");
        await fetchKategorije();
    }catch(error){
        console.error("Greska pri brisanju kategorije:", error);
        setError("Greska pri brisanju kategorije");
    }
  }

  const handleEdit = (kategorija) => {
    setNaziv(kategorija.naziv);
    setOpis(kategorija.opis);
    setEditId(kategorija.idKategorija);
  }

  return (
    <div className="page">
      <div className='hero'>
        <div className='summary-card'>
          <h2>{editId ? "Izmeni kategoriju" : "Dodaj kategoriju"}</h2>
          <form onSubmit={handleSubmit}>
            <TextInput
              placeholder="Naziv kategorije"
              value={naziv}
              onChange={(e) => setNaziv(e.target.value)}
            />
            <TextInput
              placeholder="Opis kategorije"
              value={opis}
              onChange={(e) => setOpis(e.target.value)}
            />

            {info && <p className="info">{info}</p>}
            {error && <p className="error">{error}</p>}
            {loading && <p>Učitavanje...</p>}
            
            <PrimaryButton text={editId ? "Sačuvaj izmene" : "Dodaj kategoriju"} />

          </form>
        </div>  
        {/* LISTA KATEGORIJA  */}
        <div className="features-grid">
          {
            kategorije.map(kategorija=>(
              <div key={kategorija.idKategorija} className="feature-card">
                <h3>{kategorija.naziv}</h3>
                <p>{kategorija.opis}</p>

                <div className="hero-actions">
                  <FiEdit className="edit-icon" onClick={() => handleEdit(kategorija)} />
                  <FiTrash2 className="delete-icon" onClick={() => handleDelete(kategorija.idKategorija)} />
                </div>
              </div>
            ))
          }
        </div>
      </div>
    </div>
  )
}

export default Kategorija
