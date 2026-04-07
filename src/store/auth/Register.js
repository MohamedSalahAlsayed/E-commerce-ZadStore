import { defineStore } from "pinia";
import { ref } from "vue";
import api from "../../axios";

export const useRegisterStore = defineStore("registerStore", () => {
  const token = ref(localStorage.getItem("auth_token") || null);
  const user = ref(JSON.parse(localStorage.getItem("user")) || null);
  const loading = ref(false);
  const error = ref(null);

  const registerUser = async (userData) => {
    loading.value = true;
    error.value = null;

    try {
      const payload = {
        name: userData.name,
        email: userData.email,
        phone: userData.phone,
        password: userData.password,
        password_confirmation: userData.password_confirm,
      };

      const response = await api.post("/register", payload);

      // Success
      token.value = response.data.access_token;
      user.value = response.data.user;

      localStorage.setItem("auth_token", token.value);
      localStorage.setItem("user", JSON.stringify(user.value));

      return { success: true };
    } catch (err) {
      error.value = err.response?.data?.message || err.message;
      return { success: false, message: error.value };
    } finally {
      loading.value = false;
    }
  };

  return {
    token,
    user,
    loading,
    error,
    registerUser,
  };
});
