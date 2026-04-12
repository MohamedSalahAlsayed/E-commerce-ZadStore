<template>
  <v-container
    fluid
    class="pa-6 staff-management"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <!-- Header Section -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h4 font-weight-black text-grey-darken-4 mb-1">
          {{ $t("staff.title") }}
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          {{ $t("staff.subtitle") }}
        </p>
      </div>
      <v-btn
        color="primary"
        prepend-icon="mdi-account-plus"
        size="large"
        class="rounded-lg font-weight-bold elevation-4"
        @click="openAddAdminDialog"
      >
        {{ $t("staff.add_admin") }}
      </v-btn>
    </div>

    <!-- Quick Stats -->
    <v-row class="mb-6">
      <v-col cols="12" md="4">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="primary-lighten-5" size="48" class="me-4">
              <v-icon color="primary">mdi-shield-check</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("staff.admins_label") }}
              </div>
              <div class="text-h5 font-weight-black">{{ stats.admins }}</div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="info-lighten-5" size="48" class="me-4">
              <v-icon color="info">mdi-account-star</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("staff.moderators_label") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.moderators }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card class="rounded-xl pa-4 stat-card" elevation="2">
          <div class="d-flex align-center">
            <v-avatar color="success-lighten-5" size="48" class="me-4">
              <v-icon color="success">mdi-account-group</v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-grey-darken-1 font-weight-bold">
                {{ $t("staff.total_users_label") }}
              </div>
              <div class="text-h5 font-weight-black">
                {{ stats.totalUsers }}
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
          :label="$t('staff.search_placeholder')"
          variant="solo-filled"
          flat
          hide-details
          density="compact"
          class="max-width-300 rounded-lg"
        ></v-text-field>
        <v-spacer></v-spacer>
        <v-btn-toggle
          v-model="filterRole"
          mandatory
          density="compact"
          color="primary"
        >
          <v-btn value="all">{{ $t("messaging.status_filter_all") }}</v-btn>
          <v-btn value="admin">{{ $t("staff.admins") }}</v-btn>
          <v-btn value="moderator">{{ $t("staff.moderators") }}</v-btn>
        </v-btn-toggle>
      </v-toolbar>

      <div class="table-responsive">
        <v-data-table
          :headers="headers"
          :items="filteredUsers"
          :search="search"
          :loading="loading"
          class="staff-table"
          hover
        >
          <!-- Custom Loading Slot -->
          <template v-slot:loading>
            <v-skeleton-loader
              v-for="i in 5"
              :key="i"
              type="table-row-divider@5"
            ></v-skeleton-loader>
          </template>
          <!-- User Info Slot -->
          <template v-slot:[`item.user`]="{ item }">
            <div class="d-flex align-center py-2">
              <v-avatar
                color="primary"
                size="32"
                class="me-3 text-white font-weight-bold"
              >
                {{ (item.name || "").charAt(0).toUpperCase() }}
              </v-avatar>
              <div>
                <div class="font-weight-bold">{{ item.name }}</div>
                <div class="text-caption text-grey">{{ item.email }}</div>
              </div>
            </div>
          </template>

          <!-- Role Slot -->
          <template v-slot:[`item.role`]="{ item }">
            <v-chip
              :color="getRoleColor(item.role)"
              size="small"
              variant="tonal"
              class="font-weight-bold"
            >
              {{ getRoleText(item.role) }}
            </v-chip>
          </template>

          <template v-slot:[`item.is_active`]="{ item }">
            <div class="d-flex align-center justify-center gap-2">
              <v-chip
                :color="item.is_active ? 'success' : 'grey-lighten-1'"
                size="x-small"
                variant="tonal"
                class="font-weight-bold"
                style="min-width: 65px; justify-content: center"
              >
                {{
                  item.is_active
                    ? $t("dashboard.active")
                    : $t("dashboard.inactive")
                }}
              </v-chip>
              <v-switch
                v-model="item.is_active"
                color="success"
                hide-details
                density="compact"
                class="status-switch"
                @change="toggleStatus(item)"
              ></v-switch>
            </div>
          </template>

          <!-- Last Login Slot -->
          <template v-slot:[`item.last_login_at`]="{ item }">
            <div
              class="d-flex align-center gap-1 text-body-2 font-weight-medium"
              :class="
                item.last_login_at ? 'text-blue-grey-darken-2' : 'text-grey'
              "
            >
              <v-icon
                size="16"
                :color="item.last_login_at ? 'primary' : 'grey-lighten-1'"
                class="mb-1"
              >
                mdi-clock-outline
              </v-icon>
              {{
                item.last_login_at
                  ? formatDate(item.last_login_at)
                  : $t("staff.never_logged_in")
              }}
            </div>
          </template>

          <!-- Actions Slot -->
          <template v-slot:[`item.actions`]="{ item }">
            <div class="d-flex gap-2">
              <v-btn
                icon="mdi-shield-edit-outline"
                variant="text"
                size="small"
                color="info"
                @click="openRoleDialog(item)"
                :title="$t('staff.edit_permission')"
              ></v-btn>
              <v-btn
                icon="mdi-account-lock-open-outline"
                variant="text"
                size="small"
                color="warning"
                @click="openPermissionsDialog(item)"
                :title="$t('staff.edit_permission_btn')"
              ></v-btn>
              <v-btn
                icon="mdi-delete-outline"
                variant="text"
                size="small"
                color="error"
                :disabled="item.role === 'admin' && stats.admins <= 1"
                @click="confirmDelete(item)"
                :title="$t('dashboard.delete')"
              ></v-btn>
            </div>
          </template>
        </v-data-table>
      </div>
    </v-card>

    <!-- Add Admin Dialog -->
    <v-dialog v-model="addDialog" max-width="600" persistent>
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h5 font-weight-black px-4">
          {{ $t("staff.add_admin_dialog_title") }}
        </v-card-title>
        <v-card-text>
          <v-form ref="addForm" v-model="addValid">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.name"
                  :label="$t('auth.register.full_name')"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-account"
                  :rules="[(v) => !!v || $t('inventory.required_field')]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.email"
                  :label="$t('auth.register.email')"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-email-outline"
                  type="email"
                  :rules="[
                    (v) => !!v || $t('inventory.required_field'),
                    (v) => /.+@.+\..+/.test(v) || $t('auth.email_invalid'),
                  ]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.password"
                  :label="$t('auth.register.password')"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-lock-outline"
                  type="password"
                  :rules="[
                    (v) => !!v || $t('inventory.required_field'),
                    (v) => v.length >= 8 || $t('staff.password_min_length'),
                  ]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="newAdmin.role"
                  :items="staffRoles"
                  :label="$t('staff.permission_type')"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-shield-account"
                ></v-select>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="addDialog = false"
            class="font-weight-bold"
            >{{ $t("dashboard.cancel") }}</v-btn
          >
          <v-btn
            color="primary"
            variant="flat"
            class="px-8 font-weight-bold rounded-lg"
            @click="saveNewAdmin"
            :loading="saveLoading"
          >
            {{ $t("staff.create_account") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Role Change Dialog -->
    <v-dialog v-model="roleDialog" max-width="400">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-black mb-4">{{
          $t("staff.change_role_title")
        }}</v-card-title>
        <v-card-text>
          <div class="mb-4">
            {{ $t("staff.change_permissions_of") }}
            <b>{{ selectedUser?.name }}</b> {{ $t("staff.to_label") }}
          </div>
          <v-radio-group v-model="selectedUserRole">
            <v-radio
              :label="$t('staff.role_admin')"
              value="admin"
              color="primary"
            ></v-radio>
            <v-radio
              :label="$t('staff.role_moderator')"
              value="moderator"
              color="info"
            ></v-radio>
            <v-radio
              :label="$t('staff.role_user')"
              value="user"
              color="grey"
            ></v-radio>
          </v-radio-group>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="roleDialog = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            class="rounded-lg px-6"
            @click="updateUserRole"
            >{{ $t("staff.save_changes") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Permissions Manager Dialog -->
    <v-dialog v-model="permissionsDialog" max-width="600">
      <v-card class="rounded-xl pa-4">
        <v-card-title
          class="text-h5 font-weight-black d-flex align-center gap-2"
        >
          <v-icon color="warning">mdi-account-lock-open-outline</v-icon>
          {{
            $t("staff.permissions_manager_title", { name: selectedUser?.name })
          }}
        </v-card-title>
        <v-divider class="my-4"></v-divider>
        <v-card-text class="pa-0">
          <v-row>
            <v-col
              cols="12"
              md="6"
              v-for="(group, gIndex) in permissionGroups"
              :key="gIndex"
            >
              <div class="px-3 mb-4">
                <div
                  class="text-subtitle-1 font-weight-bold text-primary mb-2 d-flex align-center gap-2"
                >
                  <v-icon size="18">{{ group.icon }}</v-icon>
                  {{ $t("staff.groups." + group.name) }}
                </div>
                <v-checkbox
                  v-for="perm in group.items"
                  :key="perm.key"
                  v-model="selectedUserPermissions"
                  :label="$t('staff.perms.' + perm.key)"
                  :value="perm.key"
                  color="primary"
                  density="compact"
                  hide-details
                ></v-checkbox>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions class="pt-6">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="permissionsDialog = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            class="rounded-lg px-8 font-weight-bold"
            @click="savePermissions"
            :loading="saveLoading"
            >{{ $t("staff.save_permissions_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="64" class="mb-4"
          >mdi-account-remove-outline</v-icon
        >
        <v-card-title class="text-h6 font-weight-black">{{
          $t("staff.delete_account_title")
        }}</v-card-title>
        <v-card-text>{{
          $t("staff.delete_confirm_msg", { name: selectedUser?.name })
        }}</v-card-text>
        <v-card-actions class="justify-center gap-4 mt-4">
          <v-btn variant="text" @click="deleteDialog = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="rounded-lg px-6"
            @click="deleteUser"
            >{{ $t("staff.confirm_delete") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Success Snackbar -->
    <v-snackbar v-model="showSuccess" color="success" timeout="3000">
      {{ successText }}
      <template v-slot:actions>
        <v-btn variant="text" @click="showSuccess = false">{{
          $t("dashboard.close")
        }}</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "@/axios";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

// State
const loading = ref(false);
const saveLoading = ref(false);
const search = ref("");
const filterRole = ref("all");
const addDialog = ref(false);
const addValid = ref(false);
const addForm = ref(null);
const roleDialog = ref(false);
const permissionsDialog = ref(false);
const deleteDialog = ref(false);
const showSuccess = ref(false);
const successText = ref("");

const permissionGroups = [
  {
    name: "catalog",
    icon: "mdi-package-variant-closed",
    items: [
      { key: "manage_products" },
      { key: "manage_categories" },
      { key: "manage_inventory" },
      { key: "view_inventory_logs" },
      { key: "manage_reviews" },
    ],
  },
  {
    name: "sales",
    icon: "mdi-cart-check",
    items: [
      { key: "manage_orders" },
      { key: "manage_returns" },
      { key: "view_financials" },
    ],
  },
  {
    name: "marketing",
    icon: "mdi-bullhorn",
    items: [{ key: "manage_marketing" }],
  },
  {
    name: "system",
    icon: "mdi-cog-outline",
    items: [{ key: "manage_settings" }, { key: "manage_staff" }],
  },
];

const stats = ref({
  admins: 0,
  moderators: 0,
  totalUsers: 0,
});

const headers = computed(() => [
  { title: t("staff.user_th"), key: "user", align: "start" },
  { title: t("staff.role_th"), key: "role", align: "center" },
  {
    title: t("dashboard.status_th"),
    key: "is_active",
    align: "center",
    sortable: false,
  },
  { title: t("staff.last_login_th"), key: "last_login_at", align: "start" },
  {
    title: t("inventory.actions_th"),
    key: "actions",
    align: "end",
    sortable: false,
  },
]);

const users = ref([]);

const staffRoles = computed(() => [
  { title: t("staff.role_admin") + " (Full Access)", value: "admin" },
  {
    title: t("staff.role_moderator") + " (Limited Access)",
    value: "moderator",
  },
]);

const newAdmin = ref({
  name: "",
  email: "",
  password: "",
  role: "admin",
});

const selectedUser = ref(null);
const selectedUserRole = ref("");
const selectedUserPermissions = ref([]);

// Computed
const filteredUsers = computed(() => {
  let result = users.value;
  if (filterRole.value !== "all") {
    result = result.filter((u) => u.role === filterRole.value);
  }
  return result;
});

// Methods
const getRoleColor = (role) => {
  const colors = { admin: "error", moderator: "info", user: "grey" };
  return colors[role] || "grey";
};

const getRoleText = (role) => {
  const texts = {
    admin: t("staff.admins"),
    moderator: t("staff.moderators"),
    user: t("staff.role_user"),
  };
  return texts[role] || role;
};

const openAddAdminDialog = () => {
  newAdmin.value = { name: "", email: "", password: "", role: "admin" };
  addDialog.value = true;
};

const saveNewAdmin = async () => {
  const { valid } = await addForm.value.validate();
  if (!valid) return;

  saveLoading.value = true;
  try {
    const res = await axios.post("/admin/staff", newAdmin.value);
    users.value.unshift(res.data.user);
    addDialog.value = false;
    successText.value = t("staff.create_success");
    showSuccess.value = true;
    updateStats();
  } catch (err) {
    console.error(err);
  } finally {
    saveLoading.value = false;
  }
};

const openRoleDialog = (user) => {
  selectedUser.value = user;
  selectedUserRole.value = user.role;
  roleDialog.value = true;
};

const openPermissionsDialog = (user) => {
  selectedUser.value = user;
  selectedUserPermissions.value = user.permissions || [];
  permissionsDialog.value = true;
};

const savePermissions = async () => {
  saveLoading.value = true;
  try {
    const res = await axios.put(
      `/admin/users/${selectedUser.value.id}/permissions`,
      {
        permissions: selectedUserPermissions.value,
      }
    );
    const index = users.value.findIndex((u) => u.id === selectedUser.value.id);
    if (index !== -1) {
      users.value[index].permissions = res.data.user.permissions;
      permissionsDialog.value = false;
      successText.value = t("staff.save_changes");
      showSuccess.value = true;
    }
  } catch (err) {
    console.error(err);
  } finally {
    saveLoading.value = false;
  }
};

const updateUserRole = async () => {
  try {
    const res = await axios.put(`/admin/users/${selectedUser.value.id}/role`, {
      role: selectedUserRole.value,
    });
    const index = users.value.findIndex((u) => u.id === selectedUser.value.id);
    if (index !== -1) {
      users.value[index].role = res.data.user.role;
      roleDialog.value = false;
      successText.value = t("staff.role_update_success");
      showSuccess.value = true;
      updateStats();
    }
  } catch (err) {
    console.error(err);
  }
};

const toggleStatus = async (item) => {
  try {
    await axios.put(`/admin/users/${item.id}/status`);
    successText.value = item.is_active
      ? t("staff.account_activated", { name: item.name })
      : t("staff.account_deactivated", { name: item.name });
    showSuccess.value = true;
  } catch (err) {
    item.is_active = !item.is_active;
  }
};

const confirmDelete = (user) => {
  selectedUser.value = user;
  deleteDialog.value = true;
};

const deleteUser = async () => {
  try {
    await axios.delete(`/admin/users/${selectedUser.value.id}`);
    users.value = users.value.filter((u) => u.id !== selectedUser.value.id);
    deleteDialog.value = false;
    successText.value = t("staff.delete_success");
    showSuccess.value = true;
    updateStats();
  } catch (err) {
    console.error(err);
  }
};

const updateStats = () => {
  stats.value.admins = users.value.filter((u) => u.role === "admin").length;
  stats.value.moderators = users.value.filter(
    (u) => u.role === "moderator"
  ).length;
  stats.value.totalUsers = users.value.length;
};

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchStaff(true);
  }, 30000);
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
    fetchStaff(true);
    startPolling();
  }
};
// ----------------------------------

const fetchStaff = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await axios.get("/admin/staff");
    users.value = res.data;
    updateStats();
  } catch (err) {
    console.error(err);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    {
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    }
  );
};

onMounted(() => {
  fetchStaff();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.staff-table :deep(thead th) {
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

.status-switch :deep(.v-selection-control) {
  min-height: 30px !important;
}

.max-width-300 {
  max-width: 300px;
}

.gap-2 {
  gap: 8px;
}
.gap-4 {
  gap: 16px;
}
</style>
