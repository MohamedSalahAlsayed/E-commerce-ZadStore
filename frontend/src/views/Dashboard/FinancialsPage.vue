<template>
  <v-container fluid class="pa-6 bg-grey-lighten-4 min-vh-100">
    <!-- Header -->
    <v-row align="center" class="mb-6">
      <v-col cols="12" md="6">
        <h1 class="text-h4 font-weight-bold grey-darken-3 d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-finance</v-icon>
          {{ $t("financials.title") }}
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          {{ $t("financials.subtitle") }}
        </p>
      </v-col>
      <v-col cols="12" md="6" class="text-md-right">
        <v-btn
          color="primary"
          prepend-icon="mdi-refresh"
          @click="fetchFinancialData"
          :loading="loading"
          class="px-6 rounded-pill elevation-2"
        >
          {{ $t("dashboard.refresh_data") }}
        </v-btn>
      </v-col>
    </v-row>

    <!-- Stats Cards -->
    <v-row v-if="summary">
      <v-col cols="12" sm="6" md="3" v-for="(card, i) in statCards" :key="i">
        <v-card
          class="rounded-xl overflow-hidden elevation-3 border-0 transition-swing"
          hover
        >
          <div :class="`pa-4 bg-gradient-${card.color}`">
            <div class="d-flex justify-space-between align-center mb-4">
              <v-avatar
                :color="card.color + '-lighten-4'"
                size="48"
                class="elevation-1"
              >
                <v-icon :color="card.color" size="28">{{ card.icon }}</v-icon>
              </v-avatar>
              <v-chip
                size="small"
                :color="card.color + '-darken-1'"
                class="font-weight-bold"
                variant="flat"
              >
                {{ card.trend }}
              </v-chip>
            </div>
            <div class="text-subtitle-2 text-grey-darken-1 mb-1">
              {{ card.title }}
            </div>
            <div class="text-h5 font-weight-black grey-darken-4">
              {{ formatCurrency(card.value) }}
              <span
                v-if="card.suffix"
                class="text-caption font-weight-medium text-grey ms-1"
                >{{ card.suffix }}</span
              >
            </div>
          </div>
          <v-progress-linear
            :model-value="85"
            :color="card.color"
            height="4"
          ></v-progress-linear>
        </v-card>
      </v-col>
    </v-row>

    <!-- Charts Section -->
    <v-row class="mt-4">
      <v-col cols="12" lg="8">
        <v-card class="rounded-xl elevation-3 border-0 pa-6 h-100">
          <div class="d-flex justify-space-between align-center mb-6">
            <v-card-title class="pa-0 text-h6 font-weight-bold grey-darken-3">
              {{ $t("financials.sales_growth") }}
            </v-card-title>
            <v-btn-toggle
              v-model="chartPeriod"
              density="compact"
              color="primary"
              mandatory
              variant="outlined"
              class="rounded-lg"
              @update:model-value="fetchFinancialData"
            >
              <v-btn value="365">{{ $t("financials.year") }}</v-btn>
              <v-btn value="30">{{ $t("financials.30_days") }}</v-btn>
              <v-btn value="7">{{ $t("financials.7_days") }}</v-btn>
            </v-btn-toggle>
          </div>
          <div v-if="!loading" class="chart-container">
            <apexchart
              type="area"
              height="350"
              :options="chartOptions"
              :series="chartSeries"
            ></apexchart>
          </div>
          <v-skeleton-loader
            v-else
            type="image"
            height="350"
            class="rounded-lg"
          ></v-skeleton-loader>
        </v-card>
      </v-col>

      <v-col cols="12" lg="4">
        <v-card class="rounded-xl elevation-3 border-0 pa-6 h-100">
          <v-card-title
            class="pa-0 text-h6 font-weight-bold grey-darken-3 mb-6"
          >
            {{ $t("financials.top_profitable_products") }}
          </v-card-title>
          <v-list lines="two" v-if="!loading">
            <v-list-item
              v-for="(item, i) in topProducts"
              :key="i"
              class="px-0 py-3 border-b-light border-opacity-50"
            >
              <template v-slot:prepend>
                <div class="product-rank mr-4 elevation-1">{{ i + 1 }}</div>
              </template>
              <v-list-item-title class="font-weight-bold text-body-1">{{
                item.name
              }}</v-list-item-title>
              <v-list-item-subtitle class="text-primary font-weight-bold mt-1">
                {{ $t("financials.profit_label") }}
                {{ formatCurrency(item.total_profit) }}
              </v-list-item-subtitle>
              <template v-slot:append>
                <div class="text-right">
                  <div class="text-caption text-grey">
                    {{ $t("financials.sold_label") }}
                  </div>
                  <div class="text-body-2 font-weight-bold">
                    {{ item.total_sold }} {{ $t("financials.items_unit") }}
                  </div>
                </div>
              </template>
            </v-list-item>
          </v-list>
          <v-skeleton-loader
            v-else
            type="list-item-avatar-three-line@5"
          ></v-skeleton-loader>

          <v-divider class="my-4"></v-divider>
          <v-btn block variant="tonal" color="primary" class="rounded-lg py-4">
            {{ $t("financials.view_full_report") }}
          </v-btn>
        </v-card>
      </v-col>
    </v-row>

    <!-- Detailed Ledger (Summary Table) -->
    <v-row class="mt-4">
      <v-col cols="12">
        <v-card class="rounded-xl elevation-3 border-0 overflow-hidden">
          <v-table class="custom-table" hover>
            <thead class="bg-grey-lighten-3">
              <tr>
                <th class="text-right font-weight-bold py-4">
                  {{ $t("financials.financial_statement") }}
                </th>
                <th class="text-center font-weight-bold py-4">
                  {{ $t("financials.total_value") }}
                </th>
                <th class="text-center font-weight-bold py-4">
                  {{ $t("dashboard.status_th") }}
                </th>
                <th class="text-center font-weight-bold py-4">
                  {{ $t("financials.percentage_of_total") }}
                </th>
              </tr>
            </thead>
            <tbody v-if="loading">
              <tr v-for="i in 4" :key="`skel-ledger-${i}`">
                <td class="px-4 py-4">
                  <v-skeleton-loader type="text" width="150" />
                </td>
                <td class="px-4 py-4">
                  <v-skeleton-loader type="text" width="100" class="mx-auto" />
                </td>
                <td class="px-4 py-4">
                  <v-skeleton-loader type="chip" class="mx-auto" />
                </td>
                <td class="px-4 py-4">
                  <v-skeleton-loader type="text" width="40" class="mx-auto" />
                </td>
              </tr>
            </tbody>
            <tbody v-else-if="summary">
              <tr>
                <td class="font-weight-bold text-grey-darken-2">
                  {{ $t("financials.revenue_label") }}
                </td>
                <td class="text-center font-weight-black text-success">
                  {{ formatCurrency(summary.total_revenue) }}
                </td>
                <td class="text-center">
                  <v-chip size="small" color="success">{{
                    $t("financials.revenue_chip")
                  }}</v-chip>
                </td>
                <td class="text-center">100%</td>
              </tr>
              <tr>
                <td class="font-weight-bold text-grey-darken-2">
                  {{ $t("financials.refunds_label") }}
                </td>
                <td class="text-center font-weight-black text-warning text-ltr">
                  - {{ formatCurrency(summary.total_refunded) }}
                </td>
                <td class="text-center">
                  <v-chip size="small" color="warning">{{
                    $t("financials.refunded_chip")
                  }}</v-chip>
                </td>
                <td class="text-center text-warning">
                  {{
                    summary.total_revenue > 0
                      ? Math.round(
                          (summary.total_refunded / summary.total_revenue) * 100
                        )
                      : 0
                  }}%
                </td>
              </tr>
              <tr>
                <td class="font-weight-bold text-grey-darken-2">
                  {{ $t("financials.costs_label") }}
                </td>
                <td class="text-center font-weight-black text-error text-ltr">
                  - {{ formatCurrency(summary.total_costs) }}
                </td>
                <td class="text-center">
                  <v-chip size="small" color="error">{{
                    $t("financials.expenses_chip")
                  }}</v-chip>
                </td>
                <td class="text-center text-error">
                  {{
                    summary.total_revenue > 0
                      ? Math.round(
                          (summary.total_costs / summary.total_revenue) * 100
                        )
                      : 0
                  }}%
                </td>
              </tr>
              <tr class="bg-blue-lighten-5">
                <td class="font-weight-black text-primary text-h6">
                  {{ $t("financials.net_profit_label") }}
                </td>
                <td class="text-center font-weight-black text-primary text-h6">
                  {{ formatCurrency(summary.net_profit) }}
                </td>
                <td class="text-center">
                  <v-chip
                    size="small"
                    color="primary"
                    class="font-weight-bold"
                    >{{ $t("financials.net_chip") }}</v-chip
                  >
                </td>
                <td class="text-center font-weight-bold">
                  {{ summary.profit_margin }}%
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
import { ref, onMounted, onUnmounted, computed } from "vue";
import api from "@/axios";
import VueApexCharts from "vue3-apexcharts";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const apexchart = VueApexCharts;
const loading = ref(true);
const summary = ref(null);
const topProducts = ref([]);
const salesHistory = ref([]);
const chartPeriod = ref("30");

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchFinancialData(true);
  }, 15000); // 15 seconds
};

