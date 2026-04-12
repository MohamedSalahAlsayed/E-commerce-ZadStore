<template>
  <section class="py-16 bg-white">
    <v-container>
      <div class="text-center mb-12">
        <v-chip color="primary" variant="tonal" class="mb-3 font-weight-bold">
          {{ $t("home.faq.chip") }}
        </v-chip>
        <h2 class="text-h3 font-weight-black text-grey-darken-4 mb-4">
          {{ $t("home.faq.title") }}
        </h2>
        <p class="text-h6 text-grey-darken-1 mx-auto" style="max-width: 600px">
          {{ $t("home.faq.subtitle") }}
        </p>
      </div>

      <v-row justify="center">
        <v-col cols="12" md="8">
          <v-expansion-panels variant="popout">
            <v-expansion-panel
              v-for="(faq, i) in faqs"
              :key="i"
              class="mb-3 rounded-xl elevation-2"
            >
              <v-expansion-panel-title class="text-h6 font-weight-bold pa-5">
                {{ faq.question }}
              </v-expansion-panel-title>
              <v-expansion-panel-text
                class="text-body-1 text-grey-darken-2 pa-4 pt-0"
                style="line-height: 1.8"
              >
                {{ faq.answer }}
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const faqs = computed(() => [
  {
    question: t("home.faq.q1"),
    answer: t("home.faq.a1"),
  },
  {
    question: t("home.faq.q2"),
    answer: t("home.faq.a2"),
  },
  {
    question: t("home.faq.q3"),
    answer: t("home.faq.a3"),
  },
  {
    question: t("home.faq.q4"),
    answer: t("home.faq.a4"),
  },
]);
</script>
