<template>
  <v-locale-provider rtl>
    <v-container fluid class="pa-6" dir="rtl">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text class="pa-4">
          <div
            class="d-flex align-center justify-space-between flex-wrap gap-4 mb-4"
          >
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              الأقسام والماركات
            </h2>

            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              class="font-weight-bold px-6 rounded-lg shadow-btn"
              height="44"
              @click="openAddDialog"
            >
              إضافة {{ activeTab === "categories" ? "قسم" : "ماركة" }}
            </v-btn>
          </div>

          <v-row class="mb-2">
            <v-col cols="12" md="6">
              <v-text-field
                v-model="searchQuery"
                prepend-inner-icon="mdi-magnify"
                :label="
                  'بحث في ' +
                  (activeTab === 'categories' ? 'الأقسام' : 'الماركات') +
                  '...'
                "
                variant="outlined"
                density="compact"
                hide-details
                color="primary"
                class="rounded-lg"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-tabs v-model="activeTab" color="primary" class="border-bottom">
            <v-tab value="categories" class="font-weight-bold text-subtitle-1">
              <v-icon right class="ml-2">mdi-shape-outline</v-icon>
              الأقسام (Categories)
            </v-tab>
            <v-tab value="brands" class="font-weight-bold text-subtitle-1">
              <v-icon right class="ml-2">mdi-tag-multiple-outline</v-icon>
              الماركات (Brands)
            </v-tab>
          </v-tabs>
        </v-card-text>
      </v-card>

      <v-window v-model="activeTab">
        <v-window-item value="categories">
          <v-card class="rounded-xl overflow-hidden" elevation="1">
            <v-table class="custom-table pa-2">
              <thead class="bg-grey-lighten-4">
                <tr>
                  <th class="text-right font-weight-bold">صورة القسم</th>
                  <th class="text-right font-weight-bold">اسم القسم</th>
                  <th class="text-right font-weight-bold">عدد المنتجات</th>
                  <th class="text-right font-weight-bold">الحالة</th>
                  <th class="text-center font-weight-bold">الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="cat in filteredCategories"
                  :key="cat.id"
                  class="hover-row"
                >
                  <td>
                    <v-avatar
                      size="60"
                      rounded="lg"
                      class="elevation-1 my-2 bg-grey-lighten-3"
                    >
                      <v-img :src="cat.image" cover></v-img>
                    </v-avatar>
                  </td>
                  <td class="font-weight-bold text-subtitle-1">
                    {{ cat.name }}
                  </td>
                  <td class="text-grey-darken-1">
                    {{ cat.productsCount }} منتج
                  </td>
                  <td>
                    <v-switch
                      :model-value="cat.isActive"
                      @update:model-value="toggleStatus(cat, 'categories')"
                      color="success"
                      hide-details
                      density="compact"
                      class="d-inline-flex"
                    ></v-switch>
                  </td>
                  <td class="text-center">
                    <v-btn
                      icon
                      size="small"
                      color="primary"
                      variant="text"
                      @click="editItem(cat)"
                    >
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                      icon
                      size="small"
                      color="error"
                      variant="text"
                      @click="openDeleteDialog(cat)"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-card>
        </v-window-item>

        <v-window-item value="brands">
          <v-card class="rounded-xl overflow-hidden" elevation="1">
            <v-table class="custom-table pa-2">
              <thead class="bg-grey-lighten-4">
                <tr>
                  <th class="text-right font-weight-bold">لوجو الماركة</th>
                  <th class="text-right font-weight-bold">اسم الماركة</th>
                  <th class="text-right font-weight-bold">عدد المنتجات</th>
                  <th class="text-right font-weight-bold">الحالة</th>
                  <th class="text-center font-weight-bold">الإجراءات</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="brand in filteredBrands"
                  :key="brand.id"
                  class="hover-row"
                >
                  <td>
                    <v-avatar
                      size="60"
                      rounded="lg"
                      class="elevation-1 my-2 bg-white"
                      style="border: 1px solid #eee"
                    >
                      <v-img :src="brand.logo" contain></v-img>
                    </v-avatar>
                  </td>
                  <td class="font-weight-bold text-subtitle-1">
                    {{ brand.name }}
                  </td>
                  <td class="text-grey-darken-1">
                    {{ brand.productsCount }} منتج
                  </td>
                  <td>
                    <v-switch
                      :model-value="brand.isActive"
                      @update:model-value="toggleStatus(brand, 'brands')"
                      color="success"
                      hide-details
                      density="compact"
                      class="d-inline-flex"
                    ></v-switch>
                  </td>
                  <td class="text-center">
                    <v-btn
                      icon
                      size="small"
                      color="primary"
                      variant="text"
                      @click="editItem(brand)"
                    >
                      <v-icon>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                      icon
                      size="small"
                      color="error"
                      variant="text"
                      @click="openDeleteDialog(brand)"
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-card>
        </v-window-item>
      </v-window>

      <v-dialog v-model="dialog" max-width="500px" persistent>
        <v-card class="rounded-xl pa-2">
          <v-card-title
            class="text-h5 font-weight-bold pt-4 px-4"
            style="color: rgb(var(--v-theme-primary))"
          >
            {{ isEditing ? "تعديل" : "إضافة" }}
            {{ activeTab === "categories" ? "قسم" : "ماركة" }}
          </v-card-title>
          <v-divider class="my-3"></v-divider>

          <v-card-text>
            <v-form ref="form" v-model="validForm">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    :label="
                      activeTab === 'categories' ? 'اسم القسم' : 'اسم الماركة'
                    "
                    variant="outlined"
                    color="primary"
                    :rules="[(v) => !!v || 'الاسم مطلوب']"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-file-input
                    v-model="imageFile"
                    :label="
                      activeTab === 'categories'
                        ? 'اختر صورة القسم'
                        : 'اختر لوجو الماركة'
                    "
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
                      width="120"
                      height="120"
                      :cover="activeTab === 'categories'"
                      :contain="activeTab === 'brands'"
                    ></v-img>
                  </v-card>
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
          <v-card-title class="text-h6 font-weight-bold text-wrap">
            هل أنت متأكد من حذف
            {{ activeTab === "categories" ? "هذا القسم" : "هذه الماركة" }}؟
          </v-card-title>
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
              :disabled="deleting"
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
import { ref, onMounted, onUnmounted, computed } from "vue";
import api from "../../axios";
import { ProductModule } from "@/store/Products";

