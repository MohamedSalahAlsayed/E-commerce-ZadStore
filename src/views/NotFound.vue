<template>
  <v-locale-provider rtl>
    <div class="not-found-container" style="overflow: hidden">
      <v-container
        class="text-center d-flex flex-column align-center justify-center h-100"
      >
        <div class="error-code font-weight-black">
          4<span class="zero-text">0</span>4
        </div>

        <h2 class="text-h4 font-weight-black mb-4 title-color">
          الصفحة غير موجودة
        </h2>

        <p class="text-grey-darken-1 text-h6 mb-8" style="max-width: 600px">
          يبدو أنك وصلت لمكان مجهول في النظام.. الصفحة التي تبحث عنها تم نقلها،
          حذفها، أو لم تكن موجودة من الأساس.
        </p>

        <v-btn
          color="rgb(var(--v-theme-primary))"
          size="x-large"
          variant="flat"
          class="px-8 font-weight-bold rounded-xl shadow-btn text-white"
          prepend-icon="mdi-home-outline"
          @click="$router.push('/')"
        >
          العودة للصفحة الرئيسية
        </v-btn>
      </v-container>
    </div>
  </v-locale-provider>
</template>

<script setup>
// مفيش لوجيك كتير هنا، التوجيه بيتم من الزرار مباشرة
</script>

<style scoped>
.not-found-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  /* خلفية متدرجة خفيفة جداً تدي عمق للصفحة */
  background: radial-gradient(circle at center, #ffffff 0%, #f4f6f8 100%);
  overflow: hidden;
}

.title-color {
  color: rgb(var(--v-theme-primary));
}

/* تنسيق رقم 404 */
.error-code {
  font-size: 160px;
  line-height: 1;
  color: rgb(var(--v-theme-primary));
  text-shadow: 6px 6px 0px rgba(14, 22, 67, 0.08);
  margin-bottom: 20px;
  animation: float 3s ease-in-out infinite; /* حركة الطفو */
}

/* تنسيق الصفر اللي في النص باللون الأصفر بتاع المتجر */
.zero-text {
  color: #ffca28;
  display: inline-block;
  animation: pulse 2s infinite ease-in-out; /* حركة نبض خفيفة */
}

/* زرار العودة بظل شيك */
.shadow-btn {
  box-shadow: 0 8px 20px rgba(14, 22, 67, 0.2) !important;
  transition: all 0.3s ease;
}

.shadow-btn:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 25px rgba(14, 22, 67, 0.3) !important;
  background-color: #ffca28 !important; /* بيقلب أصفر لما تقف عليه */
  color: rgb(var(--v-theme-primary)) !important;
}

/* =========================================
   الأنيميشن (Keyframes)
========================================= */
@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-15px);
  }
  100% {
    transform: translateY(0px);
  }
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05) rotate(3deg);
  }
  100% {
    transform: scale(1);
  }
}

/* تجاوب الشاشات الصغيرة (الموبايل) */
@media (max-width: 600px) {
  .error-code {
    font-size: 100px;
  }
  h2 {
    font-size: 24px !important;
  }
}
</style>
