<template>
  <section class="guest-products py-16 bg-white overflow-hidden">
    <v-container>
      <div class="text-center mb-12">
        <v-chip color="primary" variant="tonal" class="mb-4 font-weight-bold">{{
          $t("guest_products.chip")
        }}</v-chip>
        <h2 class="text-h3 font-weight-black text-grey-darken-4 mb-4">
          {{ $t("guest_products.title") }}
        </h2>
        <p class="text-h6 text-grey-darken-1 mx-auto" style="max-width: 700px">
          {{ $t("guest_products.description") }}
        </p>
      </div>

      <v-row>
        <v-col
          v-for="product in sampleProducts"
          :key="product.id"
          cols="12"
          sm="6"
          md="4"
          lg="3"
        >
          <v-card class="product-card rounded-xl border-0 shadow-sm" flat>
            <div class="overflow-hidden rounded-xl position-relative">
              <v-img
                :src="product.image"
                height="280"
                cover
                class="transition-scale transition-swing"
              >
                <template v-slot:placeholder>
                  <v-row
                    class="fill-height ma-0"
                    align="center"
                    justify="center"
                  >
                    <v-progress-circular
                      indeterminate
                      color="primary"
                    ></v-progress-circular>
                  </v-row>
                </template>
              </v-img>
              <div class="product-overlay d-flex align-center justify-center">
                <v-btn
                  color="white"
                  class="rounded-pill font-weight-bold text-primary px-6"
                  to="/Auth/RegisterNow"
                >
                  {{ $t("guest_products.market_this") }}
                </v-btn>
              </div>
            </div>

            <v-card-text class="pt-4 px-2">
              <div class="d-flex justify-space-between align-start mb-1">
                <h3
                  class="text-subtitle-1 font-weight-black text-grey-darken-4"
                >
                  {{ product.name }}
                </h3>
                <v-chip color="green" size="x-small" variant="flat">{{
                  $t("guest_products.high_profit")
                }}</v-chip>
              </div>
              <div class="d-flex align-center justify-space-between mt-2">
                <div>
                  <span class="text-caption text-grey font-weight-bold">{{
                    $t("guest_products.commission_label")
                  }}</span>
                  <div class="text-h6 text-primary font-weight-black">
                    {{ product.commission }} {{ $t("home.currency") }}
                  </div>
                </div>
                <div class="text-right">
                  <span class="text-caption text-grey font-weight-bold">{{
                    $t("guest_products.price_label")
                  }}</span>
                  <div class="text-subtitle-1 font-weight-bold">
                    {{ product.price }} {{ $t("home.currency") }}
                  </div>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <div class="text-center mt-12">
        <v-btn
          variant="outlined"
          color="primary"
          size="x-large"
          class="rounded-xl px-12 font-weight-bold border-2"
          to="/Auth/RegisterNow"
        >
          {{ $t("guest_products.view_all_btn") }}
        </v-btn>
      </div>
    </v-container>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const sampleProducts = computed(() => [
  {
    id: 1,
    name: t("guest_products.product_1_name"),
    price: 1250,
    commission: 150,
    image:
      "https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=500",
  },
  {
    id: 2,
    name: t("guest_products.product_2_name"),
    price: 850,
    commission: 120,
    image:
      "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=500",
  },
  {
    id: 3,
    name: t("guest_products.product_3_name"),
    price: 450,
    commission: 80,
    image:
      "https://images.unsplash.com/photo-1621607512214-68297480165e?q=80&w=500",
  },
  {
    id: 4,
    name: t("guest_products.product_4_name"),
    price: 550,
    commission: 100,
    image:
      "https://images.unsplash.com/photo-1553062407-98eeb94c6a62?q=80&w=500",
  },
]);
</script>

<style scoped>
.product-card {
  transition: transform 0.3s ease;
}

.product-card:hover .transition-scale {
  transform: scale(1.1);
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 2;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.transition-scale {
  transition: transform 0.5s ease;
}

.shadow-sm {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}

.border-2 {
  border-width: 2px !important;
}
</style>
