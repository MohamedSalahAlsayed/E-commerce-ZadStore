<template>
  <v-container fluid class="pa-6 advanced-reports">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-8">
      <div>
        <h1 class="text-h4 font-weight-black text-grey-darken-4 mb-2">
          {{ $t("dashboard.advanced_reports") || "مركز التقارير والتحليلات" }}
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          رؤية شاملة لأداء المتجر، المبيعات، والأرباح الحقيقية.
        </p>
      </div>
      <div class="d-flex gap-3">
        <v-btn-toggle
          v-model="selectedPeriod"
          mandatory
          color="primary"
          density="compact"
          variant="tonal"
        >
          <v-btn value="7">7 أيام</v-btn>
          <v-btn value="30">30 يوم</v-btn>
          <v-btn value="90">90 يوم</v-btn>
        </v-btn-toggle>
        <v-btn
          icon="mdi-refresh"
          variant="text"
          color="primary"
          @click="fetchData"
          :loading="loading"
        ></v-btn>
      </div>
    </div>

    <!-- Stats Overview -->
    <v-row class="mb-8">
      <v-col cols="12" sm="6" md="3" v-for="(stat, i) in summaryStats" :key="i">
        <v-card
          class="rounded-xl pa-5 border-0 elevation-2 stat-card overflow-hidden"
        >
          <div class="d-flex justify-space-between align-start">
            <div>
              <div
                class="text-caption text-grey-darken-1 font-weight-bold mb-1 uppercase tracking-wider"
              >
                {{ stat.title }}
              </div>
              <div class="text-h4 font-weight-black d-flex align-center">
                {{ stat.value }}
                <span v-if="stat.suffix" class="text-caption ms-1 text-grey">{{
                  stat.suffix
                }}</span>
              </div>
            </div>
            <v-avatar :color="stat.color + '-lighten-5'" size="50" rounded="lg">
              <v-icon :color="stat.color" size="28">{{ stat.icon }}</v-icon>
            </v-avatar>
          </div>
          <div
            class="mt-4 d-flex align-center text-caption"
            :class="stat.trendColor + '-text'"
          >
            <v-icon size="16" class="me-1">{{ stat.trendIcon }}</v-icon>
            {{ stat.trendText }}
          </div>
          <!-- Decorative Background Icon -->
          <v-icon class="card-bg-icon">{{ stat.icon }}</v-icon>
        </v-card>
      </v-col>
    </v-row>

    <!-- Main Charts Row -->
    <v-row class="mb-8">
      <!-- Sales Trend Chart -->
      <v-col cols="12" lg="8">
        <v-card class="rounded-xl pa-6 border-0 elevation-2 h-100">
          <div class="d-flex align-center justify-space-between mb-6">
            <h3 class="text-h6 font-weight-bold">
              تحليل المبيعات (Revenue Trend)
            </h3>
            <v-chip size="small" color="primary" variant="tonal">مباشر</v-chip>
          </div>
          <div style="height: 350px">
            <apexchart
              type="area"
              height="350"
              :options="trendChartOptions"
              :series="trendSeries"
            ></apexchart>
          </div>
        </v-card>
      </v-col>

      <!-- Category Performance -->
      <v-col cols="12" lg="4">
        <v-card class="rounded-xl pa-6 border-0 elevation-2 h-100">
          <h3 class="text-h6 font-weight-bold mb-6">المبيعات حسب الفئات</h3>
          <div class="d-flex align-center justify-center" style="height: 350px">
            <apexchart
              type="donut"
              width="100%"
              :options="categoryChartOptions"
              :series="categorySeries"
            ></apexchart>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Bottom Row: Top Products & Low Stock -->
    <v-row>
      <!-- Top Selling Products -->
      <v-col cols="12" md="6">
        <v-card class="rounded-xl pa-6 border-0 elevation-2">
          <div class="d-flex align-center justify-space-between mb-6">
            <h3 class="text-h6 font-weight-bold">المنتجات الأكثر مبيعاً</h3>
            <v-btn variant="text" color="primary" size="small">عرض الكل</v-btn>
          </div>
          <v-list class="pa-0">
            <v-list-item
              v-for="(prod, i) in topProducts"
              :key="i"
              class="px-0 py-3 border-b last-border-0"
            >
              <template v-slot:prepend>
                <v-avatar
                  size="50"
                  rounded="lg"
                  color="grey-lighten-4"
                  class="me-3"
                >
                  <v-img :src="prod.thumbnail" cover></v-img>
                </v-avatar>
              </template>
              <v-list-item-title class="font-weight-bold text-body-1">{{
                prod.title
              }}</v-list-item-title>
              <v-list-item-subtitle class="text-caption"
                >{{ prod.sold_count }} قطعة مباعة</v-list-item-subtitle
              >
              <template v-slot:append>
                <div class="text-right">
                  <div class="font-weight-black text-primary">
                    {{ prod.revenue }} ج.م
                  </div>
                  <div class="text-caption text-grey">إجمالي العائد</div>
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>

      <!-- Low Stock Alerts -->
      <v-col cols="12" md="6">
        <v-card class="rounded-xl pa-6 border-0 elevation-2">
          <div class="d-flex align-center justify-space-between mb-6">
            <h3
              class="text-h6 font-weight-bold text-error d-flex align-center gap-2"
            >
              <v-icon>mdi-alert-circle-outline</v-icon>
              تنبيهات انخفاض المخزون
            </h3>
            <v-btn
              variant="text"
              color="error"
              size="small"
              to="/Dashboard/InventoryManager"
              >طلب توريد</v-btn
            >
          </div>
          <v-table density="comfortable" class="low-stock-table">
            <thead>
              <tr>
                <th>المنتج</th>
                <th class="text-center">المخزون الحالي</th>
                <th class="text-center">الحالة</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prod in lowStock" :key="prod.id">
                <td>
                  <div class="d-flex align-center gap-2 py-1">
                    <v-avatar size="32" rounded="sm">
                      <v-img :src="prod.thumbnail"></v-img>
                    </v-avatar>
                    <span class="text-truncate" style="max-width: 200px">{{
                      prod.title
                    }}</span>
                  </div>
                </td>
                <td class="text-center font-weight-bold">{{ prod.stock }}</td>
                <td class="text-center">
                  <v-chip
                    :color="prod.stock === 0 ? 'error' : 'warning'"
                    size="x-small"
                    class="font-weight-black"
                  >
                    {{ prod.stock === 0 ? "نفذ" : "منخفض" }}
                  </v-chip>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import axios from "@/axios";
