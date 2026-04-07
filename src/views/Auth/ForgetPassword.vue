<template>
  <v-app theme="light">
    <v-locale-provider :rtl="isRtl">
      <v-main
        class="bg-grey-lighten-5 d-flex align-center justify-center"
        style="min-height: 100vh"
      >
        <v-container style="margin-top: -100px">
          <v-row justify="center">
            <v-col cols="12" sm="10" md="8" lg="5">
              <v-card class="rounded-xl elevation-5 overflow-hidden">
                <v-progress-linear
                  :model-value="(step / 3) * 100"
                  color="primary"
                  height="6"
                ></v-progress-linear>

                <v-card-text class="pa-8">
                  <v-window v-model="step">
                    <v-window-item :value="1">
                      <div class="text-center mb-6">
                        <v-avatar
                          color="primary-lighten-5"
                          size="80"
                          class="mb-4"
                        >
                          <v-icon color="primary" size="40"
                            >mdi-lock-reset</v-icon
                          >
                        </v-avatar>
                        <h2
                          class="text-h5 font-weight-bold text-primary-darken-4"
                        >
                          {{ $t("auth.forgot_password_page.title") }}
                        </h2>
                        <p class="text-grey-darken-1 mt-2">
                          {{ $t("auth.forgot_password_page.subtitle") }}
                        </p>
                      </div>

                      <v-form @submit.prevent="handleSendCode">
                        <v-text-field
                          v-model="form.emailOrPhone"
                          :label="
                            $t('auth.forgot_password_page.email_phone_label')
                          "
                          prepend-inner-icon="mdi-account-box-outline"
                          variant="outlined"
                          color="primary"
                          class="mb-4"
                          :rules="[
                            (v) =>
                              !!v ||
                              $t('auth.forgot_password_page.required_field'),
                          ]"
                        ></v-text-field>

                        <v-btn
                          block
                          color="primary"
                          size="large"
                          type="submit"
                          class="rounded-lg font-weight-bold"
                          :loading="loading"
                        >
                          {{ $t("auth.forgot_password_page.send_code_btn") }}
                        </v-btn>
                      </v-form>
                    </v-window-item>

                    <v-window-item :value="2">
                      <div class="text-center mb-6">
                        <h2
                          class="text-h5 font-weight-bold text-primary-darken-4"
                        >
                          {{ $t("auth.forgot_password_page.otp_title") }}
                        </h2>
                        <p class="text-grey-darken-1 mt-2">
                          {{ $t("auth.forgot_password_page.otp_subtitle") }}
                          <br />
                          <b class="text-primary">{{ form.emailOrPhone }}</b>
                        </p>
                      </div>

                      <v-form @submit.prevent="handleVerifyOtp">
                        <div class="d-flex justify-center mb-6">
                          <v-otp-input
                            v-model="form.otp"
                            length="5"
                            variant="outlined"
                            color="primary"
                            class="otp-field"
                            autofocus
                          ></v-otp-input>
                        </div>

                        <v-btn
                          block
                          color="primary"
                          size="large"
                          type="submit"
                          class="rounded-lg font-weight-bold mb-4"
                          :loading="loading"
                          :disabled="form.otp.length < 5"
                        >
                          {{ $t("auth.forgot_password_page.verify_btn") }}
                        </v-btn>

                        <div class="text-center">
                          <p class="text-caption text-grey">
                            {{ $t("auth.forgot_password_page.not_received") }}
                          </p>
                          <v-btn
                            variant="text"
                            color="primary"
                            size="small"
                            :disabled="timer > 0"
                            @click="resendCode"
                          >
                            {{ resendText }}
                          </v-btn>
                        </div>
                      </v-form>
                    </v-window-item>

                    <v-window-item :value="3">
                      <div class="text-center mb-6">
                        <v-avatar
                          color="green-lighten-5"
                          size="80"
                          class="mb-4"
                        >
                          <v-icon color="green" size="40"
                            >mdi-check-decagram</v-icon
                          >
                        </v-avatar>
                        <h2
                          class="text-h5 font-weight-bold text-primary-darken-4"
                        >
                          {{
                            $t("auth.forgot_password_page.new_password_title")
                          }}
                        </h2>
                        <p class="text-grey-darken-1 mt-2">
                          {{
                            $t(
                              "auth.forgot_password_page.new_password_subtitle"
                            )
                          }}
                        </p>
                      </div>

                      <v-form
                        ref="formRef"
                        @submit.prevent="handleResetPassword"
                      >
                        <v-text-field
                          v-model="form.newPassword"
                          :label="
                            $t('auth.forgot_password_page.new_password_label')
                          "
                          prepend-inner-icon="mdi-lock-outline"
                          variant="outlined"
                          color="primary"
                          :type="showPass ? 'text' : 'password'"
                          :append-inner-icon="
                            showPass ? 'mdi-eye-off' : 'mdi-eye'
                          "
                          @click:append-inner="showPass = !showPass"
                          class="mb-2"
                          :rules="[
                            (v) => !!v || $t('checkout.required'),
                            (v) =>
                              (v && v.length >= 6) ||
                              $t('auth.password_too_short'),
                          ]"
                        ></v-text-field>

                        <v-text-field
                          v-model="form.confirmPassword"
                          :label="$t('auth.register.password_confirm')"
                          prepend-inner-icon="mdi-lock-check-outline"
                          variant="outlined"
                          color="primary"
                          type="password"
                          class="mb-6"
                          :rules="[
                            (v) => !!v || $t('checkout.required'),
                            (v) =>
                              v === form.newPassword ||
                              $t('auth.password_mismatch'),
                          ]"
                        ></v-text-field>

                        <v-btn
                          block
                          color="primary"
                          size="large"
                          type="submit"
                          class="rounded-lg font-weight-bold"
                          :loading="loading"
                        >
                          {{ $t("auth.forgot_password_page.change_btn") }}
                        </v-btn>
                      </v-form>
                    </v-window-item>

                    <v-window-item :value="4">
                      <div class="text-center py-10">
                        <v-icon size="100" color="success" class="mb-4"
                          >mdi-checkbox-marked-circle-outline</v-icon
                        >
                        <h2 class="text-h4 font-weight-bold text-success mb-2">
                          {{ $t("auth.forgot_password_page.success_title") }}
                        </h2>
                        <p class="text-grey mb-8">
                          {{ $t("auth.forgot_password_page.success_desc") }}
                        </p>

                        <v-btn
                          to="/Auth/LogIn"
                          color="primary"
                          size="large"
                          rounded="pill"
                          class="px-10"
                        >
                          {{ $t("auth.forgot_password_page.login_page_btn") }}
                        </v-btn>
                      </div>
                    </v-window-item>
                  </v-window>
                </v-card-text>

                <v-card-actions
                  v-if="step < 4"
                  class="justify-center bg-grey-lighten-4 py-3"
                >
                  <v-btn
                    variant="plain"
                    color="grey-darken-1"
                    size="small"
                    @click="goBack"
                  >
                    <v-icon
                      :icon="isRtl ? 'mdi-arrow-right' : 'mdi-arrow-left'"
                      start
                    ></v-icon>
                    {{ backButtonText }}
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref, reactive, onUnmounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const router = useRouter();
const step = ref(1);
const loading = ref(false);
const showPass = ref(false);
const passwordError = ref("");
const timer = ref(0);
let intervalId = null;

