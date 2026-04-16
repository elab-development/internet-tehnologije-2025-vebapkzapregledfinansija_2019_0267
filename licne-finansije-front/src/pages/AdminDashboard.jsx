import React from 'react'
import api from '../api/api'
import './Pocetna.css';
import { useState, useEffect } from 'react';

const AdminDashboard = () => {

    const [info, setInfo] = useState(null);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

    const [korisnici, setKorisnici] = useState([]);

    const fetchKorisnici = async () => {
            try {
                setLoading(true);
                const res = await api.get('/admin/users');
                console.log("Korisnici response data:", res.data);
                setKorisnici(res.data.data);
            } catch (error) {
                console.error("Greska pri ucitavanju korisnika:", error);
                console.error('Message:', error.response.data.message);
                setError("Greska pri ucitavanju korisnika");
            } finally {
                setLoading(false);
            }
        };

    useEffect(() => {
        fetchKorisnici();
    }, []);

    const handleSoftDelete = async (userId) => {
        try {
            setLoading(true);
            await api.delete(`/admin/users/${userId}`);
            setInfo("Korisnik je uspešno MEKO obrisan");
            fetchKorisnici(); // Osvežavanje liste korisnika nakon brisanja
        }
        catch (error) {
            console.error("Greska pri brisanju korisnika:", error);
            setError("Greska pri brisanju korisnika");
        } finally {
            setLoading(false);
        }
    };

    const handleRestore = async (userId) => {
        try {
            setLoading(true);
            await api.post(`/admin/users/${userId}/restore`);
            setInfo("Korisnik je uspešno RESTOROVAN");
            fetchKorisnici(); // Osvežavanje liste korisnika nakon restauracije
        }
        catch (error) {
            console.error("Greska pri restauraciji korisnika:", error);
            setError("Greska pri restauraciji korisnika");
        } finally {
            setLoading(false);
        }
    };

    const handleForceDelete = async (userId) => {
        try {
            setLoading(true);
            await api.delete(`/admin/users/${userId}/force`);
            setInfo("Korisnik je uspešno TRAJNO obrisan");
            fetchKorisnici(); // Osvežavanje liste korisnika nakon brisanja
        }        catch (error) {
            console.error("Greska pri trajnom brisanju korisnika:", error);
            setError("Greska pri trajnom brisanju korisnika");
        } finally {
            setLoading(false);
        }
    };

    const handleUpdate= async (userId, updatedData) => {
        try {
            setLoading(true);
            await api.put(`/admin/users/${userId}`, updatedData);
            setInfo("Korisnik je uspešno AŽURIRAN");
            fetchKorisnici(); // Osvežavanje liste korisnika nakon ažuriranja
        }
        catch (error) {
            console.error("Greska pri ažuriranju korisnika:", error);
            setError("Greska pri ažuriranju korisnika");
        }
        finally {
            setLoading(false);
        }
    };

    const handlePromote= async (userId) => {
        try {
            setLoading(true);
            await api.patch(`/admin/users/${userId}/role`);
            setInfo("Korisnik je uspešno PROMOVISAN");
            fetchKorisnici(); // Osvežavanje liste korisnika nakon promocije
        }
        catch (error) {
            console.error("Greska pri promociji korisnika:", error);
            setError("Greska pri promociji korisnika");
        } finally {
            setLoading(false);
        }
    };



  return (
    <div>
        <h1>Admin Dashboard</h1>
        {info && <p className="info">{info}</p>}
        {error && <p className="error">{error}</p>}
        {loading ? (
            <p>Ucitavanje...</p>
        ) : (
            <table>
                <thead>
                    <tr> 
                        <th>ID</th>
                        <th>Ime</th>
                        <th>Prezime</th>
                        <th>Email</th>
                        <th>Uloga</th>
                        <th>Nivo</th>
                        <th>Poeni</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    {korisnici.map(korisnik => (
                        <tr key={korisnik.id} className={korisnik.deleted_at ? 'deleted' : ''}>
                            <td>{korisnik.id}</td>
                            <td>{korisnik.ime}</td>
                            <td>{korisnik.prezime}</td>
                            <td>{korisnik.email}</td>
                            <td>{korisnik.uloga}</td>
                            <td>{korisnik.nivo}</td>
                            <td>{korisnik.poeni}</td>
                            <td>{korisnik.status}</td>
                            <td>
                                {!korisnik.deleted_at ? (
                                    <>
                                        <button onClick={() => handleSoftDelete(korisnik.id)}>Meko Obrisi</button>
                                        <button onClick={() => handlePromote(korisnik.id)}>Promoviši</button>
                                    </>
                                ) : (
                                    <button onClick={() => handleRestore(korisnik.id)}>Restauriraj</button>
                                )}
                                <button onClick={() => handleForceDelete(korisnik.id)}>Trajno Obriši</button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        )}

    </div>
  )
}

export default AdminDashboard