<template>
  <!-- eslint-disable vue/valid-v-slot -->
  <v-container fluid class="pa-4 bg-grey-lighten-4 min-vh-100">
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h4 font-weight-black text-primary-darken-3 mb-1">
          إدارة المقالات
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          تحكم في الأخبار والمقالات التي تظهر في صفحة المدونة الخاصة بمتجرك
        </p>
      </div>
      <div class="d-flex gap-3">
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          size="large"
          class="text-none font-weight-bold rounded-lg px-6"
          elevation="2"
          @click="openDialog()"
        >
          كتابة مقال جديد
        </v-btn>
      </div>
    </div>

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
              تم تحديد {{ selectedIds.length }} مقالات
            </div>
          </div>
          <div class="d-flex gap-2">
            <v-btn
              color="white"
              variant="tonal"
              prepend-icon="mdi-delete-multiple"
              @click="batchDeleteDialog = true"
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

    <!-- Data Table -->
    <v-card class="rounded-xl border-0 custom-shadow" elevation="0">
      <div class="table-responsive">
        <v-data-table
          v-model="selectedIds"
          :headers="headers"
          :items="posts"
          :loading="loading"
          show-select
          hover
          class="bg-transparent"
          density="comfortable"
        >
          <!-- Table Header Customization -->
          <template v-slot:headers="{ columns }">
            <tr>
              <th
                v-for="col in columns"
                :key="col.key"
                class="text-uppercase text-caption font-weight-bold text-grey-darken-1 bg-grey-lighten-4 px-4 py-3"
              >
                {{ col.title }}
              </th>
            </tr>
          </template>

          <!-- Cover Image Column -->
          <template v-slot:item.cover_image="{ item }">
            <div class="py-2">
              <v-avatar
                rounded="lg"
                size="64"
                class="border"
                color="grey-lighten-3"
              >
                <v-img
                  v-if="item.cover_image"
                  :src="item.cover_image"
                  cover
                ></v-img>
                <v-icon v-else size="32" color="grey-lighten-1"
                  >mdi-image</v-icon
                >
              </v-avatar>
            </div>
          </template>

          <!-- Title Column -->
          <template v-slot:item.title="{ item }">
            <div class="font-weight-medium text-body-1">{{ item.title }}</div>
            <div class="text-caption text-grey">{{ item.slug }}</div>
          </template>

          <!-- Status Column -->
          <template v-slot:item.is_published="{ item }">
            <v-chip
              :color="item.is_published ? 'success' : 'grey-darken-1'"
              :variant="item.is_published ? 'flat' : 'tonal'"
              size="small"
              class="font-weight-medium px-3"
            >
              <v-icon start size="small">
                {{ item.is_published ? "mdi-check-circle" : "mdi-eye-off" }}
              </v-icon>
              {{ item.is_published ? "منشور" : "مخفي" }}
            </v-chip>
          </template>

          <!-- Views Column -->
          <template v-slot:item.views="{ item }">
            <v-chip size="small" variant="outlined" color="primary">
              {{ item.views }} مشاهدة
            </v-chip>
          </template>

          <!-- Date Column -->
          <template v-slot:item.created_at="{ item }">
            <div class="text-body-2 text-grey-darken-2">
              {{ formatDate(item.created_at) }}
            </div>
          </template>

          <!-- Category Column -->
          <template v-slot:item.category="{ item }">
            <v-chip size="x-small" color="secondary" variant="flat">
              {{ item.category || "عام" }}
            </v-chip>
          </template>

          <!-- Actions Column -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex gap-2">
              <v-btn
                icon
                variant="text"
                size="small"
                color="blue-darken-2"
                @click="openDialog(item)"
                class="hover-bg"
              >
                <v-icon>mdi-pencil</v-icon>
                <v-tooltip activator="parent" location="top">تعديل</v-tooltip>
              </v-btn>

              <v-btn
                icon
                variant="text"
                size="small"
                color="error"
                @click="confirmDelete(item)"
                class="hover-bg"
              >
                <v-icon>mdi-delete</v-icon>
                <v-tooltip activator="parent" location="top">حذف</v-tooltip>
              </v-btn>
            </div>
          </template>

          <!-- Loading State -->
          <template v-slot:loading>
            <tr v-for="i in 5" :key="`skel-post-${i}`">
              <td class="px-2 py-3">
                <v-skeleton-loader
                  type="image"
                  height="64"
                  width="64"
                  class="rounded-lg"
                />
              </td>
              <td class="px-2">
                <v-skeleton-loader type="text" style="max-width: 200px" />
                <v-skeleton-loader type="text" style="max-width: 100px" />
              </td>
              <td class="px-2">
                <v-skeleton-loader type="chip" class="mx-auto" />
              </td>
              <td class="px-2">
                <v-skeleton-loader type="chip" class="mx-auto" />
              </td>
              <td class="px-2">
                <v-skeleton-loader type="chip" class="mx-auto" />
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
                  type="actions"
                  class="mx-auto"
                  style="min-width: 100px"
                />
              </td>
            </tr>
          </template>

          <!-- Empty State -->
          <template v-slot:no-data>
            <div class="d-flex flex-column align-center justify-center py-12">
              <v-icon size="64" color="grey-lighten-2" class="mb-4"
                >mdi-file-document-box-multiple-outline</v-icon
              >
              <h3 class="text-h6 font-weight-medium text-grey-darken-1 mb-2">
                لا توجد مقالات مضافة
              </h3>
              <p class="text-body-2 text-grey">
                اضغط على "كتابة مقال جديد" للبدء في إضافة محتوى المتجر
              </p>
            </div>
          </template>
        </v-data-table>
      </div>
    </v-card>

    <!-- Batch Delete Confirmation -->
    <v-dialog v-model="batchDeleteDialog" max-width="400">
      <v-card class="rounded-xl text-center pa-6">
        <v-icon
          icon="mdi-delete-alert"
          color="error"
          size="64"
          class="mb-4"
        ></v-icon>
        <h3 class="text-h6 font-weight-bold mb-2">تأكيد الحذف الجماعي</h3>
        <p class="text-body-2 text-grey-darken-1 mb-6">
          هل أنت متأكد أنك تريد حذف {{ selectedIds.length }} مقالات؟ لا يمكن
          التراجع عن هذا الإجراء.
        </p>
        <div class="d-flex gap-3 justify-center">
          <v-btn
            variant="tonal"
            color="grey-darken-1"
            class="flex-grow-1 font-weight-bold"
            @click="batchDeleteDialog = false"
          >
            إلغاء
          </v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="flex-grow-1 font-weight-bold"
            :loading="deleting"
            @click="confirmBatchDelete"
          >
            نعم، احذف الكل
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Post Dialog (Create/Edit) -->
    <v-dialog v-model="dialog" max-width="900" persistent>
      <v-card class="rounded-xl">
        <v-toolbar color="surface" elevation="0" class="border-bottom">
          <v-toolbar-title class="text-h6 font-weight-bold">
            {{ editedItem.id ? "تعديل المقال" : "كتابة مقال جديد" }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeDialog">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent>
            <v-row>
              <v-col cols="12" md="8">
                <!-- Title -->
                <v-text-field
                  v-model="editedItem.title"
                  label="عنوان المقال"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  color="primary"
                  :rules="[(v) => !!v || 'العنوان مطلوب']"
                ></v-text-field>

                <v-row class="mb-2">
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="editedItem.category"
                      label="التصنيف"
                      variant="outlined"
                      density="comfortable"
                      color="primary"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="editedItem.author_name"
                      label="اسم الكاتب"
                      variant="outlined"
                      density="comfortable"
                      color="primary"
                    ></v-text-field>
                  </v-col>
                </v-row>

                <!-- Summary -->
                <v-textarea
                  v-model="editedItem.summary"
                  label="مقتطف قصير (يظهر في القائمة)"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  color="primary"
                  rows="2"
                  auto-grow
                ></v-textarea>

                <!-- Main Content -->
                <p class="text-subtitle-2 font-weight-bold mb-2">
                  محتوى المقال
                </p>
                <v-textarea
                  v-model="editedItem.content"
                  label="المحتوى الكامل"
                  variant="outlined"
                  density="comfortable"
                  color="primary"
                  rows="8"
                  auto-grow
                  :rules="[(v) => !!v || 'المحتوى مطلوب']"
                ></v-textarea>

                <!-- SEO Section -->
                <v-expansion-panels
                  flat
                  class="mt-4 border rounded-lg overflow-hidden"
                >
                  <v-expansion-panel elevation="0" class="bg-grey-lighten-5">
                    <v-expansion-panel-title class="font-weight-bold py-2">
                      <v-icon start color="primary" size="20"
                        >mdi-google</v-icon
                      >
                      إعدادات محركات البحث (SEO)
                    </v-expansion-panel-title>
                    <v-expansion-panel-text class="pa-2">
                      <SEOPreview
                        :title="editedItem.meta_title"
                        :description="editedItem.meta_description"
                        breadcrumb="مدونة"
                      />

                      <v-text-field
                        v-model="editedItem.meta_title"
                        label="عنوان السيو (Meta Title)"
                        variant="outlined"
                        density="compact"
                        class="mb-3"
                        hint="يظهر في صفحة نتائج البحث"
                        persistent-hint
                        counter="60"
                      ></v-text-field>
                      <v-textarea
                        v-model="editedItem.meta_description"
                        label="وصف السيو (Meta Description)"
                        variant="outlined"
                        density="compact"
                        rows="3"
                        hint="وصف مختصر يظهر تحت العنوان في جوجل"
                        persistent-hint
                        counter="160"
                      ></v-textarea>
                      <v-text-field
                        v-model="editedItem.meta_keywords"
                        label="الكلمات المفتاحية (Keywords)"
                        variant="outlined"
                        density="compact"
                        class="mt-3"
                        placeholder="افصل بفاصلة (,)"
                      ></v-text-field>
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </v-col>

              <v-col cols="12" md="4">
                <!-- Cover Image -->
                <v-card variant="outlined" class="mb-6 rounded-lg">
                  <v-card-title class="text-subtitle-2 font-weight-bold py-2">
                    التحكم في النشر
                  </v-card-title>
                  <v-divider></v-divider>
                  <v-card-text>
                    <v-switch
                      v-model="editedItem.is_published"
                      :label="
                        editedItem.is_published
                          ? 'منشور (يظهر للزوار)'
                          : 'مسودة (مخفي)'
                      "
                      color="success"
                      hide-details
                      class="mb-2"
                    ></v-switch>
                  </v-card-text>
                </v-card>

                <v-card variant="outlined" class="rounded-lg">
                  <v-card-title
                    class="text-subtitle-2 font-weight-bold py-2 pb-0"
                  >
                    صورة الغلاف
                  </v-card-title>
                  <v-card-text class="pt-2">
                    <div
                      class="image-upload-area rounded-lg d-flex flex-column align-center justify-center position-relative cursor-pointer mb-2"
                      :class="{
                        'has-image': imagePreview || editedItem.cover_image,
                      }"
                      @click="$refs.fileInput.click()"
                    >
                      <template v-if="imagePreview || editedItem.cover_image">
                        <v-img
                          :src="imagePreview || editedItem.cover_image"
                          cover
                          class="rounded-lg w-100 h-100"
                        ></v-img>
                        <div class="overlay d-flex align-center justify-center">
                          <v-btn
                            color="white"
                            variant="text"
                            icon="mdi-camera-retake"
                          ></v-btn>
                        </div>
                      </template>
                      <template v-else>
                        <v-icon size="48" color="grey-lighten-1" class="mb-2"
                          >mdi-cloud-upload</v-icon
                        >
                        <span
                          class="text-body-2 text-grey-darken-1 font-weight-medium"
                          >اختر صورة الغلاف</span
                        >
                        <span class="text-caption text-grey"
                          >JPG, PNG (تصل إلى 3MB)</span
                        >
                      </template>
                    </div>

                    <v-file-input
                      ref="fileInput"
                      v-model="editedItem.imageFile"
                      accept="image/*"
                      class="d-none"
                      @update:modelValue="onImageSelected"
                    ></v-file-input>

                    <v-btn
                      v-if="imagePreview || editedItem.cover_image"
                      color="error"
                      variant="text"
                      size="small"
                      block
                      @click="clearImage"
                    >
                      إزالة الصورة
                    </v-btn>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>

        <v-divider></v-divider>
        <v-card-actions class="pa-4 bg-grey-lighten-4">
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            class="font-weight-medium px-4"
            @click="closeDialog"
          >
            إلغاء
          </v-btn>
          <v-btn
            color="primary"
            variant="flat"
            class="font-weight-bold px-6"
            :loading="saving"
            @click="savePost"
          >
            حفظ المقال
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card class="rounded-xl text-center pa-6">
        <v-icon
          icon="mdi-alert-circle-outline"
          color="error"
          size="64"
          class="mb-4"
        ></v-icon>
        <h3 class="text-h6 font-weight-bold mb-2">تأكيد الحذف</h3>
        <p class="text-body-2 text-grey-darken-1 mb-6">
          هل أنت متأكد أنك تريد حذف هذا المقال بصورة نهائية؟ لا يمكن التراجع عن
          هذا الإجراء.
        </p>
        <div class="d-flex gap-3 justify-center">
          <v-btn
            variant="tonal"
            color="grey-darken-1"
            class="flex-grow-1 font-weight-bold"
            @click="deleteDialog = false"
          >
            إلغاء
          </v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="flex-grow-1 font-weight-bold"
            :loading="deleting"
            @click="confirmDeleteAction"
          >
            نعم، احذف
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000">
      {{ snackbar.text }}
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import api from "@/axios";
import SEOPreview from "@/components/Dashboard/SEOPreview.vue";

