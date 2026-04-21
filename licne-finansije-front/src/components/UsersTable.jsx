import React from 'react'
import { useEffect, useState } from 'react'
import api from '../api/api'
import SelectInput from './SelectInput';
import '../pages/Pocetna.css';




const UsersTable = ({users, page, totalPages, search, searchInput, showDeleted, uloga, onSearchChange, onSearchInputChange, onShowDeletedChange, onUlogaChange, onPageChange, onSoftDelete, onRestore, onPermanentDelete, onPromote, onEdit}) => {

const [selectedPage, setSelectedPage] = useState(page);

useEffect(() => {
    setSelectedPage(page);
}, [page]);
    

  return (
    <div className="summary-card">
      {/* Searchbar i checkbox */}
      <div className="filters-bar">
        <div className="search-group">
          <input
            type="text"
            placeholder="Pretraga korisnika..."
            value={searchInput}
            onChange={(e) => onSearchInputChange(e.target.value)}
            className="filter-input"
          />
          <button className="btn primary search-btn" onClick={() => onSearchChange(searchInput)}>Pretraži</button>
        </div>

        <SelectInput
          label="Uloga"
          value={uloga}
          onChange={(e) => onUlogaChange(e.target.value)}
          options={[
            { value: '', label: 'Sve' },
            { value: 'korisnik', label: 'Korisnik' },
            { value: 'premium', label: 'Premium korisnik' },
            { value: 'admin', label: 'Administrator' }
          ]}
        />

        <label className="filter-checkbox">
          <input
            type="checkbox"
            checked={showDeleted}
            onChange={(e) => onShowDeletedChange(e.target.checked)}
          />
          Prikaži obrisane
        </label>
      </div>



      

      {/* Tabela */}
      <table className="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Ime</th>
            <th>Prezime</th>
            <th>Email</th>
            <th>Status</th>
            <th>Uloga</th>
            <th>Nivo</th>
            <th>Poeni</th>
            <th>Akcije</th>
          </tr>
        </thead>
        <tbody>
          {users.map((u) => (
            <tr key={u.id}>
              <td>{u.id}</td>
              <td>{u.ime}</td>
              <td>{u.prezime}</td>
              <td>{u.email}</td>
              <td>
                <span className={`status ${u.deleted ? "deleted" : "active"}`}>
                  {u.deleted ? "Obrisan" : "Aktivan"}
                </span>
              </td>
              <td>{u.uloga}</td>
              <td>{u.nivo}</td>
              <td>{u.poeni}</td>
              <td className="actions">
                {!u.deleted ? (
                  <>
                    <button className="btn primary small" onClick={() => onSoftDelete(u.id)}>Obriši</button>
                    <button className="btn primary small" onClick={() => onEdit(u.id)}>Izmeni</button>
                    <button className="btn primary small" onClick={() => onPromote(u.id, u.uloga)} disabled={u.uloga === "admin"}>
                      Promoviši
                    </button>
                  </>
                ) : (
                  <>
                    <button className="btn primary small" onClick={() => onRestore(u.id)}>Vrati</button>
                    <button className="btn primary small" onClick={() => onEdit(u.id)}>Izmeni</button>
                    <button className="btn small" style={{ backgroundColor: "#dc2626", color: "white" }} onClick={() => onPermanentDelete(u.id)}>Trajno</button>
                  </>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Paginacija */}
      <div className="pagination">
        <button
          className="btn primary"
          disabled={page === 1}
          onClick={() => onPageChange(page - 1)}
        >
          ←
        </button>

        <button
          className="btn small primary"
          onClick={() => setSelectedPage((p) => Math.max(1, p - 1))}
          disabled={selectedPage === 1}
        >
          -
        </button>

        <button
          className="btn small primary"
          onClick={() => onPageChange(selectedPage)}
        >
          {selectedPage}
        </button>

        <button
          className="btn small primary"
          onClick={() => setSelectedPage((p) => Math.min(totalPages, p + 1))}
          disabled={selectedPage === totalPages}
        >
          +
        </button>

        <button
          className="btn primary"
          disabled={page === totalPages}
          onClick={() => onPageChange(page + 1)}
        >
          →
        </button>
      </div>
    </div>
  );
}

export default UsersTable