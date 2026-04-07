<template>
  <v-locale-provider rtl>
    <v-container fluid class="pa-6" dir="rtl">
      <!-- Header Card with Search and Filters -->
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text class="pa-4">
          <v-row align="center">
            <v-col cols="12" md="3">
              <h2
                class="text-h5 font-weight-black mb-1"
                style="color: rgb(var(--v-theme-primary))"
              >
                إدارة المنتجات
              </h2>
              <div class="text-caption text-grey-darken-1">
                إجمالي المنتجات: {{ products?.length || 0 }}
              </div>
            </v-col>

            <v-col cols="12" md="9">
              <div class="d-flex align-center gap-3 flex-wrap justify-end">
                <v-text-field
                  v-model="searchQuery"
                  prepend-inner-icon="mdi-magnify"
                  label="ابحث بالاسم..."
                  variant="outlined"
                  density="compact"
                  hide-details
                  style="max-width: 250px"
                  class="bg-white rounded-lg"
                ></v-text-field>

                <v-select
                  v-model="filterCategory"
                  :items="['الكل', ...(categories || []).map((c) => c.name)]"
                  label="القسم"
                  variant="outlined"
                  density="compact"
                  hide-details
                  style="max-width: 150px"
                ></v-select>

                <v-select
                  v-model="filterBrand"
                  :items="['الكل', ...(brands || []).map((b) => b.name)]"
                  label="الماركة"
                  variant="outlined"
                  density="compact"
                  hide-details
                  style="max-width: 150px"
                ></v-select>

                <v-select
                  v-model="filterStock"
                  :items="['الكل', 'نفذ من المخزن', 'مخزن منخفض']"
                  label="حالة المخزن"
                  variant="outlined"
                  density="compact"
                  hide-details
                  style="max-width: 150px"
                ></v-select>

                <v-btn
                  color="primary"
                  prepend-icon="mdi-plus"
                  class="font-weight-bold px-6 rounded-lg shadow-btn"
                  height="40"
                  @click="openAddDialog"
                >
                  إضافة منتج
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <!-- Batch Actions Bar -->
      <v-expand-transition>
        <v-card
          v-if="selectedIds.length > 0"
          class="mb-6 rounded-xl bg-primary text-white pa-4"
          elevation="4"
        >
          <div class="d-flex align-center justify-space-between">
            <div class="d-flex align-center gap-3">
              <v-icon>mdi-checkbox-multiple-marked</v-icon>
              <div class="text-h6 font-weight-bold">
                تم تحديد {{ selectedIds.length }} منتج
              </div>
            </div>
            <div class="d-flex gap-2">
              <v-btn
                color="white"
                variant="tonal"
                prepend-icon="mdi-delete-multiple"
                @click="openBatchDeleteDialog"
              >
                حذف المحدد
              </v-btn>
              <v-btn color="white" variant="outlined" @click="selectedIds = []"
                >إلغاء</v-btn
              >
            </div>
          </div>
        </v-card>
      </v-expand-transition>

      <!-- Products Table -->
      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <v-progress-linear
          v-if="loading"
          indeterminate
          color="primary"
        ></v-progress-linear>

        <v-table class="products-table pa-2" fixed-header height="600">
          <thead class="bg-grey-lighten-4">
            <tr>
              <th class="text-right font-weight-bold" style="width: 50px">
                <v-checkbox-btn
                  v-model="selectAll"
                  color="primary"
                  hide-details
                ></v-checkbox-btn>
              </th>
              <th class="text-right font-weight-bold">المنتج</th>
              <th class="text-right font-weight-bold">القسم / الماركة</th>
              <th class="text-right font-weight-bold">الأسعار</th>
              <th class="text-right font-weight-bold">المخزون / الربح</th>
              <th class="text-right font-weight-bold">الحالة</th>
              <th class="text-center font-weight-bold">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <!-- Skeleton Loading -->
            <template v-if="loading">
              <tr v-for="n in 5" :key="n">
                <td><v-skeleton-loader type="avatar"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="chip"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="chip"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="text"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="chip"></v-skeleton-loader></td>
                <td><v-skeleton-loader type="actions"></v-skeleton-loader></td>
              </tr>
            </template>

            <template v-else>
              <tr v-if="(filteredProducts || []).length === 0">
                <td colspan="9" class="text-center pa-12">
                  <v-icon size="64" color="grey-lighten-1" class="mb-4"
                    >mdi-package-variant-closed</v-icon
                  >
                  <div class="text-h6 text-grey-darken-1">
                    لا توجد منتجات مطابقة.
                  </div>
                </td>
              </tr>

              <tr
                v-for="item in filteredProducts"
                :key="item.id"
                class="hover-row"
              >
                <td>
                  <v-checkbox-btn
                    v-model="selectedIds"
                    :value="item.id"
                    color="primary"
                    hide-details
                  ></v-checkbox-btn>
                </td>
                <td>
                  <div class="d-flex align-center gap-3 py-2">
                    <v-avatar
                      size="50"
                      rounded="lg"
                      class="bg-grey-lighten-3 border"
                    >
                      <v-img :src="item.thumbnail" cover></v-img>
                    </v-avatar>
                    <div>
                      <div class="font-weight-bold text-subtitle-2">
                        {{ shortTitle(item.title) }}
                      </div>
                      <div class="text-caption text-grey">
                        SKU: {{ item.sku || "N/A" }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column gap-1">
                    <v-chip
                      size="x-small"
                      color="primary"
                      variant="tonal"
                      class="w-fit"
                      >{{ item.category }}</v-chip
                    >
                    <v-chip
                      size="x-small"
                      color="secondary"
                      variant="outlined"
                      v-if="item.brand"
                      class="w-fit"
                      >{{ item.brand }}</v-chip
                    >
                  </div>
                </td>
                <td>
                  <div class="font-weight-bold text-primary">
                    {{ item.price }} ج.م
                  </div>
                  <div class="text-caption text-grey">
                    التكلفة: {{ item.purchase_price || 0 }} ج.م
                  </div>
                </td>
                <td>
                  <div class="mb-1">
                    <v-chip
                      size="x-small"
                      :color="
                        item.stock > 10
                          ? 'success'
                          : item.stock > 0
                          ? 'warning'
                          : 'error'
                      "
                      class="font-weight-bold"
                    >
                      {{ item.stock }} في المخزن
                    </v-chip>
                  </div>
                  <v-chip
                    size="x-small"
                    :color="
                      item.price - item.purchase_price > 0 ? 'success' : 'grey'
                    "
                    variant="flat"
                  >
                    ربح:
                    {{ (item.price - (item.purchase_price || 0)).toFixed(1) }}
                    ج.م
                  </v-chip>
                </td>
                <td>
                  <v-switch
                    :model-value="item.isActive"
                    @update:model-value="toggleProductStatus(item)"
                    color="success"
                    hide-details
                    density="compact"
                  ></v-switch>
                </td>
                <td class="text-center">
                  <v-tooltip text="تعديل" location="top">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        icon
                        size="small"
                        color="primary"
                        variant="text"
                        @click="editItem(item)"
                      >
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                    </template>
                  </v-tooltip>
                  <v-tooltip text="حذف" location="top">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        icon
                        size="small"
                        color="error"
                        variant="text"
                        @click="openDeleteDialog(item)"
                      >
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </template>
                  </v-tooltip>
                </td>
              </tr>
            </template>
          </tbody>
        </v-table>
      </v-card>

      <!-- Add/Edit Dialog with Tabs -->
      <v-dialog v-model="dialog" max-width="700px" persistent>
        <v-card class="rounded-xl overflow-hidden">
          <v-toolbar color="primary" dark tabs>
            <v-toolbar-title class="font-weight-bold ml-4">
              <v-icon class="mr-2">{{
                isEditing ? "mdi-pencil" : "mdi-plus-circle"
              }}</v-icon>
              {{ isEditing ? "تعديل منتج" : "إضافة منتج جديد" }}
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="closeDialog" :disabled="saving">
              <v-icon>mdi-close</v-icon>
            </v-btn>

            <template v-slot:extension>
              <v-tabs v-model="activeTab" grow color="white" align-tabs="title">
                <v-tab value="basic">
                  <v-icon class="ml-2">mdi-information-outline</v-icon> البيانات
                </v-tab>
                <v-tab value="pricing">
                  <v-icon class="ml-2">mdi-cash-multiple</v-icon> الأسعار
                </v-tab>
                <v-tab value="media">
                  <v-icon class="ml-2">mdi-image-multiple</v-icon> الصور والعروض
                </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>

          <v-card-text class="pa-0">
            <v-form ref="form" v-model="validForm">
              <v-window v-model="activeTab" class="pa-6">
                <!-- Basic Info Tab -->
                <v-window-item value="basic">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model="editedItem.title"
                        label="اسم المنتج"
                        variant="outlined"
                        color="primary"
                        :rules="[(v) => !!v || 'الاسم مطلوب']"
                        prepend-inner-icon="mdi-format-title"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model="editedItem.sku"
                        label="رمز SKU"
                        variant="outlined"
                        color="primary"
                        prepend-inner-icon="mdi-barcode"
                        placeholder="اختياري"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        v-model="editedItem.description"
                        label="وصف تفصيلي"
                        variant="outlined"
                        color="primary"
                        rows="4"
                        prepend-inner-icon="mdi-text-subject"
                      ></v-textarea>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-select
                        v-model="editedItem.category_id"
                        :items="categories"
                        item-title="name"
                        item-value="id"
                        label="القسم"
                        variant="outlined"
                        prepend-inner-icon="mdi-tag-outline"
                        :rules="[(v) => !!v || 'القسم مطلوب']"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-select
                        v-model="editedItem.brand_id"
                        :items="brands"
                        item-title="name"
                        item-value="id"
                        label="الماركة"
                        variant="outlined"
                        prepend-inner-icon="mdi-watermark"
                        :rules="[(v) => !!v || 'الماركة مطلوبة']"
                      ></v-select>
                    </v-col>
                  </v-row>
                </v-window-item>

                <!-- Pricing Tab -->
                <v-window-item value="pricing">
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model.number="editedItem.price"
                        label="سعر البيع (ج.م)"
                        type="number"
                        variant="outlined"
                        prepend-inner-icon="mdi-currency-egy"
                        :rules="[(v) => !!v || 'السعر مطلوب']"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model.number="editedItem.purchase_price"
                        label="سعر التكلفة (ج.م)"
                        type="number"
                        variant="outlined"
                        prepend-inner-icon="mdi-calculator"
                        hint="يستخدم لحساب الأرباح بدقة"
                        persistent-hint
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model.number="editedItem.discountPercentage"
                        label="نسبة الخصم (%)"
                        type="number"
                        variant="outlined"
                        prepend-inner-icon="mdi-sale"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model.number="editedItem.stock"
                        label="الكمية المتوفرة"
                        type="number"
                        variant="outlined"
                        prepend-inner-icon="mdi-archive-outline"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-slider
                        v-model="editedItem.rating"
                        label="التقييم الأصلي"
                        min="1"
                        max="5"
                        step="0.1"
                        thumb-label
                        prepend-icon="mdi-star"
                        color="amber"
                      ></v-slider>
                    </v-col>
                  </v-row>
                </v-window-item>

                <!-- Media & Promo Tab -->
                <v-window-item value="media">
                  <v-row>
                    <v-col cols="12">
                      <v-file-input
                        v-model="imageFile"
                        label="تغيير صورة المنتج"
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

                    <v-col cols="12" md="6">
                      <v-card variant="outlined" class="pa-4 rounded-xl">
                        <div class="text-subtitle-1 font-weight-bold mb-2">
                          خيارات العرض
                        </div>
                        <v-switch
                          v-model="editedItem.isFlashDeal"
                          color="error"
                          label="عروض فلاش ديل (Flash Deal)"
                          hide-details
                          class="mb-1"
                        ></v-switch>
                        <v-switch
                          v-model="editedItem.isBestSeller"
                          color="primary"
                          label="الأكثر مبيعاً (Best Seller)"
                          hide-details
                        ></v-switch>
                      </v-card>
                    </v-col>

                    <v-col cols="12" md="6" class="d-flex justify-center">
                      <div v-if="previewImage" class="text-center">
                        <div class="text-caption mb-2">معاينة الصورة:</div>
                        <v-card elevation="4" class="rounded-xl pa-2">
                          <v-img
                            :src="previewImage"
                            width="160"
                            height="160"
                            cover
                            class="rounded-lg"
                          ></v-img>
                        </v-card>
                      </div>
                    </v-col>
                  </v-row>
                </v-window-item>
              </v-window>
            </v-form>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4 bg-grey-lighten-5">
            <v-spacer></v-spacer>
            <v-btn
              color="grey-darken-1"
              variant="text"
              class="px-6 font-weight-bold"
              @click="closeDialog"
              :disabled="saving"
              >إلغاء</v-btn
            >
            <v-btn
              color="primary"
              variant="flat"
              size="large"
              class="px-10 font-weight-bold shadow-btn"
              :disabled="!validForm || saving"
              :loading="saving"
              @click="saveItem"
            >
              <v-icon class="ml-2">mdi-check</v-icon>
              {{ isEditing ? "تحديث المنتج" : "تأكيد الإضافة" }}
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
            >هل أنت متأكد من حذف هذا المنتج؟</v-card-title
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
              :disabled="deleting"
              @click="confirmDelete"
              >نعم، احذف</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogBatchDelete" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3"
            >mdi-alert-circle-outline</v-icon
          >
          <v-card-title class="text-h6 font-weight-bold text-wrap"
            >هل أنت متأكد من حذف {{ selectedIds.length }} منتجات؟</v-card-title
          >
          <v-card-actions class="d-flex justify-center mt-4">
            <v-btn
              color="grey-darken-1"
              variant="tonal"
              class="px-6 font-weight-bold"
              @click="dialogBatchDelete = false"
              >إلغاء</v-btn
            >
            <v-btn
              color="error"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :loading="deleting"
              :disabled="deleting"
              @click="confirmBatchDelete"
              >نعم، احذف الكل</v-btn
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
import { ProductModule } from "@/store/Products";

const productStore = ProductModule();
const products = ref([]);
const loading = ref(false);
const activeTab = ref("basic");
const filterCategory = ref("الكل");
const filterBrand = ref("الكل");
const filterStock = ref("الكل");

const selectedIds = ref([]);
const selectAll = computed({
  get: () =>
    products.value.length > 0 &&
    selectedIds.value.length === products.value.length,
  set: (val) => {
    selectedIds.value = val ? products.value.map((p) => p.id) : [];
  },
});

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchAllData(true);
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
    fetchAllData(true);
    startPolling();
  }
};

