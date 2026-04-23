<template>
  <v-container fluid class="pa-6" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <v-card class="mb-6 rounded-xl" elevation="1">
      <v-card-text
        class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
      >
        <h2
          class="text-h5 font-weight-black"
          style="color: rgb(var(--v-theme-primary))"
        >
          {{ $t("sales.orders.title") }}
        </h2>

        <div
          class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
          style="max-width: 900px"
        >
          <v-text-field
            v-model="searchQuery"
            prepend-inner-icon="mdi-magnify"
            :label="$t('sales.orders.search_placeholder')"
            variant="outlined"
            density="compact"
            hide-details
            class="bg-white rounded-lg flex-grow-1"
          ></v-text-field>

          <v-select
            v-model="statusFilter"
            :items="statusOptions"
            :label="$t('sales.orders.status_label')"
            variant="outlined"
            density="compact"
            hide-details
            clearable
            class="bg-white rounded-lg"
            style="min-width: 130px; max-width: 150px"
          ></v-select>

          <v-btn
            color="info"
            variant="outlined"
            prepend-icon="mdi-download"
            class="rounded-lg font-weight-bold"
            height="40"
            @click="exportToCSV"
          >
            {{ $t("sales.orders.export_csv") }}
          </v-btn>

          <div
            class="d-flex align-center gap-2 bg-white pa-1 rounded-lg border"
          >
            <span class="text-caption font-weight-bold px-2">{{
              $t("sales.orders.date_from")
            }}</span>
            <input
              type="date"
              v-model="startDate"
              class="date-input"
              @change="fetchOrders"
            />
            <span class="text-caption font-weight-bold px-2">{{
              $t("sales.orders.date_to")
            }}</span>
            <input
              type="date"
              v-model="endDate"
              class="date-input"
              @change="fetchOrders"
            />
            <v-btn
              icon="mdi-refresh"
              variant="text"
              size="small"
              color="primary"
              @click="resetFilters"
            ></v-btn>
          </div>
        </div>
      </v-card-text>
    </v-card>

    <!-- Batch Actions Bar -->
    <v-expand-transition>
      <v-card
        v-if="selectedIds.length > 0"
        class="mb-6 rounded-xl bg-error text-white pa-4"
        elevation="4"
      >
        <div class="d-flex align-center justify-space-between">
          <div class="d-flex align-center gap-3">
            <v-icon>mdi-checkbox-multiple-marked</v-icon>
            <div class="text-h6 font-weight-bold">
              {{
                $t("sales.orders.selected_count", { count: selectedIds.length })
              }}
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="white"
              variant="tonal"
              prepend-icon="mdi-delete-multiple"
              @click="batchDeleteDialog = true"
            >
              {{ $t("sales.orders.delete_selected") }}
            </v-btn>
            <v-btn color="white" variant="outlined" @click="selectedIds = []">
              {{ $t("sales.orders.btns.close") }}
            </v-btn>
          </div>
        </div>
      </v-card>
    </v-expand-transition>

    <v-card class="rounded-xl overflow-hidden" elevation="1">
      <div class="table-responsive">
        <v-table class="orders-table pa-2">
          <thead class="bg-grey-lighten-4">
            <tr>
              <th class="text-right font-weight-bold" style="width: 50px">
                <v-checkbox-btn
                  v-model="selectAll"
                  color="primary"
                  hide-details
                ></v-checkbox-btn>
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.number") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.customer") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.date") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.payment") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.total") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("sales.orders.table.status") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("sales.orders.table.actions") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="i in 5" :key="`skel-order-${i}`">
                <td class="px-2"><v-skeleton-loader type="list-item" /></td>
                <td class="px-2 py-3">
                  <v-skeleton-loader type="text" style="max-width: 100px" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" style="max-width: 150px" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" style="max-width: 120px" />
                </td>
                <td class="px-2">
                  <div class="d-flex flex-column gap-1">
                    <v-skeleton-loader type="chip" style="width: 80px" />
                  </div>
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" style="max-width: 100px" />
                </td>
                <td class="px-2"><v-skeleton-loader type="chip" /></td>
                <td class="px-2">
                  <v-skeleton-loader
                    type="actions"
                    class="mx-auto"
                    style="min-width: 120px"
                  />
                </td>
              </tr>
            </template>
            <tr v-else-if="filteredOrders.length === 0">
              <td colspan="8" class="text-center pa-6 text-grey-darken-1">
                {{ $t("sales.orders.no_orders") }}
              </td>
            </tr>

            <tr
              v-for="order in filteredOrders"
              :key="order.id"
              class="hover-row"
            >
              <td>
                <v-checkbox-btn
                  v-model="selectedIds"
                  :value="order.id"
                  color="primary"
                  hide-details
                ></v-checkbox-btn>
              </td>
              <td
                class="font-weight-bold text-primary d-flex align-center gap-1"
              >
                #{{ order.orderNumber }}
                <v-tooltip
                  :text="$t('sales.orders.urgent_tip')"
                  location="top"
                  v-if="order.isUrgent"
                >
                  <template v-slot:activator="{ props }">
                    <v-icon
                      v-bind="props"
                      color="error"
                      size="small"
                      class="pulse-icon"
                      >mdi-fire</v-icon
                    >
                  </template>
                </v-tooltip>
              </td>
              <td class="font-weight-bold text-subtitle-2">
                {{ order.customerName }}
              </td>
              <td class="text-grey-darken-1">
                {{ formatDate(order.date) }}
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <v-chip
                    size="x-small"
                    :color="
                      order.paymentStatus === 'paid' ? 'success' : 'error'
                    "
                    variant="flat"
                    label
                  >
                    {{
                      order.paymentStatus === "paid"
                        ? $t("sales.orders.payment_status.paid")
                        : $t("sales.orders.payment_status.unpaid")
                    }}
                  </v-chip>
                  <span class="text-caption text-grey">{{
                    order.paymentMethod?.toUpperCase() || ""
                  }}</span>
                </div>
              </td>
              <td class="font-weight-bold text-success" dir="ltr">
                {{
                  Number(order.total).toLocaleString(
                    locale === "ar" ? "ar-EG" : "en-US"
                  )
                }}
                {{ $t("sales.orders.currency") }}
              </td>
              <td>
                <v-chip
                  size="small"
                  :color="getStatusColor(order.status)"
                  class="font-weight-bold"
                >
                  {{ getStatusText(order.status) }}
                </v-chip>
              </td>
              <td class="text-center">
                <div class="d-flex align-center justify-center gap-2">
                  <v-btn
                    color="rgb(var(--v-theme-primary))"
                    variant="tonal"
                    size="small"
                    class="font-weight-bold rounded-lg px-4"
                    @click="openOrderDetails(order)"
                  >
                    {{ $t("sales.orders.btns.details") }}
                    <v-icon right size="18" class="ml-1"
                      >mdi-eye-outline</v-icon
                    >
                  </v-btn>

                  <v-tooltip
                    :text="$t('sales.orders.messages.delete_confirm')"
                    location="top"
                  >
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        icon
                        size="small"
                        color="error"
                        variant="text"
                        @click="openDeleteDialog(order)"
                      >
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </template>
                  </v-tooltip>
                </div>
              </td>
            </tr>
          </tbody>
        </v-table>
      </div>
    </v-card>

    <v-dialog v-model="detailsDialog" max-width="700px" scrollable>
      <v-card class="rounded-xl" v-if="selectedOrder">
        <v-card-title
          class="d-flex align-center justify-space-between pt-4 px-6 bg-grey-lighten-4"
        >
          <span
            class="text-h6 font-weight-bold"
            style="color: rgb(var(--v-theme-primary))"
          >
            {{
              $t("sales.orders.details_title", {
                num: selectedOrder.orderNumber,
              })
            }}
          </span>
          <v-btn
            icon="mdi-close"
            variant="text"
            @click="detailsDialog = false"
          ></v-btn>
        </v-card-title>

        <v-card-text class="pa-0 printable-area" style="max-height: 80vh">
          <!-- Print-Only Header -->
          <div class="print-header px-6 py-4 mb-6 border-b-2 d-none">
            <div class="d-flex justify-space-between align-center">
              <div>
                <h1 class="text-h4 font-weight-black text-primary">ZadStore</h1>
                <p class="text-caption">متجر زاد - أفضل تجربة تسوق</p>
              </div>
              <div class="text-left">
                <div class="text-h6 font-weight-bold">فاتورة طلب</div>
                <div class="text-subtitle-1">
                  #{{ selectedOrder.orderNumber }}
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Order Timeline -->
          <div class="bg-primary-lighten-5 pa-4 border-b no-print">
            <v-timeline
              direction="horizontal"
              density="compact"
              align="start"
              class="timeline-custom"
            >
              <v-timeline-item
                v-for="(st, i) in orderStory"
                :key="i"
                :dot-color="
                  isStepCompleted(st.value) ? 'success' : 'grey-lighten-2'
                "
                size="x-small"
              >
                <template v-slot:icon v-if="isStepCompleted(st.value)">
                  <v-icon size="10" color="white">mdi-check</v-icon>
                </template>
                <div
                  class="text-caption font-weight-black"
                  :class="
                    isStepCompleted(st.value) ? 'text-success' : 'text-grey'
                  "
                >
                  {{ $t(`sales.orders.timeline.${st.value}`) }}
                </div>
              </v-timeline-item>
            </v-timeline>
          </div>

          <div class="pa-6">
            <v-row class="mb-4">
              <v-col cols="12" sm="6">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">
                  {{ $t("sales.orders.customer_info.title") }}
                </div>
                <div class="font-weight-bold d-flex align-center gap-2 mb-2">
                  <v-icon size="18" color="primary">mdi-account</v-icon>
                  {{ selectedOrder.customerName }}
                </div>
                <div class="text-body-2 d-flex align-center gap-2 mb-2">
                  <v-icon size="18" color="primary">mdi-phone</v-icon>
                  {{ selectedOrder.phone }}
                </div>
                <div class="text-body-2 d-flex align-start gap-2">
                  <v-icon size="18" color="primary" class="mt-1"
                    >mdi-map-marker</v-icon
                  >
                  <span>{{ selectedOrder.address }}</span>
                </div>
              </v-col>
              <v-col cols="12" sm="6">
                <div class="text-subtitle-2 text-grey-darken-1 mb-1">
                  {{ $t("sales.orders.update_status") }}
                </div>

                <!-- إشعار طلب الإرجاع -->
                <div
                  v-if="selectedOrder.returnStatus === 'requested'"
                  class="mb-4 bg-red-lighten-5 pa-3 rounded-lg border border-red-lighten-4"
                >
                  <div class="d-flex align-center gap-2 mb-2">
                    <v-icon color="error" size="20">mdi-alert-circle</v-icon>
                    <span
                      class="text-caption font-weight-bold text-red-darken-4"
                    >
                      {{ $t("sales.orders.return_request.notify") }}
                    </span>
                  </div>
                  <div class="d-flex gap-2 mt-2">
                    <v-btn
                      color="success"
                      size="small"
                      elevation="0"
                      class="flex-grow-1 font-weight-bold"
                      @click="handleReturnRequest(selectedOrder, 'approve')"
                      :loading="savingStatus"
                    >
                      {{ $t("sales.orders.return_request.approve") }}
                    </v-btn>
                    <v-btn
                      color="error"
                      variant="outlined"
                      size="small"
                      elevation="0"
                      class="flex-grow-1 font-weight-bold"
                      @click="handleReturnRequest(selectedOrder, 'reject')"
                      :loading="savingStatus"
                    >
                      {{ $t("sales.orders.return_request.reject") }}
                    </v-btn>
                  </div>
                </div>

                <v-select
                  v-else
                  v-model="selectedOrder.status"
                  :items="statusOptions"
                  variant="outlined"
                  density="compact"
                  hide-details
                  color="primary"
                  class="mb-3"
                  :loading="savingStatus"
                  :disabled="savingStatus"
                  @update:modelValue="updateOrderStatus(selectedOrder)"
                ></v-select>

                <div class="text-subtitle-2 text-grey-darken-1 mb-1">
                  تاريخ الطلب
                </div>
                <div class="font-weight-bold">
                  {{ formatDate(selectedOrder.date) }}
                </div>
              </v-col>
            </v-row>

            <v-divider class="my-4"></v-divider>

            <h3
              class="text-subtitle-1 font-weight-bold mb-3"
              style="color: rgb(var(--v-theme-primary))"
            >
              {{ $t("sales.orders.items_title") }}
            </h3>
            <v-table density="compact" class="border rounded-lg mb-4">
              <thead class="bg-grey-lighten-4">
                <tr>
                  <th class="text-right">
                    {{ $t("sales.orders.items_table.product") }}
                  </th>
                  <th class="text-center">
                    {{ $t("sales.orders.items_table.qty") }}
                  </th>
                  <th class="text-left">
                    {{ $t("sales.orders.items_table.price") }}
                  </th>
                  <th class="text-left">
                    {{ $t("sales.orders.items_table.total") }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in selectedOrder.items"
                  :key="index"
                  :style="
                    isItemReturned(selectedOrder, item)
                      ? 'opacity:0.6; background:#fff8f8;'
                      : ''
                  "
                >
                  <td class="d-flex align-center gap-3 py-2">
                    <v-avatar size="40" rounded class="bg-grey-lighten-2">
                      <v-img :src="item.image" cover></v-img>
                    </v-avatar>
                    <div>
                      <span
                        class="font-weight-bold text-caption"
                        :style="
                          isItemReturned(selectedOrder, item)
                            ? 'text-decoration:line-through;'
                            : ''
                        "
                        >{{ item.name }}</span
                      >
                      <v-chip
                        v-if="isItemReturned(selectedOrder, item)"
                        size="x-small"
                        color="error"
                        variant="tonal"
                        class="d-block mt-1"
                      >
                        {{ locale === "ar" ? "مُرجَع" : "Returned" }}
                      </v-chip>
                    </div>
                  </td>
                  <td class="text-center">{{ item.quantity }}</td>
                  <td class="text-left">
                    {{
                      Number(item.price).toLocaleString(
                        locale === "ar" ? "ar-EG" : "en-US"
                      )
                    }}
                    {{ $t("sales.orders.currency") }}
                  </td>
                  <td class="text-left font-weight-bold">
                    {{
                      Number(item.price * item.quantity).toLocaleString(
                        locale === "ar" ? "ar-EG" : "en-US"
                      )
                    }}
                    {{ $t("sales.orders.currency") }}
                  </td>
                </tr>
              </tbody>
            </v-table>

            <div class="d-flex flex-column align-end border-t pt-4">
              <div
                class="d-flex justify-space-between w-100 mb-2"
                style="max-width: 250px"
              >
                <span class="text-grey-darken-1">{{
                  $t("sales.orders.pricing.subtotal")
                }}</span>
                <span class="font-weight-bold"
                  >{{
                    Number(selectedOrder.subtotal).toLocaleString(
                      locale === "ar" ? "ar-EG" : "en-US"
                    )
                  }}
                  {{ $t("sales.orders.currency") }}</span
                >
              </div>
              <div
                class="d-flex justify-space-between w-100 mb-2"
                style="max-width: 250px"
              >
                <span class="text-grey-darken-1">{{
                  $t("sales.orders.pricing.shipping")
                }}</span>
                <span class="font-weight-bold"
                  >{{
                    Number(selectedOrder.shippingFee).toLocaleString(
                      locale === "ar" ? "ar-EG" : "en-US"
                    )
                  }}
                  {{ $t("sales.orders.currency") }}</span
                >
              </div>
              <div
                class="d-flex justify-space-between w-100 text-h6"
                style="max-width: 250px; color: rgb(var(--v-theme-primary))"
              >
                <span class="font-weight-bold">{{
                  $t("sales.orders.pricing.total")
                }}</span>
                <span class="font-weight-black text-success"
                  >{{
                    Number(selectedOrder.total).toLocaleString(
                      locale === "ar" ? "ar-EG" : "en-US"
                    )
                  }}
                  {{ $t("sales.orders.currency") }}</span
                >
              </div>
            </div>
          </div>
        </v-card-text>

        <v-card-actions class="pa-4 bg-grey-lighten-4 border-t">
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            @click="detailsDialog = false"
            >{{ $t("sales.orders.btns.close") }}</v-btn
          >
          <v-btn
            color="success"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            @click="printInvoice"
          >
            طباعة الفاتورة
            <v-icon left class="mr-2">mdi-printer</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="60" class="mb-3"
          >mdi-alert-circle-outline</v-icon
        >
        <v-card-title class="text-h6 font-weight-bold text-wrap">{{
          $t("sales.orders.messages.delete_confirm")
        }}</v-card-title>
        <v-card-actions class="d-flex justify-center mt-4">
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            >{{ $t("catalog.cancel") }}</v-btn
          >
          <v-btn
            color="error"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :loading="deleting"
            :disabled="deleting"
            @click="confirmDelete"
            >{{ $t("sales.orders.delete_selected") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Batch Delete Dialog -->
    <v-dialog v-model="batchDeleteDialog" max-width="400px">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="60" class="mb-3">mdi-delete-alert</v-icon>
        <v-card-title class="text-h6 font-weight-bold text-wrap">
          {{
            $t("sales.orders.messages.batch_delete_confirm", {
              count: selectedIds.length,
            })
          }}
        </v-card-title>
        <v-card-text>{{
          $t("sales.orders.messages.delete_warning")
        }}</v-card-text>
        <v-card-actions class="d-flex justify-center mt-4">
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            @click="batchDeleteDialog = false"
            >{{ $t("catalog.cancel") }}</v-btn
          >
          <v-btn
            color="error"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :loading="deleting"
            :disabled="deleting"
            @click="confirmBatchDelete"
            >{{ $t("sales.orders.delete_selected") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from "vue";
import { useI18n } from "vue-i18n";
import api from "../../axios";

const { t, locale } = useI18n();

const emitter = inject("emitter");

// --- بيانات الطلبات ---
const orders = ref([]);
const loading = ref(true);
const savingStatus = ref(false);

const startDate = ref("");
const endDate = ref("");

const selectedIds = ref([]);
const batchDeleteDialog = ref(false);

const selectAll = computed({
  get: () =>
    filteredOrders.value.length > 0 &&
    selectedIds.value.length === filteredOrders.value.length,
  set: (val) => {
    if (val) {
      selectedIds.value = filteredOrders.value.map((o) => o.id);
    } else {
      selectedIds.value = [];
    }
  },
});

const confirmBatchDelete = async () => {
  if (selectedIds.value.length === 0) return;
  deleting.value = true;
  try {
    await api.post("/admin/orders/batch-delete", { ids: selectedIds.value });
    showMessage(t("sales.orders.messages.delete_success"), "success");
    orders.value = orders.value.filter(
      (o) => !selectedIds.value.includes(o.id)
    );
    selectedIds.value = [];
    batchDeleteDialog.value = false;
  } catch (e) {
    console.error(e);
    showMessage(t("sales.orders.messages.error"), "error");
  } finally {
    deleting.value = false;
  }
};

const showMessage = (text, color = "success") => {
  emitter.emit("showMessage", { text, color });
};

const STATUS_MAP_EN_AR = {
  pending: t("sales.orders.status.pending"),
  processing: t("sales.orders.status.processing"),
  shipped: t("sales.orders.status.shipped"),
  completed: t("sales.orders.status.completed"),
  cancelled: t("sales.orders.status.cancelled"),
  returned: t("sales.orders.status.returned"),
  partially_returned: t("sales.orders.status.partially_returned"),
};

const STATUS_MAP_AR_EN = Object.fromEntries(
  Object.entries(STATUS_MAP_EN_AR).map(([k, v]) => [v, k])
);

const fetchOrders = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const params = {};
    if (startDate.value) params.start_date = startDate.value;
    if (endDate.value) params.end_date = endDate.value;

    const res = await api.get("/admin/orders", { params });
    orders.value = res.data.map((o) => ({
      id: o.id,
      orderNumber: o.order_number || "ORD-" + o.id,
      customerName: o.customer_name || t("reviews.unknown_user"),
      phone: o.phone || "",
      address: o.address || "",
      date: o.created_at,
      status: STATUS_MAP_EN_AR[o.status] || o.status,
      paymentStatus: o.payment_status || "unpaid",
      paymentMethod: o.payment_method || "cod",
      isUrgent: o.is_urgent,
      returnStatus: o.return_request_status,
      subtotal: o.subtotal || o.total_price,
      shippingFee: o.shipping_fee || 0,
      total: o.total || o.total_price,
      items: o.items.map((i) => ({
        name: i.product
          ? i.product.title
          : i.name || t("reviews.deleted_product"),
        price: i.price,
        quantity: i.quantity,
        image: i.product ? i.product.thumbnail : i.image || "",
      })),
    }));
  } catch (error) {
    console.error("Error fetching orders:", error);
  } finally {
    if (showLoading) loading.value = false;
  }
};

const exportToCSV = () => {
  const headers = ["Order#", "Customer", "Date", "Status", "Payment", "Total"];
  const rows = filteredOrders.value.map((o) => [
    o.orderNumber,
    o.customerName,
    o.date,
    o.status,
    o.paymentStatus,
    o.total,
  ]);

  const BOM = "\uFEFF";
  let csvContent =
    BOM + headers.join(",") + "\n" + rows.map((e) => e.join(",")).join("\n");
  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.setAttribute("href", url);
  link.setAttribute(
    "download",
    `orders_${new Date().toISOString().split("T")[0]}.csv`
  );
  document.body.appendChild(link);
  link.click();
};

const orderStory = [
  { label: t("sales.orders.timeline.ordered"), value: "pending" },
  { label: t("sales.orders.timeline.processing"), value: "processing" },
  { label: t("sales.orders.timeline.shipped"), value: "shipped" },
  { label: t("sales.orders.timeline.completed"), value: "completed" },
];

const isStepCompleted = (statusValue) => {
  if (!selectedOrder.value) return false;
  const currentStatusEn =
    STATUS_MAP_AR_EN[selectedOrder.value.status] || selectedOrder.value.status;
  const statusLevels = { pending: 1, processing: 2, shipped: 3, completed: 4 };
  return statusLevels[statusValue] <= statusLevels[currentStatusEn];
};

const resetFilters = () => {
  startDate.value = "";
  endDate.value = "";
  searchQuery.value = "";
  statusFilter.value = null;
  fetchOrders();
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchOrders(false), 20000); // 20 seconds
};

onMounted(() => {
  fetchOrders();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchOrders(false);
      startPolling();
    } else {
      if (pollInterval) clearInterval(pollInterval);
    }
  });
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});

