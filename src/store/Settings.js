import { defineStore } from "pinia";
import api from "../axios";

export const useSettingsStore = defineStore("settings", {
  state: () => ({
    storeName: "ZadStore+",
    storeSlogan: "",
    storeDescription: "",
    logoPreview: "",
    email: "",
    phone: "",
    whatsapp: "",
    address: "",
    facebook: "",
    instagram: "",
    twitter: "",
    tiktok: "",
    currency: "EGP",
    taxRate: 0,
    maintenanceMode: false,
    storeTheme: null,
    loading: false,
  }),
  actions: {
    async fetchSettings() {
      this.loading = true;
      try {
        const res = await api.get("/settings");
        const data = res.data || {};

        // Simple map for flat properties
        const props = [
          "storeName",
          "storeSlogan",
          "logoPreview",
          "email",
          "phone",
          "whatsapp",
          "address",
          "facebook",
          "instagram",
          "twitter",
          "tiktok",
          "currency",
          "storeTheme",
        ];

        props.forEach((p) => {
          if (data[p] !== undefined) this[p] = data[p];
        });

        if (data.maintenanceMode !== undefined) {
          this.maintenanceMode =
            data.maintenanceMode === "true" || data.maintenanceMode === true;
        }

        return data;
      } catch (err) {
        console.error("Error fetching settings:", err);
      } finally {
        this.loading = false;
      }
    },
  },
});
