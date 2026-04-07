<template>
  <div class="mang-purchases" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Header Section -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h4 font-weight-black text-grey-darken-4 mb-1">
          {{ $t("purchases.purchases_title") }}
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          {{ $t("purchases.purchases_subtitle") }}
        </p>
      </div>
      <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        size="large"
        class="rounded-lg font-weight-bold elevation-4"
        @click="openAddDialog"
      >
        {{ $t("purchases.add_purchase_btn") }}
      </v-btn>
    </div>

    <!-- Quick Stats -->
    <v-row class="mb-6">
      <v-col cols="12" md="3">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="primary-lighten-5" size="48" class="me-4">
              <v-icon color="primary">mdi-cart-arrow-down</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("purchases.total_purchases_stat") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.totalPurchases.toLocaleString() }}
                {{ $t("products.currency") }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="success-lighten-5" size="48" class="me-4">
              <v-icon color="success">mdi-calendar-check</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("purchases.monthly_purchases_stat") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.monthlyPurchases.toLocaleString() }}
                {{ $t("products.currency") }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="info-lighten-5" size="48" class="me-4">
              <v-icon color="info">mdi-account-tie</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("purchases.active_suppliers_stat") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.activeSuppliers }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="3">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="warning-lighten-5" size="48" class="me-4">
              <v-icon color="warning">mdi-clock-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("purchases.pending_bills_stat") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.pendingBills }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Main Table Card -->
    <v-card class="rounded-xl border-0 overflow-hidden" elevation="3">
      <v-toolbar color="white" flat class="px-4 border-b">
        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          :label="$t('purchases.search_purchases_placeholder')"
          variant="solo-filled"
          flat
          hide-details
          density="compact"
          class="max-width-300 rounded-lg"
        ></v-text-field>
        <v-spacer></v-spacer>
        <v-btn-toggle
          v-model="filterStatus"
          mandatory
          density="compact"
          color="primary"
        >
          <v-btn value="all">{{ $t("purchases.filter_all") }}</v-btn>
          <v-btn value="received">{{ $t("purchases.filter_received") }}</v-btn>
          <v-btn value="pending">{{ $t("purchases.filter_pending") }}</v-btn>
        </v-btn-toggle>
      </v-toolbar>

      <v-data-table
        :headers="headers"
        :items="filteredPurchases"
        :search="search"
        :loading="loading"
        class="purchase-table"
        hover
      >
        <!-- Status Slot -->
        <template v-slot:[`item.status`]="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            size="small"
            variant="flat"
            class="font-weight-bold"
          >
            {{ getStatusText(item.status) }}
          </v-chip>
        </template>

        <!-- Amount Slot -->
        <template v-slot:[`item.total_amount`]="{ item }">
          <span class="font-weight-black text-primary"
            >{{ item.total_amount.toLocaleString() }}
            {{ $t("products.currency") }}</span
          >
        </template>

        <!-- Actions Slot -->
        <template v-slot:[`item.actions`]="{ item }">
          <div class="d-flex gap-2">
            <v-btn
              icon="mdi-eye-outline"
              variant="text"
              size="small"
              color="info"
              @click="viewPurchase(item)"
            ></v-btn>
            <v-btn
              icon="mdi-pencil-outline"
              variant="text"
              size="small"
              color="amber-darken-3"
              @click="editPurchase(item)"
            ></v-btn>
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              size="small"
              color="error"
              @click="confirmDelete(item)"
            ></v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog v-model="dialog" max-width="900" persistent scrollable>
      <v-card class="rounded-xl overflow-hidden shadow-lg h-100">
        <v-toolbar color="primary" flat>
          <v-toolbar-title class="font-weight-black">
            {{
              isEdit
                ? $t("purchases.edit_purchase_dialog_title")
                : $t("purchases.add_purchase_dialog_title")
            }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-toolbar>

        <v-card-text class="pa-6">
          <v-form ref="form" v-model="valid">
            <v-row>
              <!-- Supplier Info -->
              <v-col cols="12" md="4">
                <div class="d-flex align-center gap-2">
                  <v-select
                    v-model="editedItem.supplier_id"
                    :items="suppliers"
                    item-title="name"
                    item-value="id"
                    :label="$t('purchases.supplier_th')"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-account-tie"
                    :rules="[(v) => !!v || $t('inventory.required_field')]"
                    class="flex-grow-1"
                  ></v-select>
                  <v-btn
                    icon="mdi-plus"
                    color="success"
                    size="small"
                    rounded="lg"
                    @click="supplierDialog = true"
                  ></v-btn>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="editedItem.invoice_number"
                  :label="$t('purchases.invoice_number_label')"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-file-document-outline"
                  :rules="[(v) => !!v || $t('inventory.required_field')]"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="editedItem.purchase_date"
                  :label="$t('purchases.purchase_date_label')"
                  type="date"
                  variant="outlined"
                  density="comfortable"
                  :rules="[(v) => !!v || $t('inventory.required_field')]"
                ></v-text-field>
              </v-col>

              <v-divider class="my-4 w-100"></v-divider>

              <!-- Items Section -->
              <v-col cols="12">
                <div class="d-flex justify-space-between align-center mb-4">
                  <h3 class="text-subtitle-1 font-weight-black text-primary">
                    {{ $t("purchases.items_section_title") }}
                  </h3>
                  <v-btn
                    color="primary"
                    variant="tonal"
                    prepend-icon="mdi-plus"
                    @click="addItem"
                    size="small"
                    >{{ $t("purchases.add_item_btn") }}</v-btn
                  >
                </div>

                <v-table density="compact" class="border rounded-lg mb-4">
                  <thead class="bg-grey-lighten-4">
                    <tr>
                      <th>{{ $t("purchases.product_th") }}</th>
                      <th style="width: 120px">
                        {{ $t("purchases.quantity_th") }}
                      </th>
                      <th style="width: 150px">
                        {{ $t("purchases.unit_price_th") }}
                      </th>
                      <th style="width: 150px">
                        {{ $t("purchases.line_total_th") }}
                      </th>
                      <th style="width: 50px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in editedItem.items" :key="index">
                      <td class="pt-2">
                        <v-autocomplete
                          v-model="item.product_id"
                          :items="productsList"
                          item-title="title"
                          item-value="id"
                          :placeholder="
                            $t('purchases.select_product_placeholder')
                          "
                          variant="outlined"
                          density="compact"
                          hide-details
                          :rules="[
                            (v) => !!v || $t('inventory.required_field'),
                          ]"
                        ></v-autocomplete>
                      </td>
                      <td class="pt-2">
                        <v-text-field
                          v-model.number="item.quantity"
                          type="number"
                          variant="outlined"
                          density="compact"
                          hide-details
                          min="1"
                        ></v-text-field>
                      </td>
                      <td class="pt-2">
                        <v-text-field
                          v-model.number="item.unit_price"
                          type="number"
                          variant="outlined"
                          density="compact"
                          hide-details
                          :prefix="$t('products.currency')"
                        ></v-text-field>
                      </td>
                      <td class="font-weight-bold pt-2">
                        {{ (item.quantity * item.unit_price).toLocaleString() }}
                        {{ $t("products.currency") }}
                      </td>
                      <td class="pt-2">
                        <v-btn
                          icon="mdi-delete"
                          variant="text"
                          color="error"
                          size="small"
                          @click="removeItem(index)"
                        ></v-btn>
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="editedItem.status"
                  :items="statusOptions"
                  item-title="text"
                  item-value="value"
                  :label="$t('purchases.supply_status_label')"
                  variant="outlined"
                  density="comfortable"
                ></v-select>
                <v-textarea
                  v-model="editedItem.notes"
                  :label="$t('purchases.notes_label')"
                  variant="outlined"
                  rows="2"
                  class="mt-4"
                ></v-textarea>
              </v-col>

              <v-col
                cols="12"
                md="6"
                class="d-flex flex-column justify-center align-end"
              >
                <div class="text-h6 text-grey">
                  {{ $t("purchases.total_amount_label") }}
                </div>
                <div class="text-h3 font-weight-black text-primary">
                  {{ calculateTotal.toLocaleString() }}
                  <small>{{ $t("products.currency") }}</small>
                </div>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions class="pa-4 bg-grey-lighten-5">
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="dialog = false"
            class="px-6 font-weight-bold"
            >{{ $t("purchases.cancel_btn") }}</v-btn
          >
          <v-btn
            color="primary"
            variant="flat"
            class="px-10 font-weight-bold shadow-btn rounded-lg"
            @click="savePurchase"
            :loading="saveLoading"
            >{{ $t("purchases.save_invoice_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Supplier Dialog -->
    <v-dialog v-model="supplierDialog" max-width="400">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="font-weight-black">{{
          $t("purchases.add_supplier_dialog_title")
        }}</v-card-title>
        <v-card-text>
          <v-text-field
            v-model="newSupplier.name"
            :label="$t('purchases.supplier_name_label')"
            variant="outlined"
          ></v-text-field>
          <v-text-field
            v-model="newSupplier.phone"
            :label="$t('purchases.supplier_phone_label')"
            variant="outlined"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="supplierDialog = false">{{
            $t("purchases.cancel_btn")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            @click="saveSupplier"
            :loading="supplierLoading"
            >{{ $t("inventory.save_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- View Details Dialog -->
    <v-dialog v-model="viewDialog" max-width="650">
      <v-card
        class="rounded-xl overflow-hidden text-right"
        dir="rtl"
        v-if="selectedPurchase"
      >
        <v-toolbar color="info" flat>
          <v-toolbar-title>{{
            $t("purchases.invoice_details_title", {
              no: selectedPurchase.invoice_number,
            })
          }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="viewDialog = false"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-toolbar>
        <v-card-text class="pa-6">
          <v-row>
            <v-col cols="6"
              ><b>{{ $t("purchases.supplier_th") }}:</b>
              {{ selectedPurchase.supplier?.name }}</v-col
            >
            <v-col cols="6"
              ><b>{{ $t("purchases.date_th") }}:</b>
              {{ selectedPurchase.purchase_date }}</v-col
            >
            <v-col cols="6"
              ><b>{{ $t("purchases.status_th") }}:</b>
              <v-chip
                :color="getStatusColor(selectedPurchase.status)"
                size="x-small"
                >{{ getStatusText(selectedPurchase.status) }}</v-chip
              >
            </v-col>
            <v-col cols="12"><v-divider class="my-2"></v-divider></v-col>
            <v-col cols="12">
              <div class="text-subtitle-2 mb-2 font-weight-bold">
                {{ $t("purchases.stock_effect_label") }}
                <span
                  :class="
                    selectedPurchase.status === 'received'
                      ? 'text-success'
                      : 'text-grey'
                  "
                >
                  {{
                    selectedPurchase.status === "received"
                      ? $t("purchases.stock_added_hint")
                      : $t("purchases.stock_pending_hint")
                  }}
                </span>
              </div>
              <v-table density="comfortable" class="border rounded-lg">
                <thead class="bg-grey-lighten-4">
                  <tr>
                    <th>{{ $t("purchases.product_th") }}</th>
                    <th>{{ $t("purchases.quantity_th") }}</th>
                    <th>{{ $t("purchases.unit_price_th") }}</th>
                    <th>{{ $t("purchases.line_total_th") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in selectedPurchase.items" :key="item.id">
                    <td>{{ item.product?.title }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.unit_price }} {{ $t("products.currency") }}</td>
                    <td class="font-weight-bold">
                      {{ (item.quantity * item.unit_price).toLocaleString() }}
                      {{ $t("products.currency") }}
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </v-col>
            <v-col
              cols="12"
              class="text-left mt-4"
              :class="locale === 'ar' ? 'text-left' : 'text-right'"
            >
              <div class="text-h4 font-weight-black text-primary">
                {{ selectedPurchase.total_amount.toLocaleString() }}
                {{ $t("products.currency") }}
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="64" class="mb-4"
          >mdi-alert-circle-outline</v-icon
        >
        <v-card-title class="text-h6 font-weight-black">{{
          $t("purchases.delete_purchase_title")
        }}</v-card-title>
        <v-card-text>
          {{ $t("purchases.delete_confirm_hint") }}
          <div
            v-if="itemToDelete?.status === 'received'"
            class="text-error font-weight-bold mt-2"
          >
            {{ $t("purchases.delete_stock_warning") }}
          </div>
        </v-card-text>
        <v-card-actions class="justify-center gap-4 mt-4">
          <v-btn variant="text" class="px-6" @click="deleteDialog = false">{{
            $t("purchases.cancel_btn")
          }}</v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="rounded-lg px-6 shadow-btn"
            @click="deletePurchase"
            :loading="deleteLoading"
            >{{ $t("purchases.delete_final_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Global Snackbar -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3000"
      location="top center"
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

// State
const loading = ref(false);
const saveLoading = ref(false);
const deleteLoading = ref(false);
const supplierLoading = ref(false);
const search = ref("");
const filterStatus = ref("all");
const dialog = ref(false);
const supplierDialog = ref(false);
const isEdit = ref(false);
const valid = ref(false);
const form = ref(null);

const snackbar = ref({ show: false, text: "", color: "success" });
const viewDialog = ref(false);
const selectedPurchase = ref(null);
const deleteDialog = ref(false);
const itemToDelete = ref(null);

const stats = ref({
  totalPurchases: 0,
  monthlyPurchases: 0,
  activeSuppliers: 0,
  pendingBills: 0,
});

const purchases = ref([]);
const suppliers = ref([]);
const productsList = ref([]);
const newSupplier = ref({ name: "", phone: "" });

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchData(true);
  }, 30000); // 30 seconds
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
    fetchData(true);
    startPolling();
  }
};
// ----------------------------------

const headers = computed(() => [
  {
    title: t("purchases.invoice_number_th"),
    key: "invoice_number",
    align: "start",
  },
  { title: t("purchases.supplier_th"), key: "supplier.name", align: "start" },
  { title: t("purchases.date_th"), key: "purchase_date", align: "start" },
  {
    title: t("purchases.total_amount_th"),
    key: "total_amount",
    align: "start",
  },
  { title: t("purchases.status_th"), key: "status", align: "center" },
  {
    title: t("purchases.actions_th"),
    key: "actions",
    align: "end",
    sortable: false,
  },
]);

const statusOptions = computed(() => [
  { text: t("purchases.received_status"), value: "received" },
  { text: t("purchases.pending_status"), value: "pending" },
  { text: t("purchases.cancelled_status"), value: "cancelled" },
]);

const editedItem = ref({
  supplier_id: null,
  invoice_number: "",
  purchase_date: new Date().toISOString().substring(0, 10),
  status: "pending",
  notes: "",
  items: [],
});

// Computed
const calculateTotal = computed(() => {
  return editedItem.value.items.reduce((total, item) => {
    return total + item.quantity * item.unit_price;
  }, 0);
});

const filteredPurchases = computed(() => {
  if (filterStatus.value === "all") return purchases.value;
  return purchases.value.filter((p) => p.status === filterStatus.value);
});

// Methods
const fetchData = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const [purchRes, statsRes, suppRes, prodRes] = await Promise.all([
      axios.get("/admin/purchases"),
      axios.get("/admin/purchases/statistics"),
      axios.get("/admin/suppliers"),
      axios.get("/admin/products"),
    ]);
    purchases.value = purchRes.data;
    stats.value = statsRes.data;
    suppliers.value = suppRes.data;
    productsList.value = prodRes.data;
  } catch (error) {
    if (!quiet) showMessage(t("purchases.fetch_failed"), "error");
  } finally {
    if (!quiet) loading.value = false;
  }
};

const showMessage = (text, color = "success") => {
  snackbar.value = { show: true, text, color };
};

const addItem = () => {
  editedItem.value.items.push({
    product_id: null,
    quantity: 1,
    unit_price: 0,
  });
};

const removeItem = (index) => {
  editedItem.value.items.splice(index, 1);
};

const openAddDialog = () => {
  isEdit.value = false;
  editedItem.value = {
    supplier_id: null,
    invoice_number: "PUR-" + Date.now().toString().slice(-6),
    purchase_date: new Date().toISOString().substring(0, 10),
    status: "pending",
    notes: "",
    items: [{ product_id: null, quantity: 1, unit_price: 0 }],
  };
  dialog.value = true;
};

const savePurchase = async () => {
  const { valid: isFormValid } = await form.value.validate();
  if (!isFormValid) return;
  if (editedItem.value.items.length === 0) {
    showMessage(t("purchases.at_least_one_item_error"), "error");
    return;
  }

  saveLoading.value = true;
  try {
    await axios.post("/admin/purchases", editedItem.value);
    showMessage(t("purchases.save_success"));
    dialog.value = false;
    fetchData(true);
  } catch (error) {
    showMessage(
      error.response?.data?.message || t("purchases.save_failed"),
      "error"
    );
  } finally {
    saveLoading.value = false;
  }
};

const saveSupplier = async () => {
  if (!newSupplier.value.name) return;
  supplierLoading.value = true;
  try {
    const res = await axios.post("/admin/suppliers", newSupplier.value);
    suppliers.value.push(res.data.supplier);
    editedItem.value.supplier_id = res.data.supplier.id;
    supplierDialog.value = false;
    newSupplier.value = { name: "", phone: "" };
    showMessage(t("purchases.supplier_add_success"));
  } catch (error) {
    showMessage(t("purchases.supplier_add_failed"), "error");
  } finally {
    supplierLoading.value = false;
  }
};

const viewPurchase = (item) => {
  selectedPurchase.value = item;
  viewDialog.value = true;
};

const editPurchase = () => {
  showMessage(t("purchases.editing_stock_notice"), "info");
};

const confirmDelete = (item) => {
  itemToDelete.value = item;
  deleteDialog.value = true;
};

const deletePurchase = async () => {
  deleteLoading.value = true;
  try {
    await axios.delete(`/admin/purchases/${itemToDelete.value.id}`);
    showMessage(t("purchases.delete_success"));
    deleteDialog.value = false;
    fetchData(true);
  } catch (error) {
    showMessage(t("purchases.delete_failed"), "error");
  } finally {
    deleteLoading.value = false;
  }
};

const getStatusColor = (status) => {
  const colors = {
    received: "success",
    pending: "warning",
    cancelled: "error",
  };
  return colors[status] || "grey";
};

const getStatusText = (status) => {
  const texts = {
    received: t("purchases.received_status"),
    pending: t("purchases.pending_status"),
    cancelled: t("purchases.cancelled_status"),
  };
  return texts[status] || status;
};

onMounted(() => {
  fetchData();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.purchase-table :deep(thead th) {
  background-color: #f8fafc !important;
  font-weight: 900 !important;
  text-transform: uppercase;
  font-size: 0.85rem !important;
  color: #64748b !important;
}

.stat-card {
  transition: transform 0.3s ease;
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
}
.stat-card:hover {
  transform: translateY(-5px);
}

.max-width-300 {
  max-width: 300px;
}

.total-field :deep(input) {
  font-size: 1.5rem !important;
  font-weight: 900 !important;
  color: #ff9800 !important;
  text-align: center !important;
}

.gap-2 {
  gap: 8px;
}
</style>