const isRtl = computed(() => locale.value === "ar");

const backButtonText = computed(() => {
  return step.value === 1
    ? t("auth.forgot_password_page.back_to_login")
    : t("auth.forgot_password_page.back_to_prev");
});

const resendText = computed(() => {
  return timer.value > 0
    ? t("auth.forgot_password_page.resend_timer", { timer: timer.value })
    : t("auth.forgot_password_page.resend_btn");
});

// بيانات النموذج
const form = reactive({
  emailOrPhone: "",
  otp: "",
  newPassword: "",
  confirmPassword: "",
});

// --- Actions ---

// 1. إرسال الكود
const handleSendCode = () => {
  if (!form.emailOrPhone) return;

  loading.value = true;
  // محاكاة الاتصال بالـ API
  setTimeout(() => {
    loading.value = false;
    step.value = 2; // الانتقال للخطوة التالية
    startTimer(); // بدء عداد إعادة الإرسال
  }, 1500);
};

// 2. التحقق من الرمز
const handleVerifyOtp = () => {
  loading.value = true;
  setTimeout(() => {
    loading.value = false;
    // هنا نتحقق من صحة الكود
    step.value = 3; // الانتقال لصفحة تغيير الباسورد
  }, 1500);
};

// 3. تغيير كلمة المرور
const formRef = ref(null);

const handleResetPassword = async () => {
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  passwordError.value = "";

  loading.value = true;
  setTimeout(() => {
    loading.value = false;
    step.value = 4; // الانتقال لشاشة النجاح
  }, 1500);
};

// زر الرجوع
const goBack = () => {
  if (step.value === 1) {
    router.push("/Auth/LogIn"); // الرجوع لصفحة الدخول
  } else {
    step.value--; // الرجوع خطوة للوراء
  }
};

// منطق عداد إعادة الإرسال
const startTimer = () => {
  timer.value = 60;
  clearInterval(intervalId);
  intervalId = setInterval(() => {
    if (timer.value > 0) {
      timer.value--;
    } else {
      clearInterval(intervalId);
    }
  }, 1000);
};

const resendCode = () => {
  // كود إعادة إرسال الرمز هنا
  startTimer();
  console.log("Resending code...");
};

onUnmounted(() => {
  clearInterval(intervalId);
});
</script>

<style scoped>
/* تنسيق خاص لحقل OTP ليكون في المنتصف */
.otp-field :deep(input) {
  text-align: center;
  font-size: 1.5rem;
  letter-spacing: 1rem;
  font-weight: bold;
}
</style>