// --- حالات الفلترة والبحث ---
const searchQuery = ref("");
const statusFilter = ref(null);
const statusOptions = Object.values(STATUS_MAP_EN_AR);

// --- حالة النافذة (Dialog) ---
const detailsDialog = ref(false);
const selectedOrder = ref(null);

const deleteDialog = ref(false);
const orderToDelete = ref(null);
const deleting = ref(false);

const isItemReturned = (order, item) => {
  if (!order.returnStatus || order.returnStatus !== "approved") return false;
  if (order.status === "مرتجع") return true;
  if (order.status === "مرتجع جزئياً" && order.return_target_items) {
    const targetIds = Array.isArray(order.return_target_items)
      ? order.return_target_items
      : JSON.parse(order.return_target_items || "[]");
    return targetIds.includes(item.id || item.product_id); // Fallback to product_id if id is not available
  }
  return item.is_returned === true || item.is_returned === 1;
};

// --- الدوال (Functions) ---

// تنسيق التاريخ
const formatDate = (dateString) => {
  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  return new Date(dateString).toLocaleDateString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    options
  );
};

// تحديد لون الـ Chip بناءً على الحالة
const getStatusColor = (status) => {
  switch (status) {
    case t("sales.orders.status.pending"):
      return "warning";
    case t("sales.orders.status.processing"):
      return "info";
    case t("sales.orders.status.shipped"):
      return "primary";
    case t("sales.orders.status.completed"):
      return "success";
    case t("sales.orders.status.cancelled"):
      return "error";
    case t("sales.orders.status.returned"):
      return "black";
    case t("sales.orders.status.partially_returned"):
      return "orange";
    default:
      return "grey";
  }
};

