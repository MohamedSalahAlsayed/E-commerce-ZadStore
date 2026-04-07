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
              <v-icon color="white" size="28">mdi-cog-outline</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              الإعدادات العامة للمتجر
            </h2>
          </div>

          <v-btn
            color="primary"
            prepend-icon="mdi-content-save-outline"
            class="font-weight-bold px-8 rounded-lg shadow-btn text-h6"
            height="48"
            :loading="isSaving"
            @click="saveSettings"
          >
            حفظ التعديلات
          </v-btn>
        </v-card-text>
      </v-card>

      <v-card class="rounded-xl" elevation="1">
        <div class="d-flex flex-column flex-md-row">
          <v-tabs
            v-model="activeTab"
            color="primary"
            direction="vertical"
            class="border-right bg-grey-lighten-4 py-4 settings-tabs"
            style="min-width: 250px"
          >
            <v-tab
              value="general"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-storefront-outline</v-icon> معلومات
              المتجر
            </v-tab>
            <v-tab
              value="contact"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-phone-outline</v-icon> بيانات
              التواصل
            </v-tab>
            <v-tab
              value="social"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-share-variant-outline</v-icon>
              منصات التواصل
            </v-tab>
            <v-tab
              value="advanced"
              class="text-right font-weight-bold d-flex justify-start px-6 py-3"
            >
              <v-icon right class="ml-3">mdi-tune-vertical</v-icon> إعدادات
              النظام
            </v-tab>
          </v-tabs>

          <v-window v-model="activeTab" class="flex-grow-1 pa-6">
            <v-window-item value="general">
              <h3
                class="text-h6 font-weight-bold mb-6"
                style="color: rgb(var(--v-theme-primary))"
              >
                المعلومات الأساسية والهوية
              </h3>
              <v-row v-if="isLoading">
                <v-col cols="12" class="text-center py-6">
                  <v-progress-circular
                    indeterminate
                    color="primary"
                  ></v-progress-circular>
                </v-col>
              </v-row>
              <v-row v-else>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.storeName"
                    label="اسم المتجر"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.storeSlogan"
                    label="الشعار اللفظي (Slogan)"
                    variant="outlined"
                    color="primary"
                    placeholder="مثال: الجودة بأفضل سعر"
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    v-model="settings.storeDescription"
                    label="وصف المتجر (يظهر في محركات البحث SEO)"
                    variant="outlined"
                    color="primary"
                    rows="3"
                  ></v-textarea>
                </v-col>

                <v-col cols="12" md="6">
                  <p class="font-weight-bold mb-2">
                    رابط الشعار المباشر (Logo URL)
                  </p>
                  <div class="d-flex align-center gap-4">
                    <v-avatar
                      size="80"
                      class="bg-grey-lighten-3 border rounded-lg"
                      rounded
                    >
                      <v-img
                        v-if="settings.logoPreview"
                        :src="settings.logoPreview"
                        cover
                      ></v-img>
                      <v-icon v-else size="40" color="grey">mdi-image</v-icon>
                    </v-avatar>
                    <v-file-input
                      v-model="logoFile"
                      variant="outlined"
                      density="compact"
                      hide-details
                      prepend-icon=""
                      prepend-inner-icon="mdi-camera"
                      label="رفع صورة الشعار..."
                      accept="image/*"
                      class="w-100"
                      @update:modelValue="handleLogoUpload"
                    ></v-file-input>
                  </div>
                </v-col>

                <!-- Color Themes Palette -->
                <v-col cols="12">
                  <v-divider class="my-4"></v-divider>
                  <h3
                    class="text-h6 font-weight-bold mb-4"
                    style="color: rgb(var(--v-theme-primary))"
                  >
                    <v-icon left>mdi-palette</v-icon> ألوان الهوية البصرية
                    للمتجر
                  </h3>
                  <p class="text-subtitle-2 text-grey-darken-1 mb-4">
                    اختر اللون الأساسي الذي سيعكس العلامة التجارية الخاصة بك.
                    سيتم تطبيق هذا اللون على كافة الروابط، الأزرار، والواجهات.
                  </p>

                  <v-text-field
                    v-model="themeSearch"
                    label="بحث عن ثيم محدد..."
                    variant="solo-filled"
                    density="compact"
                    prepend-inner-icon="mdi-magnify"
                    hide-details
                    class="mb-4"
                    clearable
                  ></v-text-field>

                  <div class="theme-gallery-container mt-4">
                    <div class="d-flex flex-wrap gap-4">
                      <v-card
                        v-for="theme in filteredPalettes"
                        :key="theme.id"
                        class="theme-card cursor-pointer"
                        :class="{
                          'theme-selected':
                            settings.storeTheme?.id === theme.id,
                        }"
                        width="180"
                        variant="outlined"
                        @click="selectTheme(theme)"
                      >
                        <div class="pa-3">
                          <div class="d-flex gap-1 mb-2 h-10">
                            <div
                              class="flex-grow-1 rounded-sm"
                              :style="{ backgroundColor: theme.primary }"
                            ></div>
                            <div
                              class="flex-grow-1 rounded-sm"
                              :style="{ backgroundColor: theme.secondary }"
                            ></div>
                            <div
                              class="flex-grow-1 rounded-sm border"
                              :style="{ backgroundColor: theme.background }"
                            ></div>
                          </div>
                          <div
                            class="text-caption font-weight-bold text-center"
                          >
                            {{ theme.name }}
                          </div>
                        </div>
                        <v-overlay
                          :model-value="settings.storeTheme?.id === theme.id"
                          contained
                          scrim="primary"
                          class="align-center justify-center pointer-events-none"
                        >
                          <v-icon color="white" size="32"
                            >mdi-check-circle</v-icon
                          >
                        </v-overlay>
                      </v-card>
                    </div>
                  </div>

                  <!-- Custom HEX Input -->
                  <div
                    class="mt-6 d-flex align-center gap-4"
                    style="max-width: 300px"
                  >
                    <v-text-field
                      v-model="settings.primaryColor"
                      label="أو أدخل كود اللون (HEX)"
                      variant="outlined"
                      density="compact"
                      prepend-inner-icon="mdi-palette"
                      hide-details
                      dir="ltr"
                      class="text-center"
                    ></v-text-field>
                    <v-avatar
                      :color="settings.primaryColor"
                      size="40"
                      class="border"
                    ></v-avatar>
                  </div>
                </v-col>
              </v-row>
            </v-window-item>

            <v-window-item value="contact">
              <h3
                class="text-h6 font-weight-bold mb-6"
                style="color: rgb(var(--v-theme-primary))"
              >
                أرقام وعناوين الدعم الفني
              </h3>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.email"
                    label="البريد الإلكتروني الرسمي"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    color="primary"
                    type="email"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.phone"
                    label="رقم خدمة العملاء"
                    prepend-inner-icon="mdi-phone-outline"
                    variant="outlined"
                    color="primary"
                    dir="ltr"
                    class="text-right-input"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.whatsapp"
                    label="رقم الواتساب"
                    prepend-inner-icon="mdi-whatsapp"
                    variant="outlined"
                    color="success"
                    dir="ltr"
                    class="text-right-input"
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    v-model="settings.address"
                    label="العنوان الفعلي (المقر الرئيسي)"
                    prepend-inner-icon="mdi-map-marker-outline"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-window-item>

            <v-window-item value="social">
              <h3
                class="text-h6 font-weight-bold mb-6"
                style="color: rgb(var(--v-theme-primary))"
              >
                روابط السوشيال ميديا
              </h3>
              <v-row>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.facebook"
                    label="رابط فيسبوك"
                    prepend-inner-icon="mdi-facebook"
                    variant="outlined"
                    color="#1877F2"
                    dir="ltr"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.instagram"
                    label="رابط إنستجرام"
                    prepend-inner-icon="mdi-instagram"
                    variant="outlined"
                    color="#E4405F"
                    dir="ltr"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.tiktok"
                    label="رابط تيك توك"
                    prepend-inner-icon="mdi-music-note"
                    variant="outlined"
                    color="black"
                    dir="ltr"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="settings.twitter"
                    label="رابط منصة X (تويتر)"
                    prepend-inner-icon="mdi-twitter"
                    variant="outlined"
                    color="#1DA1F2"
                    dir="ltr"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-window-item>

            <v-window-item value="advanced">
              <h3
                class="text-h6 font-weight-bold mb-6"
                style="color: rgb(var(--v-theme-primary))"
              >
                إعدادات متقدمة
              </h3>
              <v-row>
                <v-col cols="12" md="6">
                  <v-select
                    v-model="settings.currency"
                    :items="[
                      'جنيه مصري (EGP)',
                      'دولار أمريكي (USD)',
                      'ريال سعودي (SAR)',
                    ]"
                    label="العملة الافتراضية للمتجر"
                    prepend-inner-icon="mdi-cash"
                    variant="outlined"
                    color="primary"
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model.number="settings.taxRate"
                    label="نسبة ضريبة القيمة المضافة (%)"
                    type="number"
                    prepend-inner-icon="mdi-percent"
                    variant="outlined"
                    color="primary"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="settings.orderPrefix"
                    label="بادئة رقم الطلب (Order Prefix)"
                    variant="outlined"
                    color="primary"
                    prepend-inner-icon="mdi-format-prefix"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model.number="settings.lowStockThreshold"
                    label="تنبيه المخزون المنخفض (Global)"
                    type="number"
                    variant="outlined"
                    color="primary"
                    prepend-inner-icon="mdi-alert-box-outline"
                  ></v-text-field>
                </v-col>
              </v-row>

              <v-divider class="my-6"></v-divider>

              <v-card variant="tonal" color="info" class="rounded-lg pa-4 mb-4">
                <div class="d-flex justify-space-between align-center">
                  <div>
                    <div
                      class="d-flex align-center gap-2 mb-1 text-info font-weight-bold"
                    >
                      <v-icon>mdi-cash-marker</v-icon>
                      <span>تفعيل الدفع عند الاستلام (COD)</span>
                    </div>
                    <p class="text-caption text-grey-darken-1 mb-0">
                      تمكين العملاء من اختيار الدفع نقداً عند استلام الطلبات.
                    </p>
                  </div>
                  <v-switch
                    v-model="settings.codEnabled"
                    color="info"
                    hide-details
                    inset
                  ></v-switch>
                </div>
              </v-card>

              <v-divider class="my-6"></v-divider>

              <v-card
                variant="tonal"
                :color="settings.maintenanceMode ? 'warning' : 'grey-lighten-1'"
                class="rounded-lg pa-4"
              >
                <div class="d-flex justify-space-between align-center">
                  <div>
                    <div class="d-flex align-center gap-2 mb-1">
                      <v-icon
                        :color="settings.maintenanceMode ? 'warning' : 'grey'"
                        >mdi-alert-octagon-outline</v-icon
                      >
                      <span
                        class="text-h6 font-weight-bold"
                        :class="
                          settings.maintenanceMode
                            ? 'text-warning'
                            : 'text-grey-darken-2'
                        "
                        >وضع الصيانة (Maintenance Mode)</span
                      >
                    </div>
                    <p class="text-caption text-grey-darken-1 mb-0">
                      عند تفعيل هذا الخيار، لن يتمكن الزوار من تصفح الموقع
                      وسيرون صفحة "الموقع تحت الصيانة". (مفيد أثناء التحديثات)
                    </p>
                  </div>
                  <v-switch
                    v-model="settings.maintenanceMode"
                    color="warning"
                    hide-details
                    inset
                  ></v-switch>
                </div>
              </v-card>
            </v-window-item>
          </v-window>
        </div>
      </v-card>

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
import { ref, onMounted, computed } from "vue";
import axios from "@/axios";
import { themePalettes } from "@/utils/themePalettes";

