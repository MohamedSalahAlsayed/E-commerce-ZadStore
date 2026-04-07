<template>
  <v-container
    fluid
    class="pa-4 pa-md-8 bg-grey-lighten-5"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    style="min-height: 100vh"
  >
    <!-- العنوان -->
    <v-row class="mb-6" align="center">
      <v-col cols="12">
        <div class="d-flex align-center gap-3">
          <v-avatar color="error" size="48" variant="tonal" elevation="1">
            <v-icon size="28">mdi-heart</v-icon>
          </v-avatar>
          <div>
            <h1 class="text-h4 font-weight-black text-grey-darken-4">
              {{ $t("wishlist.title") }}
            </h1>
            <p class="text-subtitle-2 text-grey mb-0">
              {{
                $t("wishlist.item_count", { count: favStore.FavItem.length })
              }}
            </p>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- حالة التحميل -->
    <v-row v-if="loading">
      <v-col v-for="n in 4" :key="n" cols="12" sm="6" md="4" lg="3">
        <v-skeleton-loader type="card, actions" elevation="1" rounded="xl" />
      </v-col>
    </v-row>

    <!-- حالة فارغة -->
    <v-row
      v-else-if="favStore.FavItem.length === 0"
      justify="center"
      align="center"
      class="py-16"
    >
      <v-col cols="12" md="6" class="text-center">
        <div class="empty-state-container pa-10 rounded-xl">
          <v-icon size="100" color="error-lighten-4" class="mb-6"
            >mdi-heart-plus-outline</v-icon
          >
          <h2 class="text-h5 font-weight-bold grey--text text--darken-3 mb-3">
            {{ $t("wishlist.empty_title") }}
          </h2>
          <p class="text-body-1 text-grey mb-8">
            {{ $t("wishlist.empty_subtitle") }}
          </p>
          <v-btn
            color="primary"
            variant="flat"
            rounded="pill"
            size="large"
            elevation="4"
            prepend-icon="mdi-store-outline"
            to="/User/ProductsPage"
            class="px-10 font-weight-bold"
          >
            {{ $t("wishlist.browse_btn") }}
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <!-- شبكة المنتجات (المكون الموحد) -->
    <v-row v-else>
      <v-col
        v-for="product in favStore.FavItem"
        :key="product.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <ProductCard :product="product" />
      </v-col>
    </v-row>

    <!-- موديل النظرة السريعة (مطلوب للكارت) -->
    <PopUp />
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
const { locale } = useI18n();
import { AddFavProduct } from "@/store/Favourate";
import ProductCard from "@/components/ProductCard.vue";
import PopUp from "@/components/PopUp.vue";

const favStore = AddFavProduct();
const loading = ref(true);

onMounted(async () => {
  // التأكد من جلب البيانات عند التحميل
  await favStore.fetchFavorites();
  loading.value = false;
});
</script>

<style scoped>
.empty-state-container {
  background: white;
  border: 2px dashed #eeeeee;
  transition: all 0.3s ease;
}
.empty-state-container:hover {
  border-color: rgba(var(--v-theme-primary), 0.3);
  transform: translateY(-5px);
}
.gap-3 {
  gap: 12px;
}
</style>