// تحديد اسم الحالة (نص)
const getStatusText = (status) => {
  // If it's already translated (AR), we might need to map it back or just return it
  // But usually we store the EN value in the database and translate it here
  const enStatus = STATUS_MAP_AR_EN[status] || status;
  return STATUS_MAP_EN_AR[enStatus] || status;
};

// فلترة الطلبات (بحث + فلتر حالة)
const filteredOrders = computed(() => {
  return orders.value.filter((order) => {
    // فلترة البحث (بالرقم أو الاسم)
    const matchesSearch =
      order.orderNumber
        .toLowerCase()
        .includes(searchQuery.value.toLowerCase()) ||
      order.customerName
        .toLowerCase()
        .includes(searchQuery.value.toLowerCase());

    // فلترة الحالة
    const matchesStatus = statusFilter.value
      ? order.status === statusFilter.value
      : true;

    return matchesSearch && matchesStatus;
  });
});

// فتح نافذة التفاصيل
const openOrderDetails = (order) => {
  selectedOrder.value = { ...order }; // ناخد نسخة عشان نعرضها في البوب أب
  detailsDialog.value = true;
};

// تحديث حالة الطلب في المصفوفة الأصلية
const updateOrderStatus = async (order) => {
  savingStatus.value = true;
  try {
    const dbStatus = STATUS_MAP_AR_EN[order.status] || order.status;
    await api.put(`/admin/orders/${order.id}/status`, { status: dbStatus });

    // التحديث بالمصفوفة
    const index = orders.value.findIndex((o) => o.id === order.id);
    if (index !== -1) {
      orders.value[index].status = order.status;
    }

    showMessage(t("sales.orders.messages.status_success"));
  } catch (error) {
    console.error("Error updating order status:", error);
    showMessage(
      error.response?.data?.message || "حدث خطأ أثناء التحديث",
      "error"
    );
  } finally {
    savingStatus.value = false;
  }
};

