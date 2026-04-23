<template>
  <v-locale-provider :rtl="locale === 'ar'">
    <v-container fluid class="pa-6">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text class="pa-4">
          <div
            class="d-flex align-center justify-space-between flex-wrap gap-4 mb-4"
          >
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              {{ $t("brands.main_title") }}
            </h2>

            <v-btn
              color="primary"
              prepend-icon="mdi-plus"
              class="font-weight-bold px-6 rounded-lg shadow-btn"
              height="44"
              @click="openAddDialog"
            >
              إضافة
              {{
                activeTab === "categories"
                  ? $t("brands.add_cat")
                  : $t("brands.add_brand")
              }}
            </v-btn>
          </div>

          <v-row class="mb-2">
            <v-col cols="12" md="6">
              <v-text-field
                v-model="searchQuery"
                prepend-inner-icon="mdi-magnify"
                :label="
                  activeTab === 'categories'
                    ? $t('catalog.search_categories')
                    : $t('catalog.search_brands')
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
              {{ $t("catalog.categories_label") }}
            </v-tab>
            <v-tab value="brands" class="font-weight-bold text-subtitle-1">
              <v-icon right class="ml-2">mdi-tag-multiple-outline</v-icon>
              {{ $t("catalog.brands_label") }}
            </v-tab>
          </v-tabs>
        </v-card-text>
      </v-card>

      <v-window v-model="activeTab">
        <v-window-item value="categories">
          <!-- Categories Batch Actions Bar -->
          <v-expand-transition>
            <v-card
              v-if="selectedCategoryIds.length > 0"
              class="mb-6 rounded-xl bg-error text-white pa-4"
              elevation="4"
            >
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-3">
                  <v-icon>mdi-checkbox-multiple-marked</v-icon>
                  <div class="text-h6 font-weight-bold">
                    {{
                      $t("catalog.selected_categories", {
                        count: selectedCategoryIds.length,
                      })
                    }}
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <v-btn
                    color="white"
                    variant="tonal"
                    prepend-icon="mdi-delete-multiple"
                    @click="openBatchDeleteDialog('categories')"
                  >
                    {{ $t("catalog.delete_selected") }}
                  </v-btn>
                  <v-btn
                    color="white"
                    variant="outlined"
                    @click="selectedCategoryIds = []"
                    >{{ $t("catalog.cancel") }}</v-btn
                  >
                </div>
              </div>
            </v-card>
          </v-expand-transition>

          <v-card class="rounded-xl overflow-hidden" elevation="1">
            <v-table class="custom-table pa-2">
              <thead class="bg-grey-lighten-4">
                <tr>
                  <th class="text-right font-weight-bold" style="width: 50px">
                    <v-checkbox-btn
                      v-model="selectAllCategories"
                      color="primary"
                      hide-details
                      :disabled="loading"
                    ></v-checkbox-btn>
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.cat_image") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.cat_name") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.products_count") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.status") }}
                  </th>
                  <th class="text-center font-weight-bold">
                    {{ $t("catalog.table.actions") }}
                  </th>
                </tr>
              </thead>
              <tbody v-if="loading">
                <tr v-for="i in 5" :key="`skel-cat-${i}`">
                  <td class="px-2"><v-skeleton-loader type="list-item" /></td>
                  <td class="px-2 py-3"><v-skeleton-loader type="avatar" /></td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 150px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 100px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 60px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="button"
                      class="mx-auto"
                      style="max-width: 80px; height: 32px"
                    />
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr
                  v-for="cat in filteredCategories"
                  :key="cat.id"
                  class="hover-row"
                >
                  <td>
                    <v-checkbox-btn
                      v-model="selectedCategoryIds"
                      :value="cat.id"
                      color="primary"
                      hide-details
                    ></v-checkbox-btn>
                  </td>
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
                    {{
                      $t("catalog.table.products_unit", {
                        count: cat.productsCount,
                      })
                    }}
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
          <!-- Brands Batch Actions Bar -->
          <v-expand-transition>
            <v-card
              v-if="selectedBrandIds.length > 0"
              class="mb-6 rounded-xl bg-error text-white pa-4"
              elevation="4"
            >
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center gap-3">
                  <v-icon>mdi-checkbox-multiple-marked</v-icon>
                  <div class="text-h6 font-weight-bold">
                    {{
                      $t("catalog.selected_brands", {
                        count: selectedBrandIds.length,
                      })
                    }}
                  </div>
                </div>
                <div class="d-flex gap-2">
                  <v-btn
                    color="white"
                    variant="tonal"
                    prepend-icon="mdi-delete-multiple"
                    @click="openBatchDeleteDialog('brands')"
                  >
                    {{ $t("catalog.delete_selected") }}
                  </v-btn>
                  <v-btn
                    color="white"
                    variant="outlined"
                    @click="selectedBrandIds = []"
                    >{{ $t("dashboard.cancel") }}</v-btn
                  >
                </div>
              </div>
            </v-card>
          </v-expand-transition>

          <v-card class="rounded-xl overflow-hidden" elevation="1">
            <v-table class="custom-table pa-2">
              <thead class="bg-grey-lighten-4">
                <tr>
                  <th class="text-right font-weight-bold" style="width: 50px">
                    <v-checkbox-btn
                      v-model="selectAllBrands"
                      color="primary"
                      hide-details
                      :disabled="loading"
                    ></v-checkbox-btn>
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.brand_logo") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.brand_name") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.products_count") }}
                  </th>
                  <th class="text-right font-weight-bold">
                    {{ $t("catalog.table.status") }}
                  </th>
                  <th class="text-center font-weight-bold">
                    {{ $t("catalog.table.actions") }}
                  </th>
                </tr>
              </thead>
              <tbody v-if="loading">
                <tr v-for="i in 5" :key="`skel-brand-${i}`">
                  <td class="px-2"><v-skeleton-loader type="list-item" /></td>
                  <td class="px-2 py-3"><v-skeleton-loader type="avatar" /></td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 150px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 100px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 60px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="button"
                      class="mx-auto"
                      style="max-width: 80px; height: 32px"
                    />
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr
                  v-for="brand in filteredBrands"
                  :key="brand.id"
                  class="hover-row"
                >
                  <td>
                    <v-checkbox-btn
                      v-model="selectedBrandIds"
                      :value="brand.id"
                      color="primary"
                      hide-details
                    ></v-checkbox-btn>
                  </td>
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
                    {{
                      $t("catalog.table.products_unit", {
                        count: brand.productsCount,
                      })
                    }}
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
            {{ isEditing ? $t("catalog.edit") : $t("catalog.add") }}
            {{
              activeTab === "categories"
                ? $t("catalog.category")
                : $t("catalog.brand")
            }}
          </v-card-title>
          <v-divider class="my-3"></v-divider>

          <v-card-text>
            <v-form ref="form" v-model="validForm">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    :label="
                      activeTab === 'categories'
                        ? $t('catalog.fields.cat_name_label')
                        : $t('catalog.fields.brand_name_label')
                    "
                    variant="outlined"
                    color="primary"
                    :rules="[(v) => !!v || $t('catalog.fields.name_required')]"
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-file-input
                    v-model="imageFile"
                    :label="
                      activeTab === 'categories'
                        ? $t('catalog.fields.cat_image_label')
                        : $t('catalog.fields.brand_logo_label')
                    "
                    variant="outlined"
                    color="primary"
                    accept="image/*"
                    prepend-icon=""
                    prepend-inner-icon="mdi-camera"
                    @update:modelValue="handleImageUpload"
                    :rules="[
                      (v) =>
                        isEditing ||
                        !!previewImage ||
                        $t('catalog.fields.image_required'),
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

                <!-- SEO Section -->
                <v-col cols="12">
                  <v-expansion-panels
                    flat
                    border
                    class="rounded-lg overflow-hidden"
                  >
                    <v-expansion-panel elevation="0" class="bg-grey-lighten-5">
                      <v-expansion-panel-title class="font-weight-bold py-2">
                        <v-icon start color="primary" size="20"
                          >mdi-google</v-icon
                        >
                        {{ $t("catalog.seo_settings") }}
                      </v-expansion-panel-title>
                      <v-expansion-panel-text class="pa-2">
                        <SEOPreview
                          :title="editedItem.meta_title"
                          :description="editedItem.meta_description"
                          :breadcrumb="
                            activeTab === 'categories'
                              ? $t('catalog.category')
                              : $t('catalog.brand')
                          "
                        />

                        <v-text-field
                          v-model="editedItem.meta_title"
                          :label="$t('catalog.fields.meta_title')"
                          variant="outlined"
                          density="compact"
                          class="mb-3"
                          counter="60"
                        ></v-text-field>

                        <v-textarea
                          v-model="editedItem.meta_description"
                          :label="$t('catalog.fields.meta_desc')"
                          variant="outlined"
                          density="compact"
                          rows="3"
                          counter="160"
                        ></v-textarea>

                        <v-text-field
                          v-model="editedItem.meta_keywords"
                          :label="$t('catalog.fields.keywords')"
                          variant="outlined"
                          density="compact"
                          :placeholder="$t('catalog.fields.separator_hint')"
                        ></v-text-field>
                      </v-expansion-panel-text>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn @click="closeDialog">{{ $t("catalog.cancel") }}</v-btn>
            <v-btn
              color="primary"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :disabled="!validForm || saving"
              :loading="saving"
              @click="saveItem"
            >
              {{ $t("catalog.save") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialogDelete" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3"
            >mdi-alert-circle-outline</v-icon
          >
          {{
            activeTab === "categories"
              ? $t("catalog.delete_confirm_cat")
              : $t("catalog.delete_confirm_brand")
          }}
          <v-card-actions class="d-flex justify-center mt-4">
            <v-btn @click="dialogDelete = false">{{
              $t("catalog.cancel")
            }}</v-btn>
            <v-btn
              color="error"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :loading="deleting"
              :disabled="deleting"
              @click="confirmDelete"
              >{{ $t("catalog.confirm_delete_btn") }}</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Batch Delete Dialog -->
      <v-dialog v-model="dialogBatchDelete" max-width="400px">
        <v-card class="rounded-xl pa-4 text-center">
          <v-icon color="error" size="60" class="mb-3">mdi-delete-alert</v-icon>
          {{
            batchType === "categories"
              ? $t("catalog.batch_delete_confirm_cat", {
                  count: selectedCategoryIds.length,
                })
              : $t("catalog.batch_delete_confirm_brand", {
                  count: selectedBrandIds.length,
                })
          }}
          <v-card-text>{{ $t("catalog.delete_warning") }}</v-card-text>
          <v-card-actions class="d-flex justify-center mt-4">
            <v-btn @click="dialogBatchDelete = false">{{
              $t("catalog.cancel")
            }}</v-btn>
            <v-btn
              color="error"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              :loading="deleting"
              :disabled="deleting"
              @click="confirmBatchDelete"
              >{{ $t("catalog.confirm_delete_btn") }}</v-btn
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
          <v-btn variant="text" @click="snackbar.show = false">{{
            $t("catalog.cancel")
          }}</v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </v-locale-provider>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import api from "../../axios";
import { ProductModule } from "@/store/Products";
import SEOPreview from "@/components/Dashboard/SEOPreview.vue";

const { t, locale } = useI18n();
const productStore = ProductModule();

// التبويب النشط (الافتراضي: الأقسام)
const activeTab = ref("categories");
const searchQuery = ref("");

const categories = ref([]);
const brands = ref([]);
const loading = ref(false);

const selectedCategoryIds = ref([]);
const selectedBrandIds = ref([]);
const dialogBatchDelete = ref(false);
const batchType = ref(""); // 'categories' or 'brands'

const selectAllCategories = computed({
  get: () =>
    filteredCategories.value.length > 0 &&
    selectedCategoryIds.value.length === filteredCategories.value.length,
  set: (val) => {
    if (val) {
      selectedCategoryIds.value = filteredCategories.value.map((c) => c.id);
    } else {
      selectedCategoryIds.value = [];
    }
  },
});

const selectAllBrands = computed({
  get: () =>
    filteredBrands.value.length > 0 &&
    selectedBrandIds.value.length === filteredBrands.value.length,
  set: (val) => {
    if (val) {
      selectedBrandIds.value = filteredBrands.value.map((b) => b.id);
    } else {
      selectedBrandIds.value = [];
    }
  },
});

const openBatchDeleteDialog = (type) => {
  batchType.value = type;
  dialogBatchDelete.value = true;
};

const confirmBatchDelete = async () => {
  const ids =
    batchType.value === "categories"
      ? selectedCategoryIds.value
      : selectedBrandIds.value;
  if (ids.length === 0) return;

  deleting.value = true;
  try {
    await api.post(`/admin/${batchType.value}/batch-delete`, { ids });
    showMessage(t("catalog.messages.delete_success"));

    if (batchType.value === "categories") {
      categories.value = categories.value.filter((c) => !ids.includes(c.id));
      selectedCategoryIds.value = [];
      await productStore.getCategories();
    } else {
      brands.value = brands.value.filter((b) => !ids.includes(b.id));
      selectedBrandIds.value = [];
      await productStore.getBrands();
    }

    dialogBatchDelete.value = false;
  } catch (e) {
    console.error(e);
    showMessage(t("catalog.messages.delete_error"), "error");
  } finally {
    deleting.value = false;
  }
};

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
    showMessage(t("catalog.messages.status_success"));
    fetchData(true);
  } catch (e) {
    console.error(e);
    showMessage(t("catalog.messages.status_error"), "error");
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
  meta_title: "",
  meta_description: "",
  meta_keywords: "",
});

