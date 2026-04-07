<template>
  <v-container class="py-8" dir="rtl">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card elevation="3" class="rounded-xl overflow-hidden">
          <v-toolbar
            color="rgb(var(--v-theme-primary))"
            class="text-white px-2"
          >
            <v-icon class="mr-2">mdi-message-text-outline</v-icon>
            <v-toolbar-title class="font-weight-bold"
              >الرسائل الخاصة</v-toolbar-title
            >
            <v-spacer></v-spacer>
            <v-btn
              variant="outlined"
              color="white"
              prepend-icon="mdi-pencil"
              @click="composeDialog = true"
            >
              رسالة جديدة
            </v-btn>
          </v-toolbar>

          <div v-if="loading" class="text-center pa-10">
            <v-progress-circular
              indeterminate
              color="primary"
            ></v-progress-circular>
          </div>

          <v-list lines="two" class="bg-surface" v-else>
            <template v-for="(msg, index) in messages" :key="msg.id">
              <v-list-item
                :value="msg.id"
                class="pa-4 cursor-pointer transition-fast-in-fast-out hover-bg"
                @click="openMessage(msg)"
              >
                <template v-slot:prepend>
                  <v-avatar
                    size="55"
                    class="border-sm ml-4"
                    color="primary-lighten-4"
                  >
                    <span class="text-h6 text-primary">
                      {{ msg.subject ? msg.subject.charAt(0) : "Z" }}
                    </span>
                  </v-avatar>
                </template>

                <v-list-item-title
                  class="d-flex justify-space-between align-center mb-1"
                >
                  <span class="font-weight-bold text-subtitle-1">
                    {{ msg.subject || "بدون عنوان" }}
                  </span>
                  <span class="text-caption text-grey-darken-1">
                    {{ new Date(msg.created_at).toLocaleString("ar-EG") }}
                  </span>
                </v-list-item-title>

                <v-list-item-subtitle
                  class="text-body-2"
                  :class="{
                    'font-weight-bold text-black': msg.reply_text,
                    'text-grey': !msg.reply_text,
                  }"
                >
                  {{
                    msg.reply_text
                      ? "تم الرد: " + msg.reply_text.substring(0, 30) + "..."
                      : msg.message.substring(0, 40) + "..."
                  }}
                </v-list-item-subtitle>

                <template v-slot:append v-if="msg.reply_text">
                  <v-chip color="success" size="x-small" label class="mr-2"
                    >تم الرد</v-chip
                  >
                </template>
                <template v-slot:append v-else>
                  <v-chip color="warning" size="x-small" label class="mr-2"
                    >قيد المراجعة</v-chip
                  >
                </template>
              </v-list-item>

              <v-divider v-if="index < messages.length - 1" inset></v-divider>
            </template>
          </v-list>

          <div
            v-if="!loading && messages.length === 0"
            class="text-center pa-10 text-grey"
          >
            <v-icon size="60" color="grey-lighten-2" class="mb-4"
              >mdi-message-off-outline</v-icon
            >
            <h3>لا توجد رسائل حالياً</h3>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog for Reading/Viewing a Message -->
    <v-dialog v-model="viewDialog" max-width="600" dir="rtl">
      <v-card class="rounded-xl">
        <v-card-title
          class="bg-primary text-white d-flex justify-space-between align-center"
        >
          <span class="text-h6">
            {{ selectedMessage?.subject || "بدون عنوان" }}
          </span>
          <v-btn
            icon="mdi-close"
            variant="text"
            color="white"
            @click="viewDialog = false"
          ></v-btn>
        </v-card-title>
        <v-card-text class="pt-4">
          <div class="mb-4">
            <div class="text-caption text-grey">
              الرسالة المُرسلة ({{
                new Date(selectedMessage?.created_at).toLocaleString("ar-EG")
              }})
            </div>
            <p class="text-body-1 mt-1 bg-grey-lighten-4 pa-3 rounded-lg">
              {{ selectedMessage?.message }}
            </p>
          </div>
          <v-divider
            class="my-4"
            v-if="selectedMessage?.reply_text"
          ></v-divider>
          <div v-if="selectedMessage?.reply_text">
            <div class="text-caption text-success font-weight-bold">
              رد الإدارة ({{
                new Date(selectedMessage?.replied_at).toLocaleString("ar-EG")
              }})
            </div>
            <p
              class="text-body-1 mt-1 bg-green-lighten-5 pa-3 rounded-lg border-s-4 border-success"
            >
              {{ selectedMessage?.reply_text }}
            </p>
          </div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Dialog for Sending a New Message -->
    <v-dialog v-model="composeDialog" max-width="500" dir="rtl">
      <v-card class="rounded-xl">
        <v-card-title
          class="bg-primary text-white d-flex justify-space-between align-center"
        >
          <span class="text-h6">إرسال رسالة جديدة</span>
          <v-btn
            icon="mdi-close"
            variant="text"
            color="white"
            @click="composeDialog = false"
          ></v-btn>
        </v-card-title>
        <v-card-text class="pt-6">
          <v-form @submit.prevent="sendMessage">
            <v-text-field
              v-model="newMessage.subject"
              label="عنوان الرسالة"
              variant="outlined"
              density="comfortable"
              class="mb-2"
            ></v-text-field>
            <v-textarea
              v-model="newMessage.message"
              label="محتوى الرسالة"
              rows="4"
              variant="outlined"
              required
            ></v-textarea>
            <div class="d-flex justify-end gap-2 mt-4">
              <v-btn variant="text" @click="composeDialog = false">إلغاء</v-btn>
              <v-btn
                color="primary"
                type="submit"
                elevation="1"
                :loading="sending"
                >إرسال</v-btn
              >
            </div>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onUnmounted, inject } from "vue";
import api from "@/axios";

const emitter = inject("emitter");

const messages = ref([]);
const loading = ref(true);
const viewDialog = ref(false);
const composeDialog = ref(false);
const selectedMessage = ref(null);
const sending = ref(false);

const newMessage = ref({ subject: "", message: "" });

const fetchMessages = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const res = await api.get("/user/messages");
    messages.value = res.data;
    // بما أن الجلب من الـ backend يصفر العداد، نطلب تحديث الهيدر
    emitter.emit("updateHeaderData");
  } catch (error) {
    console.error("Failed to fetch messages:", error);
  } finally {
    if (showLoading) loading.value = false;
  }
};

const openMessage = (message) => {
  selectedMessage.value = message;
  viewDialog.value = true;
};

const sendMessage = async () => {
  sending.value = true;
  try {
    const res = await api.post("/user/messages", newMessage.value);
    messages.value.unshift(res.data.contact_message);
    composeDialog.value = false;
    newMessage.value = { subject: "", message: "" };
    emitter.emit("showMessage", {
      text: "تم إرسال رسالتك بنجاح!",
      color: "success",
    });
    emitter.emit("updateHeaderData"); // تحديث العدادات
  } catch (err) {
    emitter.emit("showMessage", {
      text: "حدث خطأ أثناء الإرسال",
      color: "error",
    });
    console.error(err);
  } finally {
    sending.value = false;
  }
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchMessages(false), 30000); // 30 seconds
};

onMounted(() => {
  fetchMessages();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchMessages(false);
      startPolling();
    } else {
      if (pollInterval) clearInterval(pollInterval);
    }
  });
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});
</script>

<style scoped>
.hover-bg:hover {
  background-color: #f8f9fa;
}
.gap-2 {
  gap: 8px;
}
</style>