// معالجة طلب الإرجاع (موافقة / رفض)
const handleReturnRequest = async (order, action) => {
  savingStatus.value = true;
  try {
    const res = await api.put(`/admin/orders/${order.id}/handle-return`, {
      action,
    });
    const updatedOrder = res.data.order;
    const newStatusEn = updatedOrder.status;
    const newStatusAr = STATUS_MAP_EN_AR[newStatusEn] || newStatusEn;

    // Update local object softly
    const index = orders.value.findIndex((o) => o.id === order.id);
    if (index !== -1) {
      orders.value[index].status = newStatusAr;
      orders.value[index].returnStatus = updatedOrder.return_request_status;

      if (selectedOrder.value && selectedOrder.value.id === order.id) {
        selectedOrder.value.status = newStatusAr;
        selectedOrder.value.returnStatus = updatedOrder.return_request_status;
      }
    }
  } catch (error) {
    console.error("Error handling return:", error);
    showMessage(t("sales.orders.messages.error"), "error");
  } finally {
    savingStatus.value = false;
  }
};

// طباعة الفاتورة
const printInvoice = () => {
  setTimeout(() => {
    window.print();
  }, 500);
};

// مسح الطلب
const openDeleteDialog = (order) => {
  orderToDelete.value = order;
  deleteDialog.value = true;
};

