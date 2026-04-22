import React from 'react'
import api from '../api/api'
import './Pocetna.css';
import UsersTable from '../components/UsersTable';
import { useState, useEffect } from 'react';
import { set } from 'date-fns';

const AdminDashboard = () => {

    const [info, setInfo] = useState(null);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);

    const [korisnici, setKorisnici] = useState([]);

    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [selectedPage, setSelectedPage] = useState(currentPage);
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [showDeleted, setShowDeleted] = useState(false);
    const [ulogaFilter, setUlogaFilter] = useState('');


    const fetchKorisnici = async ({page=1, search='', showDeleted=false, ulogaFilter=''}={}) => {
            try {
                setLoading(true);
                const res = await api.get('/admin/users', 
                    {params: {
                        page,
                        search,
                        include_deleted: showDeleted,
                        uloga: ulogaFilter,
                    }});
                console.log("Korisnici response data:", res.data);

                const korisnici = res.data.data.map(user => ({
                    ...user,
                    deleted: !!user.deleted_at,
                }));
                
                setKorisnici(korisnici);
                setTotalPages(res.data.last_page);
                setCurrentPage(res.data.current_page);


            } catch (error) {
                console.error("Greska pri ucitavanju korisnika:", error);
                console.error('Message:', error.response.data.message);
                setError("Greska pri ucitavanju korisnika");
                return {data: [], totalPages: 1, currentPage: 1};
            } finally {
                setLoading(false);
            }
        };

    useEffect(() => {
        fetchKorisnici({ page: currentPage, search, showDeleted, uloga: ulogaFilter });
    }, [currentPage, search, showDeleted, ulogaFilter]);


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
            fetchKorisnici({page : currentPage, search, showDeleted, ulogaFilter}); // Osvežavanje liste korisnika nakon ažuriranja
        }
        catch (error) {
            console.error("Greska pri ažuriranju korisnika:", error);
            setError("Greska pri ažuriranju korisnika");
        }
        finally {
            setLoading(false);
        }
    };

    const handlePromote= async (userId, currentUloga) => {
        try {
            setLoading(true);
            const newUloga= currentUloga === "korisnik" ? "premium" : "korisnik";
            console.log(`Promovišem korisnika ${userId} sa ulogom ${currentUloga} na ${newUloga}`);
            await api.patch(`/admin/users/${userId}/role`, { uloga: newUloga });
            setInfo("Korisnik je uspešno PROMOVISAN");
            fetchKorisnici({ page: currentPage, search, showDeleted, uloga: ulogaFilter });
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
            <UsersTable
                users={korisnici}
                page={currentPage}                
                totalPages={totalPages}
                search={search}
                searchInput={searchInput}
                showDeleted={showDeleted}
                uloga={ulogaFilter}
                onSearchChange={setSearch}
                onSearchInputChange={setSearchInput}
                onShowDeletedChange={setShowDeleted}
                onUlogaChange={setUlogaFilter}
                onPageChange={setCurrentPage}
                onSoftDelete={handleSoftDelete}
                onRestore={handleRestore}
                onPermanentDelete={handleForceDelete}
                onPromote={handlePromote}
                onEdit={handleUpdate}
            />
        )}

    </div>
  )
}

export default AdminDashboard