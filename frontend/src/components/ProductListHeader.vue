<template>
  <v-card
    flat
    rounded="xl"
    class="mb-8 border px-4 py-2 bg-white d-flex align-center flex-wrap gap-4"
  >
    <!-- Results Count -->
    <div class="d-flex align-center text-subtitle-2 text-grey-darken-1">
      <v-icon size="18" class="mr-2 ml-2" color="primary"
        >mdi-filter-variant</v-icon
      >
      {{ $t("products.found_count", { count: count }) }}
    </div>

    <v-spacer></v-spacer>

    <!-- Sorting Menu -->
    <div class="d-flex align-center gap-3">
      <v-menu location="bottom end" transition="scale-transition">
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            variant="outlined"
            color="primary"
            rounded="lg"
            class="text-none font-weight-bold sort-btn"
            append-icon="mdi-sort-variant"
            height="40"
          >
            {{
              selectedPrice ? selectedPrice.title : $t("products.sort_by_price")
            }}
          </v-btn>
        </template>
        <v-list density="compact" rounded="lg" elevation="10" class="py-1">
          <v-list-item
            v-if="selectedPrice"
            @click="$emit('update:selectedPrice', null)"
            :title="$t('products.cancel_sort')"
            color="error"
            class="text-error mb-1"
          >
            <template v-slot:prepend>
              <v-icon icon="mdi-close" color="error" size="18"></v-icon>
            </template>
          </v-list-item>
          <v-list-item
            v-for="item in priceOptions"
            :key="item.value"
            :title="item.title"
            :value="item.value"
            @click="$emit('update:selectedPrice', item)"
            :active="selectedPrice?.value === item.value"
            color="primary"
            class="rounded-md"
          >
            <template v-slot:prepend>
              <v-icon
                :icon="
                  item.value === 'asc'
                    ? 'mdi-sort-ascending'
                    : 'mdi-sort-descending'
                "
                size="18"
              ></v-icon>
            </template>
          </v-list-item>
        </v-list>
      </v-menu>
    </div>
  </v-card>
</template>

<script setup>
import { defineProps, defineEmits, computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

defineProps({
  count: {
    type: Number,
    default: 0,
  },
  selectedPrice: {
    type: Object,
    default: null,
  },
});

defineEmits(["update:selectedPrice"]);

const priceOptions = computed(() => [
  { title: t("products.price_low_high"), value: "asc" },
  { title: t("products.price_high_low"), value: "desc" },
]);
</script>

<style scoped>
.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}

.sort-btn {
  border-width: 1.5px !important;
  letter-spacing: 0;
}

:deep(.v-list-item--active) {
  background-color: rgba(var(--v-theme-primary), 0.1) !important;
  color: rgb(var(--v-theme-primary)) !important;
  font-weight: 700;
}
</style>
