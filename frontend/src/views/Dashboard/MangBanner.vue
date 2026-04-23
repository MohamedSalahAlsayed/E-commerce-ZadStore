<template>
  <v-locale-provider rtl>
    <v-container fluid class="pa-6" dir="rtl">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text class="pa-4">
          <div
            class="d-flex align-center justify-space-between flex-wrap gap-4"
          >
            <div class="d-flex align-center gap-3">
              <v-avatar
                color="rgb(var(--v-theme-primary))"
                size="48"
                class="rounded-lg"
              >
                <v-icon color="white" size="28"
                  >mdi-view-carousel-outline</v-icon
                >
              </v-avatar>
              <h2
                class="text-h5 font-weight-black"
                style="color: rgb(var(--v-theme-primary))"
              >
                {{ $t("banners.title") }}
              </h2>
            </div>

            <div class="d-flex align-center gap-3">
              <v-btn
                v-if="banners.length > 0"
                :color="
                  selectedIds.length === banners.length
                    ? 'primary'
                    : 'grey-darken-1'
                "
                variant="tonal"
                class="font-weight-bold rounded-lg"
                @click="toggleSelectAll"
              >
                {{
                  selectedIds.length === banners.length
                    ? $t("dashboard.deselect_all")
                    : $t("dashboard.select_all")
                }}
              </v-btn>
              <v-select
                v-model="filterType"
                :items="['الكل', 'slider', 'sidebar', 'popup']"
                :label="$t('banners.type_label')"
                variant="outlined"
                density="compact"
                hide-details
                style="width: 150px"
              ></v-select>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                class="font-weight-bold px-6 rounded-lg shadow-btn"
                height="44"
                @click="openAddDialog"
              >
                {{ $t("banners.add_new") }}
              </v-btn>
            </div>
          </div>
        </v-card-text>
      </v-card>

      <!-- Batch Actions Bar -->
      <v-expand-transition>
        <v-card
          v-if="selectedIds.length > 0"
          class="mb-6 rounded-xl bg-error text-white pa-4 mx-3"
          elevation="4"
        >
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center gap-3">
              <v-icon>mdi-checkbox-multiple-marked</v-icon>
              <div class="text-h6 font-weight-bold">
                {{
                  $t("banners.selected_count").replace(
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

      <v-row>
        <template v-if="loading">
          <v-col
            cols="12"
            md="6"
            lg="4"
            v-for="i in 6"
            :key="`skel-banner-${i}`"
          >
            <v-skeleton-loader type="card" class="rounded-xl" height="250" />
          </v-col>
        </template>

        <v-col
          cols="12"
          class="text-center py-10"
          v-else-if="banners.length === 0"
        >
          <v-icon size="60" color="grey-lighten-1"
            >mdi-image-off-outline</v-icon
          >
          <p class="text-grey-darken-1 mt-2 font-weight-bold">
            لا توجد بانرات إعلانية حالياً.
          </p>
        </v-col>

        <v-col
          cols="12"
          md="6"
          lg="4"
          v-for="banner in banners"
          :key="banner.id"
          v-else
        >
          <v-card class="rounded-xl overflow-hidden banner-card" elevation="2">
            <v-img
              :src="banner.image"
              height="200"
              cover
              class="align-end text-white"
            >
              <div class="overlay-gradient pa-4 h-100 flex-column d-flex">
                <div class="d-flex justify-space-between align-start w-100">
                  <v-checkbox
                    v-model="selectedIds"
                    :value="banner.id"
                    color="white"
                    hide-details
                    density="compact"
                    class="mt-n2 ms-n2"
                  ></v-checkbox>
                  <div class="d-flex gap-2 mb-2">
                    <v-chip
                      size="x-small"
                      :color="banner.is_active ? 'success' : 'error'"
                      class="font-weight-bold"
                    >
                      {{ banner.is_active ? "نشط" : "متوقف" }}
                    </v-chip>
                    <v-chip
                      size="x-small"
                      color="white"
                      variant="flat"
                      class="text-black font-weight-bold"
                    >
                      {{ banner.type?.toUpperCase() || "" }} #{{
                        banner.position
                      }}
                    </v-chip>
                    <v-chip
                      size="x-small"
                      color="info"
                      variant="flat"
                      class="font-weight-bold"
                    >
                      {{
                        banner.target === "guest"
                          ? "زوار فقط"
                          : banner.target === "user"
                          ? "مستخدمين فقط"
                          : "للكل"
                      }}
                    </v-chip>
                  </div>
                </div>
                <v-spacer></v-spacer>
                <h3
                  v-if="banner.title"
                  class="text-h6 font-weight-bold mb-1 text-truncate"
                >
                  {{ banner.title }}
                </h3>
              </div>
            </v-img>

            <v-card-actions class="bg-grey-lighten-4 pa-3">
              <v-btn
                v-if="banner.link"
                size="small"
                variant="text"
                color="primary"
                class="font-weight-bold"
                :href="banner.link"
                target="_blank"
              >
                <v-icon left class="mr-1">mdi-link</v-icon> الرابط
              </v-btn>
              <v-spacer></v-spacer>
              <v-switch
                v-model="banner.is_active"
                color="success"
                hide-details
                density="compact"
                class="mr-2"
                :disabled="processingIds.includes(banner.id)"
                @change="toggleActive(banner)"
              ></v-switch>
              <v-btn
                icon
                size="small"
                color="primary"
                variant="text"
                @click="editItem(banner)"
              >
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
              <v-btn
                icon
                size="small"
                color="error"
                variant="text"
                :disabled="processingIds.includes(banner.id)"
                @click="openDeleteDialog(banner)"
              >
                <v-progress-circular
                  v-if="
                    processingIds.includes(banner.id) && lastAction === 'delete'
                  "
                  indeterminate
                  size="16"
                ></v-progress-circular>
                <v-icon v-else>mdi-delete</v-icon>
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>

      <v-dialog v-model="dialog" max-width="600px" persistent>
        <v-card class="rounded-xl pa-2">
          <v-card-title
            class="text-h5 font-weight-bold pt-4 px-4"
            style="color: rgb(var(--v-theme-primary))"
          >
            {{ isEditing ? "تعديل بانر" : "إضافة بانر" }}
          </v-card-title>
          <v-divider class="my-3"></v-divider>

          <v-card-text>
            <v-form ref="form" v-model="validForm">
              <v-row>
                <v-col cols="12" sm="8">
                  <v-text-field
                    v-model="editedItem.title"
                    label="العنوان (اختياري)"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model.number="editedItem.position"
                    label="الترتيب"
                    type="number"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" sm="6">
                  <v-select
                    v-model="editedItem.type"
                    :items="['slider', 'sidebar', 'popup']"
                    label="نوع ومكان البانر"
                    variant="outlined"
                    color="primary"
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-select
                    v-model="editedItem.target"
                    :items="[
                      { title: 'يظهر للكل', value: 'both' },
                      { title: 'زوار الموقع فقط', value: 'guest' },
                      { title: 'لوحة تحكم المستخدمين', value: 'user' },
                    ]"
                    label="الفئة المستهدفة"
                    variant="outlined"
                    color="primary"
                  ></v-select>
                </v-col>

                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.link"
                    label="رابط التوجيه (اختياري)"
                    variant="outlined"
                    color="primary"
                    placeholder="مثال: /category/1"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-file-input
                    v-model="imageFile"
                    label="الرجاء اختيار صورة البانر (إلزامي للجديد)"
                    variant="outlined"
                    color="primary"
                    accept="image/*"
                    prepend-icon=""
                    prepend-inner-icon="mdi-camera"
                    @update:modelValue="handleImageUpload"
                    :rules="[
                      (v) => isEditing || !!previewImage || 'الصورة مطلوبة',
                    ]"
                  ></v-file-input>
                </v-col>

                <v-col
                  cols="12"
                  class="d-flex justify-center"
                  v-if="previewImage"
                >
                  <v-card
                    elevation="2"
                    class="rounded-lg pa-1 bg-grey-lighten-4"
                  >
                    <v-img
                      :src="previewImage"
                      width="200"
                      height="auto"
                      cover
                      class="rounded"
                    ></v-img>
                  </v-card>
                </v-col>

                <v-col cols="12" v-if="!isEditing">
                  <v-switch
                    v-model="editedItem.is_active"
                    color="success"
                    label="تفعيل البانر فور حفظه"
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
              >{{ $t("dashboard.cancel") }}</v-btn
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
          <v-card-title class="text-h6 font-weight-bold text-wrap">
            هل أنت متأكد من حذف هذا البانر؟
          </v-card-title>
          <v-card-actions class="d-flex justify-center mt-4">
            <v-btn
              color="grey-darken-1"
              variant="tonal"
              class="px-6 font-weight-bold"
              @click="dialogDelete = false"
              >{{ $t("dashboard.cancel") }}</v-btn
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

      <!-- Batch Delete Dialog -->
      <v-dialog v-model="batchDeleteDialog" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3">mdi-delete-alert</v-icon>
          <v-card-title class="text-h6 font-weight-bold text-wrap">
            هل أنت متأكد من حذف {{ selectedIds.length }} بانرات؟
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
import { ref, onMounted, onUnmounted } from "vue";
import axios from "@/axios";

