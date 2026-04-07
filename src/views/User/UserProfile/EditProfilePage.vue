<template>
  <v-app theme="light">
    <v-locale-provider rtl>
      <v-main class="bg-grey-lighten-5">
        <v-container class="py-10">
          <div class="d-flex align-center mb-6">
            <v-btn
              icon="mdi-arrow-right"
              variant="text"
              to="/User/UserProfile/ProfilePage"
              class="me-2"
            ></v-btn>
            <div>
              <h1 class="text-h4 font-weight-bold text-primary-darken-3">
                تعديل الملف الشخصي
              </h1>
              <p class="text-grey-darken-1">
                قم بتحديث بياناتك الشخصية وإعدادات الحساب
              </p>
            </div>
          </div>

          <v-row>
            <v-col cols="12" md="4">
              <v-card class="rounded-xl text-center pa-6 mb-4" elevation="1">
                <div class="position-relative d-inline-block">
                  <v-avatar size="120" class="border">
                    <v-img
                      :src="avatarPreview || 'https://i.pravatar.cc/300?img=12'"
                    ></v-img>
                  </v-avatar>
                  <v-btn
                    icon="mdi-camera"
                    size="small"
                    color="primary"
                    class="position-absolute bottom-0 right-0 elevation-2"
                    @click="$refs.fileInput.click()"
                  ></v-btn>
                  <input
                    type="file"
                    ref="fileInput"
                    name="avatar"
                    class="d-none"
                    @change="onFileChange"
                    accept="image/*"
                  />
                </div>
                <h3 class="text-h6 font-weight-bold mt-4">
                  {{ formData.name || "مستخدم جديد" }}
                </h3>
                <p class="text-caption text-grey">
                  يسمح بـ JPG, GIF or PNG. بحد أقصى 2MB
                </p>
              </v-card>

              <v-card class="rounded-xl pa-4" title="الإشعارات">
                <v-switch
                  v-model="notifications.email"
                  color="primary"
                  label="إشعارات البريد الإلكتروني"
                  hide-details
                  density="compact"
                ></v-switch>
                <v-switch
                  v-model="notifications.sms"
                  color="primary"
                  label="رسائل SMS"
                  hide-details
                  density="compact"
                ></v-switch>
                <v-switch
                  v-model="notifications.offers"
                  color="orange"
                  label="العروض والخصومات"
                  hide-details
                  density="compact"
                ></v-switch>
              </v-card>
            </v-col>

            <v-col cols="12" md="8">
              <v-card class="rounded-xl pa-6" elevation="1">
                <v-form
                  ref="formRef"
                  v-model="valid"
                  @submit.prevent="saveChanges"
                >
                  <h3
                    class="text-h6 font-weight-bold text-primary mb-4 d-flex align-center"
                  >
                    <v-icon start>mdi-account-details-outline</v-icon> البيانات
                    الأساسية
                  </h3>

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.name"
                        label="الاسم بالكامل"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        :rules="[(v) => !!v || 'الاسم مطلوب']"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.username"
                        label="اسم المستخدم"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        hint="يستخدم في رابط الإحالة الخاص بك"
                        :rules="[(v) => !!v || 'اسم المستخدم مطلوب']"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.email"
                        label="البريد الإلكتروني"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        prepend-inner-icon="mdi-email-outline"
                        :rules="[
                          (v) => !!v || 'البريد الإلكتروني مطلوب',
                          (v) =>
                            /.+@.+\..+/.test(v) || 'بريد إلكتروني غير صحيح',
                        ]"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.phone"
                        label="رقم الهاتف"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        prepend-inner-icon="mdi-phone-outline"
                        :rules="[
                          (v) => !!v || 'رقم الهاتف مطلوب',
                          (v) =>
                            /^[0-9]{11}$/.test(v) ||
                            'رقم هاتف غير صحيح (11 رقم)',
                        ]"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="formData.address"
                        label="العنوان"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        prepend-inner-icon="mdi-map-marker-outline"
                        :rules="[(v) => !!v || 'العنوان مطلوب']"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.preferred_shipping_company"
                        label="شركة الشحن المفضلة"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        prepend-inner-icon="mdi-truck-delivery-outline"
                        placeholder="مثال: Aramex, DHL"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        v-model="formData.bio"
                        label="نبذة تعريفية"
                        rows="3"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        placeholder="اكتب نبذة عن خبراتك..."
                      ></v-textarea>
                    </v-col>
                  </v-row>

                  <v-divider class="my-6"></v-divider>

                  <h3
                    class="text-h6 font-weight-bold text-primary mb-4 d-flex align-center"
                  >
                    <v-icon start>mdi-lock-outline</v-icon> الأمان وكلمة المرور
                  </h3>

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        type="password"
                        v-model="formData.password"
                        label="كلمة المرور الجديدة"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        autocomplete="new-password"
                        hint="اتركها فارغة إذا لم تكن تريد تغيير كلمة المرور"
                        :rules="[
                          (v) =>
                            !v ||
                            v.length >= 6 ||
                            'كلمة المرور يجب أن لا تقل عن 6 أحرف',
                        ]"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        type="password"
                        v-model="formData.password_confirmation"
                        label="تأكيد كلمة المرور"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        autocomplete="new-password"
                        :rules="[
                          (v) =>
                            !formData.password ||
                            v === formData.password ||
                            'كلمة المرور غير متطابقة',
                        ]"
                      ></v-text-field>
                    </v-col>
                  </v-row>

                  <div class="d-flex justify-end gap-3 mt-6">
                    <v-btn
                      variant="text"
                      size="large"
                      to="/User/UserProfile/ProfilePage"
                      >إلغاء</v-btn
                    >
                    <v-btn
                      color="primary"
                      size="large"
                      type="submit"
                      :loading="loading"
                      elevation="2"
                      >حفظ التغييرات</v-btn
                    >
                  </div>
                </v-form>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref, onMounted, inject, watch } from "vue";