const activeTab = ref("general");
const isSaving = ref(false);
const isLoading = ref(true);
const themeSearch = ref("");

const filteredPalettes = computed(() => {
  if (!themeSearch.value) return themePalettes;
  const q = themeSearch.value.toLowerCase();
  return themePalettes.filter(
    (p) =>
      p.name.toLowerCase().includes(q) ||
      p.primary.toLowerCase().includes(q) ||
      p.secondary.toLowerCase().includes(q)
  );
});

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

const logoFile = ref(null);

const handleLogoUpload = (fileArray) => {
  const file = Array.isArray(fileArray) ? fileArray[0] : fileArray;
  if (file) {
    settings.value.logoPreview = URL.createObjectURL(file);
  }
};

const settings = ref({
  storeName: "",
  storeSlogan: "",
  storeDescription: "",
  logoPreview: "",
  email: "",
  phone: "",
  whatsapp: "",
  address: "",
  facebook: "",
  instagram: "",
  twitter: "",
  tiktok: "",
  currency: "جنيه مصري (EGP)",
  taxRate: 0,
  orderPrefix: "ZAD-",
  lowStockThreshold: 5,
  codEnabled: true,
  maintenanceMode: false,
  primaryColor: "#f97316",
  storeTheme: null,
});

const selectTheme = (theme) => {
  settings.value.storeTheme = theme;
  settings.value.primaryColor = theme.primary;
};

