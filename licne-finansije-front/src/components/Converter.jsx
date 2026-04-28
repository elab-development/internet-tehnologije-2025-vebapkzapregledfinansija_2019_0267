import React, { use } from 'react'
import { useState, useEffect } from 'react';
import api from '../api/api';
import '../pages/Pocetna.css';
import SelectInput from './SelectInput';
import { set } from 'date-fns';
import TextInput from './TextInput';




const Converter = () => {
    const [iznos, setIznos] = useState(0);
    const [valutaIz, setValutaIz] = useState('RSD');
    const [valutaU, setValutaU] = useState('EUR');
    const [rates, setRates] = useState({});
    const [valute, setValute] = useState([]);
    const [convertedIznos, setConvertedIznos] = useState(null);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [info, setInfo] = useState(null);



    useEffect(() => {
        const fetchValute = async () => {
            const res=await fetch("https://v6.exchangerate-api.com/v6/a885afe0ecbda562f260cbde/latest/USD");
            const data = await res.json();
            console.log("Valute response data:", data);
            console.log("Valute:", Object.keys(data.conversion_rates));
            setValute(Object.keys(data.conversion_rates));
            setRates(data.conversion_rates);
            setLoading(false);
        };
        fetchValute();
    }, []);

    const convertIznos = (iznos, fromCurrency, toCurrency) => {
        if(fromCurrency === toCurrency) return iznos;

        const rateFrom= rates[fromCurrency];
        const rateTo = rates[toCurrency];

        if (!rateFrom || !rateTo) {
            console.error("Nedostaje kurs za valutu:", !rateFrom ? fromCurrency : toCurrency);
            return null;
        }

        const rate = rateTo / rateFrom;
        return iznos * rate;
    }

    const handleConvert = () => {
        const converted = convertIznos(iznos, valutaIz, valutaU);
        setConvertedIznos(converted);
    }


  return (
    <div className="summary-card">
        
        <h2>Konvertovan iznos</h2>
        <p className="balance">{convertedIznos !== null ? convertedIznos.toFixed(2) : '0.00'} {valutaU}</p>


        <div className="summary-grid">
            <div className="income">
                <span>Valuta (Iz) </span>
                <SelectInput
                    label="Iz valute: "
                    value={valutaIz}
                    onChange={(e) => setValutaIz(e.target.value)}
                    options={[
                        ...valute.map(v => ({ value: v, label: v }))
                    ]}
                    placeholder="Izaberite valutu"
                />
            </div>
            <div className="expense">
                <span>Valuta (U) </span>
                <SelectInput
                    label="U valutu: "
                    value={valutaU}
                    onChange={(e) => setValutaU(e.target.value)}
                    options={[
                        ...valute.map(v => ({ value: v, label: v }))
                    ]}
                    placeholder="Izaberite valutu"
                />
            </div>
        </div>

        <TextInput
            label="Iznos: "
            value={iznos}
            onChange={(e) => setIznos(parseFloat(e.target.value) || 0)}
            type="number"
            placeholder="Unesite iznos"
        />
        <button className="btn primary" onClick={handleConvert}>
            Konvertuj
        </button>
        {info && <p className="info">{info}</p>}
        {error && <p className="error">{error}</p>}
        {loading && <p>Učitavanje...</p>}
    </div>
  )
}

export default Converter