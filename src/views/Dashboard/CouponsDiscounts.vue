<template>
  <v-locale-provider rtl>
    <v-container fluid class="pa-6" dir="rtl">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text
          class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
        >
          <div class="d-flex align-center gap-3">
            <v-avatar
              color="rgb(var(--v-theme-primary))"
              size="48"
              class="rounded-lg"
            >
              <v-icon color="white" size="28">mdi-ticket-percent</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              الكوبونات والخصومات
            </h2>
          </div>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 650px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="ابحث بكود الخصم..."
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg flex-grow-1"
            ></v-text-field>

            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              class="font-weight-bold px-6 rounded-lg shadow-btn"
              height="44"
              @click="openAddDialog"
            >
              كوبون جديد
            </v-btn>
          </div>
        </v-card-text>
      </v-card>

      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-primary"
            elevation="1"
          >
            <v-avatar
              color="primary-lighten-4"
              size="50"
              class="mr-3 text-primary"
            >
              <v-icon>mdi-ticket-confirmation</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                الكوبونات النشطة
              </div>
              <div class="text-h5 font-weight-black">
                {{ activeCouponsCount }}
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="6">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-error"
            elevation="1"
          >
            <v-avatar color="red-lighten-4" size="50" class="mr-3 text-error">
              <v-icon>mdi-calendar-remove</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                كوبونات منتهية الصلاحية
              </div>
              <div class="text-h5 font-weight-black text-error">
                {{ expiredCouponsCount }}
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <div class="table-responsive">
          <v-table class="coupons-table pa-2">
            <thead class="bg-grey-lighten-4">
              <tr>
                <th class="text-right font-weight-bold">كود الخصم</th>
                <th class="text-center font-weight-bold">نوع الخصم</th>
                <th class="text-center font-weight-bold">قيمة الخصم</th>
                <th class="text-center font-weight-bold">الحد الأدنى للطلب</th>
                <th class="text-center font-weight-bold">تاريخ الانتهاء</th>
                <th class="text-center font-weight-bold">الاستخدام</th>
                <th class="text-center font-weight-bold">الحالة</th>
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

              <tr v-else-if="filteredCoupons.length === 0">
                <td colspan="7" class="text-center pa-6 text-grey-darken-1">
                  لا توجد كوبونات مطابقة.
                </td>
              </tr>

              <tr
                v-for="coupon in filteredCoupons"
                :key="coupon.id"
                class="hover-row"
                v-else
              >
                <td>
                  <v-chip
                    color="rgb(var(--v-theme-primary))"
                    variant="tonal"
                    class="font-weight-black text-uppercase"
                    size="large"
                  >
                    <v-icon left size="18" class="mr-2">mdi-ticket</v-icon>
                    {{ coupon.code }}
                  </v-chip>
                </td>

                <td class="text-center text-grey-darken-1 font-weight-medium">
                  {{
                    coupon.type === "percentage" || coupon.type === "percent"
                      ? "نسبة مئوية (%)"
                      : "مبلغ ثابت ($)"
                  }}
                </td>

                <td
                  class="text-center font-weight-black text-success text-subtitle-1"
                >
                  {{
                    coupon.type === "percentage" || coupon.type === "percent"
                      ? coupon.value + "%"
                      : "$" + coupon.value
                  }}
                </td>

                <td class="text-center">
                  <div class="mb-1 text-caption font-weight-bold">
                    {{ coupon.used_count || 0 }} /
                    {{ coupon.usage_limit || "∞" }}
                  </div>
                  <v-progress-linear
                    :model-value="
                      coupon.usage_limit
                        ? (coupon.used_count / coupon.usage_limit) * 100
                        : 0
                    "
                    color="primary"
                    height="6"
                    rounded
                    background-opacity="0.1"
                  ></v-progress-linear>
                </td>

                <td
                  class="text-center"
                  :class="
                    isExpired(coupon.expires_at)
                      ? 'text-error font-weight-bold'
                      : 'text-grey-darken-1'
                  "
                >
                  {{ coupon.expires_at ? formatDate(coupon.expires_at) : "-" }}
                </td>

                <td class="text-center">
                  <v-switch
                    v-model="coupon.is_active"
                    color="success"
                    hide-details
                    density="compact"
                    class="d-inline-flex"
                    :disabled="
                      isExpired(coupon.expires_at) ||
                      processingIds.includes(coupon.id)
                    "
                    @change="toggleActive(coupon)"
                  ></v-switch>
                </td>

                <td class="text-center">
                  <v-btn
                    icon
                    size="small"
                    color="primary"
                    variant="text"
                    @click="editItem(coupon)"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    icon
                    size="small"
                    color="error"
                    variant="text"
                    :disabled="processingIds.includes(coupon.id)"
                    @click="openDeleteDialog(coupon)"
                  >
                    <v-progress-circular
                      v-if="
                        processingIds.includes(coupon.id) &&
                        lastAction === 'delete'
                      "
                      indeterminate
                      size="16"
                    ></v-progress-circular>
                    <v-icon v-else>mdi-delete</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>
      </v-card>

      <v-dialog v-model="dialog" max-width="500px" persistent>
        <v-card class="rounded-xl pa-2">
          <v-card-title
            class="text-h5 font-weight-bold pt-4 px-4"
            style="color: rgb(var(--v-theme-primary))"
          >
            {{ isEditing ? "تعديل الكوبون" : "إضافة كوبون جديد" }}
          </v-card-title>
          <v-divider class="my-3"></v-divider>

          <v-card-text>
            <v-form ref="form" v-model="validForm">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.code"
                    label="كود الخصم (مثال: SUMMER50)"
                    variant="outlined"
                    color="primary"
                    class="text-uppercase"
                    :rules="[(v) => !!v || 'كود الخصم مطلوب']"
                  >
                    <template v-slot:append-inner>
                      <v-btn
                        icon="mdi-refresh"
                        variant="text"
                        size="small"
                        color="primary"
                        @click="generateRandomCode"
                        title="توليد كود عشوائي"
                      ></v-btn>
                    </template>
                  </v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-select
                    v-model="editedItem.type"
                    :items="[
                      { title: 'نسبة مئوية (%)', value: 'percent' },
                      { title: 'مبلغ ثابت ($)', value: 'fixed' },
                    ]"
                    label="نوع الخصم"
                    variant="outlined"
                    color="primary"
                  ></v-select>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model.number="editedItem.value"
                    :label="
                      editedItem.type === 'percentage' ||
                      editedItem.type === 'percent'
                        ? 'النسبة (%)'
                        : 'المبلغ ($)'
                    "
                    type="number"
                    variant="outlined"
                    color="primary"
                    :rules="[
                      (v) => !!v || 'القيمة مطلوبة',
                      (v) => v > 0 || 'يجب أن تكون القيمة أكبر من صفر',
                      (v) =>
                        (editedItem.type === 'percent' ? v <= 100 : true) ||
                        'النسبة لا يمكن أن تتجاوز 100%',
                    ]"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model.number="editedItem.min_order_amount"
                    label="الحد الأدنى ($)"
                    type="number"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model.number="editedItem.usage_limit"
                    label="حد الاستخدام"
                    type="number"
                    variant="outlined"
                    color="primary"
                    placeholder="∞"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="editedItem.expires_at"
                    label="تاريخ الانتهاء"
                    type="date"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" v-if="!isEditing">
                  <v-switch
                    v-model="editedItem.is_active"
                    color="success"
                    label="تفعيل الكوبون فور حفظه"
                  ></v-switch>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn
              color="grey-darken-1"
              variant="tonal"
              class="px-4 font-weight-bold"
              @click="closeDialog"
              >إلغاء</v-btn
            >
            <v-btn
              color="primary"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :disabled="!validForm || saving"
              :loading="saving"
              @click="saveItem"
            >
              حفظ
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogDelete" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3"
            >mdi-alert-circle-outline</v-icon
          >
          <v-card-title class="text-h6 font-weight-bold text-wrap"
            >هل أنت متأكد من حذف هذا الكوبون؟</v-card-title
          >
          <v-card-actions class="d-flex justify-center mt-4">
            <v-btn
              color="grey-darken-1"
              variant="tonal"
              class="px-6 font-weight-bold"
              @click="dialogDelete = false"
              >إلغاء</v-btn
            >
            <v-btn
              color="error"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :loading="deleting"
              @click="confirmDelete"
              >نعم، احذف</v-btn
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
import axios from "@/axios";

