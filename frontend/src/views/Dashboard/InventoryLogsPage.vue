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
          <div class="d-flex align-center gap-3">
            <v-avatar color="primary" size="48" class="rounded-lg shadow-sm">
              <v-icon color="white" size="28">mdi-history</v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h5 font-weight-black text-blue-grey-darken-4">
                {{ $t("inventory_logs.title") }}
              </h2>
              <div class="text-caption text-grey-darken-1">
                {{ $t("inventory_logs.subtitle") }}
              </div>
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="primary"
              variant="outlined"
              prepend-icon="mdi-refresh"
              class="rounded-lg font-weight-bold"
              @click="fetchLogs"
              :loading="loading"
            >
              {{ $t("dashboard.refresh_data") }}
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Toolbar Card -->
    <v-card class="mb-6 rounded-xl border" elevation="0">
      <v-card-text class="pa-4">
        <v-row align="center">
          <v-col cols="12" md="4">
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              :label="$t('inventory.search_sku_placeholder')"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg"
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-select
              v-model="typeFilter"
              :items="[
                { title: $t('messaging.status_filter_all'), value: 'all' },
                ...typeOptions,
              ]"
              :label="$t('inventory_logs.type_th')"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg"
            ></v-select>
          </v-col>
          <v-col cols="12" md="4" class="text-right">
            <v-chip
              color="info"
              variant="flat"
              size="small"
              class="font-weight-bold"
            >
              {{ filteredLogs.length }} {{ $t("inventory_logs.change_th") }}
            </v-chip>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Logs Table -->
    <v-card class="rounded-xl overflow-hidden border" elevation="1">
      <div class="table-responsive">
        <v-table class="logs-table pa-2" fixed-header height="600">
          <thead>
            <tr class="bg-grey-lighten-4">
              <th class="text-right font-weight-bold">
                {{ $t("inventory_logs.product_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory_logs.old_stock_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory_logs.change_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory_logs.new_stock_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory_logs.type_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("inventory_logs.notes_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("inventory_logs.date_th") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="pa-0">
                <v-progress-linear
                  indeterminate
                  color="primary"
                ></v-progress-linear>
              </td>
            </tr>

            <template v-else>
              <tr v-if="filteredLogs.length === 0">
                <td colspan="7" class="text-center pa-12 text-grey">
                  <v-icon size="64" class="mb-4"
                    >mdi-clipboard-text-search-outline</v-icon
                  >
                  <div class="text-h6">
                    {{ $t("inventory_logs.no_logs_found") }}
                  </div>
                </td>
              </tr>

              <tr v-for="log in filteredLogs" :key="log.id" class="hover-row">
                <td>
                  <div class="d-flex align-center gap-3 py-2">
                    <v-avatar
                      size="40"
                      rounded="lg"
                      color="grey-lighten-3 border"
                    >
                      <v-img :src="log.product?.thumbnail" cover></v-img>
                    </v-avatar>
                    <div>
                      <div
                        class="font-weight-bold text-truncate"
                        style="max-width: 180px"
                      >
                        {{
                          log.product?.title || $t("inventory.deleted_product")
                        }}
                      </div>
                      <div class="text-caption text-grey">
                        BY: {{ log.user?.name || $t("inventory.system_user") }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="text-center font-weight-bold">
                  {{ log.old_stock }}
                </td>
                <td class="text-center">
                  <v-chip
                    size="x-small"
                    :color="log.change_amount > 0 ? 'success' : 'error'"
                    class="font-weight-black"
                  >
                    {{ log.change_amount > 0 ? "+" : ""
                    }}{{ log.change_amount }}
                  </v-chip>
                </td>
                <td class="text-center font-weight-black text-primary">
                  {{ log.new_stock }}
                </td>
                <td class="text-center">
                  <v-chip
                    size="x-small"
                    variant="tonal"
                    :color="getTypeColor(log.type)"
                  >
                    {{ $t(`inventory_logs.types.${log.type}`) }}
                  </v-chip>
                </td>
                <td class="text-right" style="max-width: 200px">
                  <div class="text-caption text-truncate">
                    {{ log.notes || "-" }}
                  </div>
                </td>
                <td class="text-right text-caption">
                  <div>{{ formatDate(log.created_at) }}</div>
                  <div class="text-grey">{{ formatTime(log.created_at) }}</div>
                </td>
              </tr>
            </template>
          </tbody>
        </v-table>
      </div>
    </v-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import axios from "@/axios";

const { locale, t } = useI18n();
const loading = ref(true);
const logs = ref([]);
const searchQuery = ref("");
const typeFilter = ref("all");

const typeOptions = [
  { title: t("inventory_logs.types.manual"), value: "manual" },
  {
    title: t("inventory_logs.types.order_fulfillment"),
    value: "order_fulfillment",
  },
  {
    title: t("inventory_logs.types.order_cancellation"),
    value: "order_cancellation",
  },
  { title: t("inventory_logs.types.order_return"), value: "order_return" },
  { title: t("inventory_logs.types.purchase"), value: "purchase" },
];

const fetchLogs = async () => {
  loading.value = true;
  try {
    const res = await axios.get("/admin/inventory-logs");
    logs.value = res.data;
  } catch (error) {
    console.error("Error fetching inventory logs:", error);
  } finally {
    loading.value = false;
  }
};

const filteredLogs = computed(() => {
  return logs.value.filter((log) => {
    const query = searchQuery.value.toLowerCase();
    const title = (log.product?.title || "").toLowerCase();
    const matchesSearch = title.includes(query);

    const matchesType =
      typeFilter.value === "all" || log.type === typeFilter.value;

    return matchesSearch && matchesType;
  });
});

const getTypeColor = (type) => {
  const colors = {
    manual: "info",
    order_fulfillment: "warning",
    order_cancellation: "success",
    order_restoration: "error",
    order_return: "success",
    purchase: "primary",
    purchase_received: "primary",
    purchase_cancelled: "error",
    purchase_deletion: "error",
  };
  return colors[type] || "grey";
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    {
      year: "numeric",
      month: "short",
      day: "numeric",
    }
  );
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    {
      hour: "2-digit",
      minute: "2-digit",
    }
  );
};

onMounted(fetchLogs);
</script>

<style scoped>
.logs-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 13px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.logs-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
  font-size: 14px;
}

.hover-row {
  transition: background-color 0.2s ease;
}
.hover-row:hover {
  background-color: #fcfcfc;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
</style>
