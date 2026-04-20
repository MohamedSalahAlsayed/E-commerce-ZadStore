import { defineStore } from "pinia";
import api from "../axios";

// Helper function to map Laravel Product model to DummyJSON format
const mapProduct = (p) => ({
  ...p,
  // CamelCase for UI bindings
  discountPercentage: p.discount_percentage,
  // String values for category and brand from relations
  category: p.category ? p.category.name : "",
  brand: p.brand ? p.brand.name : "",
  // Mapping relation images to array of strings
  images:
    p.images && p.images.length
      ? p.images.map((img) => img.image_path)
      : p.thumbnail
      ? [p.thumbnail]
      : [],
});

export const ProductModule = defineStore("ProductModule", {
  state: () => ({
    flashDeals: [],
    bestSellers: [],
    brand: [],
    productDetails: "",
    categories: [],
    products: [],
  }),
  actions: {
    async getProducts() {
      if (this.products.length) return;
      try {
        const res = await api.get("/products");
        const rawProducts = res.data.data || res.data;
        this.products = rawProducts.map(mapProduct);
      } catch (err) {
        console.error("getProducts ERROR:", err);
      }
    },
    async getBestSellers() {
      if (this.bestSellers.length) return;
      try {
        const res = await api.get("/products/best-sellers");
        this.bestSellers = res.data.map(mapProduct);
      } catch (err) {
        console.error("getBestSellers ERROR:", err);
      }
    },
    async getFlashDeals() {
      if (this.flashDeals.length) return;
      try {
        const res = await api.get("/products/flash-deals");
        this.flashDeals = res.data.map(mapProduct);
      } catch (err) {
        console.error("getFlashDeals ERROR:", err);
      }
    },
    async getBrands() {
      if (this.brand.length) return;
      try {
        const res = await api.get("/brands");
        // Convert to strings if components expect array of strings, otherwise leave as objects
        this.brand = res.data;
      } catch (err) {
        console.error(err);
      }
    },
    async getSingleProducts(productId) {
      try {
        const res = await api.get(`/products/${productId}`);
        this.productDetails = mapProduct(res.data);
      } catch (err) {
        console.error(err);
      }
    },
    async getCategories() {
      if (this.categories.length) return;
      try {
        const res = await api.get("/categories");
        // If categories in dummyjson was an array of strings like ["smartphones", "laptops"]
        // we map it. If it was objects, we leave it. We'll map to strings to be safe for old logic, or just objects.
        // Assuming original logic wanted objects or strings. We'll return objects but keep 'name'.
        this.categories = res.data?.data || res.data || [];
      } catch (err) {
        console.error(err);
      }
    },
  },
});
