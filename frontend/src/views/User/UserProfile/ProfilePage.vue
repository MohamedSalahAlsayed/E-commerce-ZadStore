<template>
  <v-app theme="light">
    <v-locale-provider rtl>
      <v-main class="bg-grey-lighten-5">
        <v-container class="py-10">
          <v-card class="rounded-xl overflow-hidden mb-6" elevation="2">
            <v-img
              height="200"
              src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
              cover
              class="align-end"
            >
              <div
                class="fill-height d-flex align-end justify-end pa-4"
                style="
                  background: linear-gradient(
                    to top,
                    rgba(0, 0, 0, 0.5),
                    transparent
                  );
                "
              >
                <v-btn icon="mdi-camera" variant="text" color="white"></v-btn>
              </div>
            </v-img>

            <v-card-text class="position-relative pt-0">
              <v-row>
                <v-col
                  cols="12"
                  md="3"
                  class="d-flex flex-column align-center mt-n16"
                >
                  <v-avatar
                    size="140"
                    class="border-4 border-white elevation-4 bg-white"
                  >
                    <v-img :src="avatarUrl" alt="User Avatar"></v-img>
                  </v-avatar>
                  <div class="text-center mt-3">
                    <h2 class="text-h5 font-weight-bold text-primary-darken-4">
                      {{ user?.name || "اسم المستخدم" }}
                    </h2>
                    <p class="text-grey-darken-1">
                      {{
                        user?.bio || "هذا المستخدم لم يقم بكتابة نبذة عنه بعد."
                      }}
                    </p>
                    <v-chip
                      v-if="user?.role === 'admin' || user?.role === 'merchant'"
                      color="success"
                      size="small"
                      class="mt-1"
                      variant="flat"
                    >
                      {{
                        user?.role === "admin" ? "مدير النظام" : "تاجر معتمد"
                      }}
                    </v-chip>
                    <v-chip
                      v-else
                      color="info"
                      size="small"
                      class="mt-1"
                      variant="flat"
                    >
                      مستخدم
                    </v-chip>
                  </div>
                </v-col>

                <v-col cols="12" md="9" class="pt-6 pt-md-4">
                  <div
                    class="d-flex justify-space-between align-start flex-wrap gap-4"
                  >
                    <div class="d-none d-md-block">
                      <div class="d-flex align-center mb-1 text-grey-darken-2">
                        <v-icon start size="small" color="primary"
                          >mdi-email</v-icon
                        >
                        {{ user?.email }}
                      </div>
                      <div
                        class="d-flex align-center text-grey-darken-2"
                        v-if="user?.phone"
                      >
                        <v-icon start size="small" color="primary"
                          >mdi-phone</v-icon
                        >
                        {{ user.phone }}
                      </div>
                    </div>

                    <div class="d-flex gap-2 ms-auto">
                      <v-btn
                        variant="outlined"
                        color="primary"
                        prepend-icon="mdi-pencil"
                        to="/User/UserProfile/EditProfilePage"
                      >
                        تعديل البيانات
                      </v-btn>
                      <v-btn
                        variant="flat"
                        color="primary"
                        prepend-icon="mdi-share-variant"
                      >
                        مشاركة الرابط
                      </v-btn>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>

          <v-row>
            <v-col cols="12" md="8">
              <v-row>
                <v-col cols="12" sm="4">
                  <v-card
                    class="pa-4 rounded-xl border-indigo-light"
                    elevation="1"
                  >
                    <div class="text-overline text-primary mb-1">
                      إجمالي الأرباح
                    </div>
                    <div class="text-h5 font-weight-bold text-grey-darken-3">
                      {{ statistics.total_earnings }} ج.م
                    </div>
                    <v-icon
                      color="primary-lighten-4"
                      class="position-absolute top-0 left-0 ma-4"
                      size="40"
                      >mdi-cash-multiple</v-icon
                    >
                  </v-card>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-card
                    class="pa-4 rounded-xl border-indigo-light"
                    elevation="1"
                  >
                    <div class="text-overline text-primary mb-1">
                      الطلبات المكتملة
                    </div>
                    <div class="text-h5 font-weight-bold text-grey-darken-3">
                      {{ statistics.completed_orders }} طلب
                    </div>
                    <v-icon
                      color="primary-lighten-4"
                      class="position-absolute top-0 left-0 ma-4"
                      size="40"
                      >mdi-shopping-outline</v-icon
                    >
                  </v-card>
                </v-col>
                <v-col cols="12" sm="4">
                  <v-card
                    class="pa-4 rounded-xl border-indigo-light"
                    elevation="1"
                  >
                    <div class="text-overline text-green mb-1">
                      المحفظة الحالية
                    </div>
                    <div class="text-h5 font-weight-bold text-grey-darken-3">
                      {{ statistics.wallet_balance }} ج.م
                    </div>
                    <v-icon
                      color="green-lighten-4"
                      class="position-absolute top-0 left-0 ma-4"
                      size="40"
                      >mdi-wallet-outline</v-icon
                    >
                  </v-card>
                </v-col>
              </v-row>

              <v-card
                class="mt-6 rounded-xl"
                title="أحدث النشاطات"
                :loading="loadingStats"
              >
                <v-list
                  lines="two"
                  v-if="statistics.recent_activities.length > 0"
                >
                  <template
                    v-for="(activity, index) in statistics.recent_activities"
                    :key="index"
                  >
                    <v-list-item
                      :prepend-icon="
                        activity.type === 'order'
                          ? activity.status === 'delivered'
                            ? 'mdi-check-circle'
                            : 'mdi-package-variant'
                          : 'mdi-star-outline'
                      "
                      :title="activity.title"
                      :subtitle="activity.subtitle"
                    >
                      <template v-slot:prepend>
                        <v-icon
                          :color="
                            activity.status === 'delivered' ||
                            activity.status === 'approved'
                              ? 'green'
                              : activity.status === 'pending'
                              ? 'primary'
                              : 'primary'
                          "
                        ></v-icon>
                      </template>
                      <template v-slot:append>
                        <span class="text-caption text-grey">{{
                          new Date(activity.created_at).toLocaleDateString(
                            "ar-EG"
                          )
                        }}</span>
                      </template>
                    </v-list-item>
                    <v-divider
                      v-if="index < statistics.recent_activities.length - 1"
                      inset
                    ></v-divider>
                  </template>
                </v-list>
                <div v-else class="text-center pa-6 text-grey text-caption">
                  لا توجد نشاطات حالياً
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" md="4">
              <v-card class="rounded-xl mb-4" title="بيانات الحساب">
                <v-list density="compact">
                  <v-list-item
                    prepend-icon="mdi-calendar"
                    title="تاريخ الانضمام"
                    :subtitle="
                      user?.created_at
                        ? new Date(user.created_at).toLocaleDateString('ar-EG')
                        : 'غير متوفر'
                    "
                  ></v-list-item>
                  <v-list-item
                    prepend-icon="mdi-map-marker"
                    title="العنوان"
                    :subtitle="user?.address || 'لم يتم تحديد العنوان'"
                  ></v-list-item>
                  <v-list-item
                    prepend-icon="mdi-truck-delivery"
                    title="شركة الشحن المفضلة"
                    :subtitle="
                      user?.preferred_shipping_company || 'لم يتم التحديد'
                    "
                  ></v-list-item>
                </v-list>
              </v-card>

              <v-card
                class="rounded-xl bg-primary-darken-3 text-white"
                variant="flat"
              >
                <v-card-text
                  class="d-flex flex-column align-center text-center pa-6"
                >
                  <v-icon size="48" class="mb-2">mdi-headset</v-icon>
                  <h3 class="text-h6 font-weight-bold">تحتاج مساعدة؟</h3>
                  <p class="text-primary-lighten-4 text-caption mb-4">
                    فريق الدعم متاح 24/7 لمساعدتك
                  </p>
                  <v-btn
                    variant="flat"
                    color="white"
                    class="text-primary-darken-3 font-weight-bold rounded-pill"
                    to="/User/UserProfile/MessagePage"
                    >تواصل معنا</v-btn
                  >
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>
<script setup>
import { computed, ref, onMounted } from "vue";
import { useAuthStore } from "@/store/auth/LogIn";
import api from "@/axios";

const authStore = useAuthStore();
const user = computed(() => authStore.currentUser);

const avatarUrl = computed(() => {
  if (user.value?.avatar) {
    const baseUrl = process.env.VUE_APP_API_URL || "http://localhost:8000";
    return user.value.avatar.startsWith("http")
      ? user.value.avatar
      : `${baseUrl}${user.value.avatar}`;
  }
  return "https://i.pravatar.cc/300?img=12";
});

const loadingStats = ref(false);
const statistics = ref({
  total_earnings: 0,
  completed_orders: 0,
  wallet_balance: 0,
  recent_activities: [],
});

const fetchStatistics = async () => {
  loadingStats.value = true;
  try {
    const res = await api.get("/user/statistics");
    statistics.value = res.data;
  } catch (err) {
    console.error("Failed to load user statistics", err);
  } finally {
    loadingStats.value = false;
  }
};

onMounted(() => {
  fetchStatistics();
});
</script>
<style scoped>
.border-4 {
  border: 4px solid white !important;
}
.border-indigo-light {
  border: 1px solid #e8eaf6;
}
.gap-2 {
  gap: 8px;
}
.gap-4 {
  gap: 16px;
}
</style>
