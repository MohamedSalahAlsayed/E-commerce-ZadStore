import { defineStore } from "pinia";
import api from "../axios";
import router from "@/router";

export const AddInCart = defineStore("AddInCart", {
  state: () => ({
    CartItem: [],
  }),

  actions: {
    async fetchCart() {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        this.CartItem = [];
        return;
      }
      try {
        const res = await api.get("/user/cart");
        this.CartItem = res.data.map((item) => ({
          ...item.product,
          discountPercentage: Number(
            item.product.discount_percentage ||
              item.product.discountPercentage ||
              0
          ),
          quantity: item.quantity,
          cart_id: item.id,
        }));
      } catch (err) {
        console.error("fetchCart Error", err);
      }
    },
    async AddInCartItem(product) {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        router.push({ path: "/Auth/LogIn", query: { redirect: "cart" } });
        return;
      }

      // Optimistic update for instant UI feedback
      const prevCart = [...this.CartItem];
      const existing = this.CartItem.find((p) => p.id === product.id);
      if (existing) {
        existing.quantity += product.quantity || 1;
      } else {
        this.CartItem.push({ ...product, quantity: product.quantity || 1 });
      }

      try {
        await api.post("/user/cart", {
          product_id: product.id,
          quantity: product.quantity || 1,
        });
        this.fetchCart(); // Sync quietly
      } catch (err) {
        console.error(err);
        this.CartItem = prevCart; // Rollback
      }
    },
    async removeFromCart(product_id) {
      const token = localStorage.getItem("auth_token");
      if (!token) {
        router.push({ path: "/Auth/LogIn", query: { redirect: "cart" } });
        return;
      }

      // Optimistic delete
      const prevCart = [...this.CartItem];
      this.CartItem = this.CartItem.filter((p) => p.id !== product_id);

      try {
        await api.delete(`/user/cart/${product_id}`);
        this.fetchCart(); // Sync quietly
      } catch (err) {
        console.error(err);
        this.CartItem = prevCart; // Rollback
      }
    },
    async clearCart() {
      const token = localStorage.getItem("auth_token");
      if (!token) return;
      try {
        await api.delete(`/user/cart`);
        this.CartItem = [];
      } catch (err) {
        console.error(err);
      }
    },
  },
});
