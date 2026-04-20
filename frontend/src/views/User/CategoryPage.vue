<template>
  <div
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    class="py-10 bg-grey-lighten-4"
    style="min-height: 100vh"
  >
    <v-container>
      <div class="d-flex align-center justify-space-between mb-8">
        <div class="d-flex flex-column">
          <v-breadcrumbs
            :items="breadcrumbs"
            class="pa-0 mb-2 text-caption opacity-70"
          ></v-breadcrumbs>
          <h2
            class="text-h4 font-weight-black text-primary text-capitalize d-flex align-center gap-2"
          >
            <v-icon size="32">mdi-shape-outline</v-icon>
            {{ formattedCategoryName }}
          </h2>
        </div>
      </div>

      <!-- Filters & Sorting Header -->
      <ProductListHeader
        :count="filteredProducts.length"
        v-model:selectedPrice="selectedPrice"
      />

      <!-- Products Grid -->
      <div class="Product-section">
        <v-row v-if="loading" justify="center" class="py-15">
          <v-col cols="12" class="text-center">
            <v-progress-circular
              indeterminate
              color="primary"
              size="64"
              width="4"
            ></v-progress-circular>
            <p class="mt-4 text-primary font-weight-bold">
              {{ $t("products.loading") || "جاري تحميل المنتجات..." }}
            </p>
          </v-col>
        </v-row>

        <v-row
          v-else-if="paginatedProducts.length === 0"
          justify="center"
          class="py-15"
        >
          <v-col cols="12" md="6" class="text-center">
            <div
              class="empty-state-container pa-10 rounded-xl bg-white border border-dashed"
            >
              <v-icon size="100" color="grey-lighten-3" class="mb-4"
                >mdi-package-variant-closed</v-icon
              >
              <h3 class="text-h5 font-weight-bold text-grey-darken-2 mb-2">
                {{ $t("products.no_products_found") }}
              </h3>
              <p class="text-body-2 text-grey mb-6">
                لا توجد منتجات في هذا القسم حالياً، يرجى تصفح أقسام أخرى.
              </p>
              <v-btn
                color="primary"
                rounded="lg"
                to="/"
                prepend-icon="mdi-arrow-left"
              >
                العودة للرئيسية
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col
            cols="12"
            sm="6"
            md="4"
            lg="3"
            v-for="product in paginatedProducts"
            :key="product.id"
          >
            <!-- Using standard ProductCard component for consistency -->
            <ProductCard :product="product" />
          </v-col>
        </v-row>

        <!-- Pagination -->
        <v-row justify="center" class="mt-12 mb-6" v-if="totalPages > 1">
          <v-col cols="auto">
            <v-pagination
              v-model="currentPage"
              :length="totalPages"
              :total-visible="5"
              active-color="primary"
              color="white"
              rounded="lg"
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
import { useRoute } from "vue-router";
import api from "@/axios";
import PopUp from "@/components/PopUp.vue";
import ProductCard from "@/components/ProductCard.vue";
import ProductListHeader from "@/components/ProductListHeader.vue";
import { ProductModule } from "@/store/Products";

const { locale, t } = useI18n();
const route = useRoute();
const productStore = ProductModule();

const categoryProducts = ref([]);
const loading = ref(false);

// Helper function to map Laravel Product model to standard format used by ProductCard
const mapProduct = (p) => ({
  ...p,
  discountPercentage: p.discount_percentage || p.discountPercentage || 0,
  category: p.category?.name || p.category || "",
  brand: p.brand?.name || p.brand || "",
  thumbnail:
    p.thumbnail || (p.images && p.images.length ? p.images[0].image_path : ""),
});

const breadcrumbs = computed(() => [
  { title: t("nav.home") || "الرئيسية", disabled: false, to: "/" },
  { title: t("products.all_categories") || "الأقسام", disabled: true },
  { title: formattedCategoryName.value, disabled: true },
]);

const formattedCategoryName = computed(() => {
  const slug = route.params.slug || "";
  const category = (productStore.categories || []).find(
    (c) => String(c.id) === String(slug) || String(c.slug) === String(slug)
  );
  return category ? category.name : t("products.category_browse");
});

const loadCategoryProducts = async (slug) => {
  if (!slug) return;
  loading.value = true;
  categoryProducts.value = [];
  try {
    const res = await api.get(`/products?category_id=${slug}`);
    const rawProducts = res.data?.data || res.data || [];
    categoryProducts.value = rawProducts.map(mapProduct);
  } catch (error) {
    console.error("Error loading category products:", error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.params.slug,
  async (newSlug) => {
    currentPage.value = 1;
    await loadCategoryProducts(newSlug);
  }
);

onMounted(async () => {
  if (!productStore.categories.length) {
    await productStore.getCategories();
  }
  await loadCategoryProducts(route.params.slug);
});

// --- Filter Logic ---
const selectedPrice = ref(null);
const currentPage = ref(1);
const itemsPerPage = 12;

const filteredProducts = computed(() => {
  let products = categoryProducts.value ? [...categoryProducts.value] : [];

  if (selectedPrice.value) {
    const isAsc = selectedPrice.value.value === "asc";
    products.sort((a, b) => {
      const priceA = a.price - a.price * (a.discountPercentage / 100);
      const priceB = b.price - b.price * (b.discountPercentage / 100);
      return isAsc ? priceA - priceB : priceB - priceA;
    });
  }

  return products;
});

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage);
});

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return filteredProducts.value.slice(start, end);
});

watch([selectedPrice], () => {
  currentPage.value = 1;
});
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}

.empty-state-container {
  border: 2px dashed #e2e8f0;
}

:deep(.v-breadcrumbs-item--disabled) {
  opacity: 1 !important;
  color: rgb(var(--v-theme-primary)) !important;
  font-weight: 700;
}
</style>
