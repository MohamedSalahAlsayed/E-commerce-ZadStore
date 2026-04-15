<template>
  <v-footer class="pa-0 flex-column border-0 bg-transparent">
    <!-- Newsletter Section (Floating design) -->
    <v-container
      class="newsletter-container mb-n8 mb-md-n12 position-relative z-index-10"
    >
      <v-card
        class="newsletter-card rounded-xl border-0 overflow-hidden"
        elevation="24"
      >
        <div class="newsletter-grid">
          <div class="newsletter-content pa-6 pa-md-12 text-white">
            <h3 class="newsletter-title font-weight-black mb-2 animate-up">
              {{ $t("footer.newsletter_title") || "انضم لنخبة ZadStore" }}
            </h3>
            <p class="text-subtitle-1 opacity-80 mb-0">
              {{
                $t("footer.newsletter_subtitle") ||
                "كن أول من يحصل على العروض الحصرية والقطع النادرة"
              }}
            </p>
          </div>
          <div
            class="newsletter-action pa-6 pa-md-12 bg-white-opacity-10 backdrop-blur d-flex align-center"
          >
            <v-form @submit.prevent class="w-100 d-flex gap-3">
              <v-text-field
                :placeholder="
                  $t('footer.email_placeholder') || 'بريدك الإلكتروني'
                "
                variant="solo"
                flat
                hide-details
                rounded="lg"
                class="email-input flex-grow-1"
                density="comfortable"
              ></v-text-field>
              <v-btn
                color="white"
                class="text-primary font-weight-black px-8 rounded-lg send-btn"
                height="48"
                elevation="8"
              >
                {{ $t("footer.subscribe_btn") || "اشترك الآن" }}
              </v-btn>
            </v-form>
          </div>
        </div>
      </v-card>
    </v-container>

    <!-- Main Footer Footer -->
    <div class="premium-footer w-100 pt-16 pb-8 border-0">
      <div class="footer-mesh"></div>
      <v-container class="pt-12">
        <v-row class="footer-main-row">
          <!-- Column 1: Brand Authority -->
          <v-col cols="12" md="4" lg="4" class="pe-md-12 mb-10 mb-md-0">
            <div class="brand-section mb-6">
              <div class="d-flex align-center mb-6">
                <v-img
                  v-if="settingsStore.logoPreview"
                  :src="settingsStore.logoPreview"
                  max-width="64"
                  class="me-3"
                  contain
                ></v-img>
                <v-icon v-else color="primary" size="48" class="me-3 glow-icon"
                  >mdi-shopping</v-icon
                >
                <h2 class="brand-text font-weight-black text-white">
                  {{ settingsStore.storeName || "ZadStore" }}
                </h2>
              </div>
              <p class="text-grey-lighten-1 text-body-1 lh-relaxed mb-8">
                {{
                  settingsStore.footerAbout ||
                  $t("footer.description") ||
                  "وجهتك الأولى للتسوق الفاخر وتجربة التجارة الإلكترونية العصرية بلمسة من الرقي والجودة."
                }}
              </p>
              <div
                class="d-flex gap-4 social-wrap"
                v-if="settingsStore.footerShowSocial"
              >
                <v-btn
                  v-for="social in filteredSocials"
                  :key="social.key"
                  :icon="social.icon"
                  size="large"
                  variant="flat"
                  class="social-sphere"
                  :style="{ '--brand-color': social.color }"
                  :href="social.link"
                  target="_blank"
                ></v-btn>
              </div>
            </div>
          </v-col>

          <!-- Column 2: Curation Links -->
          <v-col cols="6" md="2" class="mb-8 mb-md-0">
            <h4 class="footer-heading mb-8">
              {{ $t("footer.shopping") || "التسوق المميز" }}
            </h4>
            <nav class="footer-nav">
              <router-link to="/all-products" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("products.all_products") }}
              </router-link>
              <router-link to="/best-sellers" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("settings.landing.switches.best_sellers") }}
              </router-link>
              <router-link to="/new-arrivals" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("settings.landing.switches.featured") }}
              </router-link>
              <router-link to="/discounts" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("settings.landing.switches.flash_deals") }}
              </router-link>
            </nav>
          </v-col>

          <!-- Column 3: Trust & Support -->
          <v-col cols="6" md="2" class="mb-8 mb-md-0">
            <h4 class="footer-heading mb-8">
              {{ $t("footer.quick_links") || "روابط هامة" }}
            </h4>
            <nav class="footer-nav">
              <router-link to="/BlogPage" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("guest_nav.blog") }}
              </router-link>
              <router-link to="/AboutPage" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("guest_nav.about_us") }}
              </router-link>
              <router-link to="/ContactPage" class="nav-link">
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ $t("guest_nav.contact") }}
              </router-link>
              <router-link
                to="/User/ControlPannel/OrdersPage"
                class="nav-link"
                v-if="userStore.isAuthenticated"
              >
                <v-icon size="14" class="me-2 icon-dot"
                  >mdi-circle-medium</v-icon
                >
                {{ locale === "ar" ? "طلباتي" : "My Orders" }}
              </router-link>
            </nav>
          </v-col>

          <!-- Column 4: Experience & Methods -->
          <v-col cols="12" md="4">
            <h4 class="footer-heading mb-8">
              {{ $t("footer.contact_us") || "تواصل معنا" }}
            </h4>
            <div class="contact-glass pa-5 pa-md-6 mb-8">
              <div v-if="settingsStore.footerShowContact" class="contact-rows">
                <div class="d-flex align-center mb-6 contact-item">
                  <div class="icon-box me-4 shadow-primary">
                    <v-icon size="20" color="primary">mdi-phone-outline</v-icon>
                  </div>
                  <div>
                    <span class="text-caption text-grey d-block mb-1">{{
                      locale === "ar" ? "الخط الساخن" : "Support Line"
                    }}</span>
                    <span
                      class="text-subtitle-1 font-weight-black text-white"
                      >{{ settingsStore.phone || "+20 123 456 7890" }}</span
                    >
                  </div>
                </div>
                <div class="d-flex align-center contact-item">
                  <div class="icon-box me-4 shadow-primary">
                    <v-icon size="20" color="primary">mdi-email-outline</v-icon>
                  </div>
                  <div>
                    <span class="text-caption text-grey d-block mb-1">{{
                      locale === "ar" ? "البريد الرسمي" : "Official Email"
                    }}</span>
                    <span
                      class="text-subtitle-1 font-weight-black text-white"
                      >{{ settingsStore.email || "info@zadstore.com" }}</span
                    >
                  </div>
                </div>
              </div>

              <div class="mt-8 border-t border-white-opacity-10 pt-6">
                <div class="d-flex align-center justify-space-between mb-4">
                  <span class="text-caption text-grey">{{
                    locale === "ar" ? "وسائل الدفع الآمنة" : "Secure Payments"
                  }}</span>
                  <div class="d-flex gap-2 payment-badges">
                    <v-icon color="grey-lighten-1" size="24"
                      >mdi-credit-card</v-icon
                    >
                    <v-icon color="grey-lighten-1" size="24">mdi-bank</v-icon>
                    <v-icon color="grey-lighten-1" size="24">mdi-wallet</v-icon>
                  </div>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <v-divider class="my-12 border-opacity-10" color="white"></v-divider>

        <!-- Dynamic Bottom Bar -->
        <v-row align="center" class="bottom-bar">
          <v-col cols="12" md="6" class="text-center text-md-start">
            <p class="text-grey-lighten-1 text-body-2 mb-0 copyright-text">
              {{
                settingsStore.footerCopyright ||
                $t("footer.all_rights_reserved", {
                  year: new Date().getFullYear(),
                  store: settingsStore.storeName || "ZadStore",
                }) ||
                `© ${new Date().getFullYear()} ${
                  settingsStore.storeName || "ZadStore"
                }. جميع الحقوق محفوظة`
              }}
            </p>
          </v-col>
          <v-col cols="12" md="6">
            <div
              class="d-flex justify-center justify-md-end gap-6 text-caption text-grey-lighten-1 legal-links"
            >
              <a href="#" class="legal-link">{{
                locale === "ar" ? "سياسة الخصوصية" : "Privacy Policy"
              }}</a>
              <a href="#" class="legal-link">{{
                locale === "ar" ? "الشروط والأحكام" : "Terms & Conditions"
              }}</a>
              <div class="made-by ms-4 opacity-50">Powered by ZadStore OS</div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>
  </v-footer>
