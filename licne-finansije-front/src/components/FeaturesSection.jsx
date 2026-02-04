import React from 'react'
import FeatureCard from './FeatureCard';

const FEATURES = [
    {
        id: "security",
        title: "Sigurnost korisnika",
        bullets: [
            "Bezbedno cuvanje podataka",
            "Enkripcija osetljivih informacija",
            "Licni nalozi i autorizacija",
        ],
    },
    {
        id: "goals",
        title: "Finansijski ciljevi",
        bullets: [
            "Postavljanje ciljeva",
            "Pracenje napretka",
            "Vizuelni prikaz rezultata",
        ],
    },
    {
        id: "categories",
        title: "Custom kategorije troskova",
        bullets: [
            "Kreiranje sopstvenih kategorija",
            "Bolja analiza potrosnje",
            "Fleksibilnost",
        ],
    },
    {
        id: "reports",
        title: "Izvestaji",
        bullets: [
            "Mesecni i godisnji izvestaji",
            "Graficki prikazi",
            "Uvid u navike",
        ],
    },
    {
        id: "transactions",
        title: "Unos i pracenje transakcija",
        bullets: [
            "Jednostavan unos",
            "Istorija transakcija",
            "Tacni podaci",
        ],
    },
];

const FeaturesSection = () => {
  return (
    <section className="features">
        <h2>Sta ti aplikacija nudi</h2>
        <div className="features-grid">
            {FEATURES.map((feature) => (
                <FeatureCard key={feature.id} feature={feature} />
            ))}
        </div>
    </section>
  )
}

export default FeaturesSection