import { useAuthStore } from "@/store/auth/LogIn";
import api from "@/axios";
import { useRouter } from "vue-router";

const emitter = inject("emitter");

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const avatarPreview = ref(null);
const fileInput = ref(null);
const fileData = ref(null);

const formData = ref({
  name: "",
  username: "",
  email: "",
  phone: "",
  address: "",
  preferred_shipping_company: "",
  bio: "",
  password: "",
  password_confirmation: "",
});

const notifications = ref({
  email: true,
  sms: false,
  offers: true,
});

const initForm = (user) => {
  if (!user) return;
  formData.value.name = user.name || "";
  formData.value.username = user.username || "";
  formData.value.email = user.email || "";
  formData.value.phone = user.phone || "";
  formData.value.address = user.address || "";
  formData.value.preferred_shipping_company =
    user.preferred_shipping_company || "";
  formData.value.bio = user.bio || "";

  if (user.avatar) {
    avatarPreview.value = user.avatar.startsWith("http")
      ? user.avatar
      : `http://localhost:8000${user.avatar}`;
  }

  if (user.notifications_prefs) {
    const prefs =
      typeof user.notifications_prefs === "string"
        ? JSON.parse(user.notifications_prefs)
        : user.notifications_prefs;

    notifications.value.email = !!(
      prefs.email === true ||
      prefs.email === "true" ||
      prefs.email === "1" ||
      prefs.email === 1
    );
    notifications.value.sms = !!(
      prefs.sms === true ||
      prefs.sms === "true" ||
      prefs.sms === "1" ||
      prefs.sms === 1
    );
    notifications.value.offers = !!(
      prefs.offers === true ||
      prefs.offers === "true" ||
      prefs.offers === "1" ||
      prefs.offers === 1
    );
  }
};

onMounted(() => {
  if (authStore.currentUser) {
    initForm(authStore.currentUser);
  }
});

// مراقبة التغيرات في المستخدم (مهم جداً عند عمل Refresh للصفحة)
watch(
  () => authStore.currentUser,
  (newUser) => {
    if (newUser) initForm(newUser);
  },
  { immediate: true }
);

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    fileData.value = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
};

const valid = ref(false);
const formRef = ref(null);

const saveChanges = async () => {
  const { valid: isFormValid } = await formRef.value.validate();
  if (!isFormValid) return;

  loading.value = true;
  try {
    const data = new FormData();
    data.append("_method", "PUT");
    data.append("name", formData.value.name);
    data.append("email", formData.value.email); // إضافة الحقل المفقود
    data.append("username", formData.value.username);
    data.append("phone", formData.value.phone);
    data.append("address", formData.value.address);
    data.append(
      "preferred_shipping_company",
      formData.value.preferred_shipping_company
    );
    data.append("bio", formData.value.bio);

    if (formData.value.password && formData.value.password.trim() !== "") {
      data.append("password", formData.value.password.trim());
      data.append(
        "password_confirmation",
        formData.value.password_confirmation.trim()
      );
    }

    if (fileData.value) {
      data.append("avatar", fileData.value);
    }

    data.append(
      "notifications[email]",
      notifications.value.email ? "true" : "false"
    );
    data.append(
      "notifications[sms]",
      notifications.value.sms ? "true" : "false"
    );
    data.append(
      "notifications[offers]",
      notifications.value.offers ? "true" : "false"
    );

    const { data: responseData } = await api.post("/user/profile", data, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    localStorage.setItem("user", JSON.stringify(responseData.user));
    authStore.currentUser = responseData.user;

    emitter.emit("showMessage", {
      text: "تم حفظ البيانات بنجاح!",
      color: "success",
    });
    router.push("/User/UserProfile/ProfilePage");
  } catch (error) {
    emitter.emit("showMessage", {
      text:
        "حدث خطأ أثناء رفع البيانات يرجى التأكد من صحة المدخلات. " +
        (error.response?.data?.message || ""),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.gap-3 {
  gap: 12px;
}
</style>
