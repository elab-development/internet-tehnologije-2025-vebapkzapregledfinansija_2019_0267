import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Pocetna from './pages/Pocetna';
import LoginPage from './pages/Login/LoginPage';
import Proba from './pages/Proba';
import Navbar from './components/Navbar/Navbar';
import Footer from './components/Footer/Footer';
import RegistrationPage from './pages/RegistrationPage';


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
        
      </Routes>
      <Footer></Footer>
     </BrowserRouter>
    </div>
  );
}

export default App;