const confirmDelete = async () => {
  if (!orderToDelete.value) return;
  deleting.value = true;
  try {
    const res = await api.delete(`/admin/orders/${orderToDelete.value.id}`);
    showMessage(res.data.message || t("sales.orders.messages.delete_success"));
    orders.value = orders.value.filter((o) => o.id !== orderToDelete.value.id);
  } catch (error) {
    console.error("Error deleting order:", error);
    showMessage("حدث خطأ أثناء الحذف", "error");
  } finally {
    deleting.value = false;
    deleteDialog.value = false;
    orderToDelete.value = null;
  }
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.shadow-btn {
  box-shadow: 0 4px 12px rgba(24, 103, 192, 0.3) !important;
  transition: transform 0.2s ease;
}
.shadow-btn:hover {
  transform: translateY(-2px);
}

.orders-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 15px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.orders-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
  padding-top: 12px !important;
  padding-bottom: 12px !important;
}

.hover-row {
  transition: background-color 0.2s ease;
}

.hover-row:hover {
  background-color: #fcfcfc;
}

.date-input {
  border: none;
  background: transparent;
  padding: 4px;
  font-family: inherit;
  font-size: 13px;
  outline: none;
}

.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}

/* تحسين شكل الجدول الداخلي للتفاصيل */
.border {
  border: 1px solid #eee;
}
.border-t {
  border-top: 1px solid #eee;
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.8;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

.pulse-icon {
  animation: pulse 1.5s infinite ease-in-out;
}

@media print {
  body * {
    visibility: hidden;
  }
  .printable-area,
  .printable-area * {
    visibility: visible;
  }
  .printable-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    background: white !important;
    z-index: 9999;
    overflow: visible !important;
    max-height: none !important;
  }
  .print-header {
    display: block !important;
    border-bottom: 2px solid #000 !important;
    margin-bottom: 30px !important;
  }
  .no-print,
  .v-btn,
  .v-field,
  .v-timeline,
  .v-card-actions,
  .v-dialog-content__scroller {
    display: none !important;
  }
  .v-card {
    box-shadow: none !important;
    border: none !important;
  }
  /* Force table layout for printing */
  .v-table {
    background: white !important;
  }
  .v-table th {
    background: #f0f0f0 !important;
    color: black !important;
    border: 1px solid #ddd !important;
  }
  .v-table td {
    border: 1px solid #ddd !important;
  }
  /* Ensure text is black for printing */
  .printable-area * {
    color: black !important;
    box-shadow: none !important;
  }
}

.timeline-custom :deep(.v-timeline-divider__dot) {
  background: white !important;
}
</style>
