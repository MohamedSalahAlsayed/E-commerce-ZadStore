<template>
  <v-locale-provider rtl>
    <v-container fluid class="pa-6" dir="rtl">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text
          class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
        >
          <h2
            class="text-h5 font-weight-black"
            style="color: rgb(var(--v-theme-primary))"
          >
            إدارة الطلبات
          </h2>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 900px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="رقم الطلب أو اسم العميل..."
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg flex-grow-1"
            ></v-text-field>

            <v-select
              v-model="statusFilter"
              :items="statusOptions"
              label="حالة الطلب"
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
              تصدير (CSV)
            </v-btn>

            <div
              class="d-flex align-center gap-2 bg-white pa-1 rounded-lg border"
            >
              <span class="text-caption font-weight-bold px-2">من:</span>
              <input
                type="date"
                v-model="startDate"
                class="date-input"
                @change="fetchOrders"
              />
              <span class="text-caption font-weight-bold px-2">إلى:</span>
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

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <div class="table-responsive">
          <v-table class="orders-table pa-2">
            <thead class="bg-grey-lighten-4">
              <tr>
                <th class="text-right font-weight-bold">رقم الطلب</th>
                <th class="text-right font-weight-bold">العميل</th>
                <th class="text-right font-weight-bold">التاريخ / الوقت</th>
                <th class="text-right font-weight-bold">الدفع</th>
                <th class="text-right font-weight-bold">الإجمالي</th>
                <th class="text-right font-weight-bold">الحالة</th>
                <th class="text-center font-weight-bold">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="text-center pa-6">
                  <v-progress-circular
                    indeterminate
                    color="primary"
                  ></v-progress-circular>
                </td>
              </tr>
              <tr v-else-if="filteredOrders.length === 0">
                <td colspan="7" class="text-center pa-6 text-grey-darken-1">
                  لا توجد طلبات مطابقة للبحث.
                </td>
              </tr>

              <tr
                v-for="order in filteredOrders"
                :key="order.id"
                class="hover-row"
              >
                <td
                  class="font-weight-bold text-primary d-flex align-center gap-1"
                >
                  #{{ order.orderNumber }}
                  <v-tooltip
                    text="طلب مستعجل!"
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
                          ? "تم الدفع"
                          : "لم يتم الدفع"
                      }}
                    </v-chip>
                    <span class="text-caption text-grey">{{
                      order.paymentMethod?.toUpperCase() || ""
                    }}</span>
                  </div>
                </td>
                <td class="font-weight-bold text-success" dir="ltr">
                  {{ Number(order.total).toLocaleString("ar-EG") }} ج.م
                </td>
                <td>
                  <v-chip
                    size="small"
                    :color="getStatusColor(order.status)"
                    class="font-weight-bold"
                  >
                    {{ order.status }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <v-btn
                    color="rgb(var(--v-theme-primary))"
                    variant="tonal"
                    size="small"
                    class="font-weight-bold rounded-lg px-4"
                    @click="openOrderDetails(order)"
                  >
                    التفاصيل
                    <v-icon right size="18" class="ml-1"
                      >mdi-eye-outline</v-icon
                    >
                  </v-btn>
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
              تفاصيل الطلب #{{ selectedOrder.orderNumber }}
            </span>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="detailsDialog = false"
            ></v-btn>
          </v-card-title>

          <v-card-text class="pa-0 printable-area" style="max-height: 80vh">
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
                    {{ st.label }}
                  </div>
                </v-timeline-item>
              </v-timeline>
            </div>

            <div class="pa-6">
              <v-row class="mb-4">
                <v-col cols="12" sm="6">
                  <div class="text-subtitle-2 text-grey-darken-1 mb-1">
                    بيانات العميل
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
                    تحديث الحالة
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
                        قدم العميل طلب استرجاع لهذا الطلب
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
                        الموافقة
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
                        رفض الطلب
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
                المنتجات المطلوبة
              </h3>
              <v-table density="compact" class="border rounded-lg mb-4">
                <thead class="bg-grey-lighten-4">
                  <tr>
                    <th class="text-right">المنتج</th>
                    <th class="text-center">الكمية</th>
                    <th class="text-left">السعر</th>
                    <th class="text-left">الإجمالي</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in selectedOrder.items" :key="index">
                    <td class="d-flex align-center gap-3 py-2">
                      <v-avatar size="40" rounded class="bg-grey-lighten-2">
                        <v-img :src="item.image" cover></v-img>
                      </v-avatar>
                      <span class="font-weight-bold text-caption">{{
                        item.name
                      }}</span>
                    </td>
                    <td class="text-center">{{ item.quantity }}</td>
                    <td class="text-left">
                      {{ Number(item.price).toLocaleString("ar-EG") }} ج.م
                    </td>
                    <td class="text-left font-weight-bold">
                      {{
                        Number(item.price * item.quantity).toLocaleString(
                          "ar-EG"
                        )
                      }}
                      ج.م
                    </td>
                  </tr>
                </tbody>
              </v-table>

              <div class="d-flex flex-column align-end border-t pt-4">
                <div
                  class="d-flex justify-space-between w-100 mb-2"
                  style="max-width: 250px"
                >
                  <span class="text-grey-darken-1">المجموع الفرعي:</span>
                  <span class="font-weight-bold"
                    >{{
                      Number(selectedOrder.subtotal).toLocaleString("ar-EG")
                    }}
                    ج.م</span
                  >
                </div>
                <div
                  class="d-flex justify-space-between w-100 mb-2"
                  style="max-width: 250px"
                >
                  <span class="text-grey-darken-1">رسوم الشحن:</span>
                  <span class="font-weight-bold"
                    >{{
                      Number(selectedOrder.shippingFee).toLocaleString("ar-EG")
                    }}
                    ج.م</span
                  >
                </div>
                <div
                  class="d-flex justify-space-between w-100 text-h6"
                  style="max-width: 250px; color: rgb(var(--v-theme-primary))"
                >
                  <span class="font-weight-bold">الإجمالي:</span>
                  <span class="font-weight-black text-success"
                    >{{
                      Number(selectedOrder.total).toLocaleString("ar-EG")
                    }}
                    ج.م</span
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
              >إغلاق</v-btn
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
    </v-container>
  </v-locale-provider>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from "vue";
import api from "../../axios";

const emitter = inject("emitter");

// --- بيانات الطلبات ---
const orders = ref([]);
const loading = ref(true);
const savingStatus = ref(false);

const startDate = ref("");
const endDate = ref("");

const showMessage = (text, color = "success") => {
  emitter.emit("showMessage", { text, color });
};

const STATUS_MAP_EN_AR = {
  pending: "قيد الانتظار",
  processing: "جاري التجهيز",
  shipped: "تم الشحن",
  completed: "مكتمل",
  cancelled: "ملغي",
  returned: "مُرتجع",
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
      customerName: o.customer_name || "غير محدد",
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
        name: i.product ? i.product.title : i.name || "منتج محذوف",
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

  let csvContent =
    "data:text/csv;charset=utf-8," +
    headers.join(",") +
    "\n" +
    rows.map((e) => e.join(",")).join("\n");
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute(
    "download",
    `orders_${new Date().toISOString().split("T")[0]}.csv`
  );
  document.body.appendChild(link);
  link.click();
};

const orderStory = [
  { label: "تم الطلب", value: "pending" },
  { label: "تجهيز", value: "processing" },
  { label: "شحن", value: "shipped" },
  { label: "اكتمال", value: "completed" },
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
  return new Date(dateString).toLocaleDateString("ar-EG", options);
};

// تحديد لون الـ Chip بناءً على الحالة
const getStatusColor = (status) => {
  switch (status) {
    case "قيد الانتظار":
      return "warning";
    case "جاري التجهيز":
      return "info";
    case "تم الشحن":
      return "primary";
    case "مكتمل":
      return "success";
    case "ملغي":
      return "error";
    case "مُرتجع":
      return "black";
    default:
      return "grey";
  }
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

    showMessage("تم تحديث حالة الطلب بنجاح!");
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
    showMessage(res.data.message);

    // Update local object softly
    const index = orders.value.findIndex((o) => o.id === order.id);
    if (index !== -1) {
      orders.value[index].returnStatus =
        action === "approve" ? "approved" : "rejected";
      if (action === "approve") {
        orders.value[index].status = STATUS_MAP_EN_AR["returned"] || "مُرتجع";
        if (selectedOrder.value && selectedOrder.value.id === order.id) {
          selectedOrder.value.status = STATUS_MAP_EN_AR["returned"] || "مُرتجع";
          selectedOrder.value.returnStatus = "approved";
        }
      } else {
        if (selectedOrder.value && selectedOrder.value.id === order.id) {
          selectedOrder.value.returnStatus = "rejected";
        }
      }
    }
  } catch (error) {
    console.error("Error handling return:", error);
    showMessage("حدث خطأ أثناء معالجة الطلب", "error");
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
  .no-print,
  .v-btn,
  .v-field,
  .date-input,
  header,
  nav {
    display: none !important;
  }
  .printable-area {
    display: block !important;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    max-height: none !important;
  }
  .v-card {
    box-shadow: none !important;
    border: none !important;
  }
}

.timeline-custom :deep(.v-timeline-divider__dot) {
  background: white !important;
}
</style>
