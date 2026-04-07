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
              <v-icon color="white" size="28">mdi-truck-fast-outline</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              إدارة الشحن والتوصيل
            </h2>
          </div>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap justify-end"
            style="max-width: 500px"
          >
            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              class="font-weight-bold px-6 rounded-lg shadow-btn"
              height="44"
              @click="openAddDialog"
            >
              إضافة منطقة جديدة
            </v-btn>
          </div>
        </v-card-text>
      </v-card>

      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card
            class="rounded-xl pa-4 border-left-success h-100 d-flex flex-column justify-center"
            elevation="1"
          >
            <div class="d-flex justify-space-between align-center mb-3">
              <div class="d-flex align-center gap-2">
                <v-avatar
                  color="green-lighten-4"
                  size="40"
                  class="text-success"
                >
                  <v-icon>mdi-gift-outline</v-icon>
                </v-avatar>
                <span class="text-subtitle-1 font-weight-bold"
                  >إعدادات الشحن المجاني</span
                >
              </div>
              <v-switch
                v-model="isFreeShippingEnabled"
                color="success"
                hide-details
                density="compact"
              ></v-switch>
            </div>
            <div
              class="d-flex align-center gap-3"
              :class="{ 'opacity-50': !isFreeShippingEnabled }"
            >
              <span class="text-grey-darken-1 text-subtitle-2"
                >تفعيل الشحن المجاني للطلبات التي تتجاوز:</span
              >
              <div class="d-flex align-center" style="max-width: 150px">
                <v-text-field
                  v-model.number="freeShippingThreshold"
                  variant="outlined"
                  density="compact"
                  hide-details
                  type="number"
                  suffix="ج.م"
                  :disabled="!isFreeShippingEnabled"
                  class="font-weight-bold bg-white"
                ></v-text-field>
              </div>
              <v-btn
                size="small"
                color="success"
                variant="tonal"
                class="font-weight-bold"
                :loading="savingSettings"
                @click="saveShippingSettings"
                >حفظ</v-btn
              >
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-primary h-100"
            elevation="1"
          >
            <v-avatar
              color="primary-lighten-4"
              size="50"
              class="mr-3 text-primary"
            >
              <v-icon>mdi-map-marker-multiple-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                مناطق التغطية
              </div>
              <div class="text-h5 font-weight-black">
                {{ zones.length }}
                <span class="text-caption text-grey">مناطق</span>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-warning h-100"
            elevation="1"
          >
            <v-avatar
              color="orange-lighten-4"
              size="50"
              class="mr-3 text-warning"
            >
              <v-icon>mdi-cash-fast</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                متوسط تكلفة الشحن
              </div>
              <div class="text-h5 font-weight-black text-warning">
                {{ averageShippingCost }}
                <span class="text-caption text-grey">ج.م</span>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <div class="table-responsive">
          <v-table class="custom-table pa-2">
            <thead class="bg-grey-lighten-4">
              <tr>
                <th class="text-right font-weight-bold">المنطقة / المحافظة</th>
                <th class="text-center font-weight-bold">تكلفة الشحن</th>
                <th class="text-center font-weight-bold">مدة التوصيل</th>
                <th class="text-center font-weight-bold">
                  رسوم الدفع عند الاستلام
                </th>
                <th class="text-center font-weight-bold">الحالة</th>
                <th class="text-center font-weight-bold">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="4" class="text-center pa-6">
                  <v-progress-circular
                    indeterminate
                    color="primary"
                  ></v-progress-circular>
                </td>
              </tr>
              <tr v-else-if="zones.length === 0">
                <td colspan="4" class="text-center pa-6 text-grey-darken-1">
                  لا توجد مناطق شحن مضافة حتى الآن.
                </td>
              </tr>

              <tr v-for="zone in zones" :key="zone.id" class="hover-row" v-else>
                <td
                  class="font-weight-bold py-3 text-subtitle-2"
                  style="color: rgb(var(--v-theme-primary))"
                >
                  <v-icon color="primary" size="20" class="mr-1 ml-2"
                    >mdi-map-marker-radius</v-icon
                  >
                  {{ zone.name }}
                </td>

                <td class="text-center">
                  <span
                    class="font-weight-black text-subtitle-1"
                    :class="zone.fee == 0 ? 'text-success' : 'text-primary'"
                  >
                    {{ zone.fee == 0 ? "مجانًا" : zone.fee + " ج.م" }}
                  </span>
                </td>

                <td class="text-center text-grey-darken-1">
                  {{ zone.delivery_time || "غير محدد" }}
                </td>

                <td class="text-center text-error font-weight-bold">
                  {{ zone.cod_fee ? zone.cod_fee + " ج.م" : "0" }}
                </td>

                <td class="text-center">
                  <v-switch
                    v-model="zone.is_active"
                    color="success"
                    hide-details
                    density="compact"
                    class="d-inline-flex"
                    :disabled="processingIds.includes(zone.id)"
                    @change="toggleZoneStatus(zone)"
                  ></v-switch>
                </td>

                <td class="text-center">
                  <v-btn
                    icon
                    size="small"
                    color="primary"
                    variant="text"
                    @click="editItem(zone)"
                    title="تعديل"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    icon
                    size="small"
                    color="error"
                    variant="text"
                    :disabled="processingIds.includes(zone.id)"
                    @click="openDeleteDialog(zone)"
                    title="حذف"
                  >
                    <v-progress-circular
                      v-if="
                        processingIds.includes(zone.id) &&
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
            {{ isEditing ? "تعديل منطقة الشحن" : "إضافة منطقة شحن جديدة" }}
          </v-card-title>
          <v-divider class="my-3"></v-divider>

          <v-card-text>
            <v-form ref="form" v-model="validForm">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    label="اسم المنطقة / المحافظة (مثال: القاهرة والجيزة)"
                    variant="outlined"
                    color="primary"
                    :rules="[(v) => !!v || 'اسم المنطقة مطلوب']"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model.number="editedItem.fee"
                    label="تكلفة الشحن (ج.م)"
                    type="number"
                    variant="outlined"
                    color="primary"
                    :rules="[
                      (v) => (v !== null && v !== '') || 'التكلفة مطلوبة',
                    ]"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    v-model.number="editedItem.cod_fee"
                    label="رسوم الدفع عند الاستلام"
                    type="number"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.delivery_time"
                    label="مدة التوصيل المتوقعة (مثال: خلال يومين إلى 3 أيام)"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" v-if="!isEditing">
                  <v-switch
                    v-model="editedItem.is_active"
                    color="success"
                    label="تفعيل المنطقة فور حفظها"
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
              حفظ البيانات
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
            >هل أنت متأكد من حذف هذه المنطقة؟</v-card-title
          >
          <v-card-text class="text-grey-darken-1"
            >لن يتمكن العملاء من اختيار هذه المنطقة أثناء إتمام
            الطلب.</v-card-text
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

