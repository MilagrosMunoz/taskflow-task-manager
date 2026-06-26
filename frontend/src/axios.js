import axios from 'axios'

const axiosClient = axios.create({
    // Aquí va la URL de tu backend local en Laragon
    baseURL: 'http://127.0.0.1:8000/api', 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
})

// Este interceptor adjuntará automáticamente el token JWT/Sanctum a tus peticiones si el usuario está logueado
axiosClient.interceptors.request.use(config => {
    const token = localStorage.getItem('TOKEN')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

export default axiosClient