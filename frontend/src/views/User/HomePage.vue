<template>
  <div class="homepage-wrapper">
    <BannerHome />

    <div class="home-sections-container">
      <section class="home-section reveal-on-scroll">
        <TopOffer />
      </section>

      <section class="home-section reveal-on-scroll">
        <FlashDeal />
      </section>

      <section class="home-section reveal-on-scroll">
        <Categoury />
      </section>

      <section class="home-section reveal-on-scroll">
        <BestSeller />
      </section>

      <section class="home-section reveal-on-scroll">
        <QualityFeature />
      </section>

      <section class="home-section reveal-on-scroll">
        <BrandS />
      </section>
    </div>
  </div>
</template>
<script setup>
import { onMounted } from "vue";
import BannerHome from "@/components/BannerHome.vue";
import TopOffer from "@/components/TopOffer.vue";
import { ProductModule } from "@/store/Products.js";
import FlashDeal from "@/components/FlashDeal.vue";
import Categoury from "@/components/CateGoury.vue";
// import NewProduct from "@/components/NewProduct.vue";
import QualityFeature from "@/components/QualityFeature.vue";
import BrandS from "@/components/BrandS.vue";
import BestSeller from "@/components/BestSeller.vue";
const ProductModules = ProductModule();

onMounted(async () => {
  // 1. Setup Scroll Reveal Immediately
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add("is-revealed");
      }
    });
  }, observerOptions);

  document.querySelectorAll(".reveal-on-scroll").forEach((el) => {
    observer.observe(el);
  });

  // 2. SEO & Schema.org
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

  // 3. Load Data Async (Non-blocking for animations)
  try {
    const promises = [];
    if (!ProductModules.products?.length)
      promises.push(ProductModules.getProducts());
    if (!ProductModules.flashDeals?.length)
      promises.push(ProductModules.getFlashDeals());
    if (!ProductModules.bestSellers?.length)
      promises.push(ProductModules.getBestSellers());

    if (promises.length > 0) {
      await Promise.all(promises);
    }
  } catch (err) {
    console.error("Home data fetch error:", err);
  }
});
</script>
<style scoped lang="scss">
.home-sections-container {
  display: flex;
  flex-direction: column;
  gap: var(--v-section-margin);
  margin-top: 20px; /* Pull TopOffer closer to Banner */
  margin-bottom: var(--v-section-margin);
}

.home-section {
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
  opacity: 0;
  transform: translateY(30px);
}

.home-section.is-revealed {
  opacity: 1;
  transform: translateY(0);
}

.section-title {
  text-align: center;
  color: rgb(var(--v-theme-on-surface));
  margin-bottom: 40px;
  font-weight: 800;
}
</style>
