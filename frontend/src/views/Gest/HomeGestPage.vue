<template>
  <div class="guest-experience-page pb-12">
    <template v-for="section in config.sectionsOrder" :key="section">
      <!-- Special case for Banner (Hero) -->
      <BannerHome v-if="section === 'BannerHome' && config.showHeroSlider" />

      <!-- Wrapped Sections (Inside individual containers or wrappers for styling) -->
      <v-lazy
        v-if="section === 'TopOffer' && config.showTopOffer"
        :min-height="200"
        transition="fade-transition"
      >
        <TopOffer />
      </v-lazy>

      <v-lazy
        v-if="section === 'NewProduct' && config.showNewProduct"
        :min-height="200"
        transition="fade-transition"
      >
        <NewProduct />
      </v-lazy>

      <v-lazy
        v-if="['FlashDeal', 'Categoury', 'BestSeller'].includes(section)"
        :min-height="300"
        transition="fade-transition"
      >
        <v-container class="px-0 px-md-4">
          <FlashDeal
            v-if="section === 'FlashDeal' && config.showFlashDeals"
            class="mb-14 rounded-xl"
          />
          <Categoury
            v-if="section === 'Categoury' && config.showCategoury"
            class="mb-14"
          />
          <BestSeller
            v-if="section === 'BestSeller' && config.showBestSellers"
            class="mb-14 rounded-xl"
          />
        </v-container>
      </v-lazy>

      <!-- Trust Hub Group -->
      <v-lazy
        v-if="section === 'TrustHub' && isTrustHubVisible"
        :min-height="400"
        transition="fade-transition"
      >
        <div
          class="trust-hub bg-white py-16 position-relative overflow-hidden elevation-4 mb-16"
        >
          <div class="decorative-circle top-right"></div>
          <div class="decorative-circle bottom-left"></div>
          <v-container class="position-relative z-index-1">
            <QualityFeature v-if="config.showQualityFeature" class="mb-12" />
            <v-divider
              v-if="config.showQualityFeature && config.showFaq"
              class="my-10 opacity-20"
            ></v-divider>
            <FaqSection v-if="config.showFaq" class="mb-12" />
          </v-container>
        </div>
      </v-lazy>

      <!-- Individual Trust Sections (if not in hub or ordered differently) -->
      <v-lazy
        v-if="section === 'QualityFeature' && config.showQualityFeature"
        :min-height="100"
        transition="fade-transition"
      >
        <div class="py-10">
          <QualityFeature />
        </div>
      </v-lazy>

      <v-lazy
        v-if="section === 'FaqSection' && config.showFaq"
        :min-height="200"
        transition="fade-transition"
      >
        <div class="py-10">
          <v-container>
            <FaqSection />
          </v-container>
        </div>
      </v-lazy>

      <v-lazy
        v-if="section === 'ShopWithUs' && config.showShopWithUs"
        :min-height="200"
        transition="fade-transition"
      >
        <div class="py-10">
          <v-container>
            <ShopWithUs />
          </v-container>
        </div>
      </v-lazy>

      <!-- CTA Section -->
      <v-lazy
        v-if="section === 'CtaSection' && config.showCta"
        :min-height="300"
        transition="fade-transition"
      >
        <v-container class="py-16 text-center">
          <v-card
            class="bg-grey-darken-4 rounded-xl pa-8 pa-md-16 overflow-hidden position-relative cta-card elevation-24"
            dark
          >
            <div class="decorative-blob blob-primary opacity-20"></div>
            <div class="position-relative" style="z-index: 2">
              <h2
                class="text-h3 font-weight-black text-white mb-6 underline-none"
              >
                {{ config.ctaTitle || $t("landing.cta_title") }}
              </h2>
              <p
                class="text-h6 text-grey-lighten-1 mb-10 mx-auto"
                style="max-width: 650px"
              >
                {{ config.ctaDesc || $t("landing.cta_desc") }}
              </p>
              <v-btn
                size="x-large"
                color="primary"
                height="80"
                class="px-16 rounded-xl font-weight-black text-h5 primary-btn-glow"
                to="/Auth/RegisterNow"
              >
                {{ config.registerFree || $t("landing.register_free") }}
                <v-icon end size="32">mdi-account-plus</v-icon>
              </v-btn>
            </div>
          </v-card>
        </v-container>
      </v-lazy>

      <!-- Brands -->
      <v-lazy
        v-if="section === 'BrandS' && config.showBrands"
        :min-height="200"
        transition="fade-transition"
      >
        <div class="bg-white py-12 mt-4 elevation-2">
          <v-container>
            <BrandS />
          </v-container>
        </div>
      </v-lazy>
    </template>
  </div>
</template>
<script setup>
import { onMounted, computed, defineAsyncComponent } from "vue";
import { useSettingsStore } from "@/store/Settings";

// Dynamic imports for better performance
const BannerHome = defineAsyncComponent(() =>
  import("@/components/BannerHome.vue")
);
const TopOffer = defineAsyncComponent(() =>
  import("@/components/TopOffer.vue")
);
const FlashDeal = defineAsyncComponent(() =>
  import("@/components/FlashDeal.vue")
);
const NewProduct = defineAsyncComponent(() =>
  import("@/components/NewProduct.vue")
);
const Categoury = defineAsyncComponent(() =>
  import("@/components/CateGoury.vue")
);
const QualityFeature = defineAsyncComponent(() =>
  import("@/components/QualityFeature.vue")
);
const FaqSection = defineAsyncComponent(() =>
  import("@/components/FaqSection.vue")
);
const ShopWithUs = defineAsyncComponent(() =>
  import("@/components/ShopWithUs.vue")
);
const BrandS = defineAsyncComponent(() => import("@/components/BrandS.vue"));
const BestSeller = defineAsyncComponent(() =>
  import("@/components/BestSeller.vue")
);

const settingsStore = useSettingsStore();
const config = computed(() => settingsStore.guestHome);

const isTrustHubVisible = computed(() => {
  return config.value.showQualityFeature || config.value.showFaq;
});

onMounted(async () => {
  // SEO & Schema.org
  const script = document.createElement("script");
  script.type = "application/ld+json";
  script.text = JSON.stringify({
    "@context": "https://schema.org",
    "@type": "WebSite",
    name: "ZadStore",
    url: window.location.origin,
    potentialAction: {
      "@type": "SearchAction",
      target:
        window.location.origin + "/#/Products?search={search_term_string}",
      "query-input": "required name=search_term_string",
    },
  });
  document.head.appendChild(script);

  if (!settingsStore.storeName) {
    await settingsStore.fetchSettings();
  }
});
</script>
<style scoped lang="scss">
h2:not(.underline-none) {
  text-align: center;
  color: rgb(var(--v-theme-on-surface));
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 12px;
}

h2:not(.underline-none)::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 4px;
  background-color: rgb(var(--v-theme-primary));
  border-radius: 4px;
}

/* CTA Styles */
.cta-card {
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.primary-btn-glow {
  box-shadow: 0 10px 30px -10px rgba(var(--v-theme-primary), 0.5) !important;
  transition: all 0.3s ease;
}

.primary-btn-glow:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 40px -12px rgba(var(--v-theme-primary), 0.6) !important;
}

.decorative-blob {
  position: absolute;
  width: 400px;
  height: 400px;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
}

.blob-primary {
  background: rgb(var(--v-theme-primary));
  bottom: -100px;
  left: -100px;
}
</style>
