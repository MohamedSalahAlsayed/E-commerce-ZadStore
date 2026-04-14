<template>
  <div :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <v-sheet
      color="primary"
      elevation="0"
      rounded="lg"
      class="header-container px-4 py-3 mb-6 mx-2 mx-md-0 border"
    >
      <div class="d-flex flex-wrap align-center justify-end w-100 gap-3">
        <v-menu location="bottom end">
          <template v-slot:activator="{ props }">
            <v-btn
              v-bind="props"
              variant="text"
              class="text-button font-weight-black text-white"
              append-icon="mdi-chevron-down"
            >
              {{
                selectedPrice
                  ? selectedPrice.title
                  : $t("products.sort_by_price")
              }}
            </v-btn>
          </template>
          <v-list density="compact" rounded="lg">
            <v-list-item
              v-if="selectedPrice"
              @click="selectedPrice = null"
              :title="$t('products.cancel_sort')"
              color="error"
              class="text-error"
            >
              <template v-slot:prepend>
                <v-icon icon="mdi-close" color="error"></v-icon>
              </template>
            </v-list-item>
            <v-list-item
              v-for="item in priceOptions"
              :key="item.value"
              :title="item.title"
              :value="item.value"
              @click="selectedPrice = item"
              :active="selectedPrice?.value === item.value"
              color="primary"
            ></v-list-item>
          </v-list>
        </v-menu>
      </div>
    </v-sheet>

    <div class="Product-section">
      <div v-if="loading" class="pa-0">
        <v-row>
          <v-col
            cols="12"
            sm="6"
            md="6"
            lg="4"
            v-for="n in 6"
            :key="'skeleton-' + n"
          >
            <PerformanceSkeleton />
          </v-col>
        </v-row>
      </div>

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
          <p class="text-grey mt-2">
            {{ $t("products.try_reset_filter") }}
          </p>
        </v-col>
      </v-row>

      <v-container v-else class="pa-0">
        <v-row>
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
              elevation="0"
            ></v-pagination>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <PopUp />
  </div>
</template>

<script setup>
import { ProductModule } from "@/store/Products.js";
import PopUp from "./PopUp.vue";
import ProductCard from "./ProductCard.vue";
import PerformanceSkeleton from "./Skeletons/PerformanceSkeleton.vue";
import { useI18n } from "vue-i18n";
import { onMounted, watch, ref, computed } from "vue";

const { locale, t } = useI18n();

const productStore = ProductModule();
const loading = ref(false);

// --- Filter Logic States ---
const selectedPrice = ref(null);
const priceOptions = computed(() => [
  { title: t("products.price_low_high"), value: "asc" },
  { title: t("products.price_high_low"), value: "desc" },
]);

// --- Pagination Logic ---
const currentPage = ref(1);
const itemsPerPage = 8;

const filteredProducts = computed(() => {
  let products = productStore.products ? [...productStore.products] : [];

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

onMounted(async () => {
  if (!productStore.products || productStore.products.length === 0) {
    loading.value = true;
    await productStore.getProducts();
    loading.value = false;
  }
});
</script>

<style scoped>
.gap-3 {
  gap: 12px;
}
</style>
