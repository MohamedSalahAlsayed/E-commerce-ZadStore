<template>
  <v-container
    fluid
    class="promo-section py-8"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
  >
    <v-row>
      <v-col cols="12" md="4">
        <v-card class="promo-card small-promo bg-blue-gradient">
          <div class="promo-content">
            <span class="promo-category">أجهزة ألعاب</span>
            <h3 class="promo-title">اشتري 2 واحصل على 1</h3>
            <span class="promo-offer">مجاناً</span>
          </div>
          <img
            src="https://pngimg.com/uploads/gamepad/gamepad_PNG41.png"
            class="promo-img small-img"
          />
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="promo-card small-promo bg-purple-gradient">
          <div class="promo-content">
            <span class="promo-category">مكبرات صوت</span>
            <h3 class="promo-title">خصم هائل يصل إلى</h3>
            <span class="promo-offer">75%</span>
          </div>
          <img
            src="https://pngimg.com/uploads/audio_speakers/audio_speakers_PNG50720.png"
            class="promo-img small-img audio-img"
          />
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="promo-card small-promo bg-green-gradient">
          <div class="promo-content">
            <span class="promo-category">كراسي استرخاء</span>
            <h3 class="promo-title">تخفيضات مذهلة</h3>
            <span class="promo-offer">50%</span>
          </div>
          <img
            src="https://pngimg.com/uploads/armchair/armchair_PNG7048.png"
            class="promo-img small-img chair-img"
          />
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col cols="12" md="6">
        <v-card class="promo-card large-promo bg-yellow-gradient">
          <div class="promo-text">
            <span class="promo-label text-white">الأكثر مبيعاً 🔥</span>
            <h2 class="promo-title-large text-white">هاتف إكس برو</h2>
            <p class="promo-desc text-white">
              أداء استثنائي وتصميم عصري يناسبك.
            </p>
            <v-btn
              class="shop-btn font-weight-black mt-2"
              rounded
              variant="flat"
              elevation="3"
              @click.stop="handleShopClick"
            >
              تسوق الآن
            </v-btn>
          </div>
          <img
            src="https://pngimg.com/uploads/smartphone/smartphone_PNG8514.png"
            class="promo-img large-img phone-img"
          />
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="promo-card large-promo bg-pink-gradient">
          <div class="promo-text">
            <span class="promo-label text-white">الأكثر شهرة ⭐</span>
            <h2 class="promo-title-large text-white">لابتوب ألترا</h2>
            <p class="promo-desc text-white">
              قوة هائلة تنجز كل مهامك اليومية بسهولة.
            </p>
            <v-btn
              class="shop-btn font-weight-black mt-2"
              rounded
              variant="flat"
              elevation="3"
              @click.stop="handleShopClick"
            >
              تسوق الآن
            </v-btn>
          </div>
          <img
            src="https://pngimg.com/uploads/macbook/macbook_PNG65.png"
            class="promo-img large-img laptop-img"
          />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";

const { locale } = useI18n();
const router = useRouter();

const handleShopClick = () => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  router.push("/User/ProductsPage");
};
</script>

<style scoped>
/* ==================================
   1. الإعدادات العامة للكروت
================================== */
.promo-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
  cursor: pointer;
  border: none;
}

.promo-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
}

/* ==================================
   2. الكروت الصغيرة (أعلى)
================================== */
.small-promo {
  height: 220px;
  padding: 24px;
  align-items: center;
}

/* خلفيات متدرجة مودرن للكروت الصغيرة */
.bg-blue-gradient {
  background: linear-gradient(135deg, #1e3c72, #2a5298);
}
.bg-purple-gradient {
  background: linear-gradient(135deg, #4b134f, #c94b4b);
}
.bg-green-gradient {
  background: linear-gradient(135deg, #11998e, #38ef7d);
}

.promo-content {
  position: relative;
  z-index: 2;
  color: #fff;
  width: 60%; /* عشان ندي مساحة للصورة */
}

.promo-category {
  font-size: 13px;
  font-weight: 700;
  color: #ffca28; /* لون أصفر مميز للفئة */
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.promo-title {
  font-size: 18px;
  font-weight: 800;
  margin: 8px 0;
  line-height: 1.3;
}

.promo-offer {
  font-size: 28px;
  font-weight: 900;
  color: #fff;
  text-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
}

/* ==================================
   3. الكروت الكبيرة (أسفل)
================================== */
.large-promo {
  height: 280px;
  padding: 32px;
  align-items: center;
}

/* خلفيات الكروت الكبيرة */
.bg-yellow-gradient {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
}
.bg-pink-gradient {
  background: linear-gradient(135deg, #fbcfe8, #f472b6);
}

.promo-text {
  position: relative;
  max-width: 55%;
  z-index: 2;
}

.promo-label {
  font-size: 14px;
  font-weight: 700;
  opacity: 0.9;
}

.promo-title-large {
  font-size: 32px;
  font-weight: 900;
  margin: 10px 0;
  line-height: 1.2;
}

.promo-desc {
  font-size: 14px;
  opacity: 0.85;
  margin-bottom: 20px;
  line-height: 1.5;
}

/* زرار التسوق */
.shop-btn {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 0 24px;
  transition: all 0.3s ease;
}

.shop-btn:hover {
  background: rgb(var(--v-theme-primary)) !important;
  color: rgb(var(--v-theme-on-primary), #fff) !important;
}

/* ==================================
   4. تنسيقات الصور وأنيميشن الـ Hover
================================== */
.promo-img {
  position: absolute;
  z-index: 1;
  transition: transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
  will-change: transform;
}

.promo-card:hover .promo-img {
  transform: scale(1.1) rotate(-3deg); /* تأثير الزووم مع ميل خفيف للصور الشفافة */
}

/* تظبيط مقاسات وأماكن الصور (يسار الكارت لأن النص يمين) */
.small-img {
  left: 10px;
  max-height: 80%;
  top: 50%;
  transform: translateY(-50%); /* توسيط عمودي للصورة */
}
/* تصغير مخصوص لبعض الصور عشان ماتضربش بره الكارت */
.audio-img {
  max-height: 70%;
  left: 20px;
}
.chair-img {
  max-height: 95%;
  left: 10px;
}

.large-img {
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  max-height: 90%;
}
.phone-img {
  left: 40px;
  max-height: 85%;
}
.laptop-img {
  left: -20px;
  max-height: 85%;
}

/* ==================================
   5. الموبايل (Responsive)
================================== */
@media (max-width: 768px) {
  .small-promo,
  .large-promo {
    height: auto;
    min-height: 220px;
    padding: 24px;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
  }

  .promo-content,
  .promo-text {
    width: 100%;
    max-width: 100%;
    z-index: 2;
  }

  .promo-img {
    position: relative;
    left: auto;
    right: auto;
    top: auto;
    transform: none;
    margin-top: 20px;
    align-self: center; /* توسيط الصورة في الموبايل */
    max-height: 160px;
  }

  /* الحفاظ على تأثير الـ Hover في الموبايل بس بدون الـ translateY */
  .promo-card:hover .promo-img {
    transform: scale(1.05) rotate(-3deg);
  }
}
</style>