</template>

<script setup>
import { computed } from "vue";
import { useSettingsStore } from "@/store/Settings";
import { useAuthStore } from "@/store/auth/LogIn";
import { useI18n } from "vue-i18n";

const settingsStore = useSettingsStore();
const userStore = useAuthStore();
const { locale } = useI18n();

const socials = {
  facebook: { icon: "mdi-facebook", color: "#1877F2" },
  instagram: { icon: "mdi-instagram", color: "#E4405F" },
  twitter: { icon: "mdi-twitter", color: "#1DA1F2" },
  tiktok: { icon: "mdi-music-note", color: "#ffffff" },
};

const filteredSocials = computed(() => {
  return Object.keys(socials)
    .filter((key) => settingsStore[key])
    .map((key) => ({
      key,
      ...socials[key],
      link: settingsStore[key],
    }));
});
</script>

<style scoped>
.premium-footer {
  background: #0d0d0d;
  position: relative;
  overflow: hidden;
}

.footer-mesh {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: radial-gradient(
      at 0% 0%,
      rgba(var(--v-theme-primary), 0.1) 0px,
      transparent 50%
    ),
    radial-gradient(
      at 100% 100%,
      rgba(var(--v-theme-primary), 0.05) 0px,
      transparent 50%
    );
  pointer-events: none;
}