const coupons = ref([]);
const loading = ref(true);
const processingIds = ref([]);
const lastAction = ref("");

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

const searchQuery = ref("");
const dialog = ref(false);
const dialogDelete = ref(false);
const isEditing = ref(false);
const validForm = ref(false);
const saving = ref(false);
const deleting = ref(false);
const itemToDelete = ref(null);

const defaultItem = {
  id: null,
  code: "",
  type: "percent",
  value: 0,
  min_order_amount: null,
  usage_limit: null,
  expires_at: "",
  is_active: true,
};

const editedItem = ref({ ...defaultItem });

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchCoupons(true);
  }, 60000);
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
    fetchCoupons(true);
    startPolling();
  }
};
// ----------------------------------

const fetchCoupons = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await axios.get("/admin/coupons");
    coupons.value = res.data;
  } catch (err) {
    console.error("Error fetching coupons:", err);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const isExpired = (dateString) => {
  if (!dateString) return false;
  return new Date(dateString) < new Date(new Date().setHours(0, 0, 0, 0));
};

const activeCouponsCount = computed(() => {
  return coupons.value.filter((c) => !!c.is_active && !isExpired(c.expires_at))
    .length;
});

const expiredCouponsCount = computed(() => {
  return coupons.value.filter((c) => isExpired(c.expires_at)).length;
});

const filteredCoupons = computed(() => {
  if (!searchQuery.value) return coupons.value;
  return coupons.value.filter((c) =>
    c.code.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatDate = (dateString) => {
  const options = { year: "numeric", month: "short", day: "numeric" };
  return new Date(dateString).toLocaleDateString("ar-EG", options);
};

const getOnlyDate = (isoString) => {
  if (!isoString) return "";
  return isoString.split("T")[0]; // YYYY-MM-DD
};

const generateRandomCode = () => {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  let result = "";
  for (let i = 0; i < 8; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  editedItem.value.code = result;
};

const openAddDialog = () => {
  isEditing.value = false;
  editedItem.value = { ...defaultItem };

  const nextMonth = new Date();
  nextMonth.setMonth(nextMonth.getMonth() + 1);
  editedItem.value.expires_at = nextMonth.toISOString().split("T")[0];

  dialog.value = true;
};

const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = { ...item };
  editedItem.value.expires_at = getOnlyDate(item.expires_at);
  editedItem.value.is_active = !!item.is_active;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  editedItem.value = { ...defaultItem };
};

const saveItem = async () => {
  editedItem.value.code = (editedItem.value.code || "").toUpperCase();
  saving.value = true;

  try {
    const payload = {
      code: editedItem.value.code,
      type: editedItem.value.type,
      value: editedItem.value.value,
      min_order_amount: editedItem.value.min_order_amount || null,
      usage_limit: editedItem.value.usage_limit || null,
      expires_at: editedItem.value.expires_at
        ? editedItem.value.expires_at + " 23:59:59"
        : null,
      is_active: editedItem.value.is_active,
    };

    if (isEditing.value) {
      const res = await axios.put(
        `/admin/coupons/${editedItem.value.id}`,
        payload
      );
      const index = coupons.value.findIndex(
        (c) => c.id === editedItem.value.id
      );
      if (index !== -1) coupons.value[index] = res.data;
      showMessage("تم تحديث الكوبون بنجاح!");
    } else {
      const res = await axios.post("/admin/coupons", payload);
      coupons.value.unshift(res.data);
      showMessage("تمت إضافة الكوبون بنجاح!");
    }
    closeDialog();
  } catch (error) {
    console.error("Error saving coupon:", error);
    if (error.response?.data?.errors?.code) {
      showMessage("رمز الكوبون هذا مستخدم بالفعل!", "error");
    } else {
      showMessage(
        error.response?.data?.message || "حدث خطأ أثناء الحفظ",
        "error"
      );
    }
  } finally {
    saving.value = false;
  }
};

const toggleActive = async (coupon) => {
  processingIds.value.push(coupon.id);
  lastAction.value = "toggle";
  try {
    await axios.put(`/admin/coupons/${coupon.id}`, {
      ...coupon,
      is_active: coupon.is_active,
    });
    showMessage(
      coupon.is_active ? "تم تفعيل الكوبون بنجاح" : "تم إلغاء تفعيل الكوبون",
      coupon.is_active ? "success" : "warning"
    );
  } catch (error) {
    console.error("Error updating status:", error);
    coupon.is_active = !coupon.is_active;
    showMessage("حدث خطأ أثناء تغيير الحالة", "error");
  } finally {
    processingIds.value = processingIds.value.filter((id) => id !== coupon.id);
  }
};

const openDeleteDialog = (item) => {
  itemToDelete.value = item;
  dialogDelete.value = true;
};

const confirmDelete = async () => {
  if (!itemToDelete.value) return;
  deleting.value = true;
  processingIds.value.push(itemToDelete.value.id);
  lastAction.value = "delete";
  try {
    await axios.delete(`/admin/coupons/${itemToDelete.value.id}`);
    coupons.value = coupons.value.filter((c) => c.id !== itemToDelete.value.id);
    showMessage("تم حذف الكوبون بنجاح");
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (error) {
    console.error("Error deleting coupon:", error);
    showMessage("حدث خطأ أثناء الحذف", "error");
  } finally {
    deleting.value = false;
    if (itemToDelete.value) {
      processingIds.value = processingIds.value.filter(
        (id) => id !== itemToDelete.value.id
      );
    }
  }
};

onMounted(() => {
  fetchCoupons();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
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

.coupons-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 14px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.coupons-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
  padding-top: 8px !important;
  padding-bottom: 8px !important;
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
.border-left-success {
  border-right: 4px solid #4caf50 !important;
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
