<template>
  <v-app theme="light">
    <v-locale-provider :rtl="isRtl">
      <v-main class="auth-page-wrapper d-flex align-center justify-center">
        <div class="decorative-blob blob-1"></div>
        <div class="decorative-blob blob-2"></div>

        <v-container class="position-relative py-8" style="z-index: 2">
          <v-row justify="center">
            <v-col cols="12" sm="10" md="10" lg="8">
              <v-card class="rounded-2xl premium-glass-card overflow-hidden">
                <v-row no-gutters>
                  <!-- Promo Side -->
                  <v-col
                    cols="12"
                    md="5"
                    class="bg-gradient-primary d-flex flex-column align-center justify-center pa-8 text-center order-1 order-md-1 promo-col"
                  >
                    <v-avatar
                      size="110"
                      class="mb-6 floating-icon promo-avatar"
                    >
                      <v-icon size="60" color="white"
                        >mdi-rocket-launch-outline</v-icon
                      >
                    </v-avatar>
                    <h2
                      class="text-h5 font-weight-black text-white mb-3 outfit-font"
                    >
                      {{ $t("auth.welcome_back") }}
                    </h2>
                    <p
                      class="text-white mb-8 text-body-2 px-2"
                      style="opacity: 0.85; line-height: 1.7"
                    >
                      {{ $t("auth.register.subtitle") }}
                    </p>
                    <v-btn
                      variant="flat"
                      color="white"
                      class="text-primary font-weight-black rounded-xl px-8 white-btn-shadow"
                      size="large"
                      height="52"
                      to="/Auth/LogIn"
                    >
                      <v-icon start>mdi-login</v-icon>
                      {{ $t("auth.register.already_have_account") }}
                    </v-btn>
                  </v-col>

                  <!-- Form Section -->
                  <v-col
                    cols="12"
                    md="7"
                    class="pa-6 pa-md-10 bg-white order-2 order-md-2"
                  >
                    <!-- Brand -->
                    <div class="d-flex align-center mb-5">
                      <v-avatar
                        color="primary"
                        size="36"
                        rounded="lg"
                        class="me-3"
                      >
                        <v-icon color="white" size="20">mdi-storefront</v-icon>
                      </v-avatar>
                      <span
                        class="text-subtitle-1 font-weight-black text-grey-darken-4"
                        >زاّد ستور</span
                      >
                    </div>

                    <div class="mb-5">
                      <h1
                        class="text-h5 font-weight-black text-grey-darken-4 mb-1 outfit-font"
                      >
                        {{ $t("auth.create_account_btn") }}
                      </h1>
                      <p class="text-grey-darken-1 text-body-2">
                        {{ $t("auth.register.subtitle") }}
                      </p>
                    </div>

                    <!-- Error Alert from store -->
                    <v-alert
                      v-if="authStore.error"
                      type="error"
                      variant="tonal"
                      density="compact"
                      class="mb-5 rounded-lg"
                      closable
                    >
                      <template #text>
                        <span class="font-weight-bold">{{
                          authStore.error
                        }}</span>
                      </template>
                    </v-alert>

                    <!-- Success Alert -->
                    <v-alert
                      v-if="successMessage"
                      type="success"
                      variant="tonal"
                      density="compact"
                      class="mb-5 rounded-lg"
                      :text="successMessage"
                    ></v-alert>

                    <v-form
                      ref="formRef"
                      v-model="valid"
                      @submit.prevent="handleRegister"
                    >
                      <v-row dense>
                        <v-col cols="12" sm="6">
                          <v-text-field
                            v-model="form.name"
                            :label="$t('auth.register.full_name')"
                            :placeholder="namePlaceholder"
                            prepend-inner-icon="mdi-account-outline"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            autocomplete="name"
                            :rules="[(v) => !!v || $t('checkout.required')]"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                          <v-text-field
                            v-model="form.email"
                            :label="$t('auth.email')"
                            :placeholder="$t('auth.email_placeholder')"
                            prepend-inner-icon="mdi-email-outline"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            type="email"
                            autocomplete="email"
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                /.+@.+\..+/.test(v) ||
                                $t('contact.email_invalid'),
                            ]"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                          <PhoneInput
                            v-model="form.phone"
                            :label="$t('auth.register.phone')"
                            :placeholder="phonePlaceholder"
                            :rules="[(v) => !!v || $t('checkout.required')]"
                          />
                        </v-col>

                        <v-col cols="12" sm="6">
                          <v-text-field
                            v-model="form.password"
                            :label="$t('auth.password')"
                            prepend-inner-icon="mdi-lock-outline"
                            :type="showPass ? 'text' : 'password'"
                            :append-inner-icon="
                              showPass ? 'mdi-eye-off' : 'mdi-eye'
                            "
                            @click:append-inner="showPass = !showPass"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            autocomplete="new-password"
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                (v && v.length >= 6) ||
                                $t('auth.password_too_short'),
                            ]"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="12" sm="6">
                          <v-text-field
                            v-model="form.password_confirm"
                            :label="$t('auth.register.password_confirm')"
                            prepend-inner-icon="mdi-lock-check-outline"
                            :type="showPass ? 'text' : 'password'"
                            :append-inner-icon="
                              showPass ? 'mdi-eye-off' : 'mdi-eye'
                            "
                            @click:append-inner="showPass = !showPass"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            autocomplete="new-password"
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                v === form.password ||
                                $t('auth.password_mismatch'),
                            ]"
                          ></v-text-field>
                        </v-col>
                      </v-row>

                      <!-- Password Strength -->
                      <div v-if="form.password" class="mb-4">
                        <v-progress-linear
                          :model-value="passwordStrength.score"
                          :color="passwordStrength.color"
                          height="5"
                          rounded
                          class="mb-1"
                        ></v-progress-linear>
                        <span
                          class="text-caption"
                          :class="`text-${passwordStrength.color}`"
                        >
                          {{ passwordStrength.label }}
                        </span>
                      </div>

                      <v-btn
                        block
                        color="primary"
                        size="x-large"
                        type="submit"
                        class="font-weight-black rounded-xl primary-btn-glow mt-2 mb-4"
                        height="52"
                        :loading="loading"
                        :disabled="loading"
                      >
                        <v-icon start>mdi-account-plus</v-icon>
                        {{ $t("auth.register.register_btn") }}
                      </v-btn>

                      <div class="d-flex align-center mb-4">
                        <v-divider></v-divider>
                        <span
                          class="mx-3 text-caption text-grey-darken-1 text-no-wrap"
                          >{{ $t("auth.or") }}</span
                        >
                        <v-divider></v-divider>
                      </div>

                      <v-btn
                        block
                        variant="outlined"
                        color="grey-darken-2"
                        size="large"
                        class="rounded-xl font-weight-bold"
                        height="48"
                        @click="handleGoogleAuth"
                      >
                        <v-icon start color="red">mdi-google</v-icon>
                        {{ $t("auth.login_with_google") }}
                      </v-btn>

                      <div
                        class="text-center text-caption text-grey-darken-1 mt-4 d-flex align-center justify-center gap-1"
                      >
                        <v-icon size="small">mdi-shield-check-outline</v-icon>
                        {{ $t("auth.register.terms_agree") }}
                      </div>

                      <!-- Mobile: Link to Login -->
                      <div class="d-md-none text-center mt-4">
                        <span class="text-body-2 text-grey-darken-1"
                          >{{ $t("auth.have_account") }}
                        </span>
                        <router-link
                          to="/Auth/LogIn"
                          class="text-primary font-weight-bold text-decoration-none"
                          >{{ $t("auth.login_btn") }}</router-link
                        >
                      </div>
                    </v-form>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRegisterStore } from "@/store/auth/Register";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import PhoneInput from "@/components/PhoneInput.vue";

