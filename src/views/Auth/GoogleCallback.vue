<template>
  <v-app>
    <v-main class="d-flex align-center justify-center">
      <v-container text-center>
        <v-progress-circular
          indeterminate
          color="primary"
          size="64"
        ></v-progress-circular>
        <div class="mt-4 text-h6">{{ $t("auth.login_subtitle") }}...</div>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/axios";
import { useAuthStore } from "@/store/auth/LogIn";
import { AddInCart } from "@/store/Cart";
import { AddFavProduct } from "@/store/Favourate";

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = AddInCart();
const favStore = AddFavProduct();

onMounted(async () => {
  const token = route.query.token;

  if (token) {
    localStorage.setItem("auth_token", token);

    try {
      // Fetch user data after successful social login
      const response = await api.get("/user");
      const user = response.data.user;

      localStorage.setItem("user", JSON.stringify(user));
      authStore.currentUser = user;
      authStore.isAuthenticated = true;

      // Sync cart and favorites
      await Promise.all([cartStore.fetchCart(), favStore.fetchFavorites()]);

      router.push("/home");
    } catch (error) {
      console.error("Failed to fetch user after Google login", error);
      router.push("/Auth/LogIn?error=auth_failed");
    }
  } else {
    router.push("/Auth/LogIn?error=no_token");
  }
});
</script>