const loadSettings = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get("/admin/settings");
    const data = res.data || {};

    // Map db data to UI ref
    if (data.storeName) settings.value.storeName = data.storeName;
    if (data.storeSlogan) settings.value.storeSlogan = data.storeSlogan;
    if (data.storeDescription)
      settings.value.storeDescription = data.storeDescription;
    if (data.logoPreview) settings.value.logoPreview = data.logoPreview;
    if (data.email) settings.value.email = data.email;
    if (data.phone) settings.value.phone = data.phone;
    if (data.whatsapp) settings.value.whatsapp = data.whatsapp;
    if (data.address) settings.value.address = data.address;
    if (data.facebook) settings.value.facebook = data.facebook;
    if (data.instagram) settings.value.instagram = data.instagram;
    if (data.twitter) settings.value.twitter = data.twitter;
    if (data.tiktok) settings.value.tiktok = data.tiktok;
    if (data.currency) settings.value.currency = data.currency;
    if (data.taxRate) settings.value.taxRate = Number(data.taxRate);
    if (data.order_prefix) settings.value.orderPrefix = data.order_prefix;
    if (data.low_stock_threshold)
      settings.value.lowStockThreshold = Number(data.low_stock_threshold);
    if (data.cod_enabled)
      settings.value.codEnabled = data.cod_enabled === "true";
    if (data.maintenanceMode)
      settings.value.maintenanceMode = data.maintenanceMode === "true";
    if (data.primaryColor) settings.value.primaryColor = data.primaryColor;

    if (data.storeTheme) {
      try {
        settings.value.storeTheme = JSON.parse(data.storeTheme);
      } catch (e) {
        settings.value.storeTheme =
          themePalettes.find((p) => p.primary === data.primaryColor) ||
          themePalettes[0];
      }
    }
  } catch (error) {
    console.error("Error loading settings:", error);
  } finally {
    isLoading.value = false;
  }
};

