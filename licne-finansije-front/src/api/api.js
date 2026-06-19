import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'https://internet-tehnologije-2025-vebapkzapregledfinansi-production.up.railway.app/api', // MENJA SE U ZAVISNOSTI OD BACKEND URL-A NA VASEM KOMPJUTERU
    //MOZE SE DESITI DA SE LOCALHOST NE POKRENE, U TOM SLUCAJU TREBA ZAMENITI SA IP ADRESOM KOMPJUTERA NA KOJEM SE BACKEND POKRECE
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;

});
export default api;