const productStore = ProductModule();

// التبويب النشط (الافتراضي: الأقسام)
const activeTab = ref("categories");
const searchQuery = ref("");

const categories = ref([]);
const brands = ref([]);
const loading = ref(false);

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchData(true);
  }, 60000); // 60 seconds (low volatility)
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
    fetchData(true);
    startPolling();
  }
};
// ----------------------------------

const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value;
  return categories.value.filter((c) =>
    c.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const filteredBrands = computed(() => {
  if (!searchQuery.value) return brands.value;
  return brands.value.filter((b) =>
    b.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const fetchData = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const [catsRes, brandsRes] = await Promise.all([
      api.get("/admin/categories"),
      api.get("/admin/brands"),
    ]);
    categories.value = catsRes.data.map((c) => ({
      ...c,
      productsCount: c.products_count || 0,
      isActive: !!c.is_active,
      image: c.thumbnail || c.image || "",
    }));
    brands.value = brandsRes.data.map((b) => ({
      ...b,
      productsCount: b.products_count || 0,
      isActive: !!b.is_active,
      logo: b.thumbnail || b.logo || "",
    }));
  } catch (e) {
    console.error(e);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const toggleStatus = async (item, type) => {
  try {
    const endpoint = `/admin/${type}/${item.id}/status`;
    await api.put(endpoint);
    item.isActive = !item.isActive;
    showMessage("تم تحديث الحالة بنجاح");
    fetchData(true);
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء تحديث الحالة", "error");
  }
};

onMounted(() => {
  fetchData();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});

// حالة النوافذ
const dialog = ref(false);
const dialogDelete = ref(false);
const isEditing = ref(false);
const validForm = ref(false);
const itemToDelete = ref(null);
const saving = ref(false);
const deleting = ref(false);

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

// متغيرات الصورة
const imageFile = ref(null);
const previewImage = ref("");

// العنصر المُعدل
const editedItem = ref({
  id: null,
  name: "",
  image: "",
  logo: "",
  is_active: true,
});

// معالجة رفع الصورة (تعمل مع الأقسام والماركات)
const handleImageUpload = (fileArray) => {
  const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
  if (file) {
    previewImage.value = URL.createObjectURL(file);
    if (activeTab.value === "categories") {
      editedItem.value.image = previewImage.value;
    } else {
      editedItem.value.logo = previewImage.value;
    }
  } else {
    if (!isEditing.value) {
      previewImage.value = "";
      editedItem.value.image = "";
      editedItem.value.logo = "";
    }
  }
};

// فتح نافذة الإضافة
const openAddDialog = () => {
  isEditing.value = false;
  editedItem.value = {
    id: null,
    name: "",
    image: "",
    logo: "",
    productsCount: 0,
    is_active: true,
  };
  imageFile.value = null;
  previewImage.value = "";
  dialog.value = true;
};

// فتح نافذة التعديل
const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = { ...item, is_active: item.isActive ?? true };
  imageFile.value = null;
  // عرض الصورة القديمة حسب التبويب
  previewImage.value =
    activeTab.value === "categories" ? item.image : item.logo;
  dialog.value = true;
};

// إغلاق النافذة
const closeDialog = () => {
  dialog.value = false;
  setTimeout(() => {
    editedItem.value = {
      id: null,
      name: "",
      image: "",
      logo: "",
      is_active: true,
    };
    imageFile.value = null;
    previewImage.value = "";
  }, 300);
};

// حفظ البيانات
const saveItem = async () => {
  if (!validForm.value) return;
  saving.value = true;
  const endpoint =
    activeTab.value === "categories" ? "/admin/categories" : "/admin/brands";
  try {
    const payload = new FormData();
    payload.append("name", editedItem.value.name);

    if (activeTab.value === "categories") {
      payload.append("is_active", editedItem.value.is_active ? 1 : 0);
    }

    const file = Array.isArray(imageFile.value)
      ? imageFile.value[0]
      : imageFile.value;
    if (file) {
      if (activeTab.value === "categories") {
        payload.append("thumbnail", file);
      } else {
        payload.append("logo", file);
      }
    }

    if (isEditing.value) {
      payload.append("_method", "PUT");
      await api.post(`${endpoint}/${editedItem.value.id}`, payload, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      showMessage("تم التحديث بنجاح!");
    } else {
      await api.post(endpoint, payload, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      showMessage("تمت الإضافة بنجاح!");
    }

    await fetchData();
    // Update Pinia store depending on active tab
    if (activeTab.value === "categories") await productStore.getCategories();
    else await productStore.getBrands();

    closeDialog();
  } catch (e) {
    console.error(e);
    showMessage(e.response?.data?.message || "حدث خطأ أثناء الحفظ", "error");
  } finally {
    saving.value = false;
  }
};

// فتح نافذة الحذف
const openDeleteDialog = (item) => {
  itemToDelete.value = item;
  dialogDelete.value = true;
};

// تأكيد الحذف
const confirmDelete = async () => {
  deleting.value = true;
  const endpoint =
    activeTab.value === "categories" ? "/admin/categories" : "/admin/brands";
  try {
    await api.delete(`${endpoint}/${itemToDelete.value.id}`);

    await fetchData();
    if (activeTab.value === "categories") await productStore.getCategories();
    else await productStore.getBrands();

    showMessage("تم الحذف بنجاح!");
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (e) {
    showMessage(
      e.response?.data?.message || "حدث خطأ أثناء محاولة الحذف",
      "error"
    );
  } finally {
    deleting.value = false;
  }
};
</script>

<style scoped>
.shadow-btn {
  box-shadow: 0 4px 12px rgba(24, 103, 192, 0.3) !important;
  transition: transform 0.2s ease;
}
.shadow-btn:hover {
  transform: translateY(-2px);
}

.border-bottom {
  border-bottom: 1px solid #eee;
}

.custom-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 15px !important;
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
  background-color: #f8f9fa;
}

.gap-4 {
  gap: 16px;
}
</style>
