<template>
  <v-app theme="light">
    <v-locale-provider :rtl="isRtl">
      <v-main class="auth-page-wrapper d-flex align-center justify-center">
        <!-- Animated Background -->
        <div class="decorative-blob blob-1"></div>
        <div class="decorative-blob blob-2"></div>

        <v-container class="position-relative py-8" style="z-index: 2">
          <v-row justify="center">
            <v-col cols="12" sm="10" md="9" lg="7">
              <v-card class="rounded-2xl premium-glass-card overflow-hidden">
                <v-row no-gutters>
                  <!-- Form Section -->
                  <v-col
                    cols="12"
                    md="7"
                    class="pa-6 pa-md-12 bg-white order-2 order-md-1"
                  >
                    <!-- Logo / Brand -->
                    <div class="d-flex align-center mb-6">
                      <v-avatar
                        color="primary"
                        size="40"
                        rounded="lg"
                        class="me-3"
                      >
                        <v-icon color="white">mdi-storefront</v-icon>
                      </v-avatar>
                      <span
                        class="text-h6 font-weight-black text-grey-darken-4"
                        >{{ $t("auth.brand_name") }}</span
                      >
                    </div>

                    <div class="mb-6">
                      <h1
                        class="text-h4 font-weight-black text-grey-darken-4 mb-1 outfit-font"
                      >
                        {{ $t("auth.welcome_back") }}
                      </h1>
                      <p class="text-grey-darken-1 text-body-2">
                        {{ $t("auth.login_subtitle") }}
                      </p>
                    </div>

                    <!-- Context alert (redirect messages) -->
                    <v-alert
                      v-if="contextMessage"
                      type="info"
                      variant="tonal"
                      density="compact"
                      class="mb-5 rounded-lg"
                      :text="contextMessage"
                      closable
                      @click:close="contextMessage = ''"
                    ></v-alert>

                    <!-- Error Alert -->
                    <v-alert
                      v-if="error"
                      type="error"
                      variant="tonal"
                      density="compact"
                      class="mb-5 rounded-lg"
                      closable
                      @click:close="error = ''"
                    >
                      <template #text>
                        <span class="font-weight-bold">{{ error }}</span>
                      </template>
                    </v-alert>

                    <v-form
                      ref="formRef"
                      v-model="valid"
                      @submit.prevent="handleLogin"
                    >
                      <v-text-field
                        v-model="form.email"
                        :label="$t('auth.email')"
                        :placeholder="$t('auth.email_placeholder')"
                        prepend-inner-icon="mdi-email-outline"
                        variant="outlined"
                        density="comfortable"
                        color="primary"
                        class="mb-4 rounded-lg"
                        autocomplete="email"
                        :rules="[
                          (v) => !!v || $t('checkout.required'),
                          (v) =>
                            /.+@.+\..+/.test(v) || $t('contact.email_invalid'),
                        ]"
                      ></v-text-field>

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
                        class="mb-2 rounded-lg"
                        autocomplete="current-password"
                        :rules="[(v) => !!v || $t('checkout.required')]"
                      ></v-text-field>

                      <div
                        class="d-flex align-center justify-space-between mb-6"
                      >
                        <v-checkbox
                          v-model="form.rememberMe"
                          :label="$t('auth.remember_me')"
                          color="primary"
                          density="compact"
                          hide-details
                          class="ms-n2"
                        ></v-checkbox>
                        <router-link
                          to="/Auth/ForgetPassword"
                          class="text-caption text-primary font-weight-bold text-decoration-none hover-underline"
                          >{{ $t("auth.forgot_password") }}</router-link
                        >
                      </div>

                      <v-btn
                        block
                        color="primary"
                        size="x-large"
                        type="submit"
                        class="font-weight-black rounded-xl primary-btn-glow mb-4"
                        height="52"
                        :loading="loading"
                        :disabled="loading"
                      >
                        <v-icon start>mdi-login</v-icon>
                        {{ $t("auth.login_btn") }}
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
                        class="rounded-xl font-weight-bold mb-4"
                        height="48"
                        @click="handleGoogleAuth"
                      >
                        <v-icon start color="red">mdi-google</v-icon>
                        {{ $t("auth.login_with_google") }}
                      </v-btn>

                      <!-- Mobile: Link to Register -->
                      <div class="d-md-none text-center mt-4">
                        <span class="text-body-2 text-grey-darken-1"
                          >{{ $t("auth.no_account") }}
                        </span>
                        <router-link
                          to="/Auth/RegisterNow"
                          class="text-primary font-weight-bold text-decoration-none"
                          >{{ $t("auth.create_account_btn") }}</router-link
                        >
                      </div>
                    </v-form>
                  </v-col>

                  <!-- Promo Side -->
                  <v-col
                    cols="12"
                    md="5"
                    class="bg-gradient-primary d-flex flex-column align-center justify-center pa-8 text-center order-1 order-md-2 promo-col"
                  >
                    <v-avatar
                      size="120"
                      class="mb-6 floating-icon promo-avatar"
                    >
                      <v-img
                        src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png"
                        cover
                        class="filter-white"
                      ></v-img>
                    </v-avatar>
                    <h2
                      class="text-h5 font-weight-black text-white mb-3 outfit-font"
                    >
                      {{ $t("auth.new_here") }}
                    </h2>
                    <p
                      class="text-white mb-8 text-body-2 px-2"
                      style="opacity: 0.85; line-height: 1.7"
                    >
                      {{ $t("auth.register_subtitle") }}
                    </p>
                    <v-btn
                      variant="flat"
                      color="white"
                      class="text-primary font-weight-black rounded-xl px-8 white-btn-shadow"
                      size="large"
                      height="52"
                      to="/Auth/RegisterNow"
                    >
                      <v-icon start>mdi-account-plus</v-icon>
                      {{ $t("auth.create_account_btn") }}
                    </v-btn>
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
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/store/auth/LogIn";
import { AddInCart } from "@/store/Cart";
import { AddFavProduct } from "@/store/Favourate";
import { useI18n } from "vue-i18n";
import { useSettingsStore } from "@/store/Settings";