const fetchAllData = async (quiet = false) => {
  await Promise.all([fetchProducts(quiet), fetchCategories(), fetchBrands()]);
};
// ----------------------------------

const fetchProducts = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await api.get("/admin/products");
    products.value = res.data.map((p) => ({
      ...p,
      category: p.category ? p.category.name : "",
      brand: p.brand ? p.brand.name : "",
      isActive: !!p.is_active,
      discountPercentage: Number(p.discount_percentage),
    }));
  } catch (e) {
    console.error(e);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const toggleProductStatus = async (item) => {
  try {
    await api.put(`/admin/products/${item.id}/status`);
    item.isActive = !item.isActive;
    showMessage("تم تحديث الحالة بنجاح");
    fetchProducts(true);
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء تحديث الحالة", "error");
  }
};

const categories = ref([]);
const fetchCategories = async () => {
  try {
    const res = await api.get("/admin/categories");
    categories.value = res.data;
  } catch (e) {
    console.error(e);
  }
};

const brands = ref([]);
const fetchBrands = async () => {
  try {
    const res = await api.get("/admin/brands");
    brands.value = res.data;
  } catch (e) {
    console.error(e);
  }
};

onMounted(() => {
  fetchAllData();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});

const searchQuery = ref("");
const dialog = ref(false);
const dialogDelete = ref(false);
const dialogBatchDelete = ref(false);
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

const imageFile = ref(null);
const previewImage = ref("");

const defaultItem = {
  id: null,
  title: "",
  description: "",
  category_id: null,
  brand_id: null,
  price: 0,
  purchase_price: 0,
  discountPercentage: 0,
  stock: 0,
  rating: 4.5,
  thumbnail: "",
  isFlashDeal: false,
  isBestSeller: false,
  is_active: true,
  sku: "",
};

const editedItem = ref({ ...defaultItem });

const handleImageUpload = (fileArray) => {
  const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
  if (file) {
    previewImage.value = URL.createObjectURL(file);
    editedItem.value.thumbnail = previewImage.value; // Warning: Real backend needs FormData or Base64
  } else {
    if (!isEditing.value) {
      previewImage.value = "";
      editedItem.value.thumbnail = "";
    }
  }
};

const shortTitle = (text) => {
  if (!text) return "";
  return text.length > 25 ? text.substring(0, 25) + "..." : text;
};

const filteredProducts = computed(() => {
  let list = products.value;

  // Search filter
  if (searchQuery.value) {
    const lowerCaseQuery = searchQuery.value.toLowerCase();
    list = list.filter((product) =>
      product.title?.toLowerCase().includes(lowerCaseQuery)
    );
  }

  // Category filter
  if (filterCategory.value && filterCategory.value !== "الكل") {
    list = list.filter((p) => p.category === filterCategory.value);
  }

  // Brand filter
  if (filterBrand.value && filterBrand.value !== "الكل") {
    list = list.filter((p) => p.brand === filterBrand.value);
  }

  // Stock filter
  if (filterStock.value && filterStock.value !== "الكل") {
    if (filterStock.value === "نفذ من المخزن")
      list = list.filter((p) => p.stock === 0);
    else if (filterStock.value === "مخزن منخفض")
      list = list.filter((p) => p.stock > 0 && p.stock <= 10);
  }

  return list;
});

const openAddDialog = () => {
  isEditing.value = false;
  activeTab.value = "basic";
  editedItem.value = { ...defaultItem };
  imageFile.value = null;
  previewImage.value = "";
  dialog.value = true;
};

const editItem = (item) => {
  isEditing.value = true;
  activeTab.value = "basic";
  editedItem.value = {
    ...item,
    isFlashDeal: !!item.is_flash_deal,
    isBestSeller: !!item.is_best_seller,
  };
  imageFile.value = null;
  previewImage.value = item.thumbnail;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  setTimeout(() => {
    editedItem.value = { ...defaultItem };
    imageFile.value = null;
    previewImage.value = "";
  }, 300);
};

const saveItem = async () => {
  if (!validForm.value) return;
  saving.value = true;
  try {
    const payload = new FormData();
    payload.append("title", editedItem.value.title);
    payload.append("sku", editedItem.value.sku || "");
    payload.append("description", editedItem.value.description || "");
    payload.append("category_id", editedItem.value.category_id);
    payload.append("brand_id", editedItem.value.brand_id);
    payload.append("price", editedItem.value.price);
    payload.append("purchase_price", editedItem.value.purchase_price || 0);
    payload.append("rating", editedItem.value.rating || 4.5);
    payload.append(
      "discount_percentage",
      editedItem.value.discountPercentage || 0
    );
    payload.append("stock", editedItem.value.stock || 0);
    payload.append("is_flash_deal", editedItem.value.isFlashDeal ? 1 : 0);
    payload.append("is_best_seller", editedItem.value.isBestSeller ? 1 : 0);

    const file = Array.isArray(imageFile.value)
      ? imageFile.value[0]
      : imageFile.value;
    if (file) {
      payload.append("thumbnail", file);
    }

    if (isEditing.value) {
      payload.append("_method", "PUT");
      await api.post(`/admin/products/${editedItem.value.id}`, payload, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      showMessage("تم تحديث المنتج بنجاح!");
    } else {
      await api.post("/admin/products", payload, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      showMessage("تم إنشاء المنتج بنجاح!");
    }

    await fetchProducts();
    await productStore.getProducts();

    closeDialog();
  } catch (e) {
    console.error(e);
    showMessage(
      e.response?.data?.message || "حدث خطأ أثناء حفظ المنتج",
      "error"
    );
  } finally {
    saving.value = false;
  }
};

const openBatchDeleteDialog = () => {
  if (selectedIds.value.length === 0) return;
  dialogBatchDelete.value = true;
};

const confirmBatchDelete = async () => {
  deleting.value = true;
  try {
    await api.post("/admin/products/batch-delete", { ids: selectedIds.value });
    await fetchProducts();
    await productStore.getProducts();
    showMessage("تم حذف المنتجات بنجاح");
    selectedIds.value = [];
    dialogBatchDelete.value = false;
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء الحذف الجماعي", "error");
  } finally {
    deleting.value = false;
  }
};

const openDeleteDialog = (item) => {
  itemToDelete.value = item;
  dialogDelete.value = true;
};

const confirmDelete = async () => {
  deleting.value = true;
  try {
    await api.delete(`/admin/products/${itemToDelete.value.id}`);

    await fetchProducts();
    // Update Pinia store immediately
    await productStore.getProducts();

    showMessage("تم حذف المنتج بنجاح!");
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (e) {
    console.error(e);
    showMessage("حدث خطأ أثناء محاولة الحذف", "error");
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

.products-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 15px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
  background-color: #f8f9fa !important;
}

.products-table td {
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
.shadow-btn {
  box-shadow: 0 4px 12px rgba(var(--v-theme-primary), 0.2) !important;
}
.shadow-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(var(--v-theme-primary), 0.3) !important;
}

.w-fit {
  width: fit-content;
}
</style>