const banners = ref([]);
const loading = ref(true);
const dialog = ref(false);
const dialogDelete = ref(false);
const isEditing = ref(false);
const validForm = ref(false);
const saving = ref(false);
const deleting = ref(false);
const itemToDelete = ref(null);

const imageFile = ref(null);
const previewImage = ref("");
const processingIds = ref([]);
const lastAction = ref("");
const filterType = ref("الكل");

const selectedIds = ref([]);
const batchDeleteDialog = ref(false);

const toggleSelectAll = () => {
  if (selectedIds.value.length === banners.value.length) {
    selectedIds.value = [];
  } else {
    selectedIds.value = banners.value.map((b) => b.id);
  }
};

const confirmBatchDelete = async () => {
  if (selectedIds.value.length === 0) return;
  deleting.value = true;
  try {
    await axios.post("/admin/banners/batch-delete", { ids: selectedIds.value });
    showMessage("تم حذف البانرات بنجاح");
    banners.value = banners.value.filter(
      (b) => !selectedIds.value.includes(b.id)
    );
    selectedIds.value = [];
    batchDeleteDialog.value = false;
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء الحذف الجماعي", "error");
  } finally {
    deleting.value = false;
  }
};

const snackbar = ref({
  show: false,
  text: "",
  color: "success",
});

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchBanners(true);
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
    fetchBanners(true);
    startPolling();
  }
};
// ----------------------------------

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const handleImageUpload = (fileArray) => {
  const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
  if (file) {
    previewImage.value = URL.createObjectURL(file);
  } else {
    if (!isEditing.value) previewImage.value = "";
  }
};

