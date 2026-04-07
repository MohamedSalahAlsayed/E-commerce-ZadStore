<template>
  <v-container class="py-16" style="min-height: 80vh">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="rounded-xl pa-8 pa-md-10" elevation="4">
          <div class="text-center mb-10">
            <v-icon size="60" color="primary" class="mb-4"
              >mdi-email-fast-outline</v-icon
            >
            <h1 class="text-h4 font-weight-black text-primary-darken-3">
              {{ $t("contact.title") }}
            </h1>
            <p class="text-grey-darken-1 mt-2">
              {{ $t("contact.subtitle") }}
            </p>
          </div>

          <v-form
            @submit.prevent="submitContact"
            ref="formRef"
            v-model="isValid"
          >
            <v-text-field
              v-model="contactData.name"
              :label="$t('contact.name_label')"
              prepend-inner-icon="mdi-account-outline"
              variant="outlined"
              class="mb-2"
              color="primary"
              :rules="[(v) => !!v || $t('contact.name_required')]"
            ></v-text-field>

            <v-text-field
              v-model="contactData.email"
              :label="$t('contact.email_label')"
              prepend-inner-icon="mdi-email-outline"
              variant="outlined"
              class="mb-2"
              color="primary"
              :rules="[
                (v) => !!v || $t('contact.email_required'),
                (v) => /.+@.+\..+/.test(v) || $t('contact.email_invalid'),
              ]"
            ></v-text-field>

            <v-textarea
              v-model="contactData.message"
              :label="$t('contact.message_label')"
              prepend-inner-icon="mdi-message-outline"
              variant="outlined"
              class="mb-6"
              color="primary"
              rows="4"
              :rules="[(v) => !!v || $t('contact.message_required')]"
            ></v-textarea>

            <v-btn
              block
              size="large"
              color="primary"
              class="rounded-lg font-weight-bold elevation-2"
              type="submit"
              :loading="loading"
            >
              {{ $t("contact.send_btn") }}
            </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, inject } from "vue";
import api from "@/axios";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const emitter = inject("emitter");

const isValid = ref(false);
const loading = ref(false);
const formRef = ref(null);

const contactData = ref({
  name: "",
  email: "",
  message: "",
});

const submitContact = async () => {
  if (!isValid.value) return;

  loading.value = true;
  try {
    const res = await api.post("/contact", contactData.value);
    emitter.emit("showMessage", {
      text: res.data.message || t("contact.success_msg"),
      color: "success",
    });
    formRef.value.reset();
  } catch (error) {
    emitter.emit("showMessage", {
      text: error.response?.data?.message || t("contact.error_msg"),
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};
</script>