const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
};

const handleVisibilityChange = () => {
  if (document.hidden) {
    stopPolling();
  } else {
    fetchFinancialData(true);
    startPolling();
  }
};
// ----------------------------------

const statCards = computed(() => {
  if (!summary.value) return [];
  return [
    {
      title: t("dashboard.total_sales"),
      value: summary.value.total_revenue,
      icon: "mdi-cash-register",
      color: "success",
      trend: "+12%",
    },
    {
      title: t("financials.total_costs"),
      value: summary.value.total_costs,
      icon: "mdi-cart-arrow-down",
      color: "error",
      trend: "-5%",
    },
    {
      title: t("dashboard.net_profit"),
      value: summary.value.net_profit,
      icon: "mdi-currency-usd",
      color: "primary",
      trend: "+18%",
    },
    {
      title: t("financials.profit_margin"),
      value: summary.value.profit_margin,
      icon: "mdi-percent",
      color: "warning",
      trend: t("financials.stable"),
      suffix: "%",
    },
  ];
});

// Chart Configuration
const chartSeries = computed(() => [
  {
    name: t("dashboard.sales_legend"),
    data: salesHistory.value.map((h) => h.daily_revenue),
  },
]);

const chartOptions = computed(() => ({
  chart: {
    fontFamily: "Tajawal, sans-serif",
    toolbar: { show: false },
    sparkline: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: { curve: "smooth", width: 3 },
  colors: ["#2196F3"],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3,
      stops: [0, 90, 100],
    },
  },
  xaxis: {
    categories: salesHistory.value.map((h) => formatDate(h.date)),
    axisBorder: { show: false },
    axisTicks: { show: false },
  },
  yaxis: {
    labels: {
      formatter: (val) => formatCurrency(val),
    },
  },
  tooltip: {
    theme: "light",
    x: { format: "dd MMM" },
    y: {
      formatter: (val) => formatCurrency(val),
    },
  },
}));

