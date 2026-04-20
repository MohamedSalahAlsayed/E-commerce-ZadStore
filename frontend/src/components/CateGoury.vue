<template>
  <v-container class="py-12 section-categories">
    <!-- Section Header -->
    <div
      class="d-flex flex-column align-center justify-center mb-10 text-center"
    >
      <v-chip
        class="category-badge mb-4 font-weight-bold px-6"
        variant="flat"
        size="large"
      >
        {{ $t("home.categories.chip") || "Browse by Categories" }}
      </v-chip>
      <h2 class="category-main-title font-weight-black mb-2">
        {{ $t("home.categories.title") || "التصنيفات" }}
      </h2>
      <div class="title-divider">
        <span class="bar long"></span>
        <span class="bar short"></span>
        <span class="bar short"></span>
      </div>
    </div>

    <!-- Loading State (Skeletons) -->
    <v-row v-if="loading">
      <v-col
        v-for="n in 6"
        :key="n"
        cols="6"
        sm="4"
        md="3"
        lg="2"
        class="pa-2 pa-md-4"
      >
        <v-skeleton-loader
          type="image, text"
          class="rounded-xl overflow-hidden"
          height="200"
        ></v-skeleton-loader>
      </v-col>
    </v-row>

    <!-- Content State -->
    <div
      v-else-if="categModule.categories.length"
      class="swiper-wrapper-relative"
    >
      <swiper
        :key="categModule.categories.length"
        :modules="modules"
        :slides-per-view="1"
        :space-between="20"
        :navigation="{
          nextEl: '.custom-nav-next',
          prevEl: '.custom-nav-prev',
        }"
        :breakpoints="{
          320: { slidesPerView: 2, spaceBetween: 15 },
          480: { slidesPerView: 3, spaceBetween: 15 },
          768: { slidesPerView: 4, spaceBetween: 20 },
          1024: { slidesPerView: 5, spaceBetween: 25 },
          1280: { slidesPerView: 6, spaceBetween: 30 },
        }"
        class="category-swiper px-2 pt-4 pb-10"
        :dir="locale === 'ar' ? 'rtl' : 'ltr'"
      >
        <swiper-slide v-for="categ in categModule.categories" :key="categ.id">
          <div
            class="category-card-wrapper"
            @click.stop="handleCategoryClick(categ)"
          >
            <v-hover v-slot:default="{ isHovering, props }">
              <div
                v-bind="props"
                class="category-card"
                :class="{ 'elevation-12': isHovering }"
              >
                <div class="img-container">
                  <v-img
                    :src="getImageUrl(categ.image || categ.thumbnail)"
                    :alt="categ.name"
                    class="category-img"
                    cover
                  >
                    <template v-slot:placeholder>
                      <div
                        class="d-flex align-center justify-center fill-height bg-grey-lighten-4"
                      >
                        <v-progress-circular
                          indeterminate
                          color="primary"
                          size="24"
                          width="2"
                        ></v-progress-circular>
                      </div>
                    </template>
                  </v-img>
                  <div class="card-overlay" :class="{ active: isHovering }">
                    <v-btn
                      icon="mdi-eye-outline"
                      color="white"
                      variant="flat"
                      size="small"
                      class="view-btn"
                    ></v-btn>
                  </div>
                </div>

                <div class="category-info pa-4 text-center">
                  <h3 class="category-name text-truncate">
                    {{ categ.name || categ.title }}
                  </h3>
                  <div class="category-count text-caption text-grey">
                    {{ categ.products_count || 0 }}
                    {{ $t("home.categories.products_unit") || "منتج" }}
                  </div>
                </div>
              </div>
            </v-hover>
          </div>
        </swiper-slide>
      </swiper>

      <!-- Custom Navigation Buttons -->
      <button class="custom-nav-btn custom-nav-prev">
        <v-icon>{{
          locale === "ar" ? "mdi-chevron-right" : "mdi-chevron-left"
        }}</v-icon>
      </button>
      <button class="custom-nav-btn custom-nav-next">
        <v-icon>{{
          locale === "ar" ? "mdi-chevron-left" : "mdi-chevron-right"
        }}</v-icon>
      </button>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-10 opacity-50">
      <v-icon size="64">mdi-shape-outline</v-icon>
      <p class="mt-4">
        {{ $t("home.categories.no_data") || "لا توجد أقسام متاحة حالياً" }}
      </p>
    </div>
  </v-container>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { ProductModule } from "@/store/Products.js";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Navigation } from "swiper/modules";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import "swiper/css";
