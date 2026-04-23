<template>
  <v-container fluid class="pa-6" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center flex-wrap gap-4">
          <div>
            <h2 class="text-h5 font-weight-black text-primary">
              {{ $t("dashboard.payments") }}
            </h2>
            <p class="text-subtitle-1 text-grey-darken-1 mt-1">
              {{ $t("payments.subtitle") }}
            </p>
          </div>
          <v-btn
            color="primary"
            prepend-icon="mdi-refresh"
            class="rounded-lg font-weight-bold shadow-btn px-6"
            @click="fetchTransactions"
            :loading="loading"
          >
            {{ $t("payments.refresh") }}
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <!-- Stats Overview -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3" v-for="(stat, i) in paymentStats" :key="i">
        <v-card elevation="1" class="rounded-xl border h-100">
          <v-card-text class="pa-5">
            <div class="d-flex justify-space-between align-start">
              <div>
                <div class="text-grey-darken-1 text-subtitle-2 mb-1">
                  {{ stat.title }}
                </div>
                <div class="text-h5 font-weight-black">{{ stat.value }}</div>
              </div>
              <v-avatar :color="stat.color" variant="tonal" rounded="lg">
                <v-icon>{{ stat.icon }}</v-icon>
              </v-avatar>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters Bar -->
    <v-card class="mb-6 rounded-xl border" elevation="1">
      <v-card-text class="pa-4">
        <v-row dense align="center">
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              :label="$t('payments.search_placeholder')"
              variant="outlined"
              density="compact"
              hide-details
              @keyup.enter="fetchTransactions"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="2">
            <v-select
              v-model="status"
              :items="statusOptions"
              :label="$t('payments.status_label')"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="fetchTransactions"
            ></v-select>
          </v-col>
          <v-col cols="12" md="2">
            <v-select
              v-model="method"
              :items="methodOptions"
              :label="$t('payments.method_label')"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="fetchTransactions"
            ></v-select>
          </v-col>
          <v-col cols="12" md="2">
            <v-btn
              block
              color="grey-lighten-3"
              class="font-weight-bold"
              height="40"
              @click="resetFilters"
            >
              {{ $t("payments.clear_filters") }}
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Transactions Table -->
    <v-card class="rounded-xl border overflow-hidden" elevation="1">
      <v-table class="payment-table">
        <thead class="bg-grey-lighten-4">
          <tr>
            <th class="text-right">{{ $t("payments.table_txn_id") }}</th>
            <th class="text-right">{{ $t("payments.table_order_no") }}</th>
            <th class="text-right">{{ $t("payments.table_customer") }}</th>
            <th class="text-right">{{ $t("payments.table_method") }}</th>
            <th class="text-right">{{ $t("payments.table_amount") }}</th>
            <th class="text-center">{{ $t("payments.status_label") }}</th>
            <th class="text-right">{{ $t("payments.table_date") }}</th>
            <th class="text-center">{{ $t("payments.table_actions") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="8" class="text-center pa-10">
              <v-progress-circular
                indeterminate
                color="primary"
              ></v-progress-circular>
            </td>
          </tr>
          <tr v-else-if="transactions.length === 0">
            <td colspan="8" class="text-center pa-10 text-grey">
              {{ $t("payments.no_data") }}
            </td>
          </tr>
          <tr v-for="txn in transactions" :key="txn.id" class="hover-row">
            <td
              class="font-weight-bold text-caption text-truncate"
              style="max-width: 120px"
            >
              {{ txn.transaction_id || "N/A" }}
            </td>
            <td class="font-weight-bold text-primary">
              #{{ txn.order?.order_number || "N/A" }}
            </td>
            <td>
              {{ txn.order?.customer_name || $t("dashboard.guest_customer") }}
            </td>
            <td>
              <v-chip
                size="x-small"
                variant="tonal"
                class="text-uppercase font-weight-bold"
              >
                {{ txn.payment_method }}
              </v-chip>
            </td>
            <td class="font-weight-bold text-success">
              {{ Number(txn.amount).toLocaleString() }}
              {{ $t("products.currency") }}
            </td>
            <td class="text-center">
              <v-chip
                size="small"
                :color="getStatusColor(txn.status)"
                variant="flat"
                class="font-weight-bold"
              >
                {{ getStatusText(txn.status) }}
              </v-chip>
            </td>
            <td class="text-grey-darken-1 text-caption">
              {{ formatDate(txn.created_at) }}
            </td>
            <td class="text-center">
              <v-btn
                icon="mdi-information-outline"
                variant="text"
                size="small"
                color="info"
                @click="showDetails(txn)"
              ></v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>

      <!-- Pagination -->
      <v-divider></v-divider>
      <div class="pa-4 d-flex justify-center">
        <v-pagination
          v-model="page"
          :length="pageCount"
          :total-visible="7"
          @update:model-value="fetchTransactions"
          density="compact"
          active-color="primary"
        ></v-pagination>
      </div>
    </v-card>

    <!-- Details Dialog -->
    <v-dialog v-model="detailsDialog" max-width="500px">
      <v-card class="rounded-xl pa-4" v-if="selectedTxn">
        <v-card-title
          class="font-weight-bold d-flex justify-space-between align-center"
        >
          {{ $t("payments.details_title") }}
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="detailsDialog = false"
          ></v-btn>
        </v-card-title>
        <v-card-text>
          <div class="bg-grey-lighten-4 pa-4 rounded-lg mb-4">
            <div class="d-flex justify-space-between mb-2">
              <span class="text-grey">{{ $t("payments.table_txn_id") }}:</span>
              <span class="font-weight-bold">{{
                selectedTxn.transaction_id
              }}</span>
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span class="text-grey"
                >{{ $t("payments.table_order_no") }}:</span
              >
              <span class="font-weight-bold text-primary"
                >#{{ selectedTxn.order?.order_number }}</span
              >
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span class="text-grey">{{ $t("payments.table_amount") }}:</span>
              <span class="font-weight-bold text-success"
                >{{ Number(selectedTxn.amount).toLocaleString() }}
                {{ $t("products.currency") }}</span
              >
            </div>
            <div class="d-flex justify-space-between mb-2">
              <span class="text-grey">{{ $t("payments.status_label") }}:</span>
              <v-chip
                size="x-small"
                :color="getStatusColor(selectedTxn.status)"
                variant="flat"
                >{{ getStatusText(selectedTxn.status) }}</v-chip
              >
            </div>
          </div>

          <div class="text-subtitle-2 font-weight-bold mb-2">
            {{ $t("payments.raw_data") }}
          </div>
          <pre class="raw-data pa-3 rounded-lg">{{
            JSON.stringify(selectedTxn.payload, null, 2)
          }}</pre>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import api from "../../axios";

const { t, locale } = useI18n();

const loading = ref(false);
const transactions = ref([]);
const page = ref(1);
const pageCount = ref(1);
const search = ref("");
const status = ref(null);
const method = ref(null);

const detailsDialog = ref(false);
const selectedTxn = ref(null);

const paymentStats = computed(() => [
  {
    title: t("payments.stats_success"),
    value: stats.value.success.toLocaleString() + " " + t("products.currency"),
    icon: "mdi-check-circle",
    color: "success",
  },
  {
    title: t("payments.stats_failed"),
    value: stats.value.failed.toLocaleString() + " " + t("products.currency"),
    icon: "mdi-close-circle",
    color: "error",
  },
  {
    title: t("payments.stats_pending"),
    value: stats.value.pending.toLocaleString() + " " + t("products.currency"),
    icon: "mdi-clock-outline",
    color: "warning",
  },
  {
    title: t("payments.stats_count"),
    value: stats.value.totalCount.toString(),
    icon: "mdi-swap-horizontal",
    color: "info",
  },
]);

const stats = ref({
  success: 0,
  failed: 0,
  pending: 0,
  totalCount: 0,
});

const statusOptions = computed(() => [
  { title: t("payments.status_success"), value: "success" },
  { title: t("payments.status_failed"), value: "failed" },
  { title: t("payments.status_pending"), value: "pending" },
]);

const methodOptions = ["paymob", "tap", "myfatoorah", "cod"];

const fetchTransactions = async () => {
  loading.value = true;
  try {
    const res = await api.get("/admin/transactions", {
      params: {
        page: page.value,
        search: search.value,
        status: status.value,
        method: method.value,
      },
    });
    transactions.value = res.data.data;
    pageCount.value = res.data.last_page;
    stats.value.totalCount = res.data.total;

    // Simulate stats calculation for this demo
    stats.value.success = transactions.value
      .filter((t) => t.status === "success")
      .reduce((acc, curr) => acc + Number(curr.amount), 0);
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
};

const getStatusColor = (s) => {
  if (s === "success") return "success";
  if (s === "failed") return "error";
  return "warning";
};

const getStatusText = (s) => {
  if (s === "success") return t("payments.status_success");
  if (s === "failed") return t("payments.status_failed");
  return t("payments.status_pending");
};

const formatDate = (d) => {
  return new Date(d).toLocaleString(locale.value === "ar" ? "ar-EG" : "en-US");
};

const showDetails = (txn) => {
  selectedTxn.value = txn;
  detailsDialog.value = true;
};

const resetFilters = () => {
  search.value = "";
  status.value = null;
  method.value = null;
  fetchTransactions();
};

onMounted(() => {
  fetchTransactions();
});

// Watch for locale changes to refresh translated labels
watch(locale, () => {
  fetchTransactions();
});
</script>

<style scoped>
.shadow-btn {
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.3) !important;
}
.payment-table th {
  font-weight: bold !important;
  color: #333 !important;
  border-bottom: 2px solid #eee !important;
}
.hover-row:hover {
  background-color: #fcfcfc;
}
.raw-data {
  background: #2d2d2d;
  color: #a6e22e;
  font-family: monospace;
  font-size: 11px;
  overflow-x: auto;
  white-space: pre-wrap;
}
</style>
