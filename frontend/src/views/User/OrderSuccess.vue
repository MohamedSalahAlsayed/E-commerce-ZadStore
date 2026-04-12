<template>
  <div
    class="success-page bg-grey-lighten-4 py-16 d-flex align-center justify-center"
    style="min-height: 100vh"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-container>
      <v-row justify="center">
        <v-col cols="12" md="7" lg="6" class="text-center">
          <v-card class="pa-8 pa-md-12 rounded-xl shadow-premium elevation-2">
            <!-- Simulated Lottie Animation -->
            <div class="success-animation mb-8">
              <div class="checkmark-circle">
                <div class="background"></div>
                <v-icon color="white" size="80">mdi-check-bold</v-icon>
              </div>
            </div>

            <h1 class="text-h3 font-weight-black text-grey-darken-4 mb-4">
              {{ $t("success.title") }}
            </h1>
            <p class="text-h6 text-grey-darken-1 mb-10 px-md-10">
              {{ $t("success.subtitle") }}
            </p>

            <v-card
              variant="flat"
              color="primary-lighten-5"
              class="rounded-xl pa-6 mb-10 border-dashed"
            >
              <div
                class="d-flex flex-column flex-sm-row justify-space-between align-center"
              >
                <div class="text-right">
                  <div class="text-caption text-grey-darken-1 font-weight-bold">
                    {{ $t("success.order_no_label") }}
                  </div>
                  <div class="text-h5 font-weight-black text-primary">
                    #{{ orderNumber || "ZAD-XXXXXX" }}
                  </div>
                </div>
                <v-divider
                  vertical
                  class="mx-6 d-none d-sm-block"
                  opacity="0.2"
                ></v-divider>
                <div class="text-right mt-4 mt-sm-0">
                  <div class="text-caption text-grey-darken-1 font-weight-bold">
                    {{ $t("success.status_label") }}
                  </div>
                  <v-chip color="success" variant="flat" size="small">{{
                    $t("success.pending_review")
                  }}</v-chip>
                </div>
              </div>
            </v-card>

            <v-row>
              <v-col cols="12" sm="6">
                <v-btn
                  block
                  color="primary"
                  height="60"
                  class="rounded-lg text-h6 font-weight-bold elevation-4"
                  to="/User/ControlPannel/OrdersPage"
                  prepend-icon="mdi-package-variant-closed"
                >
                  {{ $t("success.track_orders") }}
                </v-btn>
              </v-col>
              <v-col cols="12" sm="6">
                <v-btn
                  block
                  variant="outlined"
                  color="primary"
                  height="60"
                  class="rounded-lg text-h6 font-weight-bold border-2"
                  to="/User/ProductsPage"
                  prepend-icon="mdi-shopping-outline"
                >
                  {{ $t("success.continue_shopping") }}
                </v-btn>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
const { locale } = useI18n();
import { useRoute } from "vue-router";

const route = useRoute();
const orderNumber = ref("");

onMounted(() => {
  orderNumber.value = route.query.order_number || "";
});
</script>

<style scoped>
.success-page {
  font-family: "Cairo", "Tajawal", sans-serif;
  background-image: radial-gradient(
    circle at 50% 50%,
    rgba(255, 152, 0, 0.05) 0%,
    transparent 100%
  );
}

.shadow-premium {
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1) !important;
}

.border-dashed {
  border: 2px dashed rgba(255, 152, 0, 0.3) !important;
}

.border-2 {
  border-width: 2px !important;
}

/* Success Animation */
.checkmark-circle {
  width: 140px;
  height: 140px;
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background-color: #4caf50;
  box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
  animation: scale-up 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}

.checkmark-circle .background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: white;
  opacity: 0.2;
  animation: pulse 2s infinite;
}

@keyframes scale-up {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 0.2;
  }
  50% {
    transform: scale(1.3);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 0.2;
  }
}
</style>