const { t, locale } = useI18n();
const router = useRouter();
const route = useRoute();
const auth = useAuthStore();
const cartStore = AddInCart();
const favStore = AddFavProduct();
const settingsStore = useSettingsStore();

const isRtl = computed(() => locale.value === "ar");

const showPass = ref(false);
const error = ref("");
const contextMessage = ref("");
const loading = ref(false);
const valid = ref(false);
const formRef = ref(null);

const form = ref({
  email: "",
  password: "",
  rememberMe: false,
});

onMounted(() => {
  settingsStore.fetchSettings();
  if (route.query.redirect === "cart") {
    contextMessage.value = t("auth.login_required_cart");
  } else if (route.query.redirect === "favorites") {
    contextMessage.value = t("auth.login_required_favorites");
  }
});

const handleLogin = async () => {
  const { valid: isFormValid } = await formRef.value.validate();
  if (!isFormValid) return;

  error.value = "";
  loading.value = true;

  try {
    const result = await auth.login(form.value);

    if (!result.success) {
      error.value = result.message;
      return;
    }

    const user = auth.currentUser || JSON.parse(localStorage.getItem("user"));
    if (user && (user.role === "admin" || user.role === "moderator")) {
      router.push("/Dashboard/AdminDashboard");
    } else {
      await Promise.all([cartStore.fetchCart(), favStore.fetchFavorites()]);
      const redirect = route.query.returnTo || "/home";
      router.push(redirect);
    }
  } catch (err) {
    error.value = t("auth.unexpected_error");
  } finally {
    loading.value = false;
  }
};

const handleGoogleAuth = () => {
  const backendUrl = process.env.VUE_APP_API_URL || "http://127.0.0.1:8000";
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
  min-height: 320px;
}

.promo-col {
  position: relative;
  overflow: hidden;
}

.promo-col::before {
  content: "";
  position: absolute;
  top: -50%;
  right: -30%;
  width: 250px;
  height: 250px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
}

.promo-col::after {
  content: "";
  position: absolute;
  bottom: -30%;
  left: -20%;
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

.filter-white {
  filter: brightness(0) invert(1);
}

.hover-underline:hover {
  text-decoration: underline !important;
}

.promo-avatar {
  box-shadow: 0 0 0 12px rgba(255, 255, 255, 0.15);
}
</style>