const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const dialog = ref(false);
const deleteDialog = ref(false);
const form = ref(null);
const posts = ref([]);

const selectedIds = ref([]);
const batchDeleteDialog = ref(false);

const confirmBatchDelete = async () => {
  if (selectedIds.value.length === 0) return;
  deleting.value = true;
  try {
    await api.post("/admin/posts/batch-delete", { ids: selectedIds.value });
    showSnackbar("تم حذف المقالات بنجاح");
    posts.value = posts.value.filter((p) => !selectedIds.value.includes(p.id));
    selectedIds.value = [];
    batchDeleteDialog.value = false;
  } catch (e) {
    console.error(e);
    showSnackbar("حدث خطأ أثناء الحذف الجماعي", "error");
  } finally {
    deleting.value = false;
  }
};

const snackbar = ref({
  show: false,
  text: "",
  color: "success",
});

const headers = [
  { title: "صورة", key: "cover_image", sortable: false, width: "100px" },
  { title: "العنوان", key: "title" },
  { title: "التصنيف", key: "category", width: "120px" },
  { title: "الحالة", key: "is_published", align: "center", width: "130px" },
  { title: "المشاهدات", key: "views", align: "center", width: "100px" },
  { title: "التاريخ", key: "created_at" },
  { title: "إجراءات", key: "actions", sortable: false, align: "center" },
];

