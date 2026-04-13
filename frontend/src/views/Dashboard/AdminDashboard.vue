<template>
  <div
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    class="pa-6 bg-grey-lighten-4"
    style="min-height: 100vh"
  >
    <!-- Header Section -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center flex-wrap gap-4">
          <div>
            <h2 class="text-h5 font-weight-black text-blue-grey-darken-4">
              {{ $t("dashboard.dashboard_title") }}
            </h2>
            <p class="text-subtitle-1 text-grey-darken-1 mt-1">
              {{ $t("dashboard.dashboard_subtitle") }}
            </p>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="white"
              prepend-icon="mdi-refresh"
              class="rounded-lg font-weight-bold"
              elevation="1"
              @click="fetchStats"
              :loading="loading"
            >
              {{ $t("dashboard.refresh_data") }}
            </v-btn>
            <v-btn
              color="primary"
              prepend-icon="mdi-download"
              class="rounded-lg font-weight-bold shadow-btn px-6"
              elevation="2"
            >
              {{ $t("dashboard.download_report") }}
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Quick Stats Cards -->
    <v-row>
      <v-col cols="12" sm="6" md="3" v-for="(stat, i) in quickStats" :key="i">
        <v-card
          elevation="1"
          class="rounded-xl card-hover overflow-hidden border"
        >
          <v-skeleton-loader
            v-if="loading"
            type="list-item-two-line"
            class="bg-transparent"
          ></v-skeleton-loader>
          <v-card-text v-else class="pa-5">
            <div class="d-flex justify-space-between align-start mb-4">
              <div>
                <div class="text-grey-darken-1 text-subtitle-2 mb-1">
                  {{ stat.title }}
                </div>
                <div class="text-h5 font-weight-black">
                  {{ stat.value }}
                </div>
              </div>
              <v-avatar
                :color="stat.color"
                size="56"
                variant="tonal"
                rounded="xl"
              >
                <v-icon size="32">{{ stat.icon }}</v-icon>
              </v-avatar>
            </div>

            <div class="d-flex align-center mt-2">
              <div
                v-if="stat.trend !== 'neutral'"
                :class="`text-${
                  stat.trend === 'up' ? 'success' : 'error'
                } font-weight-bold d-flex align-center text-caption px-2 py-0.5 rounded-pill bg-${
                  stat.trend === 'up' ? 'success' : 'error'
                }-lighten-5`"
                dir="ltr"
              >
                <v-icon size="16" class="mr-1">{{
                  stat.trend === "up" ? "mdi-trending-up" : "mdi-trending-down"
                }}</v-icon>
                {{ stat.percentage }}%
              </div>
              <span class="text-caption text-grey ml-2">{{
                $t("dashboard.compare_last_month")
              }}</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Charts Section -->
    <v-row class="mt-4">
      <!-- Sales Chart -->
      <v-col cols="12" md="8">
        <v-card elevation="1" class="rounded-xl border h-100">
          <v-card-title class="font-weight-bold pa-5 d-flex align-center">
            <v-icon color="primary" class="ml-2">mdi-chart-line</v-icon>
            {{ $t("dashboard.sales_analysis_title") }}
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <v-skeleton-loader
              v-if="loading"
              type="image"
              height="350"
              class="rounded-lg"
            ></v-skeleton-loader>
            <apexchart
              v-else
              type="area"
              height="350"
              :options="chartOptions"
              :series="chartSeries"
            ></apexchart>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Category Pie Chart -->
      <v-col cols="12" md="4">
        <v-card elevation="1" class="rounded-xl border h-100">
          <v-card-title class="font-weight-bold pa-5">
            <v-icon color="secondary" class="ml-2">mdi-chart-pie</v-icon>
            {{ $t("dashboard.category_distribution_title") }}
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <v-skeleton-loader
              v-if="loading"
              type="image"
              height="350"
              class="rounded-lg"
            ></v-skeleton-loader>
            <apexchart
              v-else
              type="donut"
              height="350"
              :options="donutOptions"
              :series="donutSeries"
            ></apexchart>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Bottom Tables Section -->
    <v-row class="mt-4">
      <!-- Recent Orders -->
      <v-col cols="12" md="7">
        <v-card elevation="1" class="rounded-xl border">
          <v-card-title
            class="font-weight-bold pa-5 d-flex justify-space-between align-center"
          >
            <div class="d-flex align-center">
              <v-icon color="info" class="ml-2">mdi-clock-outline</v-icon>
              {{ $t("dashboard.recent_orders_title") }}
            </div>
            <v-btn
              variant="tonal"
              color="primary"
              to="/Dashboard/MangOrder"
              size="small"
              class="rounded-lg"
            >
              {{ $t("dashboard.view_all_stats") }}
            </v-btn>
          </v-card-title>
          <v-divider></v-divider>
          <div class="table-responsive">
            <v-table class="bg-transparent border-0">
              <thead>
                <tr class="bg-grey-lighten-5">
                  <th class="text-right">{{ $t("dashboard.order_th") }}</th>
                  <th class="text-right">{{ $t("dashboard.customer_th") }}</th>
                  <th class="text-right">{{ $t("dashboard.amount_th") }}</th>
                  <th class="text-center">{{ $t("dashboard.status_th") }}</th>
                </tr>
              </thead>
              <tbody v-if="loading">
                <tr v-for="i in 5" :key="`skel-order-${i}`">
                  <td class="py-4">
                    <v-skeleton-loader type="text" width="40" />
                  </td>
                  <td class="py-4">
                    <v-skeleton-loader type="text" width="100" />
                  </td>
                  <td class="py-4">
                    <v-skeleton-loader type="text" width="60" />
                  </td>
                  <td class="py-4">
                    <v-skeleton-loader type="chip" class="mx-auto" />
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr
                  v-for="order in recentOrders"
                  :key="order.id"
                  class="hover-row"
                >
                  <td class="font-weight-bold">#{{ order.id }}</td>
                  <td>
                    {{
                      order.user
                        ? order.user.name
                        : $t("dashboard.guest_customer")
                    }}
                  </td>
                  <td class="font-weight-bold text-success">
                    {{ Number(order.total).toLocaleString() }}
                    {{ $t("products.currency") }}
                  </td>
                  <td class="text-center">
                    <v-chip
                      size="x-small"
                      :color="getStatusColor(order.status)"
                      variant="flat"
                      class="font-weight-bold"
                    >
                      {{ getStatusText(order.status) }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </div>
        </v-card>
      </v-col>

      <!-- Top Selling Products -->
      <v-col cols="12" md="5">
        <v-card elevation="1" class="rounded-xl border">
          <v-card-title class="font-weight-bold pa-5">
            <v-icon color="warning" class="ml-2">mdi-star-face</v-icon>
            {{ $t("dashboard.top_selling_title") }}
          </v-card-title>
          <v-divider></v-divider>
          <v-list class="pa-0" v-if="loading">
            <v-list-item
              v-for="i in 5"
              :key="`skel-top-${i}`"
              class="pa-4 border-b"
            >
              <v-skeleton-loader type="list-item-avatar-two-line" />
            </v-list-item>
          </v-list>
          <v-list class="pa-0" v-else>
            <v-list-item
              v-for="(prod, idx) in stats.top_products"
              :key="prod.id"
              class="pa-4 border-b"
            >
              <template v-slot:prepend>
                <v-avatar size="50" rounded="lg" color="grey-lighten-4">
                  <v-img :src="prod.thumbnail" cover></v-img>
                </v-avatar>
              </template>
              <v-list-item-title class="font-weight-bold text-subtitle-2 mb-1">
                {{ prod.title }}
              </v-list-item-title>
              <v-list-item-subtitle class="text-primary font-weight-bold">
                {{
                  $t("dashboard.sold_count_label", { count: prod.sold_count })
                }}
              </v-list-item-subtitle>
              <template v-slot:append>
                <div class="text-h6 font-weight-black text-grey-lighten-1">
                  #{{ idx + 1 }}
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import api from "../../axios";

const loading = ref(true);
const stats = ref({
  revenue: 0,
  profit: 0,
  orders: 0,
  products: 0,
  users: 0,
  revenue_growth: 0,
  orders_growth: 0,
  daily_sales: [],
  top_products: [],
  category_distribution: [],
  profit_growth: 0,
  users_growth: 0,
});

const displayStats = ref({
  revenue: 0,
  profit: 0,
  orders: 0,
  users: 0,
});

const animateValue = (key, target) => {
  const duration = 1500;
  const start = displayStats.value[key];
  const startTime = performance.now();

  const update = (now) => {
    const elapsed = now - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easeOutQuad = (t) => t * (2 - t);
    const current = start + (target - start) * easeOutQuad(progress);
    displayStats.value[key] = Math.floor(current);

    if (progress < 1) {
      requestAnimationFrame(update);
    } else {
      displayStats.value[key] = target;
    }
  };
  requestAnimationFrame(update);
};

const recentOrders = ref([]);

const quickStats = computed(() => [
  {
    title: t("dashboard.total_sales"),
    value:
      Number(displayStats.value.revenue).toLocaleString() +
      " " +
      t("products.currency"),
    icon: "mdi-cash-multiple",
    color: "success",
    percentage: stats.value.revenue_growth,
    trend: stats.value.revenue_growth >= 0 ? "up" : "down",
  },
  {
    title: t("dashboard.net_profit"),
    value:
      Number(displayStats.value.profit).toLocaleString() +
      " " +
      t("products.currency"),
    icon: "mdi-wallet",
    color: "primary",
    percentage: stats.value.profit_growth,
    trend: stats.value.profit_growth >= 0 ? "up" : "down",
  },
  {
    title: t("dashboard.total_orders"),
    value: displayStats.value.orders.toString(),
    icon: "mdi-cart-check",
    color: "info",
    percentage: stats.value.orders_growth,
    trend: stats.value.orders_growth >= 0 ? "up" : "down",
  },
  {
    title: t("dashboard.total_customers"),
    value: displayStats.value.users.toString(),
    icon: "mdi-account-group",
    color: "warning",
    percentage: stats.value.users_growth,
    trend: stats.value.users_growth >= 0 ? "up" : "down",
  },
]);

// Area Chart Options
const chartOptions = computed(() => ({
  chart: { toolbar: { show: false }, fontFamily: "Cairo" },
  dataLabels: { enabled: false },
  stroke: { curve: "smooth", width: 3 },
  colors: ["rgb(var(--v-theme-primary))"],
  xaxis: {
    categories: stats.value.daily_sales.map((d) => d.label),
  },
  tooltip: {
    y: {
      formatter: (val) => val.toLocaleString() + " " + t("products.currency"),
    },
  },
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.2,
      stops: [0, 90, 100],
    },
  },
}));

const chartSeries = computed(() => [
  {
    name: t("dashboard.sales_legend"),
    data: stats.value.daily_sales.map((d) => d.revenue),
  },
]);

// Donut Chart Options
const donutOptions = computed(() => ({
  labels: stats.value.category_distribution.map((c) => c.name),
  chart: { fontFamily: "Cairo" },
  dataLabels: { enabled: false },
  legend: { position: "bottom" },
  colors: ["#1e88e5", "#43a047", "#fdd835", "#e53935", "#8e24aa"],
}));

const donutSeries = computed(() =>
  stats.value.category_distribution.map((c) => c.count)
);

const fetchStats = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const res = await api.get(`/admin/statistics?t=${Date.now()}`);
    stats.value = res.data;
    recentOrders.value = res.data.recent_orders || [];

    // Trigger animations
    animateValue("revenue", stats.value.revenue);
    animateValue("profit", stats.value.profit);
    animateValue("orders", stats.value.orders);
    animateValue("users", stats.value.users);
  } catch (e) {
    console.error("Error fetching statistics:", e);
  } finally {
    loading.value = false;
  }
};

const getStatusColor = (status) => {
  const map = {
    pending: "warning",
    processing: "info",
    shipped: "primary",
    completed: "success",
    cancelled: "error",
    returned: "grey",
  };
  return map[status] || "grey";
};

const getStatusText = (status) => {
  const map = {
    pending: t("sales.orders.status.pending"),
    processing: t("sales.orders.status.processing"),
    shipped: t("sales.orders.status.shipped"),
    completed: t("sales.orders.status.completed"),
    cancelled: t("sales.orders.status.cancelled"),
    returned: t("sales.orders.status.returned"),
  };
  return map[status] || status;
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchStats(false), 15000); // 15 seconds
};

onMounted(() => {
  fetchStats();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchStats(false);
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

<script>
// For registering components locally if needed
import VueApexCharts from "vue3-apexcharts";
export default {
  components: {
    apexchart: VueApexCharts,
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.shadow-btn {
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3) !important;
}
.card-hover {
  transition: all 0.3s ease;
}
.card-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
}
.hover-row {
  transition: background-color 0.2s ease;
}
.hover-row:hover {
  background-color: #f8f9fa;
}
.gap-2 {
  gap: 8px;
}
.gap-4 {
  gap: 16px;
}
.border {
  border: 1px solid #f0f0f0 !important;
}
.border-b {
  border-bottom: 1px solid #f0f0f0 !important;
}
</style>
