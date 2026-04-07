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
              <v-icon color="white" size="28">mdi-star-check-outline</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              التقييمات والمراجعات
            </h2>
          </div>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 600px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              label="ابحث باسم العميل أو المنتج..."
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg flex-grow-1"
            ></v-text-field>

            <v-select
              v-model="statusFilter"
              :items="['الكل', 'مخفي (غير موافق عليه)', 'مقبول']"
              label="حالة التقييم"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg"
              style="min-width: 150px; max-width: 200px"
            ></v-select>
          </div>
        </v-card-text>
      </v-card>

      <v-row class="mb-4">
        <v-col cols="12" md="4">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-warning"
            elevation="1"
          >
            <v-avatar
              color="orange-lighten-4"
              size="50"
              class="mr-3 text-warning"
            >
              <v-icon>mdi-eye-off-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                التقييمات المخفية
              </div>
              <div class="text-h5 font-weight-black text-warning">
                {{ pendingCount }}
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-success"
            elevation="1"
          >
            <v-avatar
              color="green-lighten-4"
              size="50"
              class="mr-3 text-success"
            >
              <v-icon>mdi-check-decagram</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                التقييمات المنشورة
              </div>
              <div class="text-h5 font-weight-black text-success">
                {{ approvedCount }}
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-primary"
            elevation="1"
          >
            <v-avatar
              color="primary-lighten-4"
              size="50"
              class="mr-3 text-primary"
            >
              <v-icon>mdi-star-circle</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                متوسط التقييمات
              </div>
              <div class="text-h5 font-weight-black">
                {{ averageRating }}
                <span class="text-caption text-grey">/ 5</span>
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
                <th class="text-right font-weight-bold">المنتج</th>
                <th class="text-right font-weight-bold">العميل</th>
                <th class="text-center font-weight-bold">التقييم</th>
                <th class="text-right font-weight-bold">التعليق</th>
                <th class="text-center font-weight-bold">الرد</th>
                <th class="text-center font-weight-bold">الحالة</th>
                <th class="text-center font-weight-bold">إجراءات</th>
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

              <tr v-else-if="filteredReviews.length === 0">
                <td colspan="6" class="text-center pa-6 text-grey-darken-1">
                  لا توجد تقييمات مطابقة لبحثك.
                </td>
              </tr>

              <tr
                v-for="review in filteredReviews"
                :key="review.id"
                class="hover-row"
                v-else
              >
                <td
                  class="d-flex align-center gap-3 py-2"
                  style="max-width: 200px"
                >
                  <v-avatar
                    size="45"
                    rounded
                    class="bg-grey-lighten-2 elevation-1"
                  >
                    <v-img :src="review.product?.thumbnail" cover></v-img>
                  </v-avatar>
                  <span
                    class="font-weight-bold text-subtitle-2 text-truncate"
                    >{{ review.product?.title || "منتج محذوف" }}</span
                  >
                </td>

                <td class="text-grey-darken-1 font-weight-medium">
                  <div class="d-flex align-center gap-2">
                    <v-avatar
                      size="30"
                      color="primary"
                      class="text-white text-caption font-weight-bold"
                    >
                      {{ review.user?.name ? review.user.name.charAt(0) : "؟" }}
                    </v-avatar>
                    {{ review.user?.name || "مستخدم غير معروف" }}
                  </div>
                </td>

                <td class="text-center">
                  <v-rating
                    :model-value="review.rating"
                    color="amber-darken-2"
                    density="compact"
                    size="small"
                    readonly
                    half-increments
                  ></v-rating>
                </td>

                <td
                  style="max-width: 250px"
                  class="text-truncate text-grey-darken-2"
                  :title="review.comment"
                >
                  {{ review.comment || "لا يوجد تعليق" }}
                </td>

                <td class="text-center">
                  <v-chip
                    size="small"
                    :color="review.admin_reply ? 'info' : 'grey'"
                    variant="outlined"
                    class="font-weight-bold"
                  >
                    {{ review.admin_reply ? "تم الرد" : "بدون رد" }}
                  </v-chip>
                </td>

                <td class="text-center">
                  <v-chip
                    size="small"
                    :color="review.is_approved ? 'success' : 'warning'"
                    class="font-weight-bold"
                  >
                    {{ review.is_approved ? "مقبول" : "مخفي" }}
                  </v-chip>
                </td>

                <td class="text-center">
                  <v-btn
                    icon
                    size="small"
                    color="info"
                    variant="text"
                    @click="viewReview(review)"
                    title="قراءة التفاصيل"
                  >
                    <v-icon>mdi-eye-outline</v-icon>
                  </v-btn>

                  <v-btn
                    icon
                    size="small"
                    :color="review.is_approved ? 'warning' : 'success'"
                    variant="text"
                    @click="toggleStatus(review)"
                    :disabled="processingIds.includes(review.id)"
                    :title="
                      review.is_approved ? 'إخفاء التقييم' : 'الموافقة ونشر'
                    "
                  >
                    <v-progress-circular
                      v-if="
                        processingIds.includes(review.id) &&
                        lastAction === 'toggle'
                      "
                      indeterminate
                      size="16"
                    ></v-progress-circular>
                    <v-icon v-else>{{
                      review.is_approved
                        ? "mdi-eye-off-outline"
                        : "mdi-check-circle-outline"
                    }}</v-icon>
                  </v-btn>

                  <v-btn
                    icon
                    size="small"
                    color="error"
                    variant="text"
                    :disabled="processingIds.includes(review.id)"
                    @click="deleteReview(review.id)"
                    title="حذف"
                  >
                    <v-progress-circular
                      v-if="
                        processingIds.includes(review.id) &&
                        lastAction === 'delete'
                      "
                      indeterminate
                      size="16"
                    ></v-progress-circular>
                    <v-icon v-else>mdi-delete-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>
      </v-card>

      <v-dialog v-model="viewDialog" max-width="500px">
        <v-card class="rounded-xl" v-if="selectedReview">
          <v-card-title
            class="d-flex align-center justify-space-between pt-4 px-4 pb-0"
          >
            <span
              class="text-h6 font-weight-bold"
              style="color: rgb(var(--v-theme-primary))"
              >تفاصيل التقييم</span
            >
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="viewDialog = false"
            ></v-btn>
          </v-card-title>

          <v-card-text class="pt-4">
            <div
              class="d-flex align-center gap-4 mb-4 p-3 bg-grey-lighten-4 rounded-lg pa-3"
            >
              <v-avatar size="60" rounded class="elevation-1">
                <v-img :src="selectedReview.product?.thumbnail" cover></v-img>
              </v-avatar>
              <div>
                <div class="font-weight-bold text-subtitle-1">
                  {{ selectedReview.product?.title }}
                </div>
                <div class="text-caption text-grey-darken-1">
                  بواسطة: {{ selectedReview.user?.name }} |
                  {{ formatDate(selectedReview.created_at) }}
                </div>
              </div>
            </div>

            <div class="mb-2">
              <span class="font-weight-bold mr-2">التقييم:</span>
              <v-rating
                :model-value="selectedReview.rating"
                color="amber-darken-2"
                density="compact"
                size="small"
                readonly
              ></v-rating>
            </div>

            <div
              class="bg-blue-grey-lighten-5 pa-4 rounded-lg text-body-1"
              style="line-height: 1.8"
            >
              "{{ selectedReview.comment || "لا يوجد تعليق" }}"
            </div>

            <div
              v-if="selectedReview.admin_reply"
              class="mt-4 bg-primary-lighten-5 pa-4 rounded-lg border-left-primary"
            >
              <div
                class="text-caption font-weight-bold text-primary mb-1 d-flex align-center gap-2"
              >
                <v-icon size="small">mdi-reply</v-icon> رد الإدارة:
              </div>
              <div class="text-body-2">{{ selectedReview.admin_reply }}</div>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4 bg-grey-lighten-4 justify-center gap-3">
            <v-btn
              color="primary"
              variant="flat"
              class="px-6 font-weight-bold shadow-btn"
              prepend-icon="mdi-reply"
              @click="openReplyDialog(selectedReview)"
            >
              الرد على التقييم
            </v-btn>
            <v-btn
              v-if="!selectedReview.is_approved"
              color="success"
              variant="tonal"
              class="px-4 font-weight-bold"
              @click="
                toggleStatus(selectedReview);
                viewDialog = false;
              "
            >
              نشر
            </v-btn>
            <v-btn
              v-else
              color="warning"
              variant="tonal"
              class="px-4 font-weight-bold"
              @click="
                toggleStatus(selectedReview);
                viewDialog = false;
              "
            >
              إخفاء
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Reply Dialog -->
      <v-dialog v-model="replyDialog" max-width="500px">
        <v-card class="rounded-xl">
          <v-card-title class="bg-primary text-white pa-4"
            >الرد على التقييم</v-card-title
          >
          <v-card-text class="pt-6">
            <v-textarea
              v-model="replyText"
              label="اكتب ردك هنا..."
              variant="outlined"
              rows="4"
              auto-grow
            ></v-textarea>
          </v-card-text>
          <v-card-actions class="pa-4 bg-grey-lighten-4">
            <v-spacer></v-spacer>
            <v-btn color="grey" variant="text" @click="replyDialog = false"
              >إلغاء</v-btn
            >
            <v-btn
              color="primary"
              variant="flat"
              :loading="replying"
              @click="sendReply"
              >إرسال الرد</v-btn
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