const saveSettings = async () => {
  isSaving.value = true;
  try {
    const payload = new FormData();
    Object.entries(settings.value).forEach(([key, val]) => {
      if (key === "storeTheme" && val) {
        payload.append(key, JSON.stringify(val));
      } else {
        let keyMapping = {
          orderPrefix: "order_prefix",
          lowStockThreshold: "low_stock_threshold",
          codEnabled: "cod_enabled",
        };
        let finalKey = keyMapping[key] || key;

        payload.append(
          finalKey,
          key === "maintenanceMode" || key === "codEnabled"
            ? val
              ? "true"
              : "false"
            : val
        );
      }
    });

    const file = Array.isArray(logoFile.value)
      ? logoFile.value[0]
      : logoFile.value;
    if (file) {
      payload.append("logo_file", file);
    }

    await axios.post("/admin/settings", payload, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    showMessage("تم حفظ الإعدادات بنجاح! سيتم تحديث الموقع...");
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  } catch (error) {
    console.error("Error saving settings:", error);
    showMessage("حدث خطأ أثناء حفظ الإعدادات", "error");
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  loadSettings();
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

.border-right {
  border-left: 1px solid rgba(var(--v-theme-on-surface), 0.12);
}

.settings-tabs .v-btn {
  text-transform: none !important;
  letter-spacing: normal !important;
  border-radius: 0;
  border-right: 4px solid transparent;
}
.settings-tabs .v-tab--selected {
  background-color: rgb(var(--v-theme-surface));
  border-right: 4px solid rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-primary)) !important;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
.gap.h-10 {
  height: 40px;
}
.theme-gallery-container {
  max-height: 400px;
  overflow-y: auto;
  padding-right: 4px;
}
/* Custom Scrollbar */
.theme-gallery-container::-webkit-scrollbar {
  width: 6px;
}
.theme-gallery-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.theme-gallery-container::-webkit-scrollbar-thumb {
  background: rgb(var(--v-theme-primary));
  border-radius: 10px;
}

.text-right-input :deep(input) {
  text-align: right;
}

@media (max-width: 959px) {
  .border-right {
    border-left: none;
    border-bottom: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  }
  .settings-tabs .v-tab--selected {
    border-right: none;
    border-bottom: 4px solid rgb(var(--v-theme-primary));
  }
}

.cursor-pointer {
  cursor: pointer;
  transition: transform 0.2s;
}
.cursor-pointer:hover {
  transform: scale(1.15);
}
.border-md {
  border: 2px solid transparent;
}
.color-selected {
  outline: 3px solid #000;
  outline-offset: 2px;
  transform: scale(1.15);
}
</style>
