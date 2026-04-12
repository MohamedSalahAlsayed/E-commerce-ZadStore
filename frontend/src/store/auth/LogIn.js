import { defineStore } from "pinia";
import { ref } from "vue";
import api from "../../axios";

export const useAuthStore = defineStore("auth", () => {
  const currentUser = ref(JSON.parse(localStorage.getItem("user")) || null);
  const isAuthenticated = ref(!!localStorage.getItem("auth_token"));
  const loading = ref(false);

  const login = async ({ email, password, rememberMe }) => {
    loading.value = true;
    try {
      const response = await api.post("/login", { email, password });
      const { access_token, user } = response.data;

      // Store token and user
      localStorage.setItem("auth_token", access_token);
      localStorage.setItem("user", JSON.stringify(user));

      if (rememberMe) {
        localStorage.setItem("rememberMe", "true");
      } else {
        localStorage.removeItem("rememberMe");
      }

      currentUser.value = user;
      isAuthenticated.value = true;

      return { success: true };
    } catch (error) {
      return {
        success: false,
        message:
          error.response?.data?.message ||
          "البريد الإلكتروني أو كلمة المرور غير صحيحة",
      };
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      if (isAuthenticated.value) {
        await api.post("/logout");
      }
    } catch (err) {
      console.error(err);
    } finally {
      currentUser.value = null;
      isAuthenticated.value = false;
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      localStorage.removeItem("rememberMe");
    }
  };

  /** Sync user from API (roles, avatar URL, counts) — call after token changes */
  const fetchCurrentUser = async () => {
    const { data } = await api.get("/user");
    if (data.user) {
      currentUser.value = data.user;
      localStorage.setItem("user", JSON.stringify(data.user));
      isAuthenticated.value = true;
    }
    return data.user;
  };

  const can = (permission) => {
    const user = currentUser.value;
    if (!user) return false;

    // Super Admin: if role is admin and no granular permissions are defined
    if (
      user.role === "admin" &&
      (!user.permissions || user.permissions.length === 0)
    ) {
      return true;
    }

    // Check granular permissions
    return user.permissions?.includes(permission) || false;
  };

  return {
    currentUser,
    isAuthenticated,
    loading,
    login,
    logout,
    fetchCurrentUser,
    can,
  };
});