const reviews = ref([]);
const loading = ref(true);
const processingIds = ref([]);
const lastAction = ref(""); // used to show precise loader in row

const searchQuery = ref("");
const statusFilter = ref("الكل");

const viewDialog = ref(false);
const replyDialog = ref(false);
const selectedReview = ref(null);
const replyText = ref("");
const replying = ref(false);

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
    fetchReviews(true);
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
    fetchReviews(true);
    startPolling();
  }
};
// ----------------------------------

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const pendingCount = computed(
  () => reviews.value.filter((r) => !r.is_approved).length
);
const approvedCount = computed(
  () => reviews.value.filter((r) => r.is_approved).length
);
const averageRating = computed(() => {
  if (reviews.value.length === 0) return "0.0";
  const sum = reviews.value.reduce((acc, curr) => acc + curr.rating, 0);
  return (sum / reviews.value.length).toFixed(1);
});

const filteredReviews = computed(() => {
  return reviews.value.filter((review) => {
    const userName = review.user?.name || "";
    const productName = review.product?.title || "";
    const matchSearch =
      userName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      productName.toLowerCase().includes(searchQuery.value.toLowerCase());

    let matchStatus = true;
    if (statusFilter.value === "مخفي (غير موافق عليه)")
      matchStatus = !review.is_approved;
    if (statusFilter.value === "مقبول") matchStatus = review.is_approved;

    return matchSearch && matchStatus;
  });
});

