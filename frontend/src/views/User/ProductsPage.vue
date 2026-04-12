<template>
  <div
    class="bg-grey-lighten-4 py-8"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    style="min-height: 100vh"
  >
    <v-container>
      <!-- العنوان العلوي -->
      <div class="d-flex align-center justify-space-between mb-6">
        <div>
          <h1 class="text-h4 font-weight-black text-primary-dark mb-2">
            {{ $t("products.browse_title") }}
          </h1>
          <p class="text-subtitle-1 text-grey-darken-1">
            {{ $t("products.browse_subtitle") }}
          </p>
        </div>
      </div>

      <v-row>
        <!-- القائمة الجانبية للتصنيفات -->
        <v-col cols="12" md="3" lg="3" class="d-none d-md-block">
          <v-card elevation="1" class="rounded-xl sticky-sidebar border">
            <v-card-title
              class="bg-primary text-white font-weight-bold d-flex align-center py-4 text-h6"
            >
              <v-icon :class="locale === 'ar' ? 'ml-3' : 'mr-3'" size="28"
                >mdi-format-list-bulleted-type</v-icon
              >
              {{ $t("products.all_categories") }}
            </v-card-title>

            <v-card-text class="pa-0">
              <div v-if="loadingCategories" class="pa-4">
                <v-skeleton-loader
                  v-for="n in 6"
                  :key="n"
                  type="list-item"
                  class="mb-1"
                ></v-skeleton-loader>
              </div>

              <v-list
                v-else
                density="compact"
                nav
                class="px-2 py-3 category-list"
              >
                <v-list-item
                  v-for="cat in productStore.categories"
                  :key="cat.id || cat.slug || cat"
                  :to="`/User/category/${cat.id || cat.slug || cat}`"
                  color="primary"
                  rounded="lg"
                  class="mb-1 category-item transition-all"
                >
                  <v-list-item-title
                    class="font-weight-medium text-body-1 text-capitalize px-2 py-1"
                  >
                    {{ (cat.name || cat.slug || cat).replace(/-/g, " ") }}
                  </v-list-item-title>
                  <template v-slot:append>
                    <v-icon size="small" color="grey-darken-1">{{
                      locale === "ar" ? "mdi-chevron-left" : "mdi-chevron-right"
                    }}</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>

          <!-- القائمة الجانبية للماركات -->
          <v-card elevation="1" class="rounded-xl sticky-sidebar border mt-6">
            <v-card-title
              class="bg-primary text-white font-weight-bold d-flex align-center py-4 text-h6"
            >
              <v-icon :class="locale === 'ar' ? 'ml-3' : 'mr-3'" size="28"
                >mdi-tag-multiple</v-icon
              >
              {{ $t("products.all_brands") }}
            </v-card-title>

            <v-card-text class="pa-0">
              <div v-if="loadingBrands" class="pa-4">
                <v-skeleton-loader
                  v-for="n in 6"
                  :key="n"
                  type="list-item"
                  class="mb-1"
                ></v-skeleton-loader>
              </div>

              <v-list
                v-else
                density="compact"
                nav
                class="px-2 py-3 category-list"
              >
                <v-list-item
                  v-for="b in productStore.brand"
                  :key="b.id || b.slug || b"
                  :to="`/User/brand/${b.id || b.slug || b}`"
                  color="primary"
                  rounded="lg"
                  class="mb-1 category-item transition-all"
                >
                  <v-list-item-title
                    class="font-weight-medium text-body-1 text-capitalize px-2 py-1"
                  >
                    {{ (b.name || b.slug || b).replace(/-/g, " ") }}
                  </v-list-item-title>
                  <template v-slot:append>
                    <v-icon size="small" color="grey-darken-1">{{
                      locale === "ar" ? "mdi-chevron-left" : "mdi-chevron-right"
                    }}</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- منطقة عرض كروت المنتجات الرئيسية -->
        <v-col cols="12" md="9" lg="9">
          <!-- شريط الفلتر للموبايل -->
          <v-menu v-if="$vuetify.display.smAndDown" location="bottom">
            <template v-slot:activator="{ props }">
              <v-btn
                color="primary"
                class="mb-4 text-none w-100 rounded-lg"
                v-bind="props"
                height="48"
                elevation="1"
              >
                <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                  >mdi-format-list-bulleted-type</v-icon
                >
                {{ $t("products.category_browse") }}
                <v-icon :class="locale === 'ar' ? 'mr-auto' : 'ml-auto'"
                  >mdi-chevron-down</v-icon
                >
              </v-btn>
            </template>
            <v-list max-height="400" class="rounded-lg">
              <v-list-item
                v-for="cat in productStore.categories"
                :key="cat.id || cat.slug || cat"
                :to="`/User/category/${cat.id || cat.slug || cat}`"
                color="primary"
              >
                <v-list-item-title class="text-capitalize">{{
                  (cat.name || cat.slug || cat).replace(/-/g, " ")
                }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <v-menu v-if="$vuetify.display.smAndDown" location="bottom">
            <template v-slot:activator="{ props }">
              <v-btn
                color="primary"
                class="mb-4 text-none w-100 rounded-lg font-weight-bold"
                v-bind="props"
                height="48"
                elevation="1"
              >
                <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                  >mdi-tag-multiple</v-icon
                >
                {{ $t("products.brand_browse") }}
                <v-icon :class="locale === 'ar' ? 'mr-auto' : 'ml-auto'"
                  >mdi-chevron-down</v-icon
                >
              </v-btn>
            </template>
            <v-list max-height="400" class="rounded-lg">
              <v-list-item
                v-for="b in productStore.brand"
                :key="b.id || b.slug || b"
                :to="`/User/brand/${b.id || b.slug || b}`"
                color="primary"
              >
                <v-list-item-title class="text-capitalize">{{
                  (b.name || b.slug || b).replace(/-/g, " ")
                }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <CardProductOnce />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
const { locale } = useI18n();
import CardProductOnce from "@/components/CardProductOnce.vue";
import { ProductModule } from "@/store/Products";

const productStore = ProductModule();
const loadingCategories = ref(false);
const loadingBrands = ref(false);

onMounted(async () => {
  if (!productStore.categories || productStore.categories.length === 0) {
    loadingCategories.value = true;
    await productStore.getCategories();
    loadingCategories.value = false;
  }
  if (!productStore.brand || productStore.brand.length === 0) {
    loadingBrands.value = true;
    await productStore.getBrands();
    loadingBrands.value = false;
  }
});
</script>

<style scoped>
.text-primary-dark {
  color: #111 !important;
}

.sticky-sidebar {
  position: sticky;
  top: 90px; /* Adjust according to your navbar height */
  max-height: calc(100vh - 110px);
  display: flex;
  flex-direction: column;
}

.category-list {
  overflow-y: auto;
  flex-grow: 1;
  max-height: calc(100vh - 180px); /* Leave room for title */
}

/* Custom Scrollbar for sidebar */
.category-list::-webkit-scrollbar {
  width: 6px;
}
.category-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}
.category-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}
.category-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.category-item {
  border: 1px solid transparent;
}
.category-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.05);
  border-color: rgba(var(--v-theme-primary), 0.1);
  transform: translateX(-4px);
}
</style>
