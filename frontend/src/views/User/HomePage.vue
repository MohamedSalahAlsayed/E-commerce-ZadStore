<template>
  <div>
    <BannerHome />
    <TopOffer />
    <FlashDeal />
    <Categoury />
    <BestSeller />
    <!-- <NewProduct /> -->
    <QualityFeature />
    <BrandS />
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
});
</script>
<style scoped lang="scss">
.section-title {
  text-align: center;
  color: rgb(var(--v-theme-on-surface));
  margin-bottom: 40px;
  font-weight: 800;
}
</style>
