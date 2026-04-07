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
              <v-icon color="white" size="28">mdi-warehouse</v-icon>
            </v-avatar>
            <div>
              <h2 class="text-h5 font-weight-black text-blue-grey-darken-4">
                {{ $t("inventory.inventory_title") }}
              </h2>
              <div class="text-caption text-grey-darken-1">
                {{ $t("inventory.inventory_subtitle") }}
              </div>
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="success"
              variant="flat"
              prepend-icon="mdi-file-excel"
              class="rounded-lg font-weight-bold shadow-btn"
              @click="exportReport"
            >
              {{ $t("inventory.export_report") }}
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Stats Grid -->
    <v-row class="mb-6">
      <v-col
        cols="12"
        sm="6"
        md="3"
        v-for="(stat, i) in inventoryStats"
        :key="i"
      >
        <v-card elevation="1" class="rounded-xl border h-100 overflow-hidden">
          <v-card-text class="pa-4">
            <div class="d-flex align-center gap-3">
              <v-avatar
                :color="stat.color"
                size="44"
                variant="tonal"
                rounded="lg"
              >
                <v-icon size="24">{{ stat.icon }}</v-icon>
              </v-avatar>
              <div>
                <div class="text-caption text-grey-darken-1">
                  {{ stat.title }}
                </div>
                <div
                  class="text-h6 font-weight-black"
                  :class="`text-${stat.color}`"
                >
                  {{ stat.value }}
                </div>
              </div>
            </div>
          </v-card-text>
          <v-progress-linear
            v-if="loading"
            indeterminate
            :color="stat.color"
            height="2"
          ></v-progress-linear>
        </v-card>
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
          <v-col cols="12" md="8">
            <div class="d-flex align-center gap-3 flex-wrap justify-end">
              <v-select
                v-model="filterCategory"
                :items="[
                  t('messaging.status_filter_all'),
                  ...categories.map((c) => c.name),
                ]"
                :label="$t('inventory.category_label')"
                variant="outlined"
                density="compact"
                hide-details
                style="min-width: 140px"
              ></v-select>
              <v-select
                v-model="statusFilter"
                :items="[
                  t('messaging.status_filter_all'),
                  ...statusOptions.map((o) => o.title),
                ]"
                :label="$t('inventory.status_label')"
                variant="outlined"
                density="compact"
                hide-details
                style="min-width: 140px"
              ></v-select>
            </div>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Inventory Table -->
    <v-card class="rounded-xl overflow-hidden border" elevation="1">
      <div class="table-responsive">
        <v-table class="inventory-table pa-2" fixed-header height="600">
          <thead>
            <tr class="bg-grey-lighten-4">
              <th class="text-right font-weight-bold">
                {{ $t("inventory.product_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("inventory.category_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory.quantity_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory.quick_update_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory.status_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("inventory.actions_th") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <!-- Skeletons -->
            <template v-if="loading">
              <tr v-for="n in 5" :key="n">
                <td>
                  <v-skeleton-loader
                    type="list-item-avatar-two-line"
                  ></v-skeleton-loader>
                </td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="chip"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="actions"></v-skeleton-loader></td>
              </tr>
            </template>

            <template v-else>
              <tr v-if="filteredInventory.length === 0">
                <td colspan="6" class="text-center pa-12 text-grey">
                  <v-icon size="64" class="mb-4"
                    >mdi-package-variant-closed</v-icon
                  >
                  <div class="text-h6">
                    {{ $t("inventory.no_products_found") }}
                  </div>
                </td>
              </tr>

              <tr
                v-for="item in filteredInventory"
                :key="item.id"
                class="hover-row"
              >
                <td>
                  <div class="d-flex align-center gap-3">
                    <v-avatar
                      size="45"
                      rounded="lg"
                      color="grey-lighten-3 border"
                    >
                      <v-img :src="item.thumbnail" cover></v-img>
                    </v-avatar>
                    <div>
                      <div
                        class="font-weight-bold text-truncate"
                        style="max-width: 250px"
                      >
                        {{ item.title }}
                      </div>
                      <div class="text-caption text-grey">
                        SKU: PRD-{{ item.id.toString().padStart(4, "0") }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <v-chip size="x-small" variant="tonal" color="primary">{{
                    item.category?.name || "عام"
                  }}</v-chip>
                </td>
                <td class="text-center">
                  <v-avatar
                    :color="getQuantityColor(item)"
                    size="36"
                    variant="tonal"
                    class="font-weight-black"
                  >
                    {{ item.stock }}
                  </v-avatar>
                </td>
                <td class="text-center">
                  <div class="d-flex align-center justify-center gap-2">
                    <v-btn
                      icon="mdi-minus"
                      size="x-small"
                      color="error"
                      variant="tonal"
                      :disabled="item.stock === 0 || processingId === item.id"
                      @click="quickAdjust(item, -1)"
                    ></v-btn>
                    <v-btn
                      icon="mdi-plus"
                      size="x-small"
                      color="success"
                      variant="tonal"
                      :disabled="processingId === item.id"
                      @click="quickAdjust(item, 1)"
                    ></v-btn>
                  </div>
                </td>
                <td class="text-center">
                  <v-chip
                    size="x-small"
                    :color="getStatus(item).color"
                    class="font-weight-bold"
                  >
                    {{ getStatus(item).text }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <v-btn
                    icon="mdi-pencil"
                    size="small"
                    variant="text"
                    color="primary"
                    @click="openAdjustDialog(item)"
                  ></v-btn>
                </td>
              </tr>
            </template>
          </tbody>
        </v-table>
      </div>
    </v-card>

    <!-- Advanced Adjustment Dialog -->
    <v-dialog v-model="adjustDialog" max-width="450px" persistent>
      <v-card class="rounded-xl overflow-hidden shadow-lg">
        <v-toolbar color="primary" flat>
          <v-toolbar-title class="font-weight-bold">{{
            $t("inventory.adjust_stock_title")
          }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeAdjustDialog"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-toolbar>

        <v-card-text class="pa-6" v-if="selectedItem">
          <div
            class="d-flex align-center gap-4 mb-6 pa-4 bg-primary-lighten-5 rounded-xl border"
          >
            <v-avatar size="60" rounded="lg">
              <v-img :src="selectedItem.thumbnail" cover></v-img>
            </v-avatar>
            <div>
              <div class="font-weight-bold text-subtitle-1">
                {{ selectedItem.title }}
              </div>
              <div class="text-caption text-primary">
                {{
                  $t("inventory.current_quantity_label", {
                    count: selectedItem.stock,
                  })
                }}
              </div>
            </div>
          </div>

          <v-form ref="form" v-model="validForm">
            <div class="text-subtitle-2 font-weight-bold mb-3">
              {{ $t("inventory.adjustment_type_label") }}
            </div>
            <v-radio-group v-model="adjustmentType" inline class="mb-4">
              <v-radio
                :label="$t('inventory.adjustment_type_add')"
                value="add"
                color="success"
              ></v-radio>
              <v-radio
                :label="$t('inventory.adjustment_type_deduct')"
                value="deduct"
                color="error"
              ></v-radio>
              <v-radio
                :label="$t('inventory.adjustment_type_set')"
                value="set"
                color="primary"
              ></v-radio>
            </v-radio-group>

            <v-text-field
              v-model.number="adjustmentAmount"
              :label="$t('inventory.adjustment_amount_label')"
              type="number"
              variant="outlined"
              color="primary"
              prepend-inner-icon="mdi-numeric"
              :rules="[
                (v) =>
                  (v !== null && v !== '') || t('inventory.required_field'),
                (v) =>
                  (adjustmentType !== 'set' ? v > 0 : v >= 0) ||
                  t('inventory.invalid_value'),
                (v) =>
                  (adjustmentType === 'deduct'
                    ? v <= selectedItem.stock
                    : true) || t('inventory.greater_than_available'),
              ]"
            ></v-text-field>

            <v-textarea
              :label="$t('inventory.adjustment_notes_label')"
              variant="outlined"
              rows="2"
              :placeholder="$t('inventory.adjustment_notes_placeholder')"
            ></v-textarea>
          </v-form>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions class="pa-4 bg-grey-lighten-5">
          <v-spacer></v-spacer>
          <v-btn
            color="grey"
            variant="text"
            class="px-6 font-weight-bold"
            @click="closeAdjustDialog"
            >{{ $t("inventory.cancel_btn") }}</v-btn
          >
          <v-btn
            color="primary"
            variant="flat"
            class="px-8 font-weight-bold shadow-btn"
            :loading="saving"
            :disabled="!validForm || saving"
            @click="saveStockAdjustment"
            >{{ $t("inventory.save_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3000"
      location="bottom center"
    >
      {{ snackbar.text }}
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import axios from "@/axios";
import { ProductModule } from "@/store/Products";

const productStore = ProductModule();
const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const processingId = ref(null);

const searchQuery = ref("");
const statusFilter = ref(t("messaging.status_filter_all"));
const filterCategory = ref(t("messaging.status_filter_all"));
const statusOptions = computed(() => [
  { title: t("inventory.in_stock_chip"), value: "available" },
  { title: t("inventory.low_stock_chip"), value: "low" },
  { title: t("inventory.out_of_stock_chip"), value: "out" },
]);

const adjustDialog = ref(false);
const selectedItem = ref(null);
const validForm = ref(false);
const adjustmentType = ref("add");
const adjustmentAmount = ref(null);
const saving = ref(false);

const snackbar = ref({
  show: false,
  text: "",
  color: "success",
});

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const minQuantity = 10;

const fetchProducts = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const [prodRes, catRes] = await Promise.all([
      axios.get("/admin/products"),
      axios.get("/admin/categories"),
    ]);
    products.value = prodRes.data;
    categories.value = catRes.data;
  } catch (error) {
    console.error("Error fetching inventory data:", error);
  } finally {
    if (showLoading) loading.value = false;
  }
};

const lowStockCount = computed(
  () =>
    products.value.filter((i) => i.stock > 0 && i.stock <= minQuantity).length
);
const outOfStockCount = computed(
  () => products.value.filter((i) => i.stock === 0).length
);

const totalInventoryValue = computed(() => {
  return products.value.reduce((total, item) => {
    return total + item.stock * (item.purchase_price || 0);
  }, 0);
});

const inventoryStats = computed(() => [
  {
    title: t("inventory.total_products"),
    value: products.value.length,
    icon: "mdi-package-variant",
    color: "primary",
  },
  {
    title: t("inventory.low_stock_alerts"),
    value: lowStockCount.value,
    icon: "mdi-alert-outline",
    color: "warning",
  },
  {
    title: t("inventory.out_of_stock_stat"),
    value: outOfStockCount.value,
    icon: "mdi-close-circle-outline",
    color: "error",
  },
  {
    title: t("inventory.inventory_value"),
    value:
      totalInventoryValue.value.toLocaleString() + " " + t("products.currency"),
    icon: "mdi-currency-egy",
    color: "success",
  },
]);

const getStatus = (item) => {
  if (item.stock === 0) {
    return {
      text: t("inventory.out_of_stock_chip"),
      color: "error",
      icon: "mdi-close-circle",
    };
  } else if (item.stock <= minQuantity) {
    return {
      text: t("inventory.low_stock_chip"),
      color: "warning",
      icon: "mdi-alert",
    };
  } else {
    return {
      text: t("inventory.in_stock_chip"),
      color: "success",
      icon: "mdi-check-circle",
    };
  }
};

const getQuantityColor = (item) => {
  if (item.stock === 0) return "error";
  if (item.stock <= minQuantity) return "warning";
  return "success";
};

const filteredInventory = computed(() => {
  return products.value.filter((item) => {
    const query = searchQuery.value.toLowerCase();
    const sku = `PRD-${item.id.toString().padStart(4, "0")}`.toLowerCase();
    const matchesSearch =
      (item.title && item.title.toLowerCase().includes(query)) ||
      sku.includes(query);

    let matchesStatus = true;
    if (
      statusFilter.value &&
      statusFilter.value !== t("messaging.status_filter_all")
    ) {
      const statusText = getStatus(item).text;
      matchesStatus = statusText === statusFilter.value;
    }

    let matchesCategory = true;
    if (
      filterCategory.value &&
      filterCategory.value !== t("messaging.status_filter_all")
    ) {
      matchesCategory = item.category?.name === filterCategory.value;
    }

    return matchesSearch && matchesStatus && matchesCategory;
  });
});

const quickAdjust = async (item, amount) => {
  processingId.value = item.id;
  const newStock = Math.max(0, item.stock + amount);
  try {
    await axios.put(`/admin/products/${item.id}/stock`, { stock: newStock });
    item.stock = newStock;
    productStore.getProducts();
  } catch (error) {
    showMessage(t("inventory.quick_update_failed"), "error");
  } finally {
    processingId.value = null;
  }
};

const openAdjustDialog = (item) => {
  selectedItem.value = { ...item };
  adjustmentType.value = "add";
  adjustmentAmount.value = null;
  adjustDialog.value = true;
};

const closeAdjustDialog = () => {
  adjustDialog.value = false;
  setTimeout(() => {
    selectedItem.value = null;
    adjustmentAmount.value = null;
  }, 300);
};

const saveStockAdjustment = async () => {
  if (!selectedItem.value) return;
  saving.value = true;

  let newStock = selectedItem.value.stock;
  const amount = Number(adjustmentAmount.value);

  if (adjustmentType.value === "add") {
    newStock += amount;
  } else if (adjustmentType.value === "deduct") {
    newStock -= amount;
  } else if (adjustmentType.value === "set") {
    newStock = amount;
  }

  try {
    await axios.put(`/admin/products/${selectedItem.value.id}/stock`, {
      stock: newStock,
    });

    await fetchProducts();
    await productStore.getProducts();

    showMessage(t("inventory.update_success"));
    closeAdjustDialog();
  } catch (error) {
    console.error("Error updating stock:", error);
    showMessage(
      error.response?.data?.message || "حدث خطأ أثناء تحديث المخزون",
      "error"
    );
  } finally {
    saving.value = false;
  }
};

const exportReport = () => {
  try {
    const csvRows = [];
    const headers = [
      t("inventory.id_csv"),
      t("inventory.product_csv"),
      t("inventory.sku_csv"),
      t("inventory.price_csv"),
      t("inventory.cost_csv"),
      t("inventory.quantity_csv"),
      t("inventory.status_csv"),
    ];
    csvRows.push(headers.join(","));

    products.value.forEach((item) => {
      const id = item.id;
      const title = `"${(item.title || "").replace(/"/g, '""')}"`;
      const sku = `PRD-${item.id.toString().padStart(4, "0")}`;
      const price = item.price;
      const purchasePrice = item.purchase_price || 0;
      const stock = item.stock;

      let statusText = t("inventory.in_stock_chip");
      if (stock === 0) statusText = t("inventory.out_of_stock_chip");
      else if (stock <= minQuantity) statusText = t("inventory.low_stock_chip");

      csvRows.push(
        [id, title, sku, price, purchasePrice, stock, statusText].join(",")
      );
    });

    const csvContent = "\uFEFF" + csvRows.join("\n");
    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;

    const today = new Date().toISOString().slice(0, 10);
    link.setAttribute("download", `ZadStore_Inventory_${today}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showMessage(t("inventory.export_success"));
  } catch (error) {
    console.error("Export Error:", error);
    showMessage(t("inventory.export_error"), "error");
  }
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchProducts(false), 20000); // 20 seconds
};

onMounted(() => {
  fetchProducts();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchProducts(false);
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
.shadow-btn {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
  transition: transform 0.2s ease;
}
.shadow-btn:hover {
  transform: translateY(-2px);
}

.inventory-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 14px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.inventory-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
}

.hover-row {
  transition: background-color 0.2s ease;
}
.hover-row:hover {
  background-color: #fcfcfc;
}

.border-left-primary {
  border-right: 4px solid rgb(var(--v-theme-primary)) !important;
}
.border-left-warning {
  border-right: 4px solid #fb8c00 !important;
}
.border-left-error {
  border-right: 4px solid #e53935 !important;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
</style>
