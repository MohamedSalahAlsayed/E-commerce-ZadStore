<template>
  <v-container class="py-8 fill-height flex-column" dir="rtl">
    <v-row justify="center" class="chat-wrapper w-100">
      <v-col cols="12" md="10" lg="8" class="h-100 d-flex flex-column">
        <!-- Chat Header -->
        <v-card
          elevation="4"
          class="chat-header rounded-t-xl overflow-hidden border-b"
          color="primary"
        >
          <div class="header-overlay"></div>
          <v-card-text class="pa-4 d-flex align-center position-relative">
            <v-avatar size="56" class="me-4 border-sm bg-white-opacity-20">
              <v-icon size="32" color="white">mdi-headphones</v-icon>
            </v-avatar>
            <div>
              <h3 class="text-h6 font-weight-bold text-white mb-0">
                الدعم الفني والرسائل
              </h3>
              <div
                class="d-flex align-center text-caption text-white opacity-80"
              >
                <v-badge dot color="success" class="me-2" inline></v-badge>
                متصل الآن - سنرد عليك في أقرب وقت
              </div>
            </div>
            <v-spacer></v-spacer>
            <v-btn
              icon="mdi-refresh"
              variant="text"
              color="white"
              @click="fetchMessages"
              :loading="loading"
            ></v-btn>
          </v-card-text>
        </v-card>

        <!-- Chat History Area -->
        <v-card
          elevation="4"
          class="chat-body rounded-0 flex-grow-1 overflow-y-auto pa-4 bg-grey-lighten-4"
          ref="chatContainer"
        >
          <div
            v-if="loading && !messages.length"
            class="d-flex fill-height align-center justify-center"
          >
            <v-progress-circular
              indeterminate
              color="primary"
              size="64"
            ></v-progress-circular>
          </div>

          <div
            v-else-if="!messages.length"
            class="empty-chat d-flex flex-column align-center justify-center fill-height text-grey-darken-1"
          >
            <v-icon size="100" color="grey-lighten-2" class="mb-4"
              >mdi-chat-question-outline</v-icon
            >
            <h3 class="text-h6">لا توجد محادثات سابقة</h3>
            <p>ابدأ بمراسلتنا الآن بخصوص أي استفسار</p>
          </div>

          <div v-else class="messages-list d-flex flex-column gap-6">
            <div v-for="msg in flatMessages" :key="msg.id + '-' + msg.type">
              <!-- User Message Bubble -->
              <div
                v-if="msg.sender === 'user'"
                class="d-flex justify-start mb-4"
              >
                <div class="bubble-container user-bubble">
                  <div class="bubble-content shadow-sm">
                    <div
                      class="text-caption font-weight-bold mb-1 opacity-70"
                      v-if="msg.subject"
                    >
                      {{ msg.subject }}
                    </div>
                    <div class="text-body-1">{{ msg.text }}</div>
                    <div class="text-micro mt-2 text-end opacity-60">
                      {{ formatTime(msg.time) }}
                      <v-icon size="12" class="ms-1">{{
                        msg.replied ? "mdi-check-all" : "mdi-check"
                      }}</v-icon>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Admin Reply Bubble -->
              <div v-else class="d-flex justify-end mb-4">
                <div class="bubble-container admin-bubble">
                  <v-avatar
                    size="32"
                    class="mb-n6 ms-n4 position-relative z-index-2 border shadow-sm"
                    color="white"
                  >
                    <v-icon size="16" color="primary">mdi-shield-check</v-icon>
                  </v-avatar>
                  <div class="bubble-content shadow-sm bg-white">
                    <div
                      class="text-caption font-weight-bold mb-1 text-primary d-flex align-center"
                    >
                      فريق الدعم
                      <v-chip
                        size="x-small"
                        color="primary"
                        class="ms-2 px-1"
                        variant="tonal"
                        >مسؤول</v-chip
                      >
                    </div>
                    <div class="text-body-1">{{ msg.text }}</div>
                    <div class="text-micro mt-2 opacity-60">
                      {{ formatTime(msg.time) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </v-card>

        <!-- Chat Input -->
        <v-card
          elevation="8"
          class="chat-input-card rounded-b-xl pa-3 border-t"
        >
          <v-form
            @submit.prevent="sendMessage"
            class="d-flex align-center gap-2"
          >
            <v-text-field
              v-model="newMessage.message"
              placeholder="اكتب رسالتك هنا..."
              variant="flat"
              bg-color="grey-lighten-4"
              hide-details
              rounded="pill"
              class="message-field"
              :loading="sending"
              @keydown.enter.prevent="sendMessage"
            >
              <template v-slot:prepend-inner>
                <v-icon color="grey">mdi-emoticon-outline</v-icon>
              </template>
            </v-text-field>
            <v-btn
              color="primary"
              icon="mdi-send"
              size="large"
              elevation="4"
              class="send-btn"
              type="submit"
              :disabled="!newMessage.message.trim()"
              :loading="sending"
            ></v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import {
  ref,
  computed,
  onMounted,
  onUnmounted,
  inject,
  nextTick,
  watch,
} from "vue";
import api from "@/axios";

const emitter = inject("emitter");
const chatContainer = ref(null);

const messages = ref([]);
const loading = ref(true);
const sending = ref(false);
const newMessage = ref({ subject: "", message: "" });

// Transform single message-reply pairs into flat chronological bubbles
const flatMessages = computed(() => {
  const result = [];
  messages.value.forEach((m) => {
    // Original User message
    result.push({
      id: m.id,
      type: "user_msg",
      sender: "user",
      text: m.message,
      subject: m.subject === "مراسلة من الشات" ? null : m.subject,
      time: m.created_at,
      replied: !!m.reply_text,
    });
    // Admin reply if exists
    if (m.reply_text) {
      result.push({
        id: m.id,
        type: "admin_reply",
        sender: "admin",
        text: m.reply_text,
        time: m.replied_at || m.created_at,
      });
    }
  });
  // Sort by time
  return result.sort((a, b) => new Date(a.time) - new Date(b.time));
});

const fetchMessages = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const res = await api.get("/user/messages");
    messages.value = res.data;
    emitter.emit("updateHeaderData");
  } catch (error) {
    console.error("Failed to fetch messages:", error);
  } finally {
    if (showLoading) loading.value = false;
  }
};

