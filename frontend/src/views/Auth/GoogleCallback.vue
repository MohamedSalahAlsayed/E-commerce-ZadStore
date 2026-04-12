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
  const code = route.query.code;
  const legacyToken = route.query.token;

  try {
    if (code) {
      const { data } = await api.post("/auth/oauth-exchange", { code });
      localStorage.setItem("auth_token", data.access_token);
      localStorage.setItem("user", JSON.stringify(data.user));
      authStore.currentUser = data.user;
      authStore.isAuthenticated = true;

      await Promise.all([cartStore.fetchCart(), favStore.fetchFavorites()]);
      router.replace("/home");
      return;
    }

    if (legacyToken) {
      localStorage.setItem("auth_token", legacyToken);
      await authStore.fetchCurrentUser();
      await Promise.all([cartStore.fetchCart(), favStore.fetchFavorites()]);
      router.replace("/home");
      return;
    }

    router.replace("/Auth/LogIn?error=no_token");
  } catch (error) {
    console.error("Google callback failed", error);
    router.replace("/Auth/LogIn?error=auth_failed");
  }
});
</script>
