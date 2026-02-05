import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api', // MENJA SE U ZAVISNOSTI OD BACKEND URL-A NA VASEM KOMPJUTERU
  //MOZE SE DESITI DA SE LOCALHOST NE POKRENE, U TOM SLUCAJU TREBA ZAMENITI SA IP ADRESOM KOMPJUTERA NA KOJEM SE BACKEND POKRECE

});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token') || sessionStorage;
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;

});
export default api;