const zones = ref([]);
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

const isFreeShippingEnabled = ref(false);
const freeShippingThreshold = ref(0);
const savingSettings = ref(false);

const dialog = ref(false);
const dialogDelete = ref(false);
const isEditing = ref(false);
const validForm = ref(false);
const saving = ref(false);
const deleting = ref(false);
const itemToDelete = ref(null);

const defaultItem = {
  id: null,
  name: "",
  fee: 0,
  delivery_time: "",
  cod_fee: 0,
  is_active: true,
};
const editedItem = ref({ ...defaultItem });

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchZonesAndSettings(true);
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
    fetchZonesAndSettings(true);
    startPolling();
  }
};
// ----------------------------------

const fetchZonesAndSettings = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const [zonesRes, settingsRes] = await Promise.all([
      axios.get("/admin/shipping-zones"),
      axios.get("/admin/settings"),
    ]);
    zones.value = zonesRes.data;

    // settings payload returns an object mapping key to value
    if (settingsRes.data) {
      isFreeShippingEnabled.value =
        settingsRes.data.free_shipping_enabled === "true";
      freeShippingThreshold.value =
        Number(settingsRes.data.free_shipping_threshold) || 0;
    }
  } catch (error) {
    console.error("Error loading data:", error);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const averageShippingCost = computed(() => {
  if (zones.value.length === 0) return 0;
  const total = zones.value.reduce((sum, zone) => sum + Number(zone.fee), 0);
  return Math.round(total / zones.value.length);
});

const saveShippingSettings = async () => {
  savingSettings.value = true;
  try {
    await axios.post("/admin/settings", {
      free_shipping_enabled: isFreeShippingEnabled.value ? "true" : "false",
      free_shipping_threshold: freeShippingThreshold.value,
    });
    showMessage("تم حفظ إعدادات الشحن بنجاح");
  } catch (err) {
    console.error("Error saving settings", err);
    showMessage("حدث خطأ في حفظ الإعدادات", "error");
  } finally {
    savingSettings.value = false;
  }
};

const openAddDialog = () => {
  isEditing.value = false;
  editedItem.value = { ...defaultItem };
  dialog.value = true;
};

const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = { ...item };
  editedItem.value.is_active = !!editedItem.value.is_active;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  editedItem.value = { ...defaultItem };
};