const defaultItem = {
  id: null,
  title: "",
  link: "",
  image: "",
  type: "slider",
  position: 0,
  target: "both",
  is_active: true,
};
const editedItem = ref({ ...defaultItem });

const fetchBanners = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const response = await axios.get("/admin/banners");
    banners.value = response.data;
  } catch (error) {
    console.error("Error fetching banners:", error);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const openAddDialog = () => {
  isEditing.value = false;
  editedItem.value = { ...defaultItem };
  imageFile.value = null;
  previewImage.value = "";
  dialog.value = true;
};

const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = { ...item };
  // ensure boolean
  editedItem.value.is_active = !!editedItem.value.is_active;

  imageFile.value = null;
  previewImage.value = item.image;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  editedItem.value = { ...defaultItem };
  imageFile.value = null;
  previewImage.value = "";
};

const saveItem = async () => {
  saving.value = true;
  try {
    const formData = new FormData();
    if (editedItem.value.title)
      formData.append("title", editedItem.value.title);
    if (editedItem.value.link) formData.append("link", editedItem.value.link);
    formData.append("type", editedItem.value.type);
    formData.append("position", editedItem.value.position);
    formData.append("target", editedItem.value.target || "both");
    formData.append("is_active", editedItem.value.is_active ? 1 : 0);

    const fileToUpload = Array.isArray(imageFile.value)
      ? imageFile.value[0]
      : imageFile.value;
    if (fileToUpload) {
      formData.append("image", fileToUpload);
    }

    if (isEditing.value) {
      formData.append("_method", "PUT");
      const res = await axios.post(
        `/admin/banners/${editedItem.value.id}`,
        formData,
        {
          headers: { "Content-Type": "multipart/form-data" },
        }
      );
      const index = banners.value.findIndex(
        (b) => b.id === editedItem.value.id
      );
      if (index !== -1) banners.value[index] = res.data;
      showMessage("تم تحديث البانر بنجاح");
    } else {
      const res = await axios.post("/admin/banners", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      banners.value.unshift(res.data);
      showMessage("تم تنزيل البانر بنجاح");
    }
    closeDialog();
    fetchBanners(true);
  } catch (error) {
    console.error("Error saving banner:", error);
    showMessage("حدث خطأ أثناء الحفظ", "error");
  } finally {
    saving.value = false;
  }
};

const toggleActive = async (banner) => {
  processingIds.value.push(banner.id);
  lastAction.value = "toggle";
  try {
    await axios.put(`/admin/banners/${banner.id}`, {
      ...banner,
      is_active: banner.is_active ? 1 : 0,
    });
    showMessage(
      banner.is_active ? "تم تفعيل البانر" : "تم إلغاء تفعيل البانر",
      banner.is_active ? "success" : "warning"
    );
  } catch (error) {
    console.error("Error updating status:", error);
    banner.is_active = !banner.is_active; // revert
    showMessage("حدث خطأ أثناء تغيير الحالة", "error");
  } finally {
    processingIds.value = processingIds.value.filter((id) => id !== banner.id);
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
    await axios.delete(`/admin/banners/${itemToDelete.value.id}`);
    banners.value = banners.value.filter((b) => b.id !== itemToDelete.value.id);
    showMessage("تم حذف البانر بنجاح");
    dialogDelete.value = false;
    itemToDelete.value = null;
    fetchBanners(true);
  } catch (error) {
    console.error("Error deleting banner:", error);
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
  fetchBanners();
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
  box-shadow: 0 4px 12px rgba(24, 103, 192, 0.3) !important;
  transition: transform 0.2s ease;
}
.shadow-btn:hover {
  transform: translateY(-2px);
}

.banner-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.banner-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
}

.overlay-gradient {
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.8) 0%,
    rgba(0, 0, 0, 0) 100%
  );
  width: 100%;
}
</style>