const sendMessage = async () => {
  if (!newMessage.value.message.trim()) return;
  sending.value = true;
  try {
    const res = await api.post("/user/messages", {
      subject: null,
      message: newMessage.value.message,
    });
    messages.value.push(res.data.contact_message);
    newMessage.value = { subject: "", message: "" };
    emitter.emit("updateHeaderData");
    nextTick(() => scrollChatToBottom());
  } catch (err) {
    emitter.emit("showMessage", {
      text: "حدث خطأ أثناء الإرسال",
      color: "error",
    });
  } finally {
    sending.value = false;
  }
};

const scrollChatToBottom = () => {
  if (chatContainer.value?.$el) {
    const container = chatContainer.value.$el;
    container.scrollTo({
      top: container.scrollHeight,
      behavior: "smooth",
    });
  }
};

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleTimeString("ar-EG", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Handle polling logic
let pollInterval = null;
const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchMessages(false), 15000); // More frequent for chat (15s)
};

watch(
  flatMessages,
  () => {
    nextTick(() => scrollChatToBottom());
  },
  { deep: true }
);

onMounted(() => {
  fetchMessages().then(() => {
    nextTick(() => scrollChatToBottom());
  });
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
.fill-height {
  height: calc(100vh - 100px);
}
.chat-wrapper {
  height: 100%;
}
.chat-header {
  position: relative;
  z-index: 2;
}
.header-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    135deg,
    rgba(255, 255, 255, 0.1) 0%,
    transparent 100%
  );
  z-index: 1;
}
.bg-white-opacity-20 {
  background-color: rgba(255, 255, 255, 0.2) !important;
}
.chat-body {
  background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
  scrollbar-width: thin;
}
.chat-body::-webkit-scrollbar {
  width: 6px;
}
.chat-body::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}

.bubble-container {
  max-width: 80%;
  position: relative;
}
.bubble-content {
  padding: 12px 16px;
  border-radius: 20px;
  position: relative;
}

/* User Bubble Styles (Right/Primary) */
.user-bubble .bubble-content {
  background-color: rgb(var(--v-theme-primary));
  color: white;
  border-bottom-right-radius: 4px;
}
.user-bubble::after {
  content: "";
  position: absolute;
  bottom: 0;
  right: -8px;
  width: 12px;
  height: 12px;
  background-color: rgb(var(--v-theme-primary));
  clip-path: polygon(0 0, 0% 100%, 100% 100%);
}

/* Admin Bubble Styles (Left/White) */
.admin-bubble .bubble-content {
  background-color: white;
  color: #333;
  border-bottom-left-radius: 4px;
}
.admin-bubble::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: -8px;
  width: 12px;
  height: 12px;
  background-color: white;
  clip-path: polygon(100% 0, 0% 100%, 100% 100%);
}

.text-micro {
  font-size: 10px;
}
.gap-6 {
  gap: 24px;
}
.message-field {
  flex-grow: 1;
}
.send-btn {
  transition: transform 0.2s;
}
.send-btn:active {
  transform: scale(0.9);
}
.z-index-2 {
  z-index: 2;
}

@media (max-width: 600px) {
  .bubble-container {
    max-width: 90%;
  }
  .fill-height {
    height: calc(100vh - 60px);
  }
}
</style>