import { useI18n } from "vue-i18n";

useI18n(); // Initialized but variables not needed in script setup
const loading = ref(false);
const selectedPeriod = ref("30");

// Data State
const statsData = ref({
  summary: { revenue: 0, orders: 0, profit: 0, avg_order_value: 0 },
  trends: [],
  category_performance: [],
  low_stock: [],
});
const topProducts = ref([]);

const summaryStats = computed(() => [
  {
    title: "إجمالي المبيعات",
    value: statsData.value.summary.revenue.toLocaleString(),
    suffix: "ج.م",
    icon: "mdi-currency-usd",
    color: "primary",
    trendIcon: "mdi-trending-up",
    trendText: "+12.5% عن الشهر السابق",
    trendColor: "success",
  },
  {
    title: "الربح الصافي (Net)",
    value: statsData.value.summary.profit.toLocaleString(),
    suffix: "ج.م",
    icon: "mdi-hand-coin",
    color: "success",
    trendIcon: "mdi-trending-up",
    trendText: "+8.2% تحسن في الهامش",
    trendColor: "success",
  },
  {
    title: "إجمالي الطلبات",
    value: statsData.value.summary.orders.toLocaleString(),
    suffix: "طلب",
    icon: "mdi-cart-outline",
    color: "info",
    trendIcon: "mdi-trending-up",
    trendText: "+24 طلب جديد",
    trendColor: "info",
  },
  {
    title: "متوسط قيمة الطلب",
    value: Math.round(statsData.value.summary.avg_order_value).toLocaleString(),
    suffix: "ج.م",
    icon: "mdi-calculator",
    color: "warning",
    trendIcon: "mdi-minus",
    trendText: "ثابت تقريباً",
    trendColor: "grey",
  },
]);

// Chart Options
const trendSeries = computed(() => [
  {
    name: "المبيعات",
    data: statsData.value.trends.map((t) => t.revenue),
  },
]);

const trendChartOptions = computed(() => ({
  chart: {
    fontFamily: "Inter, sans-serif",
    toolbar: { show: false },
    zoom: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: "smooth", width: 3, colors: ["#fb923c"] },
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100],
    },
  },
  markers: {
    size: 4,
    colors: ["#fb923c"],
    strokeColors: "#fff",
    strokeWidth: 2,
  },
  grid: { borderColor: "#f1f5f9", strokeDashArray: 4 },
  xaxis: {
    categories: statsData.value.trends.map((t) => t.date),
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { style: { colors: "#64748b" } },
  },
  yaxis: {
    labels: { style: { colors: "#64748b" }, formatter: (v) => `${v} ج.م` },
  },
  tooltip: { theme: "light", x: { show: true }, marker: { show: false } },
}));

const categorySeries = computed(() =>
  statsData.value.category_performance.map((c) => c.value)
);
const categoryChartOptions = computed(() => ({
  labels: statsData.value.category_performance.map((c) => c.name),
  colors: ["#fb923c", "#0ea5e9", "#10b981", "#f43f5e", "#8b5cf6"],
  plotOptions: {
    pie: {
      donut: {
        size: "75%",
        labels: {
          show: true,
          total: {
            show: true,
            label: "إجمالي الوحدات",
            formatter: () =>
              statsData.value.category_performance.reduce(
                (a, b) => a + b.value,
                0
              ),
          },
        },
      },
    },
  },
  legend: { position: "bottom", labels: { colors: "#64748b" } },
  stroke: { show: false },
  dataLabels: { enabled: false },
}));

const lowStock = computed(() => statsData.value.low_stock);

const fetchData = async () => {
  loading.value = true;
  try {
    const [overviewRes, productRes] = await Promise.all([
      axios.get(`/admin/analytics/overview?days=${selectedPeriod.value}`),
      axios.get("/admin/analytics/products"),
    ]);
    statsData.value = overviewRes.data;
    topProducts.value = productRes.data.top_sellers;
  } catch (error) {
    console.error("Error fetching analytics:", error);
  } finally {
    loading.value = false;
  }
};

watch(selectedPeriod, fetchData);

onMounted(fetchData);
</script>

<style scoped>
.advanced-reports {
  background-color: #f8fafc;
  min-height: 100vh;
}

.stat-card {
  position: relative;
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.card-bg-icon {
  position: absolute;
  right: -10px;
  bottom: -10px;
  font-size: 80px !important;
  opacity: 0.03;
  transform: rotate(-15deg);
}

.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}

.uppercase {
  text-transform: uppercase;
}
.tracking-wider {
  letter-spacing: 0.05em;
}

.low-stock-table :deep(th) {
  font-weight: bold !important;
  color: #64748b !important;
  font-size: 0.75rem !important;
  text-transform: uppercase;
}

.last-border-0:last-child {
  border-bottom: none !important;
}
</style>
