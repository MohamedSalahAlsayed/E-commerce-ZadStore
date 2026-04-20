<template>
  <div
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    class="py-10 bg-grey-lighten-4"
    style="min-height: 100vh"
  >
    <v-container>
      <div class="d-flex align-center justify-space-between mb-6">
        <h2 class="text-h4 font-weight-black text-primary text-capitalize">
          {{ formattedBrandName }}
        </h2>
      </div>

      <!-- Filters & Sorting Header -->
      <ProductListHeader
        :count="filteredProducts.length"
        v-model:selectedPrice="selectedPrice"
      />

      <div class="Product-section">
        <v-row v-if="loading" justify="center" class="py-15">
          <v-progress-circular
            indeterminate
            color="primary"
            size="64"
          ></v-progress-circular>
        </v-row>

        <v-row
          v-else-if="paginatedProducts.length === 0"
          justify="center"
          class="py-15"
        >
          <v-col cols="12" class="text-center">
            <v-icon size="80" color="grey-lighten-2" class="mb-4"
              >mdi-package-variant-closed</v-icon
            >
            <h3 class="text-h4 font-weight-bold text-grey-darken-1">
              {{ $t("products.no_products_found") }}
            </h3>
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col
            cols="12"
            sm="6"
            md="6"
            lg="4"
            v-for="product in paginatedProducts"
            :key="product.id"
          >
            <ProductCard :product="product" />
          </v-col>
        </v-row>

        <v-row justify="center" class="mt-8 mb-4" v-if="totalPages > 1">
          <v-col cols="auto">
            <v-pagination
              v-model="currentPage"
              :length="totalPages"
              :total-visible="7"
              active-color="primary"
              color="grey-lighten-2"
              rounded="circle"
              elevation="1"
            ></v-pagination>
          </v-col>
        </v-row>
      </div>
    </v-container>

    <PopUp />
  </div>
</template>

<script setup>
import { onMounted, watch, ref, computed } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import { useRoute } from "vue-router";
import api from "@/axios";
import PopUp from "@/components/PopUp.vue";
import ProductCard from "@/components/ProductCard.vue";
import ProductListHeader from "@/components/ProductListHeader.vue";
import { ProductModule } from "@/store/Products";

const route = useRoute();
const productStore = ProductModule();

const brandProducts = ref([]);
const loading = ref(false);

const formattedBrandName = computed(() => {
  const slug = route.params.slug || "";
  const brand = (productStore.brand || []).find(
    (b) => String(b.id) === String(slug)
  );
  return brand ? brand.name : t("products.brand_browse");
});

// Map Laravel snake_case to camelCase used by templates
const mapProduct = (p) => ({
  ...p,
  discountPercentage: p.discount_percentage || p.discountPercentage || 0,
  category: p.category ? p.category.name || p.category : "",
  brand: p.brand ? p.brand.name || p.brand : "",
  images:
    p.images && p.images.length
      ? p.images.map((img) => (typeof img === "string" ? img : img.image_path))
      : p.thumbnail
      ? [p.thumbnail]
      : [],
});

const loadBrandProducts = async (slug) => {
  if (!slug) return;
  loading.value = true;
  brandProducts.value = [];
  try {
    const res = await api.get(`/products?brand_id=${slug}`);
    const raw = res.data?.data || res.data || [];
    brandProducts.value = raw.map(mapProduct);
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.params.slug,
  async (newSlug) => {
    currentPage.value = 1;
    await loadBrandProducts(newSlug);
  }
);

onMounted(async () => {
  // Ensure brands are loaded for name display
  if (!productStore.brand || productStore.brand.length === 0) {
    await productStore.getBrands();
  }
  await loadBrandProducts(route.params.slug);
});

// --- Filter Logic States ---
const selectedPrice = ref(null);
// --- Pagination Logic ---
const currentPage = ref(1);
const itemsPerPage = 12; // Adjusted to be multiple of 3

const filteredProducts = computed(() => {
  let products = brandProducts.value ? [...brandProducts.value] : [];

  if (selectedPrice.value) {
    const isAsc = selectedPrice.value.value === "asc";
    products.sort((a, b) => {
      const priceA = a.price * (1 - a.discountPercentage / 100);
      const priceB = b.price * (1 - b.discountPercentage / 100);
      return isAsc ? priceA - priceB : priceB - priceA;
    });
  }

  return products;
});

watch([selectedPrice], () => {
  currentPage.value = 1;
});

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage);
});

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredProducts.value.slice(start, end);
});
</script>

<style scoped>
/* ================= ألوان المتغيرات ================= */
.text-primary-dark {
  color: rgb(14, 22, 103) !important;
}
</style>
