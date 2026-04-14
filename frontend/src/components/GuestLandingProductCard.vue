<template>
  <div class="guest-lp-card-wrap">
    <v-card
      class="guest-lp-card rounded-xl overflow-hidden h-100 d-flex flex-column"
      elevation="0"
    >
      <div class="guest-lp-card__media position-relative">
        <v-img
          :src="imageSrc"
          :alt="title"
          height="220"
          cover
          class="guest-lp-card__img"
        >
          <template v-slot:placeholder>
            <div
              class="d-flex align-center justify-center fill-height bg-grey-lighten-3"
            >
              <v-progress-circular indeterminate color="primary" size="32" />
            </div>
          </template>
        </v-img>
        <div v-if="discountPct > 0" class="guest-lp-card__badge">
          -{{ Math.round(discountPct) }}%
        </div>
      </div>
      <v-card-text class="pa-4 flex-grow-1 d-flex flex-column">
        <div class="text-caption text-medium-emphasis mb-1 text-truncate">
          {{ categoryLabel }}
        </div>
        <h3
          class="text-subtitle-1 font-weight-black mb-2 text-truncate"
          style="line-height: 1.35"
        >
          {{ title }}
        </h3>
        <div class="d-flex align-center justify-space-between mt-auto pt-2">
          <div>
            <span class="text-caption text-medium-emphasis d-block">{{
              priceLabel
            }}</span>
            <span class="text-h6 font-weight-black text-primary">{{
              formattedPrice
            }}</span>
          </div>
          <v-btn
            color="primary"
            variant="flat"
            size="small"
            class="font-weight-bold rounded-lg"
            :to="ctaTo"
          >
            {{ ctaText }}
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
.guest-lp-card-wrap {
  height: 100%;
}

.guest-lp-card {
  background: rgb(var(--v-theme-surface));
  border: 1px solid rgba(0, 0, 0, 0.06);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.guest-lp-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12) !important;
}

.guest-lp-card__media {
  position: relative;
  padding: 3px;
  border-radius: 12px 12px 0 0;
  background: linear-gradient(
    135deg,
    rgb(var(--v-theme-primary)) 0%,
    #fb923c 45%,
    #ea580c 100%
  );
}

.guest-lp-card__img {
  border-radius: 10px 10px 0 0;
}

.guest-lp-card__badge {
  position: absolute;
  top: 12px;
  z-index: 2;
  inset-inline-end: 12px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #fff;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 999px;
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35);
}
</style>
