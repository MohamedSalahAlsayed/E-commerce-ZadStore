import { defineStore } from "pinia";
import api from "../axios";
import { mergeGuestHome } from "../utils/guestHomeDefaults";

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
    primaryColor: "#f97316",
    storeTheme: null,
    guestHome: mergeGuestHome(null),
    loading: false,
  }),
  getters: {},
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
          "primaryColor",
          "storeTheme",
        ];

        props.forEach((p) => {
          if (data[p] !== undefined) this[p] = data[p];
        });

        if (data.maintenanceMode !== undefined) {
          this.maintenanceMode =
            data.maintenanceMode === "true" || data.maintenanceMode === true;
        }

        if (data.guest_home !== undefined) {
          this.guestHome = mergeGuestHome(data.guest_home);
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
