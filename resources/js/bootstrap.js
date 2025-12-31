import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// // Helper to set or clear the Authorization header for API clients.
// window.setAuthToken = function(token) {
// 	if (token) {
// 		window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
// 	} else {
// 		delete window.axios.defaults.headers.common['Authorization'];
// 	}
// }
