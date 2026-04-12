<template>
  <section class="py-16 bg-grey-lighten-5">
    <v-container>
      <div class="text-center mb-16">
        <h2 class="text-h3 font-weight-black mb-4 text-grey-darken-4">
          {{ $t("home.how_it_works_title") }}
        </h2>
        <p class="text-h6 text-grey-darken-1">
          {{ $t("home.how_it_works_subtitle") }}
        </p>
      </div>
      <v-row>
        <v-col cols="12" sm="6" md="3" v-for="(step, i) in steps" :key="i">
          <div class="text-center px-4 h-100 d-flex flex-column align-center">
            <v-avatar
              color="primary"
              size="80"
              class="mb-6 elevation-8 position-relative"
            >
              <v-icon size="40" color="white">{{ step.icon }}</v-icon>
              <div class="step-badge">{{ i + 1 }}</div>
            </v-avatar>
            <h3 class="text-h5 font-weight-black mb-3">{{ step.title }}</h3>
            <p class="text-body-1 text-grey-darken-1">{{ step.desc }}</p>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

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
</script>

<style scoped>
.step-badge {
  position: absolute;
  top: 0;
  inset-inline-end: 0;
  background: #ffc107;
  color: #1a1a2e;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
  border: 2px solid #fff;
}
</style>