const fetchReviews = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const res = await axios.get("/admin/reviews");
    reviews.value = res.data;
  } catch (error) {
    console.error("Error fetching reviews:", error);
  } finally {
    if (!quiet) loading.value = false;
  }
};

const viewReview = (review) => {
  selectedReview.value = review;
  viewDialog.value = true;
};

const openReplyDialog = (review) => {
  selectedReview.value = review;
  replyText.value = review.admin_reply || "";
  replyDialog.value = true;
};

const sendReply = async () => {
  if (!replyText.value.trim()) return;
  replying.value = true;
  try {
    const res = await axios.post(
      `/admin/reviews/${selectedReview.value.id}/reply`,
      {
        reply: replyText.value,
      }
    );
    const index = reviews.value.findIndex(
      (r) => r.id === selectedReview.value.id
    );
    if (index !== -1) {
      reviews.value[index].admin_reply = res.data.admin_reply;
      reviews.value[index].replied_at = res.data.replied_at;
    }
    showMessage("تم إرسال الرد بنجاح");
    replyDialog.value = false;
    viewDialog.value = false;
  } catch (err) {
    showMessage("حدث خطأ", "error");
  } finally {
    replying.value = false;
  }
};

const toggleStatus = async (review) => {
  processingIds.value.push(review.id);
  lastAction.value = "toggle";
  try {
    const res = await axios.put(`/admin/reviews/${review.id}/status`);
    const index = reviews.value.findIndex((r) => r.id === review.id);
    if (index !== -1) {
      reviews.value[index].is_approved = res.data.is_approved;
    }
    const txt = res.data.is_approved
      ? "تم نشر التقييم بنجاح"
      : "تم إخفاء التقييم";
    showMessage(txt, res.data.is_approved ? "success" : "warning");
    fetchReviews(true);
  } catch (error) {
    console.error("Error toggling status:", error);
    showMessage("حدث خطأ أثناء تغيير الحالة", "error");
  } finally {
    processingIds.value = processingIds.value.filter((id) => id !== review.id);
  }
};

const deleteReview = async (id) => {
  if (!confirm("هل أنت متأكد من حذف التقييم بشكل نهائي؟")) return;
  processingIds.value.push(id);
  lastAction.value = "delete";
  try {
    await axios.delete(`/admin/reviews/${id}`);
    reviews.value = reviews.value.filter((r) => r.id !== id);
    showMessage("تم مسح التقييم نهائياً");
    fetchReviews(true);
  } catch (error) {
    console.error("Error deleting review:", error);
    showMessage("حدث خطأ أثناء الحذف", "error");
  } finally {
    processingIds.value = processingIds.value.filter((i) => i !== id);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString("ar-EG", options);
};

onMounted(() => {
  fetchReviews();
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