const { t, locale } = useI18n();
const router = useRouter();
const authStore = useRegisterStore();

const isRtl = computed(() => locale.value === "ar");
const namePlaceholder = computed(() =>
  t("auth.register.full_name_placeholder")
);
const phonePlaceholder = computed(() => t("auth.register.phone_placeholder"));

const valid = ref(false);
const formRef = ref(null);
const loading = ref(false);
const showPass = ref(false);
const successMessage = ref("");

const form = ref({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirm: "",
});

// Password strength indicator
const passwordStrength = computed(() => {
  const p = form.value.password;
  if (!p) return { score: 0, color: "grey", label: "" };
  let score = 0;
  if (p.length >= 6) score += 25;
  if (p.length >= 10) score += 25;
  if (/[A-Z]/.test(p)) score += 25;
  if (/[0-9!@#$%^&*]/.test(p)) score += 25;

  if (score <= 25) return { score, color: "error", label: "كلمة مرور ضعيفة" };
  if (score <= 50)
    return { score, color: "warning", label: "كلمة مرور متوسطة" };
  if (score <= 75) return { score, color: "success", label: "كلمة مرور جيدة" };
  return { score, color: "blue", label: "كلمة مرور قوية جداً 💪" };
});

const handleRegister = async () => {
  const { valid: isFormValid } = await formRef.value.validate();
  if (!isFormValid) return;

  loading.value = true;
  successMessage.value = "";

  const result = await authStore.registerUser(form.value);

  if (result.success) {
    successMessage.value = "تم إنشاء حسابك بنجاح! جاري التوجيه...";
    setTimeout(() => router.push("/home"), 1200);
  }

  loading.value = false;
};

const handleGoogleAuth = () => {
  const backendUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
  window.location.href = `${backendUrl}/auth/google`;
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap");

.outfit-font {
  font-family: "Outfit", "Cairo", sans-serif !important;
}

.auth-page-wrapper {
  background: linear-gradient(135deg, #ffffff 0%, #fff3e0 100%);
  min-height: 100vh;
  position: relative;
  overflow: hidden;
}

.rounded-2xl {
  border-radius: 20px !important;
}

.premium-glass-card {
  background: rgba(255, 255, 255, 0.95) !important;
  box-shadow: 0 25px 60px -12px rgba(255, 152, 0, 0.2),
    0 10px 30px rgba(0, 0, 0, 0.08) !important;
}

.bg-gradient-primary {
  background: linear-gradient(145deg, #ff9800 0%, #f44336 100%);
  min-height: 300px;
}

.promo-col {
  position: relative;
  overflow: hidden;
}

.promo-col::before {
  content: "";
  position: absolute;
  top: -40%;
  left: -30%;
  width: 250px;
  height: 250px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
}

.promo-col::after {
  content: "";
  position: absolute;
  bottom: -30%;
  right: -20%;
  width: 200px;
  height: 200px;
  background: rgba(255, 255, 255, 0.06);
  border-radius: 50%;
}

.primary-btn-glow {
  box-shadow: 0 8px 20px rgba(255, 152, 0, 0.35) !important;
  transition: all 0.3s ease;
}
.primary-btn-glow:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(255, 152, 0, 0.5) !important;
}

.white-btn-shadow {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
  transition: all 0.3s ease;
}
.white-btn-shadow:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 28px rgba(0, 0, 0, 0.2) !important;
}

.decorative-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  z-index: 1;
  pointer-events: none;
}
.blob-1 {
  top: -10%;
  left: -5%;
  width: 500px;
  height: 500px;
  background: rgba(255, 152, 0, 0.12);
}
.blob-2 {
  bottom: -10%;
  right: -5%;
  width: 400px;
  height: 400px;
  background: rgba(244, 67, 54, 0.08);
}

.floating-icon {
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-14px);
  }
}

.promo-avatar {
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 0 12px rgba(255, 255, 255, 0.12);
}

.gap-1 {
  gap: 4px;
}
</style>
