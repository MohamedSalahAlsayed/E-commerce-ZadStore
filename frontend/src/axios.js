import axios from "axios";

const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL
    ? `${process.env.VUE_APP_API_URL}/api`
    : "http://127.0.0.1:8000/api",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// Interceptor to attach the token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Interceptor to handle unauthorized access across the app
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response && error.response.status === 401) {
      // User token expired or invalid
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");

      // Force redirect to login
      window.location.hash = "#/Auth/LogIn";
    }
    return Promise.reject(error);
  }
);

export default api;
