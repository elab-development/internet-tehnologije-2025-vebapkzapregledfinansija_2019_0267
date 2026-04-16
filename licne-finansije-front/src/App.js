import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Pocetna from './pages/Pocetna';
import LoginPage from './pages/Login/LoginPage';
import Budzet from './pages/Budzet';
import Proba from './pages/Proba';
import FinansijskiCilj from './pages/FinansijskiCilj';
import Podsetnik from './pages/Podsetnik';
import Kategorija from './pages/Kategorija';
import Transakcija from './pages/Transakcija';
import Navbar from './components/Navbar/Navbar';
import Footer from './components/Footer/Footer';
import RegistrationPage from './pages/RegistrationPage';
import MojProfil from './pages/MojProfil';
import AdminDashboard from './pages/AdminDashboard';
import AdminStats from './pages/AdminStats';


function App() {
  return (
    <div>
     <BrowserRouter>
      <Navbar></Navbar>
      <Routes>
        <Route path="/" element={<Pocetna />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegistrationPage />} />
        <Route path="/proba" element={<Proba />} />
        <Route path="/budzet" element={<Budzet />} />
        <Route path="/finansijski-cilj" element={<FinansijskiCilj />} />
        <Route path="/podsetnik" element={<Podsetnik />} />
        <Route path="/kategorija" element={<Kategorija />} />
        <Route path="/transakcija" element={<Transakcija />} />
        <Route path="/moj-profil" element={<MojProfil />} />
        <Route path="/admin-dashboard" element={<AdminDashboard />} />
        <Route path="/admin-stats" element={<AdminStats />} />
      </Routes>
      <Footer></Footer>
     </BrowserRouter>
    </div>
  );
}

export default App;