const fetchFinancialData = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await api.get("/admin/financials", {
      params: { days: chartPeriod.value },
    });
    summary.value = res.data.summary;
    topProducts.value = res.data.top_products;
    salesHistory.value = res.data.charts.sales_history;
  } catch (error) {
    console.error("Error fetching financials:", error);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat(locale.value === "ar" ? "ar-EG" : "en-US", {
    style: "currency",
    currency: "EGP",
    maximumFractionDigits: 0,
  }).format(value);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString(locale.value === "ar" ? "ar-EG" : "en-US", {
    day: "numeric",
    month: "short",
  });
};

onMounted(() => {
  fetchFinancialData();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.bg-gradient-success {
  background: linear-gradient(135deg, #ffffff 0%, #f1f8e9 100%);
}
.bg-gradient-error {
  background: linear-gradient(135deg, #ffffff 0%, #fffde7 100%);
} /* custom mix */
.bg-gradient-primary {
  background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 100%);
}
.bg-gradient-warning {
  background: linear-gradient(135deg, #ffffff 0%, #fff8e1 100%);
}

.product-rank {
  background: #2196f3;
  color: white;
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  font-weight: bold;
  font-size: 12px;
}

.custom-table th {
  font-size: 0.9rem;
}

.custom-table td {
  font-size: 0.95rem;
  padding: 1rem !important;
}

.text-ltr {
  direction: ltr;
  display: inline-block;
}

.transition-swing {
  transition: transform 0.2s ease-in-out;
}
.transition-swing:hover {
  transform: translateY(-5px);
}
</style>
