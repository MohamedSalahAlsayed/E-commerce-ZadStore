<template>
  <v-app theme="light">
    <div :dir="locale === 'ar' ? 'rtl' : 'ltr'">
      <v-main class="bg-grey-lighten-4">
        <v-container class="py-8">
          <v-row align="center" class="mb-6">
            <v-col cols="12" md="6">
              <h2 class="text-h4 font-weight-bold text-primary">
                {{ $t("statistics.dashboard_title") }}
              </h2>
              <div class="text-caption text-grey mt-1">
                {{ $t("statistics.last_update", { time: lastUpdated }) }}
              </div>
            </v-col>
            <v-col cols="12" md="6" class="text-md-end">
              <v-btn
                :loading="isLoading"
                color="rgb(14 22 67)"
                variant="text"
                prepend-icon="mdi-refresh"
                @click="fetchDashboardData"
              >
                {{ $t("statistics.refresh_data") }}
              </v-btn>
            </v-col>
          </v-row>

          <v-card elevation="0" rounded="lg" class="mb-6 pa-4 border">
            <v-row dense align="center">
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="filters.searchQuery"
                  density="comfortable"
                  variant="outlined"
                  :label="$t('statistics.search_order_placeholder')"
                  prepend-inner-icon="mdi-magnify"
                  hide-details
                  color="purple"
                  @update:model-value="debouncedSearch"
                ></v-text-field>
              </v-col>

              <v-col cols="12" sm="6" md="3">
                <v-text-field
                  v-model="filters.startDate"
                  type="date"
                  density="comfortable"
                  variant="outlined"
                  :label="$t('statistics.from_date')"
                  hide-details
                  color="rgb(14 22 67)"
                  @change="fetchDashboardData"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-text-field
                  v-model="filters.endDate"
                  type="date"
                  density="comfortable"
                  variant="outlined"
                  :label="$t('statistics.to_date')"
                  hide-details
                  color="rgb(14 22 67)"
                  @change="fetchDashboardData"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="2">
                <v-btn
                  block
                  color="rgb(14 22 67)"
                  height="48"
                  class="text-body-1 font-weight-bold text-white"
                  @click="fetchDashboardData"
                  :loading="isLoading"
                >
                  {{ $t("statistics.apply_filter") }}
                </v-btn>
              </v-col>
            </v-row>
          </v-card>

          <v-card elevation="0" rounded="lg" class="mb-8 pa-2 border">
            <v-row dense align="center" class="ma-1">
              <v-col cols="12" md="3">
                <v-autocomplete
                  v-model="filters.seller"
                  :items="sellersList"
                  :label="$t('statistics.filter_by_seller')"
                  variant="underlined"
                  color="purple"
                  prepend-icon="mdi-store-search"
                  hide-details
                  class="font-weight-medium"
                  @update:model-value="fetchDashboardData"
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" sm="6" md="3">
                <v-select
                  v-model="filters.status"
                  :items="statusOptions"
                  item-title="title"
                  item-value="value"
                  :label="$t('statistics.order_status')"
                  variant="underlined"
                  color="purple"
                  prepend-icon="mdi-list-status"
                  hide-details
                  @update:model-value="fetchDashboardData"
                ></v-select>
              </v-col>

              <v-col cols="12" sm="6" md="6" class="text-end">
                <v-btn
                  color="rgb(14 22 67)"
                  variant="tonal"
                  prepend-icon="mdi-file-excel"
                >
                  {{ $t("statistics.export_excel") }}
                </v-btn>
              </v-col>
            </v-row>
          </v-card>

          <v-row>
            <template v-if="isLoading">
              <v-col v-for="n in 9" :key="n" cols="12" sm="6" md="4">
                <v-skeleton-loader
                  type="card"
                  height="160"
                  class="rounded-lg"
                ></v-skeleton-loader>
              </v-col>
            </template>

            <template v-else>
              <v-col
                v-for="(stat, index) in stats"
                :key="index"
                cols="12"
                sm="6"
                md="4"
              >
                <v-hover v-slot="{ isHovering, props }">
                  <v-card
                    v-bind="props"
                    :elevation="isHovering ? 6 : 1"
                    :class="['stat-card', { 'on-hover': isHovering }]"
                    rounded="lg"
                    class="cursor-pointer h-100 py-4"
                  >
                    <v-card-text
                      class="d-flex flex-column align-center justify-center text-center"
                    >
                      <div :class="`icon-bg mb-4 bg-${stat.color}-lighten-5`">
                        <v-icon :color="stat.color" size="36">{{
                          stat.icon
                        }}</v-icon>
                      </div>

                      <div
                        class="text-h6 text-grey-darken-2 font-weight-medium mb-2"
                      >
                        {{ stat.title }}
                      </div>

                      <h3 class="text-h4 font-weight-bold text-grey-darken-4">
                        {{ stat.value }}
                      </h3>

                      <v-divider
                        :color="stat.color"
                        class="mt-4 border-opacity-100"
                        thickness="4"
                        style="width: 50px; border-radius: 4px"
                      ></v-divider>
                    </v-card-text>
                  </v-card>
                </v-hover>
              </v-col>
            </template>
          </v-row>
          <v-row class="mt-6" justify="center">
            <v-col cols="12" md="4">
              <v-btn
                block
                variant="outlined"
                color="primary"
                append-icon="mdi-arrow-left"
                size="large"
                class="dashed-border-btn"
              >
                {{ $t("statistics.advanced_stats") }}
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </div>
  </v-app>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();

