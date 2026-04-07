<template>
  <div class="guest-home-wrapper">
    <div
      class="hero-bg position-relative overflow-hidden pt-8 pb-8 pt-md-16 pb-md-16"
    >
      <!-- Animated Background Elements -->
      <div class="decorative-blob blob-1"></div>
      <div class="decorative-blob blob-2"></div>

      <v-container
        class="pt-10 pt-md-16 mt-5 mt-md-10 position-relative"
        style="z-index: 2"
      >
        <v-row align="center">
          <v-col cols="12" md="6" class="text-center text-md-start">
            <v-chip
              color="primary"
              variant="flat"
              class="mb-6 font-weight-black shadow-sm pulse-slow"
              rounded="lg"
            >
              {{ $t("home.hero_chip") }}
            </v-chip>
            <h1
              class="text-h2 text-md-h1 font-weight-black text-grey-darken-4 mb-6 lh-tight outfit-font"
            >
              {{ $t("home.hero_title_1") }} <br />
              <span class="text-gradient">{{ $t("home.hero_title_2") }}</span>
            </h1>
            <p
              class="text-h6 text-grey-darken-1 mb-10 px-4 px-md-0"
              style="line-height: 1.8; max-width: 550px"
            >
              {{ $t("home.hero_desc") }}
            </p>

            <div
              class="d-flex flex-column flex-sm-row gap-4 justify-center justify-md-start"
            >
              <v-btn
                size="x-large"
                color="primary"
                elevation="8"
                class="rounded-xl px-12 font-weight-bold text-h6 primary-btn-glow"
                to="/Auth/RegisterNow"
                height="70"
              >
                {{ $t("home.start_now") }}
                <v-icon
                  end
                  :icon="
                    isRtl ? 'mdi-rocket-launch' : 'mdi-rocket-launch-outline'
                  "
                  size="28"
                ></v-icon>
              </v-btn>
              <v-btn
                size="x-large"
                variant="outlined"
                color="primary"
                class="rounded-xl px-10 font-weight-bold text-h6 border-2"
                height="70"
              >
                <v-icon start>mdi-play-circle</v-icon>
                {{ $t("home.how_to_earn") }}
              </v-btn>
            </div>

            <div
              class="mt-12 d-flex align-center justify-center justify-md-start gap-6"
            >
              <div class="d-flex align-center">
                <v-icon
                  color="amber-darken-2"
                  icon="mdi-star"
                  size="24"
                ></v-icon>
                <v-icon
                  color="amber-darken-2"
                  icon="mdi-star"
                  size="24"
                ></v-icon>
                <v-icon
                  color="amber-darken-2"
                  icon="mdi-star"
                  size="24"
                ></v-icon>
                <v-icon
                  color="amber-darken-2"
                  icon="mdi-star"
                  size="24"
                ></v-icon>
                <v-icon
                  color="amber-darken-2"
                  icon="mdi-star"
                  size="24"
                ></v-icon>
                <span class="ms-2 font-weight-black text-h6">4.9/5</span>
              </div>
              <v-divider vertical class="mx-2 d-none d-sm-block"></v-divider>
              <div class="text-body-1 font-weight-bold text-grey-darken-3">
                {{ $t("home.trusted_by", { count: "+10,000" }) }}
              </div>
            </div>
          </v-col>

          <v-col cols="12" md="6" class="position-relative">
            <!-- Interactive Profit Calculator Card -->
            <v-card
              class="rounded-xl premium-glass-card mx-auto mt-10 mt-md-0"
              max-width="540"
              elevation="24"
            >
              <v-toolbar color="primary-darken-4" flat height="10"> </v-toolbar>
              <v-card-text class="pa-8">
                <h3 class="text-h5 font-weight-black mb-6 text-center">
                  {{ $t("home.calc_title") }}
                </h3>

                <div class="mb-8">
                  <div class="d-flex justify-space-between mb-2">
                    <span class="font-weight-bold text-grey-darken-2">{{
                      $t("home.daily_orders")
                    }}</span>
                    <span class="text-primary font-weight-black text-h6"
                      >{{ dailyOrders }} {{ $t("home.orders_unit") }}</span
                    >
                  </div>
                  <v-slider
                    v-model="dailyOrders"
                    min="1"
                    max="50"
                    step="1"
                    color="primary"
                    thumb-label
                    hide-details
                  ></v-slider>
                </div>

                <div class="mb-10">
                  <div class="d-flex justify-space-between mb-2">
                    <span class="font-weight-bold text-grey-darken-2">{{
                      $t("home.avg_commission")
                    }}</span>
                    <span class="text-primary font-weight-black text-h6"
                      >{{ avgCommission }} {{ $t("home.currency") }}</span
                    >
                  </div>
                  <v-slider
                    v-model="avgCommission"
                    min="50"
                    max="500"
                    step="10"
                    color="primary"
                    thumb-label
                    hide-details
                  ></v-slider>
                </div>

                <v-card
                  variant="flat"
                  color="primary-lighten-5"
                  class="rounded-xl pa-6 text-center mb-0"
                >
                  <div
                    class="text-subtitle-1 font-weight-bold text-grey-darken-1 mb-1"
                  >
                    {{ $t("home.monthly_profit") }}
                  </div>
                  <div
                    class="text-h3 font-weight-black text-primary outfit-font counters-animation"
                  >
                    {{ (dailyOrders * avgCommission * 30).toLocaleString() }}
                    <span class="text-h6">{{ $t("home.currency") }}</span>
                  </div>
                  <div class="text-caption text-success font-weight-bold mt-2">
                    <v-icon icon="mdi-trending-up" class="me-1"></v-icon>
                    {{ $t("home.profit_note") }}
                  </div>
                </v-card>
              </v-card-text>
            </v-card>

            <!-- Floating Stats -->
            <v-card
              class="position-absolute floating-stat shadow-lg rounded-pill pa-3 d-flex align-center"
              elevation="8"
              style="top: 10%; right: -20px"
            >
              <v-avatar color="green-lighten-4" size="32" class="me-3">
                <v-icon color="green-darken-2" size="20">mdi-cash-check</v-icon>
              </v-avatar>
              <span class="font-weight-black text-body-2">{{
                $t("home.fast_payouts")
              }}</span>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <TheFeature />

    <!-- How It Works Section -->
    <section class="py-16 bg-grey-lighten-5">
      <v-container>
        <div class="text-center mb-16">
          <h2 class="text-h3 font-weight-black text-grey-darken-4 mb-4">
            {{ $t("home.steps_title") }}
          </h2>
          <p class="text-h6 text-grey-darken-1">
            {{ $t("home.steps_subtitle") }}
          </p>
        </div>
        <v-row>
          <v-col cols="12" md="3" v-for="(step, i) in steps" :key="i">
            <div class="text-center px-4">
              <v-avatar
                color="primary"
                size="80"
                class="mb-6 elevation-8 shadow-primary-sm"
              >
                <v-icon size="40" color="white">{{ step.icon }}</v-icon>
                <div class="step-badge">{{ i + 1 }}</div>
              </v-avatar>
              <h3 class="text-h5 font-weight-black mb-3">
                {{ step.title }}
              </h3>
              <p class="text-body-1 text-grey-darken-1">{{ step.desc }}</p>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <GuestProducts />

    <section class="py-16">
      <ShopWithUs />
    </section>

    <!-- Stats Section -->
    <div class="bg-primary-darken-4 py-16 position-relative overflow-hidden">
      <div class="decorative-blob blob-white opacity-10"></div>
      <v-container class="position-relative" style="z-index: 2">
        <v-row class="text-center text-white">
          <v-col cols="6" md="3">
            <div class="text-h2 font-weight-black mb-2 outfit-font">+10K</div>
            <div class="text-primary-lighten-4 font-weight-bold">
              {{ $t("home.stats_merchants") }}
            </div>
          </v-col>
          <v-col cols="6" md="3">
            <div class="text-h2 font-weight-black mb-2 outfit-font">+2,500</div>
            <div class="text-primary-lighten-4 font-weight-bold">
              {{ $t("home.stats_products") }}
            </div>
          </v-col>
          <v-col cols="6" md="3">
            <div class="text-h2 font-weight-black mb-2 outfit-font">24h</div>
            <div class="text-primary-lighten-4 font-weight-bold">
              {{ $t("home.stats_collection") }}
            </div>
          </v-col>
          <v-col cols="6" md="3">
            <div class="text-h2 font-weight-black mb-2 outfit-font">2M+</div>
            <div class="text-primary-lighten-4 font-weight-bold">
              {{ $t("home.stats_profits") }}
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <!-- CTA Section -->
    <v-container class="py-16 text-center">
      <v-card
        class="bg-grey-darken-4 rounded-xl pa-8 pa-md-16 overflow-hidden position-relative cta-card"
        elevation="24"
        dark
      >
        <div class="decorative-blob blob-primary opacity-20"></div>
        <div class="position-relative" style="z-index: 2">
          <h2 class="text-h3 font-weight-black text-white mb-6">
            {{ $t("home.cta_title") }}
          </h2>
          <p
            class="text-h6 text-grey-lighten-1 mb-10 mx-auto"
            style="max-width: 650px"
          >
            {{ $t("home.cta_desc") }}
          </p>
          <v-btn
            size="x-large"
            color="primary"
            height="80"
            class="px-16 rounded-xl font-weight-black text-h5 primary-btn-glow"
            to="/Auth/RegisterNow"
          >
            {{ $t("home.register_free") }}
            <v-icon end size="32">mdi-account-plus</v-icon>
          </v-btn>
        </div>
      </v-card>
    </v-container>

    <v-footer class="bg-grey-darken-4 py-8 text-center d-flex flex-column">
      <div class="d-flex align-center gap-4 mb-4">
        <v-btn
          v-if="settingsStore.facebook"
          icon
          variant="text"
          color="white"
          :href="settingsStore.facebook"
          target="_blank"
          ><v-icon>mdi-facebook</v-icon></v-btn
        >
        <v-btn
          v-if="settingsStore.twitter"
          icon
          variant="text"
          color="white"
          :href="settingsStore.twitter"
          target="_blank"
          ><v-icon>mdi-twitter</v-icon></v-btn
        >
        <v-btn
          v-if="settingsStore.instagram"
          icon
          variant="text"
          color="white"
          :href="settingsStore.instagram"
          target="_blank"
          ><v-icon>mdi-instagram</v-icon></v-btn
        >
        <v-btn
          v-if="settingsStore.tiktok"
          icon
          variant="text"
          color="white"
          :href="settingsStore.tiktok"
          target="_blank"
          ><v-icon>mdi-music-note</v-icon></v-btn
        >
      </div>
      <div class="text-grey">
        © {{ new Date().getFullYear() }} جميع الحقوق محفوظة لمنصة
        {{ settingsStore.storeName }}
      </div>
    </v-footer>
  </div>
