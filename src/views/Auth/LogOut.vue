<template>
  <v-app theme="light">
    <v-locale-provider :rtl="isRtl">
      <v-main
        class="bg-primary-lighten-5 d-flex align-center justify-center"
        style="min-height: 100vh"
      >
        <v-scale-transition appear>
          <v-card
            class="rounded-xl elevation-10 text-center pa-10"
            width="100%"
            max-width="400"
          >
            <div class="mb-6 position-relative d-inline-block">
              <div class="pulse-ring"></div>
              <v-avatar color="primary-lighten-5" size="100">
                <v-icon size="50" color="primary" class="logout-icon"
                  >mdi-hand-wave</v-icon
                >
              </v-avatar>
            </div>

            <h2 class="text-h4 font-weight-bold text-primary-darken-3 mb-2">
              {{ $t("auth.logout_subtitle") }}
            </h2>
            <p class="text-grey-darken-1 mb-8">
              {{ $t("auth.logout_status") }}
            </p>

            <v-progress-linear
              indeterminate
              color="primary"
              height="6"
              rounded
              class="mb-6"
            ></v-progress-linear>

            <div class="text-caption text-grey">
              {{ $t("auth.logout_redirect") }}
            </div>

            <v-btn
              variant="text"
              color="primary"
              class="mt-4 font-weight-bold"
              to="/Auth/LogIn"
            >
              {{ $t("auth.logout_btn") }}
            </v-btn>
          </v-card>
        </v-scale-transition>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth/LogIn";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const router = useRouter();
const authStore = useAuthStore();

const isRtl = computed(() => locale.value === "ar");

onMounted(async () => {
  await authStore.logout();
  setTimeout(() => {
    router.push("/Auth/LogIn");
  }, 1500);
});
</script>

<style scoped>
/* تأثير النبض خلف الأيقونة */
.pulse-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 100%;
  border-radius: 50%;
  border: 2px solid #3949ab;
  animation: pulse 2s infinite;
  opacity: 0;
}

@keyframes pulse {
  0% {
    width: 100%;
    height: 100%;
    opacity: 0.5;
  }
  100% {
    width: 140%;
    height: 140%;
    opacity: 0;
  }
}

/* حركة بسيطة للأيقونة */
.logout-icon {
  animation: wave 1.5s infinite ease-in-out;
  transform-origin: 70% 70%;
}

@keyframes wave {
  0% {
    transform: rotate(0deg);
  }
  25% {
    transform: rotate(15deg);
  }
  50% {
    transform: rotate(0deg);
  }
  75% {
    transform: rotate(-15deg);
  }
  100% {
    transform: rotate(0deg);
  }
}
</style>