// حالة التحميل
const isLoading = ref(true);
const lastUpdated = ref("");

// الفلاتر
const filters = reactive({
  searchQuery: "",
  startDate: "",
  endDate: "",
  seller: null,
  status: "all",
});

const statusOptions = computed(() => [
  { title: t("statistics.status_all"), value: "all" },
  { title: t("statistics.status_completed"), value: "completed" },
  { title: t("statistics.status_cancelled"), value: "cancelled" },
  { title: t("statistics.status_processing"), value: "processing" },
]);

// قوائم وهمية
const sellersList = [
  "متجر الأناقة",
  "محل التقنية",
  "بوتيك الموضة",
  "سوق الجملة",
];

// بيانات الإحصائيات (فارغة في البداية)
const stats = ref([]);

// دالة محاكاة جلب البيانات من الـ API
const fetchDashboardData = (showLoading = true) => {
  if (showLoading) isLoading.value = true;

  // محاكاة تأخير الشبكة (1.5 ثانية)
  setTimeout(() => {
    stats.value = [
      {
        title: t("statistics.total_orders"),
        value: "15,000",
        icon: "mdi-shopping",
        color: "purple",
      },
      {
        title: t("statistics.delivered_orders"),
        value: "12,450",
        icon: "mdi-check-circle",
        color: "success",
      },
      {
        title: t("statistics.on_delivery"),
        value: "1,200",
        icon: "mdi-truck-fast",
        color: "info",
      },
      {
        title: t("statistics.awaiting_confirmation"),
        value: "320",
        icon: "mdi-clock-alert",
        color: "warning",
      },
      {
        title: t("statistics.cancelled_orders"),
        value: "45",
        icon: "mdi-close-circle",
        color: "error",
      },
      {
        title: t("statistics.expected_commission"),
        value: `5,000 ${t("products.currency")}`,
        icon: "mdi-cash-clock",
        color: "deep-purple",
      },
      {
        title: t("statistics.due_commission"),
        value: `15,000 ${t("products.currency")}`,
        icon: "mdi-cash-multiple",
        color: "teal",
      },
      {
        title: t("statistics.paid_commission"),
        value: `10,000 ${t("products.currency")}`,
        icon: "mdi-hand-coin",
        color: "indigo",
      },
      {
        title: t("statistics.delivery_rate"),
        value: "82.5%",
        icon: "mdi-chart-pie",
        color: "cyan",
      },
    ];

    const now = new Date();
    lastUpdated.value = now.toLocaleTimeString(
      locale.value === "ar" ? "ar-EG" : "en-US"
    );

    if (showLoading) isLoading.value = false;
  }, 1000);
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchDashboardData(false), 15000); // 15 seconds
};

onMounted(() => {
  fetchDashboardData();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchDashboardData(false);
      startPolling();
    } else {
      if (pollInterval) clearInterval(pollInterval);
    }
  });
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});
</script>

<style scoped>
.bg-grey-lighten-4 {
  background-color: #f8f9fa !important;
}

/* تنسيق الكارد للحركة */
.stat-card {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.stat-card.on-hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px -10px rgba(156, 39, 176, 0.3) !important;
  border-color: rgba(156, 39, 176, 0.2);
}

/* خلفية الأيقونة */
.icon-bg {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  transition: transform 0.3s ease;
}

.on-hover .icon-bg {
  transform: scale(1.1);
}
.dashed-border-btn {
  border-style: dashed;
  border-width: 2px;
  transition: background-color 0.3s;
}

.dashed-border-btn:hover {
  background-color: rgba(98, 0, 234, 0.05);
}
</style>