</template>
<script setup>
import TheFeature from "@/components/TheFeature.vue";
import ShopWithUs from "@/components/ShopWithUs.vue";
import GuestProducts from "@/components/GuestProducts.vue";
import { onMounted, ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useSettingsStore } from "@/store/Settings";

const { t, locale } = useI18n();
const settingsStore = useSettingsStore();

const isRtl = computed(() => locale.value === "ar");

const dailyOrders = ref(10);
const avgCommission = ref(150);

const steps = computed(() => [
  {
    title: t("home.step_1_title"),
    desc: t("home.step_1_desc"),
    icon: "mdi-account-plus-outline",
  },
  {
    title: t("home.step_2_title"),
    desc: t("home.step_2_desc"),
    icon: "mdi-basket-outline",
  },
  {
    title: t("home.step_3_title"),
    desc: t("home.step_3_desc"),
    icon: "mdi-bullhorn-outline",
  },
  {
    title: t("home.step_4_title"),
    desc: t("home.step_4_desc"),
    icon: "mdi-wallet-outline",
  },
]);

onMounted(() => {
  settingsStore.fetchSettings();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap");

.outfit-font {
  font-family: "Outfit", "Cairo", sans-serif !important;
}

/* خلفية الهيرو المتدرجة برؤية حديثة */
.hero-bg {
  background: linear-gradient(135deg, #ffffff 0%, #fff3e0 100%);
  min-height: 90vh;
  display: flex;
  align-items: center;
  position: relative;
}

.text-gradient {
  background: linear-gradient(90deg, #ff9800, #f44336);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* الأشكال الديكورية الخلفية */
.decorative-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  z-index: 1;
}
.blob-1 {
  top: -10%;
  left: -5%;
  width: 600px;
  height: 600px;
  background: rgba(255, 152, 0, 0.1);
}
.blob-2 {
  bottom: 10%;
  right: 5%;
  width: 400px;
  height: 400px;
  background: rgba(244, 67, 54, 0.08);
}
.blob-white {
  width: 800px;
  height: 800px;
  background: white;
  top: -50%;
  right: -20%;
}
.blob-primary {
  width: 600px;
  height: 600px;
  background: #ff9800;
  bottom: -30%;
  left: -20%;
}

/* الأزرار الاحترافية */
.primary-btn-glow {
  box-shadow: 0 15px 35px rgba(255, 152, 0, 0.4) !important;
  transition: all 0.3s ease;
}
.primary-btn-glow:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 45px rgba(255, 152, 0, 0.6) !important;
}

.border-2 {
  border-width: 2px !important;
}

/* الخطوات */
.step-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ff9800;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 900;
  border: 4px solid white;
  font-size: 14px;
}
.shadow-primary-sm {
  box-shadow: 0 8px 25px rgba(255, 152, 0, 0.3) !important;
}

.cta-card {
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* كارت الحاسبة المميز */
.premium-glass-card {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 152, 0, 0.2);
  transition: transform 0.3s ease;
}
.premium-glass-card:hover {
  transform: scale(1.02);
}

.floating-stat {
  z-index: 10;
  background: white;
  animation: float 5s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(2deg);
  }
}

.pulse-slow {
  animation: pulse-border 3s infinite;
}

@keyframes pulse-border {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.4);
  }
  70% {
    box-shadow: 0 0 0 15px rgba(255, 152, 0, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 152, 0, 0);
  }
}

.lh-tight {
  line-height: 1.1 !important;
}

.gap-6 {
  gap: 24px;
}
.gap-4 {
  gap: 16px;
}
</style>
