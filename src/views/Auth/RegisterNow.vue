<template>
  <v-app theme="light">
    <v-locale-provider :rtl="isRtl">
      <v-main class="auth-page-wrapper d-flex align-center justify-center">
        <!-- Animated Background Elements -->
        <div class="decorative-blob blob-1"></div>
        <div class="decorative-blob blob-2"></div>

        <v-container class="position-relative" style="z-index: 2">
          <v-row justify="center">
            <v-col cols="12" sm="10" md="10" lg="8">
              <v-card class="rounded-xl premium-glass-card overflow-hidden">
                <v-row no-gutters>
                  <!-- Welcome Section -->
                  <v-col
                    cols="12"
                    md="5"
                    class="bg-gradient-primary d-flex flex-column align-center justify-center pa-10 text-center"
                  >
                    <div class="decorative-circles mb-8">
                      <v-icon size="80" color="white" class="floating-icon"
                        >mdi-rocket-launch-outline</v-icon
                      >
                    </div>
                    <h2
                      class="text-h4 font-weight-black text-white mb-4 outfit-font"
                    >
                      {{ $t("auth.welcome_back") }}
                    </h2>
                    <p class="text-white opacity-80 mb-10 text-body-1 px-4">
                      {{ $t("auth.register.subtitle") }}
                    </p>
                    <v-btn
                      variant="flat"
                      color="white"
                      class="text-primary-darken-3 font-weight-black rounded-xl px-12 h-60 white-btn-shadow"
                      size="large"
                      to="/Auth/LogIn"
                    >
                      {{ $t("auth.register.already_have_account") }}
                    </v-btn>
                  </v-col>

                  <!-- Form Section -->
                  <v-col cols="12" md="7" class="pa-8 pa-md-10 bg-white/80">
                    <div class="mb-8">
                      <h1
                        class="text-h4 font-weight-black text-grey-darken-4 mb-2 outfit-font"
                      >
                        {{ $t("auth.create_account_btn") }}
                      </h1>
                      <p class="text-grey-darken-1 text-body-1">
                        {{ $t("auth.register.subtitle") }}
                      </p>
                    </div>

                    <v-alert
                      v-if="authStore.error"
                      type="error"
                      variant="tonal"
                      density="compact"
                      class="mb-6 rounded-lg"
                      closable
                      :text="authStore.error"
                    ></v-alert>

                    <v-form
                      ref="formRef"
                      v-model="valid"
                      @submit.prevent="handleRegister"
                    >
                      <v-row>
                        <v-col cols="12" sm="6" class="py-1">
                          <v-text-field
                            v-model="form.name"
                            :label="$t('auth.register.full_name')"
                            :placeholder="namePlaceholder"
                            prepend-inner-icon="mdi-account-outline"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            :rules="[(v) => !!v || $t('checkout.required')]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-1">
                          <v-text-field
                            v-model="form.email"
                            :label="$t('auth.email')"
                            :placeholder="$t('auth.email_placeholder')"
                            prepend-inner-icon="mdi-email-outline"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                /.+@.+\..+/.test(v) ||
                                $t('contact.email_invalid'),
                            ]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" class="py-1">
                          <v-text-field
                            v-model="form.phone"
                            :label="$t('auth.register.phone')"
                            :placeholder="phonePlaceholder"
                            prepend-inner-icon="mdi-phone-outline"
                            variant="outlined"
                            density="comfortable"
                            color="primary"
                            class="rounded-lg"
                            :rules="[(v) => !!v || $t('checkout.required')]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-1">
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
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                (v && v.length >= 6) ||
                                $t('auth.password_too_short'),
                            ]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6" class="py-1">
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
                            :rules="[
                              (v) => !!v || $t('checkout.required'),
                              (v) =>
                                v === form.password ||
                                $t('auth.password_mismatch'),
                            ]"
                          ></v-text-field>
                        </v-col>
                      </v-row>

                      <v-btn
                        block
                        color="primary"
                        size="x-large"
                        type="submit"
                        class="font-weight-black rounded-xl primary-btn-glow mb-6 mt-4 py-4"
                        :loading="loading"
                      >
                        {{ $t("auth.register.register_btn") }}
                      </v-btn>

                      <div class="d-flex align-center mb-6">
                        <v-divider></v-divider>
                        <span class="mx-4 text-caption text-grey-darken-1">{{
                          $t("auth.or")
                        }}</span>
                        <v-divider></v-divider>
                      </div>

                      <v-btn
                        block
                        variant="outlined"
                        color="grey-darken-2"
                        size="large"
                        class="rounded-xl font-weight-bold"
                        prepend-icon="mdi-google"
                        @click="handleGoogleAuth"
                      >
                        {{ $t("auth.login_with_google") }}
                      </v-btn>

                      <div
                        class="text-center text-caption text-grey-darken-1 mt-8"
                      >
                        <v-icon size="small" class="me-1"
                          >mdi-shield-check-outline</v-icon
                        >
                        {{ $t("auth.register.terms_agree") }}
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

const form = ref({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirm: "",
});

const handleRegister = async () => {
  const { valid: isFormValid } = await formRef.value.validate();
  if (!isFormValid) return;

  loading.value = true;
  const result = await authStore.registerUser(form.value);

  if (result.success) {
    router.push("/home");
  }
  loading.value = false;
};

const handleGoogleAuth = () => {
  // Redirect to backend socialite route
  window.location.href = "http://127.0.0.1:8000/auth/google";
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

.bg-gradient-primary {
  background: linear-gradient(135deg, #ff9800 0%, #f44336 100%);
}

.premium-glass-card {
  background: rgba(255, 255, 255, 0.8) !important;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 25px 50px -12px rgba(255, 152, 0, 0.25) !important;
}

.primary-btn-glow {
  box-shadow: 0 10px 20px rgba(255, 152, 0, 0.3) !important;
  transition: all 0.3s ease;
}

.primary-btn-glow:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(255, 152, 0, 0.5) !important;
}

.white-btn-shadow {
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
  transition: all 0.3s ease;
}

.white-btn-shadow:hover {
  transform: translateY(-3px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

.h-60 {
  height: 60px !important;
}

/* الأشكال الديكورية الخلفية */
.decorative-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  z-index: 1;
}
.blob-1 {
  top: -10%;
  left: -5%;
  width: 500px;
  height: 500px;
  background: rgba(255, 152, 0, 0.15);
}
.blob-2 {
  bottom: -10%;
  right: -5%;
  width: 400px;
  height: 400px;
  background: rgba(244, 67, 54, 0.1);
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
    transform: translateY(-15px);
  }
}

.opacity-80 {
  opacity: 0.8;
}
</style>
