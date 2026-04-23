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
    youtube: "",
    showFacebook: true,
    showInstagram: true,
    showTwitter: true,
    showTiktok: true,
    showYoutube: true,
    currency: "EGP",
    taxRate: 0,
    freeShippingEnabled: false,
    freeShippingThreshold: 1000,
    maintenanceMode: false,
    primaryColor: "#f97316",
    storeTheme: null,
    footerAbout: "",
    footerCopyright: "",
    footerShowSocial: true,
    footerShowContact: true,
    guestHome: mergeGuestHome(null),
    promoOffers: {
      small1: {
        category: "أجهزة ألعاب",
        title: "اشتري 2 واحصل على 1",
        offer: "مجاناً",
        image: "https://pngimg.com/uploads/gamepad/gamepad_PNG41.png",
        color: "blue",
      },
      small2: {
        category: "مكبرات صوت",
        title: "خصم هائل يصل إلى",
        offer: "75%",
        image:
          "https://pngimg.com/uploads/audio_speakers/audio_speakers_PNG50720.png",
        color: "purple",
      },
      small3: {
        category: "كراسي استرخاء",
        title: "تخفيضات مذهلة",
        offer: "50%",
        image: "https://pngimg.com/uploads/armchair/armchair_PNG7048.png",
        color: "green",
      },
      large1: {
        label: "الأكثر مبيعاً 🔥",
        title: "هاتف إكس برو",
        desc: "أداء استثنائي وتصميم عصري يناسبك.",
        image: "https://pngimg.com/uploads/smartphone/smartphone_PNG8514.png",
        color: "yellow",
      },
      large2: {
        label: "الأكثر شهرة ⭐",
        title: "لابتوب ألترا",
        desc: "قوة هائلة تنجز كل مهامك اليومية بسهولة.",
        image: "https://pngimg.com/uploads/macbook/macbook_PNG65.png",
        color: "pink",
      },
    },
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
          "youtube",
          "currency",
          "primaryColor",
          "storeTheme",
          "footerAbout",
          "footerCopyright",
        ];

        // Handle social show/hide booleans
        const boolSocials = [
          "showFacebook",
          "showInstagram",
          "showTwitter",
          "showTiktok",
          "showYoutube",
        ];
        boolSocials.forEach((key) => {
          if (data[key] !== undefined) {
            this[key] = data[key] === "true" || data[key] === true;
          }
        });

        props.forEach((p) => {
          if (data[p] !== undefined) this[p] = data[p];
        });

        // Handle Booleans
        if (data.footerShowSocial !== undefined) {
          this.footerShowSocial =
            data.footerShowSocial === "true" || data.footerShowSocial === true;
        }
        if (data.footerShowContact !== undefined) {
          this.footerShowContact =
            data.footerShowContact === "true" ||
            data.footerShowContact === true;
        }

        if (data.free_shipping_enabled !== undefined) {
          this.freeShippingEnabled =
            data.free_shipping_enabled === "true" ||
            data.free_shipping_enabled === true;
        }

        if (data.free_shipping_threshold !== undefined) {
          this.freeShippingThreshold = Number(data.free_shipping_threshold);
        }

        if (data.maintenanceMode !== undefined) {
          this.maintenanceMode =
            data.maintenanceMode === "true" || data.maintenanceMode === true;
        }

        if (data.guest_home !== undefined) {
          this.guestHome = mergeGuestHome(data.guest_home);
        }

        if (data.promo_offers !== undefined) {
          try {
            const parsed =
              typeof data.promo_offers === "string"
                ? JSON.parse(data.promo_offers)
                : data.promo_offers;
            this.promoOffers = { ...this.promoOffers, ...parsed };
          } catch (e) {
            console.error("Error parsing promo_offers", e);
          }
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