const saveItem = async () => {
  saving.value = true;
  try {
    const payload = {
      name: editedItem.value.name,
      fee: editedItem.value.fee,
      delivery_time: editedItem.value.delivery_time,
      cod_fee: editedItem.value.cod_fee,
      is_active: editedItem.value.is_active,
    };

    if (isEditing.value) {
      const res = await axios.put(
        `/admin/shipping-zones/${editedItem.value.id}`,
        payload
      );
      const index = zones.value.findIndex((z) => z.id === editedItem.value.id);
      if (index !== -1) zones.value[index] = res.data;
      showMessage("تم تحديث المنطقة بنجاح");
    } else {
      const res = await axios.post("/admin/shipping-zones", payload);
      zones.value.unshift(res.data);
      showMessage("تمت إضافة المنطقة بنجاح");
    }
    closeDialog();
  } catch (err) {
    console.error("Error saving zone:", err);
    showMessage(err.response?.data?.message || "حدث خطأ أثناء الحفظ", "error");
  } finally {
    saving.value = false;
  }
};

const toggleZoneStatus = async (zone) => {
  processingIds.value.push(zone.id);
  lastAction.value = "toggle";
  try {
    await axios.put(`/admin/shipping-zones/${zone.id}`, {
      ...zone,
      is_active: zone.is_active,
    });
    showMessage(
      zone.is_active ? "تم تفعيل منطقة الشحن" : "تم إلغاء تفعيل منطقة الشحن",
      zone.is_active ? "success" : "warning"
    );
  } catch (err) {
    console.error("Error toggling zone status:", err);
    zone.is_active = !zone.is_active;
    showMessage("حدث خطأ أثناء تغيير الحالة", "error");
  } finally {
    processingIds.value = processingIds.value.filter((id) => id !== zone.id);
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
    await axios.delete(`/admin/shipping-zones/${itemToDelete.value.id}`);
    zones.value = zones.value.filter((z) => z.id !== itemToDelete.value.id);
    showMessage("تم حذف المنطقة بنجاح");
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (error) {
    console.error("Error deleting zone:", error);
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
  fetchZonesAndSettings();
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

.custom-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 14px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.custom-table td {
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
.border-left-success {
  border-right: 4px solid #4caf50 !important;
}
.border-left-warning {
  border-right: 4px solid #fb8c00 !important;
}

.opacity-50 {
  opacity: 0.5;
  pointer-events: none;
  transition: opacity 0.3s ease;
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
