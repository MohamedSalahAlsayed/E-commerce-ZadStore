/** Default guest landing config (merged with DB `guest_home` JSON). */
export function defaultGuestHome() {
  return {
    showHeroSlider: true,
    showHeroFallback: true,
    showCalculator: true,
    showTopFeaturesStrip: true,
    showSteps: true,
    showFlashDeals: true,
    showBestSellers: true,
    showFeaturedProducts: true,
    showBrands: true,
    showWhyShop: true,
    showDifferentiators: true,
    showStats: true,
    showShopWithUs: true,
    showCta: true,
    showFaq: true,
    showQualityFeature: true,
    showTopOffer: true,
    showCategoury: true,
    showNewProduct: true,
    sectionsOrder: [
      "BannerHome",
      "TopOffer",
      "FlashDeal",
      "NewProduct",
      "Categoury",
      "BestSeller",
      "QualityFeature",
      "FaqSection",
      "ShopWithUs",
      "BrandS",
      "CtaSection",
    ],
    featuredLimit: 8,
    brandsLimit: 12,
    heroTitle: "",
    heroSubtitle: "",
    flashTitle: "",
    bestSellersTitle: "",
    featuredTitle: "",
    brandsTitle: "",
    faqTitle: "",
    whyShopTitle: "",
    ctaTitle: "",
    ctaDesc: "",
    registerFree: "",
    guestPrimaryColor: "#f97316",
    activeTemplate: "modern",
  };
}

export function mergeGuestHome(raw) {
  const base = defaultGuestHome();
  if (raw == null || raw === "") return base;
  let obj = raw;
  if (typeof raw === "string") {
    try {
      obj = JSON.parse(raw);
    } catch {
      return base;
    }
  }
  return { ...base, ...obj };
}
