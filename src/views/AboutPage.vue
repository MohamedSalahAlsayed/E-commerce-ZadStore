<template>
  <v-container class="py-16" style="min-height: 80vh">
    <v-row align="center">
      <v-col cols="12" md="6">
        <h1 class="text-h3 font-weight-black text-primary-darken-3 mb-6">
          {{
            $t("about.about_store", {
              name: settingsStore.storeName || $t("about.default_store_name"),
            })
          }}
        </h1>
        <p class="text-h6 text-grey-darken-1 mb-6 lh-relaxed">
          {{
            settingsStore.storeDescription || $t("about.default_description")
          }}
        </p>
        <p
          class="text-body-1 text-grey-darken-2 mb-8 lh-relaxed"
          v-if="!settingsStore.storeDescription"
        >
          {{ $t("about.modern_experience") }}
        </p>
        <v-btn
          color="primary"
          size="x-large"
          class="rounded-lg font-weight-bold"
          to="/ContactPage"
        >
          {{ $t("about.contact_btn") }}
        </v-btn>
      </v-col>
      <v-col cols="12" md="6">
        <v-img
          src="https://illustrations.popsy.co/amber/business-team.svg"
          height="400"
          alt="About Us"
          class="rounded-xl bg-grey-lighten-4 pa-8"
        ></v-img>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { onMounted } from "vue";
import { useSettingsStore } from "@/store/Settings";
import { useI18n } from "vue-i18n";

useI18n();
const settingsStore = useSettingsStore();

onMounted(() => {
  if (!settingsStore.storeName) {
    settingsStore.fetchSettings();
  }
});
</script>

<style scoped>
.lh-relaxed {
  line-height: 1.8;
}
</style>
