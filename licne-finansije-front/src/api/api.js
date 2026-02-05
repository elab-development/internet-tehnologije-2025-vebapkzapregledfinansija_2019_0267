import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8081/api', // MENJA SE U ZAVISNOSTI OD BACKEND URL-A NA VASEM KOMPJUTERU
  //MOZE SE DESITI DA SE LOCALHOST NE POKRENE, U TOM SLUCAJU TREBA ZAMENITI SA IP ADRESOM KOMPJUTERA NA KOJEM SE BACKEND POKRECE

  //   headers: {
//     'Content-Type': 'application/json',
//   },
});
export default api;