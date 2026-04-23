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
            {{ $t("users.title") }}
          </h2>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 600px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              :label="$t('users.search_placeholder')"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg flex-grow-1"
            ></v-text-field>

            <v-btn
              color="info"
              variant="outlined"
              prepend-icon="mdi-download"
              class="rounded-lg font-weight-bold"
              height="40"
              @click="exportToCSV"
            >
              {{ $t("users.export_csv") }}
            </v-btn>
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
                  $t("users.selected_count").replace(
                    "{count}",
                    selectedIds.length
                  )
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
                {{ $t("dashboard.delete_selected") }}
              </v-btn>
              <v-btn
                color="white"
                variant="outlined"
                @click="selectedIds = []"
                >{{ $t("dashboard.cancel") }}</v-btn
              >
            </div>
          </div>
        </v-card>
      </v-expand-transition>

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <div class="table-responsive">
          <v-table class="users-table pa-2">
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
                  {{ $t("users.customer_th") }}
                </th>
                <th class="text-right font-weight-bold">
                  {{ $t("users.email_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("users.join_date_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("users.total_orders_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("users.total_spent_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("users.account_status_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("dashboard.actions_th") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-if="loading">
                <tr v-for="i in 5" :key="`skel-user-${i}`">
                  <td class="px-2"><v-skeleton-loader type="list-item" /></td>
                  <td class="px-2 py-3">
                    <v-skeleton-loader type="list-item-avatar" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 150px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="text"
                      style="max-width: 100px"
                      class="mx-auto"
                    />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="text"
                      style="max-width: 50px"
                      class="mx-auto"
                    />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="text"
                      style="max-width: 100px"
                      class="mx-auto"
                    />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="chip" class="mx-auto" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="actions"
                      class="mx-auto"
                      style="min-width: 100px"
                    />
                  </td>
                </tr>
              </template>
              <tr v-else-if="filteredUsers.length === 0">
                <td colspan="8" class="text-center pa-6 text-grey-darken-1">
                  لا يوجد عملاء مطابقين للبحث.
                </td>
              </tr>

              <tr
                v-for="user in filteredUsers"
                :key="user.id"
                class="hover-row"
              >
                <td>
                  <v-checkbox-btn
                    v-model="selectedIds"
                    :value="user.id"
                    color="primary"
                    hide-details
                  ></v-checkbox-btn>
                </td>
                <td class="d-flex align-center gap-3 py-3">
                  <v-avatar
                    size="45"
                    class="bg-primary text-white font-weight-bold"
                  >
                    {{ user.name.charAt(0) }}
                  </v-avatar>
                  <span class="font-weight-bold text-subtitle-2">{{
                    user.name
                  }}</span>
                </td>
                <td class="text-grey-darken-1">{{ user.email }}</td>
                <td class="text-center text-grey-darken-1">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="text-center font-weight-bold text-primary">
                  {{ user.ordersCount }}
                </td>
                <td class="text-center font-weight-bold text-success">
                  {{ Number(user.totalSpent).toLocaleString("ar-EG") }} ج.م
                </td>
                <td class="text-center">
                  <v-chip
                    size="small"
                    :color="user.isActive ? 'success' : 'error'"
                    class="font-weight-bold"
                  >
                    {{ user.isActive ? "نشط" : "محظور" }}
                  </v-chip>
                </td>
                <td class="text-center">
                  <div class="d-flex align-center justify-center gap-2">
                    <v-tooltip location="top">
                      <template v-slot:activator="{ props }">
                        <v-btn
                          icon
                          size="small"
                          :color="user.isActive ? 'warning' : 'success'"
                          variant="text"
                          v-bind="props"
                          @click="toggleUserStatus(user)"
                          :disabled="processingIds.includes(user.id)"
                        >
                          <v-progress-circular
                            v-if="processingIds.includes(user.id)"
                            indeterminate
                            size="16"
                          ></v-progress-circular>
                          <v-icon v-else>{{
                            user.isActive
                              ? "mdi-block-helper"
                              : "mdi-check-circle-outline"
                          }}</v-icon>
                        </v-btn>
                      </template>
                      <span>{{
                        user.isActive ? "حظر العميل" : "تفعيل العميل"
                      }}</span>
                    </v-tooltip>

                    <v-tooltip text="حذف العميل نهائياً" location="top">
                      <template v-slot:activator="{ props }">
                        <v-btn
                          v-bind="props"
                          icon="mdi-delete"
                          variant="text"
                          color="error"
                          size="small"
                          @click="openDeleteDialog(user)"
                        ></v-btn>
                      </template>
                    </v-tooltip>
                  </div>
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>
      </v-card>

      <!-- Delete Dialog -->
      <v-dialog v-model="deleteDialog" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3"
            >mdi-alert-circle-outline</v-icon
          >
          <v-card-title class="text-h6 font-weight-bold text-wrap"
            >هل أنت متأكد من حذف العميل بشكل نهائي؟</v-card-title
          >
          <v-card-text
            >سيؤدي هذا إلى حذف حساب العميل والبيانات المرتبطة
            بأكملها.</v-card-text
          >
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
              >نعم، احذف</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Batch Delete Dialog -->
      <v-dialog v-model="batchDeleteDialog" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3">mdi-delete-alert</v-icon>
          <v-card-title class="text-h6 font-weight-bold text-wrap">
            هل أنت متأكد من حذف {{ selectedIds.length }} عميل؟
          </v-card-title>
          <v-card-text>هذا الإجراء لا يمكن التراجع عنه.</v-card-text>
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
              >نعم، ا{{ $t("dashboard.delete_selected") }}</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Snackbar for notifications -->
      <v-snackbar
        v-model="snackbar.show"
        :color="snackbar.color"
        timeout="3000"
        location="top center"
        class="font-weight-bold"
      >
        {{ snackbar.text }}
        <template v-slot:actions>
          <v-btn variant="text" @click="snackbar.show = false">إغلاق</v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </v-locale-provider>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import api from "../../axios";

const users = ref([]);
const loading = ref(true);
const processingIds = ref([]);
const selectedIds = ref([]);
const batchDeleteDialog = ref(false);

const selectAll = computed({
  get: () =>
    filteredUsers.value.length > 0 &&
    selectedIds.value.length === filteredUsers.value.length,
  set: (val) => {
    if (val) {
      selectedIds.value = filteredUsers.value.map((u) => u.id);
    } else {
      selectedIds.value = [];
    }
  },
});

const deleteDialog = ref(false);
const userToDelete = ref(null);
const deleting = ref(false);

const confirmBatchDelete = async () => {
  if (selectedIds.value.length === 0) return;
  deleting.value = true;
  try {
    await api.post("/admin/users/batch-delete", { ids: selectedIds.value });
    showMessage("تم حذف العملاء بنجاح", "success");
    users.value = users.value.filter((u) => !selectedIds.value.includes(u.id));
    selectedIds.value = [];
    batchDeleteDialog.value = false;
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء الحذف الجماعي", "error");
  } finally {
    deleting.value = false;
  }
};

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchUsers(true);
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
    fetchUsers(true);
    startPolling();
  }
};
// ----------------------------------

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

const fetchUsers = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await api.get("/admin/users");
    users.value = res.data.map((u) => ({
      ...u,
      isActive: !!u.is_active,
      ordersCount: u.orders_count || 0,
      totalSpent: u.orders_sum_total || 0,
    }));
  } catch (error) {
    console.error("Error fetching users:", error);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString("ar-EG", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const exportToCSV = () => {
  const headers = ["Name", "Email", "Join Date", "Orders", "Spent"];
  const rows = filteredUsers.value.map((u) => [
    u.name,
    u.email,
    u.created_at,
    u.ordersCount,
    u.totalSpent,
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
    `customers_${new Date().toISOString().split("T")[0]}.csv`
  );
  document.body.appendChild(link);
  link.click();
};

const searchQuery = ref("");

// فلترة البحث
const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;
  const lowerCaseQuery = searchQuery.value.toLowerCase();
  return users.value.filter(
    (user) =>
      user.name.toLowerCase().includes(lowerCaseQuery) ||
      user.email.toLowerCase().includes(lowerCaseQuery)
  );
});

// تغيير حالة العميل (حظر / تنشيط)
const toggleUserStatus = async (user) => {
  processingIds.value.push(user.id);
  const index = users.value.findIndex((u) => u.id === user.id);

  if (index !== -1) {
    try {
      // Backend actually toggles the status directly based on /status endpoint
      const res = await api.put(`/admin/users/${user.id}/status`);
      users.value[index].isActive = res.data.user.is_active;

      const newStatus = users.value[index].isActive;
      showMessage(
        newStatus ? "تم تنشيط حساب العميل بنجاح!" : "تم حظر العميل بنجاح!",
        newStatus ? "success" : "warning"
      );
      fetchUsers(true);
    } catch (e) {
      console.error(e);
      showMessage("حدث خطأ أثناء تغيير حالة العميل", "error");
    } finally {
      processingIds.value = processingIds.value.filter((id) => id !== user.id);
    }
  }
};

const openDeleteDialog = (user) => {
  userToDelete.value = user;
  deleteDialog.value = true;
};

const confirmDelete = async () => {
  if (!userToDelete.value) return;
  deleting.value = true;
  try {
    const res = await api.delete(`/admin/users/${userToDelete.value.id}`);
    showMessage(res.data.message || "تم حذف العميل بنجاح", "success");
    users.value = users.value.filter((u) => u.id !== userToDelete.value.id);
  } catch (error) {
    console.error("Error deleting user:", error);
    showMessage("حدث خطأ أثناء الحذف", "error");
  } finally {
    deleting.value = false;
    deleteDialog.value = false;
    userToDelete.value = null;
  }
};

onMounted(() => {
  fetchUsers();
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
.users-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 15px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.users-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
}

.hover-row {
  transition: background-color 0.2s ease;
}

.hover-row:hover {
  background-color: #f8f9fa;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
</style>