const defaultItem = {
  id: null,
  title: "",
  category: "",
  author_name: "",
  summary: "",
  content: "",
  meta_title: "",
  meta_description: "",
  meta_keywords: "",
  is_published: true,
  cover_image: null,
  imageFile: null,
};

const editedItem = ref({ ...defaultItem });
const imagePreview = ref(null);

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchPosts(true);
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
    fetchPosts(true);
    startPolling();
  }
};
// ----------------------------------

const fetchPosts = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await api.get("/admin/posts");
    posts.value = res.data;
  } catch (err) {
    if (!quiet) showSnackbar("حدث خطأ أثناء جلب المقالات", "error");
  } finally {
    if (!quiet) loading.value = false;
  }
};

const openDialog = (item = null) => {
  if (item) {
    editedItem.value = { ...item, imageFile: null };
  } else {
    editedItem.value = { ...defaultItem };
  }
  imagePreview.value = null;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  setTimeout(() => {
    editedItem.value = { ...defaultItem };
    if (form.value) form.value.resetValidation();
  }, 300);
};

const onImageSelected = (file) => {
  if (file && !Array.isArray(file)) {
    imagePreview.value = URL.createObjectURL(file);
    editedItem.value.imageFile = file;
  } else if (Array.isArray(file) && file.length > 0) {
    imagePreview.value = URL.createObjectURL(file[0]);
    editedItem.value.imageFile = file[0];
  } else {
    imagePreview.value = null;
    editedItem.value.imageFile = null;
  }
};