.newsletter-card {
  background: linear-gradient(
    135deg,
    rgb(var(--v-theme-primary)) 0%,
    #1a1a1a 100%
  );
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
}
@media (max-width: 600px) {
  .newsletter-card {
    border-radius: 20px !important;
  }
}

.newsletter-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

@media (max-width: 960px) {
  .newsletter-grid {
    grid-template-columns: 1fr;
  }
}

.bg-white-opacity-10 {
  background-color: rgba(255, 255, 255, 0.04) !important;
}

.backdrop-blur {
  backdrop-filter: blur(20px);
}

.brand-text {
  font-size: clamp(1.25rem, 3.5vw, 2.25rem); /* Slightly smaller */
  letter-spacing: -0.5px;
  background: linear-gradient(to right, #fff, rgba(255, 255, 255, 0.7));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.newsletter-title {
  font-size: clamp(1.15rem, 3.2vw, 1.85rem);
}

.lh-relaxed {
  line-height: 1.6; /* More compact for mobile */
}
@media (min-width: 960px) {
  .lh-relaxed {
    line-height: 1.8;
  }
}

.footer-heading {
  color: #fff;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
  position: relative;
  padding-bottom: 12px;
  font-size: clamp(0.95rem, 2.2vw, 1.15rem); /* Scaled down */
}

.footer-heading::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 30px;
  height: 3px;
  background: rgb(var(--v-theme-primary));
  border-radius: 2px;
}

:dir(rtl) .footer-heading::after {
  left: auto;
  right: 0;
}

.footer-nav {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.nav-link {
  color: #9e9e9e;
  text-decoration: none;
  font-size: clamp(0.85rem, 1.7vw, 0.95rem); /* Scaled down */
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  display: flex;
  align-items: center;
}

.nav-link .icon-dot {
  opacity: 0;
  transform: scale(0);
  transition: all 0.3s;
  color: rgb(var(--v-theme-primary));
}

.nav-link:hover {
  color: #fff;
  padding-inline-start: 10px;
}

.nav-link:hover .icon-dot {
  opacity: 1;
  transform: scale(1);
}

.contact-glass {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  backdrop-filter: blur(10px);
}

.icon-box {
  width: 44px;
  height: 44px;
  background: rgba(var(--v-theme-primary), 0.1);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.contact-item:hover .icon-box {
  background: rgb(var(--v-theme-primary));
}

.contact-item:hover .icon-box i {
  color: white !important;
}

.social-sphere {
  background: rgba(255, 255, 255, 0.05) !important;
  color: #fff !important;
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.social-sphere:hover {
  background: var(--brand-color) !important;
  transform: translateY(-8px) scale(1.1);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.legal-link {
  color: inherit;
  text-decoration: none;
  transition: color 0.3s;
}

.legal-link:hover {
  color: rgb(var(--v-theme-primary));
}

.glow-icon {
  filter: drop-shadow(0 0 8px rgba(var(--v-theme-primary), 0.5));
}

.z-index-10 {
  z-index: 10;
}

.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
.gap-6 {
  gap: 24px;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-up {
  animation: fadeInUp 0.8s ease-out forwards;
}

/* Supplemental Responsive Overrides */
.text-body-1 {
  font-size: clamp(0.85rem, 1.8vw, 0.95rem) !important;
  line-height: 1.6;
}

.text-subtitle-1 {
  font-size: clamp(0.875rem, 2vw, 1rem) !important;
}

.copyright-text {
  font-size: clamp(0.7rem, 1.6vw, 0.8rem) !important;
}

@media (max-width: 600px) {
  .footer-nav {
    gap: 12px;
  }
  .newsletter-content {
    text-align: center;
  }
  .social-wrap {
    justify-content: center;
  }
}
</style>
