<template>
  <v-locale-provider rtl>
    <v-container>
      <v-card class="pa-5" elevation="3" :loading="loading">
        <v-card-title class="text-h5 mb-4 font-weight-bold">
          إضافة تاجر جديد
        </v-card-title>

        <v-form @submit.prevent="submitMerchant" ref="merchantForm">
          <h3 class="text-h6 mb-3 text-primary">البيانات الأساسية</h3>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="merchant.fullName"
                label="اسم التاجر بالكامل (الاسم الشخصي)"
                variant="outlined"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="merchant.storeName"
                label="اسم المتجر / الشركة"
                variant="outlined"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <PhoneInput
                v-model="merchant.phone"
                label="رقم التليفون"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="merchant.email"
                label="البريد الإلكتروني (لتسجيل الدخول)"
                type="email"
                variant="outlined"
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="merchant.password"
                label="كلمة المرور (دخول التاجر لحسابه)"
                type="password"
                variant="outlined"
                required
              ></v-text-field>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <h3 class="text-h6 mb-3 text-primary">بيانات النشاط التجاري</h3>
          <v-row>
            <v-col cols="12" md="4">
              <v-select
                v-model="merchant.category"
                :items="categories"
                label="تصنيف المتجر"
                variant="outlined"
              ></v-select>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="merchant.taxId"
                label="رقم البطاقة الضريبية"
                variant="outlined"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="merchant.commercialRecord"
                label="رقم السجل التجاري"
                variant="outlined"
              ></v-text-field>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <h3 class="text-h6 mb-3 text-primary">العنوان والموقع</h3>
          <v-row>
            <v-col cols="12" md="4">
              <v-select
                v-model="merchant.city"
                :items="cities"
                label="المحافظة / المدينة"
                variant="outlined"
              ></v-select>
            </v-col>
            <v-col cols="12" md="8">
              <v-textarea
                v-model="merchant.address"
                label="العنوان بالتفصيل"
                rows="2"
                variant="outlined"
              ></v-textarea>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <h3 class="text-h6 mb-3 text-primary">المرفقات والأوراق الرسمية</h3>
          <v-row>
            <v-col cols="12" md="6">
              <v-file-input
                v-model="merchant.logo"
                label="شعار المتجر (Logo)"
                accept="image/*"
                prepend-icon="mdi-camera"
                variant="outlined"
              ></v-file-input>
            </v-col>
            <v-col cols="12" md="6">
              <v-file-input
                v-model="merchant.documents"
                label="صور الأوراق (سجل/بطاقة)"
                accept="image/*,.pdf"
                prepend-icon="mdi-file-document-multiple"
                variant="outlined"
                multiple
              ></v-file-input>
            </v-col>
          </v-row>

          <v-card-actions class="mt-5 justify-end">
            <v-btn
              color="error"
              variant="text"
              @click="resetForm"
              :disabled="loading"
              >مسح البيانات</v-btn
            >
            <v-btn
              color="primary"
              type="submit"
              variant="elevated"
              size="large"
              :loading="loading"
              >حفظ بيانات التاجر</v-btn
            >
          </v-card-actions>
        </v-form>
      </v-card>

      <!-- Snackbar للملاحظات -->
      <v-snackbar
        v-model="snackbar.show"
        :color="snackbar.color"
        timeout="3000"
      >
        {{ snackbar.text }}
        <template v-slot:actions>
          <v-btn color="white" variant="text" @click="snackbar.show = false">
            إغلاق
          </v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </v-locale-provider>
</template>

<script setup>
import { ref } from "vue";
import axios from "@/axios";
import PhoneInput from "@/components/PhoneInput.vue";

const merchantForm = ref(null);
const loading = ref(false);
const snackbar = ref({ show: false, text: "", color: "success" });

const merchant = ref({
  fullName: "",
  storeName: "",
  phone: "",
  email: "",
  password: "",
  category: null,
  taxId: "",
  commercialRecord: "",
  city: null,
  address: "",
  logo: null,
  documents: [],
});

const categories = [
  "إلكترونيات",
  "ملابس وأزياء",
  "أغذية ومشروبات",
  "صحة وجمال",
  "أخرى",
];
const cities = ["القاهرة", "الجيزة", "الإسكندرية", "المنصورة", "أخرى"];

const showMessage = (text, color = "success") => {
  snackbar.value = { show: true, text, color };
};

const submitMerchant = async () => {
  if (!merchantForm.value) return;
  const { valid } = await merchantForm.value.validate();
  if (!valid) return;

  loading.value = true;
  try {
    const payload = new FormData();
    payload.append("fullName", merchant.value.fullName);
    payload.append("storeName", merchant.value.storeName);
    payload.append("phone", merchant.value.phone);
    if (merchant.value.email) payload.append("email", merchant.value.email);
    if (merchant.value.password)
      payload.append("password", merchant.value.password);
    if (merchant.value.category)
      payload.append("category", merchant.value.category);
    if (merchant.value.taxId) payload.append("taxId", merchant.value.taxId);
    if (merchant.value.commercialRecord)
      payload.append("commercialRecord", merchant.value.commercialRecord);
    if (merchant.value.city) payload.append("city", merchant.value.city);
    if (merchant.value.address)
      payload.append("address", merchant.value.address);

    const lFile = Array.isArray(merchant.value.logo)
      ? merchant.value.logo[0]
      : merchant.value.logo;
    if (lFile) {
      payload.append("logo", lFile);
    }

    if (merchant.value.documents && merchant.value.documents.length) {
      merchant.value.documents.forEach((file) => {
        payload.append("documents[]", file);
      });
    }

    await axios.post("/admin/merchants", payload, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    showMessage("تم إضافة التاجر وإنشاء حسابه بنجاح!");
    // reset after success
    merchant.value = {
      fullName: "",
      storeName: "",
      phone: "",
      email: "",
      password: "",
      category: null,
      taxId: "",
      commercialRecord: "",
      city: null,
      address: "",
      logo: null,
      documents: [],
    };
    resetForm();
  } catch (error) {
    console.error(error);
    if (error.response?.status === 422) {
      showMessage(
        "تأكد من البيانات المُدخلة، قد يكون البريد الإلكتروني مسجل مسبقاً",
        "error"
      );
    } else {
      showMessage("حدث خطأ أثناء حفظ التاجر بالنظام", "error");
    }
  } finally {
    loading.value = false;
  }
};

const resetForm = () => {
  if (merchantForm.value) merchantForm.value.reset();
};
</script>