import "swiper/css/navigation";

const { locale } = useI18n();
const categModule = ProductModule();
const router = useRouter();
const modules = [Navigation];
const loading = ref(false);

const getImageUrl = (img) => {
  if (!img) return "https://cdn-icons-png.flaticon.com/512/3502/3502601.png";
  if (img.startsWith("http")) return img;
  const baseUrl = process.env.VUE_APP_API_URL || "http://localhost:8000";
  return `${baseUrl}${img}`;
};

const handleCategoryClick = (categ) => {
  // Navigation should work even for guests, or as per project requirements
  router.push({ name: "CategoryPage", params: { slug: categ.id } });
};

onMounted(async () => {
  if (!categModule.categories.length) {
    loading.value = true;
    try {
      await categModule.getCategories();
    } finally {
      loading.value = false;
    }
  }
});
</script>

<style scoped>
.swiper-wrapper-relative {
  position: relative;
}

/* Custom Navigation Styling */
.custom-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 44px;
  height: 44px;
  background: white;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  z-index: 10;
  cursor: pointer;
  color: rgb(var(--v-theme-primary));
}

.custom-nav-btn:hover {
  background: rgb(var(--v-theme-primary));
  color: white;
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 8px 20px rgba(var(--v-theme-primary), 0.3);
}

.custom-nav-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
  transform: translateY(-50%) scale(1);
}

.custom-nav-prev {
  left: -10px;
}
.custom-nav-next {
  right: -10px;
}

@media (max-width: 960px) {
  .custom-nav-prev {
    left: 0;
  }
  .custom-nav-next {
    right: 0;
  }
}

@media (max-width: 600px) {
  .custom-nav-btn {
    display: none !important;
  }
}

/* Category Card Styling */
.category-card-wrapper {
  cursor: pointer;
  height: 100%;
  padding: 8px;
}

.category-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
  border: 1px solid rgba(0, 0, 0, 0.04);
  height: 100%;
  display: flex;
  flex-direction: column;
}

.img-container {
  position: relative;
  width: 100%;
  aspect-ratio: 1/1;
  background: #fdfdfd;
  overflow: hidden;
}

.category-img {
  width: 100%;
  height: 100%;
  transition: transform 1.2s cubic-bezier(0.19, 1, 0.22, 1);
}

.category-card:hover .category-img {
  transform: scale(1.1) rotate(-1deg);
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    to top,
    rgba(var(--v-theme-primary), 0.4),
    transparent
  );
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.4s ease;
  transform: translateY(100%);
}

.card-overlay.active {
  opacity: 1;
  transform: translateY(0);
}

.view-btn {
  transform: scale(0);
  transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.1s;
}

.card-overlay.active .view-btn {
  transform: scale(1);
}

.category-name {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1a202c;
  margin-bottom: 2px;
  transition: color 0.3s ease;
}

.category-card:hover .category-name {
  color: rgb(var(--v-theme-primary));
}

.category-count {
  font-weight: 600;
  color: #718096 !important;
}

/* Section Header Redesign */
.category-badge {
  background-color: rgba(var(--v-theme-primary), 0.1) !important;
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 0.9rem !important;
  letter-spacing: 0.5px;
}

.category-main-title {
  font-size: clamp(1.8rem, 3.5vw, 2.8rem);
  color: rgb(var(--v-theme-primary));
  line-height: 1.2;
}

.title-divider {
  display: flex;
  gap: 12px;
  justify-content: center;
  align-items: center;
  margin-top: 10px;
}

.bar {
  height: 6px;
  background-color: rgb(var(--v-theme-primary));
  border-radius: 10px;
  display: inline-block;
}

.bar.long {
  width: 100px;
}

.bar.short {
  width: 25px;
}

@media (max-width: 600px) {
  .category-main-title {
    font-size: 2.5rem;
  }
  .bar.long {
    width: 60px;
  }
  .bar.short {
    width: 15px;
  }
  .title-divider {
    gap: 8px;
  }
}
</style>