// معالجة رفع الصورة (تعمل مع {{ $t("brands.main_title") }})
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
    meta_title: "",
    meta_description: "",
    meta_keywords: "",
  };
  imageFile.value = null;
  previewImage.value = "";
  dialog.value = true;
};

// فتح نافذة التعديل
const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = {
    ...item,
    is_active: item.isActive ?? true,
    meta_title: item.meta_title || "",
    meta_description: item.meta_description || "",
    meta_keywords: item.meta_keywords || "",
  };
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
      meta_title: "",
      meta_description: "",
      meta_keywords: "",
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

    payload.append("meta_title", editedItem.value.meta_title || "");
    payload.append("meta_description", editedItem.value.meta_description || "");
    payload.append("meta_keywords", editedItem.value.meta_keywords || "");

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
      showMessage(t("catalog.messages.save_success"));
    } else {
      await api.post(endpoint, payload, {
        headers: { "Content-Type": "multipart/form-data" },
      });
      showMessage(t("catalog.messages.save_success"));
    }

    await fetchData();
    // Update Pinia store depending on active tab
    if (activeTab.value === "categories") await productStore.getCategories();
    else await productStore.getBrands();

    closeDialog();
  } catch (e) {
    console.error(e);
    showMessage(
      e.response?.data?.message || t("catalog.messages.save_error"),
      "error"
    );
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

    showMessage(t("catalog.messages.delete_success"));
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (e) {
    showMessage(
      e.response?.data?.message || t("catalog.messages.delete_error"),
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
