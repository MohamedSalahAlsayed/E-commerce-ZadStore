<template>
  <v-container>
    <div class="d-flex align-center justify-center mb-6">
      <h2 class="text-h4 font-weight-black text-primary">
        {{ $t("home.categories.title") }}
      </h2>
    </div>

    <v-slide-group show-arrows class="py-4 category-slider">
      <v-slide-group-item v-for="categ in Categoury.flashDeals" :key="categ.id">
        <div
          class="category-item mx-4 d-flex flex-column align-center text-center"
          @click.stop="handleCategoryClick(categ.title)"
        >
          <div class="img-wrapper elevation-3">
            <img
              :src="categ.thumbnail"
              class="category-img"
              alt="Category Image"
            />
          </div>

          <h3
            class="mt-4 text-subtitle-1 font-weight-bold text-truncate category-title"
          >
            {{ categ.title }}
          </h3>
        </div>
      </v-slide-group-item>
    </v-slide-group>
  </v-container>
</template>

<script setup>
import { onMounted } from "vue";
import { ProductModule } from "@/store/Products.js";
import { useRouter } from "vue-router";
import { useI18n } from "vue-i18n";

useI18n();

const Categoury = ProductModule();
const router = useRouter();

const handleCategoryClick = (title) => {
  if (!localStorage.getItem("auth_token")) {
    router.push("/Auth/LogIn");
    return;
  }
  router.push({ name: "CategoryPage", params: { slug: title } });
};

onMounted(async () => {
  await Categoury.getProducts();
  console.log(Categoury.flashDeals);
});
</script>

<style scoped>
/* إعدادات السلايدر */
.category-slider :deep(.v-slide-group__content) {
  padding-bottom: 20px;
}

/* العنصر نفسه (القسم) */
.category-item {
  width: 140px; /* تثبيت العرض عشان يكونوا كلهم قد بعض */
  cursor: pointer;
  transition: all 0.3s ease;
}

/* حاوية الصورة الدائرية */
.img-wrapper {
  width: 130px;
  height: 130px;
  border-radius: 50%;
  overflow: hidden; /* مهم جداً عشان الصورة متطلعش برا الدايرة لما تكبر */
  border: 3px solid rgb(var(--v-theme-surface));
  background-color: rgb(
    var(--v-theme-background)
  ); /* لون خلفية تحسباً لو الصورة شفافة */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

/* الصورة نفسها */
.category-img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* لملء الدايرة بدون مط الصورة */
  transition: transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* تأثيرات الـ Hover */
.category-item:hover .category-img {
  transform: scale(1.15); /* التكبير بنسبة أهدأ شوية عشان يكون أنيق */
}

.category-item:hover .img-wrapper {
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 8px 20px rgba(14, 22, 67, 0.2);
}

.category-item:hover .category-title {
  color: rgb(var(--v-theme-primary));
}

/* عنوان القسم */
.category-title {
  max-width: 130px; /* عشان الاسم الطويل يتقص ويعمل نقط ... */
  color: rgb(var(--v-theme-on-surface));
  transition: color 0.3s ease;
}

/* تنسيق الخط تحت كلمة Category */
.section-title {
  position: relative;
  display: inline-block;
  padding-bottom: 12px;
  font-weight: 800;
  color: rgb(var(--v-theme-primary));
  font-size: 28px;
  text-align: right;
}

.section-title::before {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%; /* خط قصير وأنيق */
  height: 4px;
  background: rgb(var(--v-theme-primary));
  border-radius: 4px;
}
</style>
