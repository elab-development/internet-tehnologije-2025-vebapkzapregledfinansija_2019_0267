import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Pocetna from './pages/Pocetna';
import LoginPage from './pages/LoginPage';
import Navbar from './components/Navbar/Navbar';
import Footer from './components/Footer/Footer';


function App() {
  return (
    <div>
     <BrowserRouter>
      <Navbar></Navbar>
      <Routes>
        <Route path="/" element={<Pocetna />} />
        <Route path="/login" element={<LoginPage />} />
        
      </Routes>
      <Footer></Footer>
     </BrowserRouter>
    </div>
  );
}

export default App;
