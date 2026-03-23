import React from 'react'
import './Pocetna.css';
import api from '../api/api';
import { useState } from 'react';
import { useEffect } from 'react';
import TextInput from '../components/TextInput';
import SelectInput from '../components/SelectInput';
import PrimaryButton from '../components/PrimaryButton';
import { FiEdit, FiTrash2 } from 'react-icons/fi';

const months = [
  { value: 1, label: 'Januar' },
  { value: 2, label: 'Februar' },
  { value: 3, label: 'Mart' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Maj' },
  { value: 6, label: 'Jun' },
  { value: 7, label: 'Jul' },
  { value: 8, label: 'Avgust' },
  { value: 9, label: 'Septembar' },
  { value: 10, label: 'Oktobar' },
  { value: 11, label: 'Novembar' },
  { value: 12, label: 'Decembar' }
];


const Budzet = () => {

    const user= JSON.parse(localStorage.getItem("user"));

    const [error, setError] = useState('');
    const [info, setInfo] = useState('');
    const [loading, setLoading] = useState(false);

    const [budgets, setBudgets] = useState([]);
    const [mesec, setMesec] = useState('');
    const [godina, setGodina] = useState('');
    const [limit, setLimit] = useState('');
    const [potroseno, setPotroseno] = useState('');
    const [editId, setEditId] = useState(null);

    const fetchBudgets = async () => {
        try {
            setLoading(true);
            const res = await api.get(`/budzeti/korisnik/${user.id}`);
            console.log("Budget response data:", res.data);
            setBudgets(res.data.data);
        } catch (err) {
            console.error(err);
            setError("Greška prilikom učitavanja budžeta");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchBudgets();
    }, []);

        const handleSubmit = async (e) => {
            e.preventDefault();

            const payload = {
                idKorisnik: user.id,
                mesec,
                godina,
                limit,
                potroseno
            };

            setError('');
            setInfo('');
            setLoading(true);

            if (editId) {
                try {
                    const res = await api.put(`/budzeti/${editId}`, payload);
                    setBudgets(budgets.map(b => b.idBudzet === editId ? res.data : b));
                    setInfo("Budžet uspešno izmenjen");
                    setEditId(null);
                } catch (err) {
                    setError("Greška prilikom izmene budžeta");
                } finally {
                    setLoading(false);
                }
            } else {
                try {
                    const res = await api.post("/budzeti", payload);
                    const newBudget = res.data.data;
                    // setBudgets(prev => {
                    //     const clean=prev.filter(b=>b && b.mesec!==undefined);
                    //     return [...clean, newBudget]});
                    setInfo("Budžet uspešno kreiran");
                    await fetchBudgets();
                } catch (err) {
                    setError("Greška prilikom kreiranja budžeta");
                } finally {
                    setLoading(false);
                }
            }

            setMesec('');
            setGodina('');
            setLimit('');
            setPotroseno('');
        }

        const handleDelete = async (id) => {
            if (!window.confirm("Da li ste sigurni da želite da obrišete ovaj budžet?")) return;
            try {
                await api.delete(`/budzeti/${id}`);
                // setBudgets(budgets.filter(b => b.idBudzet !== id));
                setInfo("Budžet uspešno obrisan");
                await fetchBudgets();
            } catch (err) {
                setError("Greška prilikom brisanja budžeta");
            }
        }

        const handleEdit = (budget) => {
            setMesec(budget.mesec);
            setGodina(budget.godina);
            setLimit(budget.limit);
            setPotroseno(budget.potroseno);
            setEditId(budget.idBudzet);
        }

        
  return (
    <div className="page">
      <div className="hero">
        {/* FORMA */}
        <div className="summary-card">
          <h2>{editId ? "Izmena budžeta" : "Novi budžet"}</h2>

          <form onSubmit={handleSubmit}>
            <SelectInput
              label="Mesec:"
              value={mesec}
              onChange={(e) => setMesec(e.target.value)}
              options={months}
            />

            <TextInput
              placeholder="Godina"
              value={godina}
              onChange={(e) => setGodina(e.target.value)}
            />

            <TextInput
              placeholder="Limit"
              value={limit}
              onChange={(e) => setLimit(e.target.value)}
            />

            <TextInput
              placeholder="Potrošeno"
              value={potroseno}
              onChange={(e) => setPotroseno(e.target.value)}
            />
            {info && <div className="info">{info}</div>}
            {error && <div className="error-popup">{error}</div>}
            {loading && <p>Učitavanje...</p>}
            <PrimaryButton text={editId ? "Sačuvaj izmene" : "Kreiraj budžet"} />
          </form>
        </div>

        {/* LISTA */}
        <div className="features-grid">
          {budgets.filter(b => b && b.mesec !== undefined).map((b) => (
            <div className="feature-card" key={b.idBudzet}>
              <h3>{months.find(m => m.value === Number(b.mesec))?.label} {b.godina}</h3>
              <p>Limit: {b.limit}</p>
              <p>Potrošeno: {b.potroseno}</p>

              <div className="hero-actions">
                <FiEdit onClick={() => handleEdit(b)} cursor="pointer" />
                <FiTrash2 onClick={() => handleDelete(b.idBudzet)} cursor="pointer" />
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  )
}

export default Budzet
