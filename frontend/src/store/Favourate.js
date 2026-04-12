import { defineStore } from "pinia";
import api from "../axios";
import router from "@/router";

export const AddFavProduct = defineStore("AddFavProduct", {
  state: () => ({
    FavItem: [],
  }),

  actions: {
    async fetchFavorites() {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        this.FavItem = [];
        return;
      }
      try {
        const res = await api.get("/user/favorites");
        this.FavItem = res.data.map((item) => ({
          ...item.product,
          fav_id: item.id,
        }));
      } catch (err) {
        console.error("fetchFavorites Error", err);
      }
    },
    async AddFavProductItem(product) {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        router.push({ path: "/Auth/LogIn", query: { redirect: "favorites" } });
        return;
      }

      // Optimistic Add
      const fallback = [...this.FavItem];
      if (!this.FavItem.find((p) => p.id === product.id)) {
        this.FavItem.push({ ...product });
      }

      try {
        await api.post("/user/favorites", { product_id: product.id });
        this.fetchFavorites();
      } catch (err) {
        console.error(err);
        this.FavItem = fallback;
      }
    },
    async removeFromCart(product_id) {
      // Named removeFromCart due to legacy dependencies matching CardProduct.vue
      const token = localStorage.getItem("auth_token");
      if (!token) {
        router.push({ path: "/Auth/LogIn", query: { redirect: "favorites" } });
        return;
      }

      // Optimistic Delete
      const fallback = [...this.FavItem];
      this.FavItem = this.FavItem.filter((p) => p.id !== product_id);

      try {
        await api.post("/user/favorites", { product_id: product_id });
        this.fetchFavorites();
      } catch (err) {
        console.error(err);
        this.FavItem = fallback;
      }
    },
  },
});
