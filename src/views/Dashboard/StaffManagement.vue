<template>
  <v-container fluid class="pa-6 staff-management">
    <!-- Header Section -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h4 font-weight-black text-grey-darken-4 mb-1">
          إدارة الصلاحيات والموظفين 🔐
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          إضافة مدراء جدد، إدارة الأدوار، والتحكم في حسابات المستخدمين
        </p>
      </div>
      <v-btn
        color="primary"
        prepend-icon="mdi-account-plus"
        size="large"
        class="rounded-lg font-weight-bold elevation-4"
        @click="openAddAdminDialog"
      >
        إضافة أدمن جديد
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
                المدراء (Admins)
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
                المشرفين (Moderators)
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
                إجمالي المستخدمين
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
          label="البحث عن موظف أو مستخدم..."
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
          <v-btn value="all">الكل</v-btn>
          <v-btn value="admin">مدراء</v-btn>
          <v-btn value="moderator">مشرفين</v-btn>
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
          <!-- User Info Slot -->
          <template v-slot:[`item.user`]="{ item }">
            <div class="d-flex align-center py-2">
              <v-avatar
                color="primary"
                size="32"
                class="me-3 text-white font-weight-bold"
              >
                {{ item.name.charAt(0).toUpperCase() }}
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

          <!-- Status Slot -->
          <template v-slot:[`item.is_active`]="{ item }">
            <v-switch
              v-model="item.is_active"
              color="success"
              hide-details
              density="compact"
              @change="toggleStatus(item)"
            ></v-switch>
          </template>

          <!-- Last Login Slot -->
          <template v-slot:[`item.last_login`]="{ item }">
            <div class="text-caption text-grey">
              {{
                item.last_login_at
                  ? formatDate(item.last_login_at)
                  : "لم يدخل بعد"
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
                title="تعديل الصلاحية"
              ></v-btn>
              <v-btn
                icon="mdi-delete-outline"
                variant="text"
                size="small"
                color="error"
                :disabled="item.role === 'admin' && stats.admins <= 1"
                @click="confirmDelete(item)"
                title="حذف"
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
          إضافة مدير (Admin) جديد
        </v-card-title>
        <v-card-text>
          <v-form ref="addForm" v-model="addValid">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.name"
                  label="الاسم بالكامل"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-account"
                  :rules="[(v) => !!v || 'مطلوب']"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.email"
                  label="البريد الإلكتروني"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-email-outline"
                  type="email"
                  :rules="[
                    (v) => !!v || 'مطلوب',
                    (v) => /.+@.+\..+/.test(v) || 'بريد غير صالح',
                  ]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="newAdmin.password"
                  label="كلمة المرور"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-lock-outline"
                  type="password"
                  :rules="[
                    (v) => !!v || 'مطلوب',
                    (v) => v.length >= 8 || '8 أحرف على الأقل',
                  ]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="newAdmin.role"
                  :items="staffRoles"
                  label="نوع الصلاحية"
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
            >إلغاء</v-btn
          >
          <v-btn
            color="primary"
            variant="flat"
            class="px-8 font-weight-bold rounded-lg"
            @click="saveNewAdmin"
            :loading="saveLoading"
          >
            إنشاء الحساب
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Role Change Dialog -->
    <v-dialog v-model="roleDialog" max-width="400">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-black mb-4"
          >تغيير دور المستخدم</v-card-title
        >
        <v-card-text>
          <div class="mb-4">
            تغيير صلاحيات <b>{{ selectedUser?.name }}</b> إلى:
          </div>
          <v-radio-group v-model="selectedUserRole">
            <v-radio
              label="أدمن (Admin)"
              value="admin"
              color="primary"
            ></v-radio>
            <v-radio
              label="مشرف (Moderator)"
              value="moderator"
              color="info"
            ></v-radio>
            <v-radio label="عميل (User)" value="user" color="grey"></v-radio>
          </v-radio-group>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="roleDialog = false">إلغاء</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            class="rounded-lg px-6"
            @click="updateUserRole"
            >حفظ التعديل</v-btn
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
        <v-card-title class="text-h6 font-weight-black"
          >حذف الحساب</v-card-title
        >
        <v-card-text
          >هل أنت متأكد من حذف حساب <b>{{ selectedUser?.name }}</b
          >؟ سيؤدي هذا إلى فقدان جميع بياناته.</v-card-text
        >
        <v-card-actions class="justify-center gap-4 mt-4">
          <v-btn variant="text" @click="deleteDialog = false">إلغاء</v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="rounded-lg px-6"
            @click="deleteUser"
            >تأكيد الحذف</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Success Snackbar -->
    <v-snackbar v-model="showSuccess" color="success" timeout="3000">
      {{ successText }}
      <template v-slot:actions>
        <v-btn variant="text" @click="showSuccess = false">إغلاق</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "@/axios";

// State
const loading = ref(false);
const saveLoading = ref(false);
const search = ref("");
const filterRole = ref("all");
const addDialog = ref(false);
const addValid = ref(false);
const addForm = ref(null);
const roleDialog = ref(false);
const deleteDialog = ref(false);
const showSuccess = ref(false);
const successText = ref("");

const stats = ref({
  admins: 0,
  moderators: 0,
  totalUsers: 0,
});

const headers = [
  { title: "المستخدم", key: "user", align: "start" },
  { title: "الدور", key: "role", align: "center" },
  { title: "نشط", key: "is_active", align: "center", sortable: false },
  { title: "آخر دخول", key: "last_login_at", align: "start" },
  { title: "إجراءات", key: "actions", align: "end", sortable: false },
];

const users = ref([]);

const staffRoles = [
  { title: "أدمن (Full Access)", value: "admin" },
  { title: "مشرف (Limited Access)", value: "moderator" },
];

const newAdmin = ref({
  name: "",
  email: "",
  password: "",
  role: "admin",
});

const selectedUser = ref(null);
const selectedUserRole = ref("");

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
  const texts = { admin: "مدير", moderator: "مشرف", user: "عميل" };
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
    successText.value = "تم إنشاء حساب الموظف بنجاح";
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

const updateUserRole = async () => {
  try {
    const res = await axios.put(`/admin/users/${selectedUser.value.id}/role`, {
      role: selectedUserRole.value,
    });
    const index = users.value.findIndex((u) => u.id === selectedUser.value.id);
    if (index !== -1) {
      users.value[index].role = res.data.user.role;
      roleDialog.value = false;
      successText.value = "تم تحديث دور المستخدم بنجاح";
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
      ? `تم تفعيل حساب ${item.name}`
      : `تم تعطيل حساب ${item.name}`;
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
    successText.value = "تم حذف الحساب بنجاح";
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
  return new Date(dateString).toLocaleString("ar-EG", {
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
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
