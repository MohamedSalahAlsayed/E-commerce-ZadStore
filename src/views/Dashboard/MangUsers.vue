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
            إدارة العملاء
          </h2>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 600px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="ابحث بالاسم أو البريد الإلكتروني..."
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
              تصدير (CSV)
            </v-btn>
          </div>
        </v-card-text>
      </v-card>

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <v-table class="users-table pa-2">
          <thead class="bg-grey-lighten-4">
            <tr>
              <th class="text-right font-weight-bold">العميل</th>
              <th class="text-right font-weight-bold">البريد الإلكتروني</th>
              <th class="text-center font-weight-bold">تاريخ الانضمام</th>
              <th class="text-center font-weight-bold">إجمالي الطلبات</th>
              <th class="text-center font-weight-bold">إجمالي الإنفاق</th>
              <th class="text-center font-weight-bold">حالة الحساب</th>
              <th class="text-center font-weight-bold">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="6" class="text-center pa-6">
                <v-progress-circular
                  indeterminate
                  color="primary"
                ></v-progress-circular>
              </td>
            </tr>
            <tr v-else-if="filteredUsers.length === 0">
              <td colspan="6" class="text-center pa-6 text-grey-darken-1">
                لا يوجد عملاء مطابقين للبحث.
              </td>
            </tr>

            <tr v-for="user in filteredUsers" :key="user.id" class="hover-row">
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
                <v-tooltip location="top">
                  <template v-slot:activator="{ props }">
                    <v-btn
                      icon
                      size="small"
                      :color="user.isActive ? 'error' : 'success'"
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
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>

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
