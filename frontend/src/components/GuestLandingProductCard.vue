<template>
  <div class="guest-lp-card-wrap">
    <v-card
      class="guest-lp-card overflow-hidden h-100 d-flex flex-column"
      elevation="0"
    >
      <div class="guest-lp-card__media position-relative">
        <v-img
          :src="imageSrc"
          :alt="title"
          height="260"
          cover
          class="guest-lp-card__img"
        >
          <template v-slot:placeholder>
            <div
              class="d-flex align-center justify-center fill-height bg-grey-lighten-4"
            >
              <v-progress-circular indeterminate color="primary" size="32" />
            </div>
          </template>
        </v-img>
        <div v-if="discountPct > 0" class="guest-lp-card__badge">
          -{{ Math.round(discountPct) }}%
        </div>
      </div>
      <v-card-text class="pa-4 pt-5 flex-grow-1 d-flex flex-column">
        <div class="category-label mb-2">
          {{ categoryLabel }}
        </div>
        <h3 class="product-title mb-2">
          {{ title }}
        </h3>
        <div
          class="d-flex align-center justify-space-between mt-auto pt-3"
          style="border-top: 1px solid rgba(0, 0, 0, 0.05)"
        >
          <div class="price-info">
            <span class="price-label">{{ priceLabel }}</span>
            <span class="price-value">{{ formattedPrice }}</span>
          </div>
          <v-btn
            color="primary"
            variant="flat"
            height="44"
            class="cta-btn font-weight-black rounded-lg px-4"
            :to="ctaTo"
          >
            {{ ctaText }}
            <v-icon end size="18" class="mr-1">mdi-arrow-left</v-icon>
          </v-btn>
        </div>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  product: { type: Object, required: true },
  apiBase: { type: String, default: "" },
  priceLabel: { type: String, default: "السعر" },
  ctaText: { type: String, default: "سوق الآن" },
  ctaTo: { type: String, default: "/Auth/RegisterNow" },
});

const title = computed(() => props.product.title || props.product.name || "—");

const categoryLabel = computed(() => {
  const c = props.product.category;
  if (typeof c === "object" && c?.name) return c.name;
  if (typeof c === "string") return c;
  return "—";
});

const discountPct = computed(
  () =>
    Number(
      props.product.discount_percentage ?? props.product.discountPercentage ?? 0
    ) || 0
);

const imageSrc = computed(() => {
  const th = props.product.thumbnail || props.product.image;
  if (!th) return "https://placehold.co/400x400/f3f4f6/64748b?text=Product";
  if (typeof th === "string" && th.startsWith("http")) return th;
  const base = props.apiBase.replace(/\/$/, "");
  return `${base}${th.startsWith("/") ? "" : "/"}${th}`;
});

const formattedPrice = computed(() => {
  const p = Number(props.product.price ?? 0);
  return `${p.toLocaleString()} ج.م`;
});
</script>

<style scoped>
/* ================= Base Card ================= */
.guest-lp-card {
  position: relative;
  background: white;
  border-radius: 20px !important;
  transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
  border: 1px solid rgba(0, 0, 0, 0.03) !important;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
}

.guest-lp-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08) !important;
  border-color: rgba(var(--v-theme-primary), 0.15) !important;
}

/* ================= Media Section ================= */
.guest-lp-card__media {
  position: relative;
  overflow: hidden;
  border-radius: 20px 20px 0 0;
  background: #f8fafc;
}

.guest-lp-card__img {
  transition: transform 1.2s cubic-bezier(0.23, 1, 0.32, 1);
}

.guest-lp-card:hover .guest-lp-card__img {
  transform: scale(1.1);
}

.guest-lp-card__badge {
  position: absolute;
  top: 15px;
  inset-inline-end: 15px;
  z-index: 2;
  background: linear-gradient(135deg, #ff4757, #ff6b81);
  color: #fff;
  font-size: 13px;
  font-weight: 900;
  padding: 6px 12px;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(255, 71, 87, 0.3);
}

/* ================= Content Section ================= */
.category-label {
  font-size: 10px;
  color: #fb923c;
  font-weight: 900;
  text-transform: uppercase;
  background: rgba(251, 146, 60, 0.1);
  padding: 3px 10px;
  border-radius: 6px;
  letter-spacing: 0.5px;
  display: inline-block;
  width: fit-content;
}

.product-title {
  font-size: 17px;
  font-weight: 900;
  color: #1a1a1a;
  line-height: 1.5;
  height: 50px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.price-info {
  display: flex;
  flex-direction: column;
}

.price-label {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 700;
  margin-bottom: 2px;
}

.price-value {
  font-size: 22px;
  font-weight: 900;
  color: #ff4757;
  line-height: 1;
}

/* ================= Buttons ================= */
.cta-btn {
  text-transform: none !important;
  font-size: 13px !important;
  letter-spacing: 0.5px !important;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1) !important;
  box-shadow: 0 4px 15px rgba(var(--v-theme-primary), 0.2) !important;
}

.cta-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(var(--v-theme-primary), 0.3) !important;
}
</style>
