import React, { use } from 'react'
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './Pocetna.css';
import api from '../api/api';
import TextInput from '../components/TextInput';
import DateInput from '../components/DateInput';
import SelectInput from '../components/SelectInput';
import PrimaryButton from '../components/PrimaryButton';
import { FiEdit, FiTrash2 } from 'react-icons/fi';
import { set } from 'date-fns';
import { format } from 'date-fns';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';




const Transakcija = () => {
    const user= JSON.parse(localStorage.getItem("user"));

    const [error, setError] = useState('');
    const [info, setInfo] = useState('');
    const [loading, setLoading] = useState(false);

    const navigate = useNavigate();

    const [transakcije, setTransakcije] = useState([]);
    const [kategorije, setKategorije] = useState([]);

    const [idKategorija, setIdKategorija] = useState('');
    const [kategorija, setKategorija] = useState('');
    const [datumVreme, setDatumVreme] = useState(new Date());
    const [tipTransakcije, setTipTransakcije] = useState('');
    const [iznos, setIznos] = useState('');
    const [valuta, setValuta] = useState('');
    const [opis, setOpis] = useState('');

    //RAD SA IZBOROM VALUTE I KURSEVIMA
    const [valute, setValute] = useState([]);
    const [rates, setRates] = useState({});

    useEffect(() => {
        const fetchValute = async () => {
            const res=await fetch("https://v6.exchangerate-api.com/v6/a885afe0ecbda562f260cbde/latest/USD");
            const data = await res.json();
            console.log("Valute response data:", data);
            console.log("Valute:", Object.keys(data.conversion_rates));
            setValute(Object.keys(data.conversion_rates));//ucitava kodove valuta
            setRates(data.conversion_rates);//ucitava kurseve valuta u odnosu na USD
        };
        fetchValute();
    }, []);

    //Funkcija za konverziju iznosa iz jedne valute u drugu
    const convertIznos=(iznos, fromCurrency, toCurrency) => {
        if(fromCurrency === toCurrency){
            return iznos;
        }
        const rate = rates[toCurrency] / rates[fromCurrency];
        return iznos * rate;
    };


    //DA LI DA SE DOZVOLI IZMENA TRANSAKCIJE...
    const [editId, setEditId] = useState(null);

    const [activeKategorija, setActiveKategorija] = useState(null);

    const handleKategorijaClick = async (idKategorija) => {
        setActiveKategorija(idKategorija);
        setTransakcije([]);
        setLoading(true);
        await fetchTransakcije(idKategorija);
        setLoading(false);
    };

    const fetchKategorije = async () => {
        try {
            setLoading(true);
            const res = await api.get(`/kategorije/korisnik/${user.id}`);
            console.log("Kategorije response data:", res.data);
            setKategorije(res.data.data);
        } catch (error) {
            console.error("Greska pri ucitavanju kategorija:", error);
            setError("Greska pri ucitavanju kategorija");
        } finally {
            setLoading(false);
        }
    };

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

    

    useEffect(() => {
        fetchKategorije();
        fetchTransakcije();
    }, [user.id]);


    //rad sa pdf-om

    const exportToPDF = (transakcije) => {
        const doc = new jsPDF();

        console.log("Exporting to PDF, transakcije:", transakcije);

        doc.setFontSize(18);
        doc.text("Izveštaj transakcija", 14, 20);

        
        const table=transakcije.map(t => [
            t.idTransakcija,
            t.kategorija?.naziv || '',
            t.datum_vreme,
            t.tipTransakcije,
            t.iznos.toLocaleString("sr-RS", { style: "currency", currency: t.valuta }),
            t.valuta,
            t.opis
        ]);

        autoTable(doc, {
            head: [['ID', 'Kategorija', 'Datum i vreme', 'Tip', 'Iznos', 'Valuta', 'Opis']],
            body: table,
            startY: 30,
        });


        const pdfUrl = doc.output('bloburl');
        window.open(pdfUrl);
    };



    const handleSubmit = async (e) => {
        e.preventDefault();

        const payload = {
            idKorisnik: user.id,
            idKategorija,
            datum_vreme: format(datumVreme, "yyyy-MM-dd HH:mm:ss"),
            tipTransakcije,
            iznos,
            valuta,
            opis
        };

        setError('');
        setInfo('');
        setLoading(true);

        if (editId) {
            try {
                console.log("Payload za ažuriranje transakcije:", payload);
                const res = await api.put(`/transakcije/${editId}`, payload);
                console.log("Update transakcija response:", res);
                console.log("Updejtovana transakcija data:", res.data);
                setTransakcije(transakcije.map(t => t.idTransakcija === editId ? res.data : t));
                setInfo("Transakcija uspešno ažurirana");
                setEditId(null);
            } catch (error) {
                console.log(error.response.data);
                console.log(error.response.data.errors);
                console.error("Greska pri ažuriranju transakcije:", error);
                setError("Greska pri ažuriranju transakcije");
            } finally {
                setLoading(false);
            }
        }else{
            try{
                console.log("Payload za kreiranje transakcije:", payload);
                const res = await api.post('/transakcije', payload);
                console.log("Kreiranje transakcije response:", res.data);
                console.log("Kreirana transakcija data:", res.data.data);
                const newTransakcija = res.data;
                setTransakcije([...transakcije, newTransakcija]);
                setInfo("Transakcija uspešno kreirana");
                await fetchTransakcije();
            }catch(error){
                console.log(error.response.data);
                console.log(error.response.data.errors);
                console.error("Greska pri kreiranju transakcije:", error);
                setError("Greska pri kreiranju transakcije");
            }finally{
                setLoading(false);
            }
        }

        setIdKategorija('');
        setDatumVreme(new Date());
        setTipTransakcije('');
        setIznos(0);
        setValuta('');
        setOpis('');
    }


    const handleDelete = async (id) => {
        if (!window.confirm("Da li ste sigurni da želite da obrišete ovu transakciju?")) {
            return;
        }
        try{
            await api.delete(`/transakcije/${id}`);
            setInfo("Transakcija uspešno obrisana");
            await fetchTransakcije();
        }catch(error){
            console.error("Greska pri brisanju transakcije:", error);
            setError("Greska pri brisanju transakcije");
        }
    }

    const handleEdit = (transakcija) => {
        setIdKategorija(transakcija.idKategorija);
        setKategorija(transakcija.kategorija);
        setDatumVreme(new Date(transakcija.datum_vreme));
        setTipTransakcije(transakcija.tip_transakcije);
        setIznos(transakcija.iznos);
        setValuta(transakcija.valuta);
        setOpis(transakcija.opis);
        setEditId(transakcija.idTransakcija);
    }

    const handlePromenaValute = async(transakcija) => {
        console.log("Promena valute za transakciju:", transakcija);
        await api.put(`/transakcije/${transakcija.idTransakcija}/valuta`, {
            valuta: transakcija.valuta,
            iznos: transakcija.iznos
        });
    }




  return (
    <div className="page">
        <div className='hero'>
            <div className='summary-card'>
                <h2>{editId ? "Izmena transakcije" : "Kreiranje transakcije"}</h2>
                <form onSubmit={handleSubmit} >
                    
                    <SelectInput
                        label="Kategorija:" 
                        value={kategorija}
                        onChange={(e) => {
                            const value = e.target.value;
                            if(value==="NEW"){
                                navigate("/kategorija");
                            }else{
                                setKategorija(value);
                                const selected = kategorije.find(k => k.naziv === value);
                                if(selected){
                                    setIdKategorija(selected.idKategorija);
                                }
                            }
                        }}
                        options={[...kategorije.map(k => ({
                             value: k.naziv,
                             label: k.naziv })),
                            { value: 'NEW', label: 'Dodaj novu kategoriju' }
                            ]}
                        placeholder="Izaberite kategoriju"
                    />
                    
                    
                    <SelectInput
                        label="Tip transakcije:"
                        value={tipTransakcije}
                        onChange={(e) => setTipTransakcije(e.target.value)}
                        options={[
                            { value: 'PRIHOD', label: 'Prihod' },
                            { value: 'RASHOD', label: 'Rashod' }
                        ]}
                        placeholder="Izaberite tip transakcije"
                    />

                    <SelectInput
                        label="Valuta:"
                        value={valuta}
                        onChange={(e) => setValuta(e.target.value)}
                        options={[
                            ...valute.map(v => ({ value: v, label: v }))
                        ]}
                        placeholder="Izaberite valutu"
                    />

                    <TextInput
                        label="Iznos"
                        value={iznos}
                        onChange={(e) => setIznos(e.target.value)}
                        placeholder="Unesite iznos"
                    />
                    
                    <DateInput
                        label="Datum i vreme"
                        value={datumVreme}
                        onChange={(date) => setDatumVreme(date)}
                        placeholder="Izaberite datum i vreme"
                    />

                    <TextInput
                        label="Opis"
                        value={opis}
                        onChange={(e) => setOpis(e.target.value)}
                        placeholder="Unesite opis"
                    />


                    {info && <p className="info">{info}</p>}
                    {error && <p className="error">{error}</p>}
                    {loading && <p>Učitavanje...</p>}

                    <PrimaryButton text={editId ? "Sačuvaj izmene" : "Kreiraj transakciju"} />    

                </form>
                {user.uloga === "korisnik" ? <p style={{ marginTop: "20px", fontStyle: "italic" }}>Napomena: Pretplatite se na premium paket da biste mogli da izvezete izveštaj u PDF.</p> 
                : <p style={{ marginTop: "20px", fontStyle: "italic" }}>Napomena: Kao PREMIUM korisnik, možete da izvezete izveštaj u PDF.</p>}
                <button className="btn small primary" text="Izvezi u PDF" onClick={() => exportToPDF(transakcije)} disabled={user.uloga === "korisnik"}>Izvezi u PDF</button>
            </div>

            {/* //LISTA KATEGORIJA - KLIKOM NA KATEGORIJU SE PRIKAZUJU TRANSAKCIJE TE KATEGORIJE */}
            {loading?(<p>Učitavanje...</p>):(
                <div className="features-grid">
                    {activeKategorija === null ? (
                    <>
                        <div 
                            className="feature-card clickable" 
                            onClick={() =>{
                                setActiveKategorija(0);
                                fetchTransakcije();
                            } } 
                            >
                            <h3>Sve transakcije</h3>
                            <p>Prikaži sve transakcije korisnika</p>
                        </div>

                        {
                        kategorije.map(kategorija=>(
                            <div key={kategorija.idKategorija} 
                                className="feature-card clickable" 
                                onClick={() => 
                                    handleKategorijaClick(kategorija.idKategorija)
                                }
                            >
                            <h3>{kategorija.naziv}</h3>
                            <p>{kategorija.opis}</p>
                            </div>
                        ))
                        }
                            
                    </>
                    ) : (
                        
                        <>

                        <div className="hero-actions">
                            <button
                            className="back-button"
                            onClick={() => setActiveKategorija(null)}
                            >
                            ← Nazad na kategorije
                            </button>
                        </div>


                        {transakcije.length === 0 ? (   
                        <p>Nema transakcija za prikaz.</p>
                        ) : (
                        
                        transakcije.map(transakcija => (
                        <div key={transakcija.idTransakcija} className="feature-card">
                            <h3>{transakcija.tipTransakcije} - {transakcija.iznos} {transakcija.valuta}</h3>
                            <p>Kategorija: {transakcija.kategorija?.naziv}</p>
                            <p>Datum: {new Date(transakcija.datum_vreme).toLocaleString()}</p>
                            <p>Opis: {transakcija.opis}</p>

                            <div>
                                <SelectInput
                                    label="Promeni valutu:"
                                    value={valuta}
                                    onChange={(e) => {
                                        const novaValuta = e.target.value;
                                        const convertedIznos = convertIznos(transakcija.iznos, transakcija.valuta, e.target.value);
                                        setTransakcije(prev =>
                                            prev.map(t =>
                                                t.idTransakcija === transakcija.idTransakcija
                                                ? { ...t, iznos: convertedIznos.toFixed(2), valuta: novaValuta }
                                                : t
                                            )
                                        );


                                        handlePromenaValute({ ...transakcija, iznos: convertedIznos.toFixed(2), valuta: novaValuta });
                                    }}
                                    options={[
                                        ...valute.map(v => ({ value: v, label: v }))
                                    ]}
                                    placeholder="Izaberite valutu"
                                />
                            </div>
                            <div className="hero-actions">
                                <FiEdit className="edit-icon" onClick={() => handleEdit(transakcija)} />
                                <FiTrash2 className="delete-icon" onClick={() => handleDelete(transakcija.idTransakcija)} />
                            </div>
                        </div>
                        ))
                        

                        )}
                        </>
                    )}
                </div>
            )}
        </div>
    </div>
  )
}

export default Transakcija
