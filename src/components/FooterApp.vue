<template>
  <v-footer
    class="bg-grey-lighten-10 pt-10 pb-5 text-body-2 border-top"
    style="border-top: 1px solid rgba(var(--v-theme-on-surface), 0.2)"
  >
    <v-container>
      <v-row>
        <v-col cols="12" sm="6" md="3">
          <h3
            class="text-subtitle-1 text-justify font-weight-bold mb-4 text-uppercase"
          >
            {{ $t("footer.shop") }}
          </h3>
          <ul class="list-unstyled">
            <li v-for="(item, index) in shopLinks" :key="index" class="mb-2">
              <a href="#" class="footer-link">{{ item }}</a>
            </li>
          </ul>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <h3
            class="text-subtitle-1 text-justify font-weight-bold mb-4 text-uppercase"
          >
            {{ $t("footer.info") }}
          </h3>
          <ul class="list-unstyled">
            <li v-for="(item, index) in infoLinks" :key="index" class="mb-2">
              <a href="#" class="footer-link">{{ item }}</a>
            </li>
          </ul>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <h3
            class="text-subtitle-1 text-justify font-weight-bold mb-4 text-uppercase"
          >
            {{ $t("footer.customer_service") }}
          </h3>
          <ul class="list-unstyled">
            <li v-for="(item, index) in serviceLinks" :key="index" class="mb-2">
              <a href="#" class="footer-link">{{ item }}</a>
            </li>
          </ul>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <div class="mb-4">
            <h1
              class="font-weight-bold logo-text text-justify"
              style="color: rgb(var(--v-theme-primary)); font-size: 25px"
            >
              {{ settingsStore.storeName || "زاّد ستور" }}
            </h1>
          </div>

          <div class="contact-info">
            <div class="d-flex align-start mb-3 text-grey-darken-1">
              <v-icon
                icon="mdi-map-marker"
                size="small"
                class="ms-3 mt-1 text-grey-darken-2"
              ></v-icon>
              <span>{{ settingsStore.address || $t("loading") }}</span>
            </div>

            <div class="d-flex align-center mb-3">
              <v-icon
                icon="mdi-phone"
                size="small"
                class="ms-3 text-grey-darken-2"
              ></v-icon>
              <span class="text-grey-darken-1">
                {{ $t("footer.contact_us_label") }}:
                <a
                  :href="'tel:' + settingsStore.phone"
                  class="text-decoration-underline text-grey-darken-1"
                >
                  <span dir="ltr">
                    {{ settingsStore.phone || "---" }}
                  </span>
                </a>
              </span>
            </div>

            <div class="d-flex align-center mb-5">
              <v-icon
                icon="mdi-email"
                size="small"
                class="ms-3 text-grey-darken-2"
              ></v-icon>
              <span class="text-grey-darken-1">
                {{ settingsStore.email || "---" }}
              </span>
            </div>
          </div>

          <div class="social-icons d-flex gap-2">
            <v-btn
              v-if="settingsStore.facebook"
              :href="settingsStore.facebook"
              target="_blank"
              icon="mdi-facebook"
              size="small"
              variant="flat"
              color="#1877F2"
              class="rounded-circle mr-2 text-white hover-btn"
            ></v-btn>
            <v-btn
              v-if="settingsStore.twitter"
              :href="settingsStore.twitter"
              target="_blank"
              icon="mdi-twitter"
              size="small"
              variant="flat"
              color="#1DA1F2"
              class="rounded-circle mr-2 text-white hover-btn"
            ></v-btn>
            <v-btn
              v-if="settingsStore.instagram"
              :href="settingsStore.instagram"
              target="_blank"
              icon="mdi-instagram"
              size="small"
              variant="flat"
              color="#E4405F"
              class="rounded-circle mr-2 text-white hover-btn"
            ></v-btn>
            <v-btn
              v-if="settingsStore.tiktok"
              :href="settingsStore.tiktok"
              target="_blank"
              icon="mdi-music-note"
              size="small"
              variant="flat"
              color="black"
              class="rounded-circle mr-2 text-white hover-btn"
            ></v-btn>
          </div>
        </v-col>
      </v-row>

      <v-divider class="my-8"></v-divider>

      <v-row justify="center">
        <v-col cols="12" class="text-center">
          <p class="mb-4 text-grey-darken-1">
            {{ $t("footer.rights_reserved") }}
          </p>
          <div
            class="d-flex justify-center align-center flex-wrap"
            style="gap: 15px; opacity: 0.7"
          >
            <div class="payment-badge">
              <v-icon icon="mdi-shield-check" color="black"></v-icon> Norton
            </div>
            <div class="payment-badge">
              <v-icon icon="mdi-check-decagram" color="green"></v-icon> TRUSTe
            </div>
            <div class="payment-badge">
              <v-icon icon="mdi-credit-card" color="blue-darken-4"></v-icon>
              VISA
            </div>
            <div class="payment-badge">
              <v-icon icon="mdi-credit-card-outline" color="red"></v-icon>
              MasterCard
            </div>
            <div class="payment-badge">
              <v-icon icon="mdi-domain" color="blue"></v-icon> BBB
            </div>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useSettingsStore } from "@/store/Settings";

const { t } = useI18n();
const settingsStore = useSettingsStore();

onMounted(() => {
  if (!settingsStore.email) {
    settingsStore.fetchSettings();
  }
});

// بيانات القوائم
const shopLinks = computed(() => [
  t("footer.electronics"),
  t("footer.computers"),
  t("footer.smartphones"),
  t("footer.cameras"),
  t("footer.games"),
  t("footer.furniture"),
  t("footer.specials"),
]);

const infoLinks = computed(() => [
  t("footer.about_us"),
  t("footer.customer_service"),
  t("footer.reward_program"),
  t("footer.shipping_returns"),
  t("footer.privacy_policy"),
  t("footer.terms_conditions"),
  t("footer.blog"),
]);

const serviceLinks = computed(() => [
  t("footer.search_terms"),
  t("footer.advanced_search"),
  t("nav.orders"), // Reuse Orders key
  t("footer.contact_us"),
  t("footer.faqs"),
  t("footer.consultant"),
  t("footer.locations"),
]);
</script>

<style scoped>
/* إزالة النقاط من القوائم */
.list-unstyled {
  padding: 0;
  list-style: none;
}

/* تنسيق الروابط */
.footer-link {
  color: rgba(var(--v-theme-on-surface), 0.6);
  /* لون رمادي */
  text-decoration: none;
  transition: color 0.2s ease-in-out;
  font-size: 14px;
  text-align: justify;
  display: block;
}

.footer-link:hover {
  color: rgb(var(--v-theme-on-surface));
  /* لون أسود عند التمرير */
  text-decoration: underline;
}

/* تنسيق بسيط لشعارات الدفع */
.payment-badge {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.2);
  background: rgb(var(--v-theme-surface));
  padding: 5px 10px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: bold;
  font-size: 12px;
}

/* تأثير بسيط لأزرار السوشيال */
.hover-btn:hover {
  background-color: rgb(var(--v-theme-primary)) !important;
}
</style>
