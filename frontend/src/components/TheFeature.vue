<template>
  <div class="features-wrapper">
    <v-container class="py-3">
      <v-row align="center" justify="space-around" class="text-center">
        <v-col cols="12" sm="4" md="auto" class="feature-item py-3 py-sm-1">
          <v-icon size="26" class="feature-icon">mdi-truck-fast-outline</v-icon>
          <span class="feature-text">{{ $t("features.free_shipping") }}</span>
        </v-col>

        <v-divider
          vertical
          class="d-none d-md-block custom-divider"
        ></v-divider>

        <v-col cols="12" sm="4" md="auto" class="feature-item py-3 py-sm-1">
          <v-icon size="26" class="feature-icon">mdi-cash-multiple</v-icon>
          <span class="feature-text">{{ $t("features.lowest_price") }}</span>
        </v-col>

        <v-divider
          vertical
          class="d-none d-md-block custom-divider"
        ></v-divider>

        <v-col cols="12" sm="4" md="auto" class="feature-item py-3 py-sm-1">
          <v-icon size="26" class="feature-icon">mdi-trophy-outline</v-icon>
          <span class="feature-text">{{
            $t("features.longest_warranty")
          }}</span>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style scoped lang="scss">
/* الغلاف الخارجي للشريط */
.features-wrapper {
  background-color: #f8fafc; /* لون رمادي مائل للأزرق فاتح ومريح للعين */
  border-bottom: 1px solid #e2e8f0;
  border-top: 1px solid #e2e8f0;
}

/* تنسيق العنصر الواحد */
.feature-item {
  display: flex;
  align-items: center;
  justify-content: center; /* التوسيط في الموبايل */
  gap: 12px; /* المسافة بين الأيقونة والكلمة */
  cursor: pointer;
  transition: all 0.3s ease;
}

/* تأثير الـ Hover */
.feature-item:hover {
  transform: translateY(-2px);
}

.feature-item:hover .feature-icon {
  color: rgb(var(--v-theme-primary));
  transform: scale(1.1);
}

/* تنسيق الأيقونة */
.feature-icon {
  color: rgb(var(--v-theme-primary));
  transition: all 0.3s ease;
}

/* تنسيق النص */
.feature-text {
  font-size: 15px; /* كبرت الخط سنة عشان العربي يكون أوضح */
  font-weight: 700;
  color: #334155;
  white-space: nowrap;
}

/* تنسيق الخط الفاصل */
.custom-divider {
  border-color: #cbd5e1 !important;
  opacity: 0.6;
  height: 30px;
  align-self: center;
}

/* تظبيط مخصوص للموبايل */
@media (max-width: 599px) {
  .feature-item {
    border-bottom: 1px solid #f1f5f9;
  }
  .feature-item:last-child {
    border-bottom: none;
  }
  .feature-text {
    white-space: normal; /* لو الشاشة صغيرة أوي الكلام ينزل سطر جديد بشياكة */
  }
}
</style>
