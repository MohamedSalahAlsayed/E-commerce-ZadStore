<template>
  <v-container fluid class="pa-6" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <!-- Header Section -->
    <v-card class="mb-6 rounded-xl" elevation="1">
      <v-card-text
        class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
      >
        <div class="d-flex align-center gap-3">
          <v-avatar color="primary" variant="tonal" size="48">
            <v-icon color="primary">mdi-receipt-refund-outline</v-icon>
          </v-avatar>
          <div>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              {{ $t("returns.title") }}
            </h2>
            <p class="text-caption text-grey-darken-1 mb-0">
              {{ $t("returns.subtitle") }}
            </p>
          </div>
        </div>

        <v-text-field
          v-model="searchQuery"
          prepend-inner-icon="mdi-magnify"
          :label="$t('returns.search_placeholder')"
          variant="outlined"
          density="compact"
          hide-details
          class="bg-white rounded-lg"
          style="max-width: 300px"
        ></v-text-field>
      </v-card-text>
    </v-card>

    <!-- Summary Bar -->
    <v-row class="mb-6">
      <v-col cols="12" sm="4">
        <v-card class="rounded-xl pa-4 d-flex align-center gap-4" elevation="1">
          <v-avatar color="warning" variant="tonal" size="50">
            <v-icon>mdi-clock-outline</v-icon>
          </v-avatar>
          <div>
            <div class="text-caption text-grey">
              {{ $t("returns.pending_label") }}
            </div>
            <div class="text-h5 font-weight-black">{{ pendingCount }}</div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4">
        <v-card class="rounded-xl pa-4 d-flex align-center gap-4" elevation="1">
          <v-avatar color="success" variant="tonal" size="50">
            <v-icon>mdi-check-circle-outline</v-icon>
          </v-avatar>
          <div>
            <div class="text-caption text-grey">
              {{ $t("returns.approved_label") }}
            </div>
            <div class="text-h5 font-weight-black">{{ approvedCount }}</div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="4">
        <v-card class="rounded-xl pa-4 d-flex align-center gap-4" elevation="1">
          <v-avatar color="error" variant="tonal" size="50">
            <v-icon>mdi-close-circle-outline</v-icon>
          </v-avatar>
          <div>
            <div class="text-caption text-grey">
              {{ $t("returns.rejected_label") }}
            </div>
            <div class="text-h5 font-weight-black">{{ rejectedCount }}</div>
          </div>
        </v-card>
      </v-col>
    </v-row>

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
              {{ $t("returns.selected_count", { count: selectedIds.length }) }}
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="white"
              variant="tonal"
              prepend-icon="mdi-delete-multiple"
              @click="batchDeleteDialog = true"
            >
              {{ $t("returns.delete_selected") }}
            </v-btn>
            <v-btn color="white" variant="outlined" @click="selectedIds = []">{{
              $t("dashboard.cancel")
            }}</v-btn>
          </div>
        </div>
      </v-card>
    </v-expand-transition>

    <!-- Returns Table -->
    <v-card class="rounded-xl overflow-hidden" elevation="1">
      <div class="table-responsive">
        <v-table class="returns-table pa-2">
          <thead class="bg-grey-lighten-4">
            <tr>
              <th class="text-right font-weight-bold p-4" style="width: 50px">
                <v-checkbox-btn
                  v-model="selectAll"
                  color="primary"
                  hide-details
                ></v-checkbox-btn>
              </th>
              <th class="text-right font-weight-bold p-4">
                {{ $t("returns.order_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("returns.customer_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("returns.date_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("returns.amount_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("returns.reason_th") }}
              </th>
              <th class="text-right font-weight-bold">
                {{ $t("dashboard.status_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("returns.actions_th") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="i in 5" :key="`skel-return-${i}`">
                <td class="px-2"><v-skeleton-loader type="list-item" /></td>
                <td class="px-2 py-3">
                  <v-skeleton-loader type="text" style="max-width: 100px" />
                </td>
                <td class="px-2">
                  <div class="d-flex flex-column gap-1 w-100">
                    <v-skeleton-loader type="text" style="max-width: 120px" />
                    <v-skeleton-loader type="text" style="max-width: 80px" />
                  </div>
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" style="max-width: 100px" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" style="max-width: 100px" />
                </td>
                <td class="px-2"><v-skeleton-loader type="chip" /></td>
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
            <tr v-else-if="filteredReturns.length === 0">
              <td colspan="8" class="text-center pa-6 text-grey-darken-1">
                {{ $t("returns.no_returns") }}
              </td>
            </tr>

            <tr
              v-for="order in filteredReturns"
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
              <td class="font-weight-bold text-primary">
                #{{ order.order_number }}
              </td>
              <td>
                <div class="font-weight-bold">{{ order.user?.name }}</div>
                <div class="text-caption text-grey">
                  {{ order.phone || order.user?.phone }}
                </div>
              </td>
              <td class="text-body-2">{{ formatDate(order.created_at) }}</td>
              <td class="font-weight-bold text-success" dir="ltr">
                {{ Number(order.total).toLocaleString() }}
                {{ $t("products.currency") }}
              </td>
              <td>
                <v-chip
                  size="x-small"
                  color="primary"
                  variant="outlined"
                  class="font-weight-bold"
                >
                  {{ getReasonText(order.return_reason_code) }}
                </v-chip>
              </td>
              <td>
                <v-chip
                  size="small"
                  :color="getReturnStatusColor(order.return_request_status)"
                  variant="flat"
                  class="font-weight-bold"
                >
                  <v-icon left size="14" class="ml-1">{{
                    getReturnStatusIcon(order.return_request_status)
                  }}</v-icon>
                  {{ getReturnStatusText(order.return_request_status) }}
                </v-chip>
              </td>
              <td class="text-center">
                <v-btn
                  icon="mdi-eye-outline"
                  variant="text"
                  color="info"
                  size="small"
                  class="ml-1"
                  @click="openDetails(order)"
                ></v-btn>

                <template v-if="order.return_request_status === 'pending'">
                  <v-btn
                    icon="mdi-check-circle-outline"
                    variant="text"
                    color="success"
                    size="small"
                    class="ml-1"
                    @click="confirmAction(order, 'approve')"
                  ></v-btn>
                  <v-btn
                    icon="mdi-close-circle-outline"
                    variant="text"
                    color="error"
                    size="small"
                    @click="confirmAction(order, 'reject')"
                  ></v-btn>
                </template>

                <v-tooltip
                  :text="$t('returns.delete_order_tooltip')"
                  location="top"
                >
                  <template v-slot:activator="{ props }">
                    <v-btn
                      v-bind="props"
                      icon="mdi-delete"
                      variant="text"
                      color="error"
                      size="small"
                      @click="openDeleteDialog(order)"
                    ></v-btn>
                  </template>
                </v-tooltip>
              </td>
            </tr>
          </tbody>
        </v-table>
      </div>
    </v-card>

    <!-- Details Dialog -->
    <v-dialog v-model="detailsDialog" max-width="600" scrollable>
      <v-card v-if="selectedOrder" class="rounded-xl overflow-hidden">
        <v-toolbar color="white" flat class="border-b px-4">
          <v-toolbar-title class="font-weight-black text-primary">{{
            $t("returns.details_title")
          }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="detailsDialog = false"
            ><v-icon>mdi-close</v-icon></v-btn
          >
        </v-toolbar>

        <v-card-text class="pa-6">
          <div class="mb-6 bg-grey-lighten-4 pa-4 rounded-lg">
            <h3 class="text-subtitle-1 font-weight-bold mb-3 border-b pb-2">
              {{ $t("returns.reason_label") }}
            </h3>
            <div class="d-flex flex-column gap-1">
              <v-chip
                v-if="selectedOrder.return_reason_code"
                size="small"
                color="primary"
                variant="flat"
                class="mb-2 w-fit-content"
              >
                {{ getReasonText(selectedOrder.return_reason_code) }}
              </v-chip>
              <p class="text-body-1 font-italic text-grey-darken-3">
                "{{ selectedOrder.return_reason || $t("returns.no_details") }}"
              </p>
            </div>
          </div>

          <h3 class="text-subtitle-1 font-weight-bold mb-3">
            {{ $t("returns.order_items") }}
          </h3>
          <v-card variant="outlined" class="rounded-lg mb-4">
            <v-list density="compact">
              <v-list-item
                v-for="item in selectedOrder.items"
                :key="item.id"
                class="border-b last-child-no-border"
              >
                <template v-slot:prepend>
                  <v-img
                    :src="item.product?.thumbnail"
                    width="50"
                    class="rounded mr-3 ml-3"
                    cover
                  ></v-img>
                </template>
                <v-list-item-title class="font-weight-bold">{{
                  item.product?.title
                }}</v-list-item-title>
                <v-list-item-subtitle
                  >{{ item.quantity }} × {{ item.price }}
                  {{ $t("products.currency") }}</v-list-item-subtitle
                >
              </v-list-item>
            </v-list>
          </v-card>

          <div class="d-flex justify-space-between align-center px-2">
            <span class="font-weight-bold">{{
              $t("returns.total_to_refund")
            }}</span>
            <span class="text-h6 font-weight-black text-primary"
              >{{ Number(selectedOrder.total).toLocaleString() }}
              {{ $t("products.currency") }}</span
            >
          </div>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions
          class="pa-4 bg-grey-lighten-5"
          v-if="selectedOrder.return_request_status === 'pending'"
        >
          <v-spacer></v-spacer>
          <v-btn
            color="success"
            variant="elevated"
            elevation="2"
            class="px-6 rounded-lg font-weight-bold"
            prepend-icon="mdi-check"
            @click="confirmAction(selectedOrder, 'approve')"
            :loading="actionLoading"
            >{{ $t("returns.approve_btn") }}</v-btn
          >
          <v-btn
            color="error"
            variant="outlined"
            class="px-6 rounded-lg font-weight-bold mr-3"
            prepend-icon="mdi-close"
            @click="confirmAction(selectedOrder, 'reject')"
            :loading="actionLoading"
            >{{ $t("returns.reject_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Confirmation Dialog -->
    <v-dialog v-model="confirmDialog" max-width="400">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-bold text-primary mb-2">
          {{ $t("returns.confirm_action_title") }}
        </v-card-title>
        <v-card-text>
          {{
            $t("returns.confirm_action_msg", {
              action: confirmActionText,
              number: pendingActionOrder?.order_number,
            })
          }}
        </v-card-text>
        <v-card-actions class="pt-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="confirmDialog = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            :color="confirmActionColor"
            variant="flat"
            class="px-6 rounded-lg"
            @click="executeConfirmedAction"
            :loading="actionLoading"
          >
            {{ $t("dashboard.confirm") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="60" class="mb-3"
          >mdi-alert-circle-outline</v-icon
        >
        <v-card-title class="text-h6 font-weight-bold text-wrap">{{
          $t("returns.delete_confirm_title")
        }}</v-card-title>
        <v-card-text>{{ $t("returns.delete_confirm_msg_body") }}</v-card-text>
        <v-card-actions class="d-flex justify-center mt-4">
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            @click="deleteDialog = false"
            >{{ $t("dashboard.cancel") }}</v-btn
          >
          <v-btn
            color="error"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :loading="deleting"
            :disabled="deleting"
            @click="confirmDelete"
            >{{ $t("returns.confirm_delete_btn") }}</v-btn
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
            $t("returns.batch_delete_confirm_title", {
              count: selectedIds.length,
            })
          }}
        </v-card-title>
        <v-card-text>{{
          $t("returns.batch_delete_confirm_msg_body")
        }}</v-card-text>
        <v-card-actions class="d-flex justify-center mt-4">
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            @click="batchDeleteDialog = false"
            >{{ $t("dashboard.cancel") }}</v-btn
          >
          <v-btn
            color="error"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :loading="deleting"
            :disabled="deleting"
            @click="confirmBatchDelete"
            >{{ $t("returns.confirm_batch_delete_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, inject } from "vue";
import api from "../../axios";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const emitter = inject("emitter");

const loading = ref(true);
const actionLoading = ref(false);
const returns = ref([]);
const searchQuery = ref("");
const detailsDialog = ref(false);
const selectedOrder = ref(null);

const confirmDialog = ref(false);
const pendingActionOrder = ref(null);
const pendingActionType = ref("");

const selectedIds = ref([]);
const batchDeleteDialog = ref(false);

const selectAll = computed({
  get: () =>
    filteredReturns.value.length > 0 &&
    selectedIds.value.length === filteredReturns.value.length,
  set: (val) => {
    if (val) {
      selectedIds.value = filteredReturns.value.map((o) => o.id);
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
    emitter.emit("showMessage", {
      text: t("returns.batch_delete_success"),
      color: "success",
    });
    returns.value = returns.value.filter(
      (o) => !selectedIds.value.includes(o.id)
    );
    selectedIds.value = [];
    batchDeleteDialog.value = false;
  } catch (e) {
    console.error(e);
    emitter.emit("showMessage", {
      text: t("returns.batch_delete_error"),
      color: "error",
    });
  } finally {
    deleting.value = false;
  }
};

const deleteDialog = ref(false);
const orderToDelete = ref(null);
const deleting = ref(false);

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchReturns(true);
  }, 20000); // 20 seconds
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
    fetchReturns(true);
    startPolling();
  }
};
// ----------------------------------

const confirmActionText = computed(() =>
  pendingActionType.value === "approve"
    ? t("returns.approve_action")
    : t("returns.reject_action")
);
const confirmActionColor = computed(() =>
  pendingActionType.value === "approve" ? "success" : "error"
);

const pendingCount = computed(
  () =>
    returns.value.filter((r) => r.return_request_status === "pending").length
);
const approvedCount = computed(
  () =>
    returns.value.filter((r) => r.return_request_status === "approved").length
);
const rejectedCount = computed(
  () =>
    returns.value.filter((r) => r.return_request_status === "rejected").length
);

const fetchReturns = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await api.get("/admin/return-requests");
    returns.value = res.data;
  } catch (error) {
    console.error("Error fetching returns:", error);
    if (!quiet) {
      emitter.emit("showMessage", {
        text: t("dashboard.error_loading"),
        color: "error",
      });
    }
  } finally {
    if (!quiet) loading.value = false;
  }
};

const handleReturn = async (orderId, action) => {
  actionLoading.value = true;
  try {
    await api.put(`/admin/orders/${orderId}/handle-return`, { action });
    emitter.emit("showMessage", {
      text:
        action === "approve"
          ? t("returns.approve_success")
          : t("returns.reject_success"),
      color: action === "approve" ? "success" : "warning",
    });
    detailsDialog.value = false;
    confirmDialog.value = false;
    fetchReturns(true);
  } catch (error) {
    console.error("Error handling return:", error);
    emitter.emit("showMessage", {
      text: t("returns.handle_error"),
      color: "error",
    });
  } finally {
    actionLoading.value = false;
  }
};

const openDetails = (order) => {
  selectedOrder.value = order;
  detailsDialog.value = true;
};

const confirmAction = (order, action) => {
  pendingActionOrder.value = order;
  pendingActionType.value = action;
  confirmDialog.value = true;
};

const executeConfirmedAction = () => {
  if (pendingActionOrder.value && pendingActionType.value) {
    handleReturn(pendingActionOrder.value.id, pendingActionType.value);
  }
};

const openDeleteDialog = (order) => {
  orderToDelete.value = order;
  deleteDialog.value = true;
};

const confirmDelete = async () => {
  if (!orderToDelete.value) return;
  deleting.value = true;
  try {
    await api.delete(`/admin/orders/${orderToDelete.value.id}`);
    emitter.emit("showMessage", {
      text: t("returns.delete_success"),
      color: "success",
    });
    returns.value = returns.value.filter(
      (r) => r.id !== orderToDelete.value.id
    );
  } catch (error) {
    console.error("Error deleting order:", error);
    emitter.emit("showMessage", {
      text: t("returns.delete_error"),
      color: "error",
    });
  } finally {
    deleting.value = false;
    deleteDialog.value = false;
    orderToDelete.value = null;
  }
};

const filteredReturns = computed(() => {
  if (!searchQuery.value) return returns.value;
  return returns.value.filter(
    (r) =>
      r.order_number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      r.user?.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    {
      year: "numeric",
      month: "long",
      day: "numeric",
    }
  );
};

const getReturnStatusColor = (status) => {
  const map = {
    pending: "warning",
    approved: "success",
    rejected: "error",
  };
  return map[status] || "grey";
};

const getReturnStatusText = (status) => {
  const map = {
    pending: t("returns.pending_label"),
    approved: t("returns.approved"),
    rejected: t("returns.rejected"),
  };
  return map[status] || t("returns.undefined");
};

const getReturnStatusIcon = (status) => {
  const map = {
    pending: "mdi-clock",
    approved: "mdi-check-circle",
    rejected: "mdi-close-circle",
  };
  return map[status] || "mdi-help";
};

const getReasonText = (code) => {
  const map = {
    WRONG_ITEM: t("returns.reasons.WRONG_ITEM"),
    DAMAGED: t("returns.reasons.DAMAGED"),
    SIZE_ISSUE: t("returns.reasons.SIZE_ISSUE"),
    NOT_AS_DESCRIBED: t("returns.reasons.NOT_AS_DESCRIBED"),
    QUALITY_ISSUE: t("returns.reasons.QUALITY_ISSUE"),
    CHANGED_MIND: t("returns.reasons.CHANGED_MIND"),
    OTHER: t("returns.reasons.OTHER"),
  };
  return map[code] || code;
};

onMounted(() => {
  fetchReturns();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.returns-table :deep(th) {
  background-color: transparent !important;
  color: #555 !important;
  font-size: 0.9rem;
}

.hover-row {
  transition: background-color 0.2s ease;
}

.hover-row:hover {
  background-color: #f7f9fc;
}

.last-child-no-border:last-child {
  border-bottom: none !important;
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
</style>