const clearImage = () => {
  editedItem.value.imageFile = null;
  editedItem.value.cover_image = null;
  imagePreview.value = null;
  if (form.value) form.value.resetValidation();
};

const savePost = async () => {
  const { valid } = await form.value.validate();
  if (!valid) return;

  saving.value = true;
  try {
    const formData = new FormData();
    formData.append("title", editedItem.value.title);
    if (editedItem.value.category)
      formData.append("category", editedItem.value.category);
    if (editedItem.value.author_name)
      formData.append("author_name", editedItem.value.author_name);
    if (editedItem.value.summary)
      formData.append("summary", editedItem.value.summary);
    formData.append("content", editedItem.value.content);
    if (editedItem.value.meta_title)
      formData.append("meta_title", editedItem.value.meta_title);
    if (editedItem.value.meta_description)
      formData.append("meta_description", editedItem.value.meta_description);
    if (editedItem.value.meta_keywords)
      formData.append("meta_keywords", editedItem.value.meta_keywords);
    formData.append("is_published", editedItem.value.is_published ? "1" : "0");

    if (editedItem.value.imageFile) {
      formData.append("cover_image", editedItem.value.imageFile);
    }

    let isUpdate = !!editedItem.value.id;
    let endpoint = isUpdate
      ? `/admin/posts/${editedItem.value.id}`
      : "/admin/posts";

    // In Laravel, PUT with multipart/form-data can be tricky, use POST with _method=PUT
    if (isUpdate) {
      formData.append("_method", "PUT");
      endpoint = `/admin/posts/${editedItem.value.id}`;
    }

    const res = await api.post(endpoint, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (isUpdate) {
      const index = posts.value.findIndex((p) => p.id === editedItem.value.id);
      if (index !== -1) posts.value[index] = res.data;
      showSnackbar("تم تعديل المقال بنجاح");
    } else {
      posts.value.unshift(res.data);
      showSnackbar("تم نشر المقال بنجاح");
    }

    closeDialog();
  } catch (err) {
    console.error("Save Error:", err);
    showSnackbar(err.response?.data?.message || "حدث خطأ أثناء الحفظ", "error");
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (item) => {
  editedItem.value = { ...item };
  deleteDialog.value = true;
};

const confirmDeleteAction = async () => {
  deleting.value = true;
  try {
    await api.delete(`/admin/posts/${editedItem.value.id}`);
    posts.value = posts.value.filter((p) => p.id !== editedItem.value.id);
    showSnackbar("تم حذف المقال بنجاح");
    deleteDialog.value = false;
  } catch (err) {
    showSnackbar("حدث خطأ أثناء الحذف", "error");
  } finally {
    deleting.value = false;
  }
};

const showSnackbar = (text, color = "success") => {
  snackbar.value = { show: true, text, color };
};

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleDateString("ar-EG", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

onMounted(() => {
  fetchPosts();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.custom-shadow {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
}

.hover-bg {
  transition: background-color 0.2s;
}
.hover-bg:hover {
  background-color: rgba(var(--v-theme-primary), 0.1);
}

.image-upload-area {
  height: 200px;
  border: 2px dashed rgba(var(--v-theme-primary), 0.3);
  background-color: rgba(var(--v-theme-primary), 0.02);
  transition: all 0.3s ease;
  overflow: hidden;
}

.image-upload-area:hover {
  border-color: rgba(var(--v-theme-primary), 0.8);
  background-color: rgba(var(--v-theme-primary), 0.05);
}

.image-upload-area.has-image {
  border-style: solid;
  border-color: transparent;
}

.image-upload-area .overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  opacity: 0;
  transition: opacity 0.3s;
}

.image-upload-area:hover .overlay {
  opacity: 1;
}
</style>
