import React, { use} from 'react'
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/api';
import '../pages/Pocetna.css';
import SelectInput from './SelectInput';
import { set } from 'date-fns';




const SummaryCard = () => {

    const user=JSON.parse(localStorage.getItem("user"));

    const [stanje, setStanje] = useState(0);
    const [prihodi, setPrihodi] = useState(0);
    const [rashodi, setRashodi] = useState(0);
    const [valuta, setValuta] = useState('RSD');
    const [rates, setRates] = useState({});
    const [valute, setValute] = useState([]);

    const [transakcije, setTransakcije] = useState([]);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [info, setInfo] = useState(null);

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
        fetchTransakcije();
    }, [user.id]);


    const calculateSummary = () => {
        let ukupnoPrihodi = 0;
        let ukupnoRashodi = 0;
        let ukupnoStanje = 0;

        if(!rates || Object.keys(rates).length === 0){
            setError("Nije moguće prikazati podatke jer nisu učitani kursevi valuta.");
            return;
        }

        transakcije.forEach(transakcija => {
            const convertedIznos = convertIznos(transakcija.iznos, transakcija.valuta, valuta);
            if(transakcija.tipTransakcije ==='PRIHOD'){
                ukupnoPrihodi += Number(convertedIznos);
            }
            else if(transakcija.tipTransakcije ==='RASHOD'){
                ukupnoRashodi += Number(convertedIznos);
            }
        })

        ukupnoStanje = ukupnoPrihodi - ukupnoRashodi;

        setPrihodi(ukupnoPrihodi);
        setRashodi(ukupnoRashodi);
        setStanje(ukupnoStanje);

        setInfo(`Prikazani su svi podaci konvertovani u ${valuta}`);
    };

    useEffect(() => {
            if (transakcije.length > 0 && valuta) {
                calculateSummary();
            }
        }, [transakcije, valuta]
    );


    //Funkcija za konverziju iznosa iz jedne valute u drugu
    const convertIznos=(iznos, fromCurrency, toCurrency) => {
        if(fromCurrency === toCurrency){
            return iznos;
        }

        const rateFrom = rates[fromCurrency];
        const rateTo = rates[toCurrency];

        if(!rateFrom || !rateTo){
            console.warn(`Nedostaje kurs za valute: from ${fromCurrency} (${rateFrom}), to ${toCurrency} (${rateTo})`);
            return 0; //ako ne možemo da konvertujemo, vratimo originalni iznos
        }

        const rate = rateTo / rateFrom;
        return iznos * rate;
    };

  return (
    <div className="summary-card">
        
        <h2>Ukupno stanje</h2>
        <p className="balance">{stanje.toFixed(2)} {valuta}</p>


        <div className="summary-grid">
            <div className="income">
                <span>Prilivi </span>
                <strong>{Number(prihodi).toFixed(2)} {valuta}</strong>
            </div>
            <div className="expense">
                <span>Odlivi </span>
                <strong>{Number(rashodi).toFixed(2)} {valuta}</strong>
            </div>
        </div>

        <SelectInput
            label="Promeni valutu: "
            value={valuta}
            onChange={(e) => {
                const novaValuta = e.target.value;
                setValuta(novaValuta);
                
            }}
            options={[
                ...valute.map(v => ({ value: v, label: v }))
            ]}
            placeholder="Izaberite valutu"
        />
        {info && <p className="info">{info}</p>}
        {error && <p className="error">{error}</p>}
        {loading && <p>Učitavanje...</p>}
    </div>
  )
}

export default SummaryCard