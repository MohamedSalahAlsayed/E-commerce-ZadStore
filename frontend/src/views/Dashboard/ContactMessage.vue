<template>
  <div
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    class="admin-chat-view fill-height"
  >
    <v-container fluid class="pa-0 h-100">
      <v-row no-gutters class="h-100 overflow-hidden">
        <!-- Sidebar: Conversation List -->
        <v-col
          cols="12"
          md="4"
          lg="3"
          class="border-e h-100 d-flex flex-column bg-white"
        >
          <div class="pa-4 border-b">
            <h2
              class="text-h6 font-weight-black text-primary mb-4 d-flex align-center"
            >
              <v-icon class="me-2">mdi-forum-outline</v-icon>
              {{ $t("messaging.visitor_messages") }}
            </h2>
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              :label="$t('messaging.search_contacts_placeholder')"
              variant="solo-filled"
              flat
              density="compact"
              hide-details
              rounded="pill"
              class="mb-2"
            ></v-text-field>
          </div>

          <v-list class="flex-grow-1 overflow-y-auto pa-0 conversations-list">
            <template v-if="loading && !groupedConversations.length">
              <v-skeleton-loader
                v-for="i in 6"
                :key="i"
                type="list-item-avatar-two-line"
              ></v-skeleton-loader>
            </template>

            <template v-else-if="!groupedConversations.length">
              <div class="text-center pa-8 text-grey mt-10">
                <v-icon size="64" color="grey-lighten-2"
                  >mdi-mailbox-open-outline</v-icon
                >
                <div class="mt-2">{{ $t("messaging.no_messages_found") }}</div>
              </div>
            </template>

            <v-list-item
              v-for="conv in groupedConversations"
              :key="conv.id"
              :active="selectedConvId === conv.id"
              @click="selectConversation(conv)"
              class="conversation-item border-b py-3 px-4"
              :class="{ 'unread-item': conv.hasUnread }"
            >
              <template v-slot:prepend>
                <v-avatar color="primary-lighten-4" class="me-3">
                  <span class="text-primary font-weight-bold">{{
                    conv.name.charAt(0)
                  }}</span>
                </v-avatar>
              </template>

              <v-list-item-title class="font-weight-bold d-flex align-center">
                {{ conv.name }}
                <v-icon
                  v-if="!conv.user_id"
                  size="14"
                  color="warning"
                  class="ms-2"
                  icon="mdi-account-alert-outline"
                ></v-icon>
              </v-list-item-title>
              <v-list-item-subtitle
                class="text-truncate mt-1 text-grey-darken-1"
              >
                {{ conv.lastMessage }}
              </v-list-item-subtitle>

              <template v-slot:append>
                <div class="d-flex flex-column align-end">
                  <span class="text-micro text-grey mb-1">{{
                    formatDate(conv.time)
                  }}</span>
                  <v-badge
                    v-if="conv.hasUnread"
                    dot
                    color="error"
                    inline
                  ></v-badge>
                </div>
              </template>
            </v-list-item>
          </v-list>
        </v-col>

        <!-- Main Chat Area -->
        <v-col
          cols="12"
          md="8"
          lg="9"
          class="h-100 d-flex flex-column bg-grey-lighten-5 position-relative"
        >
          <template v-if="selectedConversation">
            <!-- Chat Header -->
            <v-toolbar
              color="white"
              border="b"
              class="px-4 py-2"
              style="height: auto !important; min-height: 80px"
            >
              <v-avatar size="48" color="primary" class="me-4">
                <span class="text-white">{{
                  selectedConversation.name.charAt(0)
                }}</span>
              </v-avatar>
              <div class="flex-grow-1">
                <div class="d-flex align-center">
                  <h3 class="text-h6 font-weight-bold mb-0 me-2">
                    {{ selectedConversation.name }}
                  </h3>
                  <v-chip
                    size="x-small"
                    :color="
                      selectedConversation.user_id ? 'primary' : 'warning'
                    "
                    variant="tonal"
                  >
                    {{
                      selectedConversation.user_id
                        ? $t("messaging.registered_label")
                        : $t("messaging.guest_label")
                    }}
                  </v-chip>
                </div>
                <div class="text-caption text-grey">
                  {{ selectedConversation.email }}
                </div>
              </div>
              <v-spacer></v-spacer>
              <v-btn
                icon="mdi-delete-outline"
                color="error"
                variant="text"
                @click="confirmDelete(selectedConversation)"
              ></v-btn>
              <v-btn
                icon="mdi-refresh"
                variant="text"
                @click="fetchMessages(false)"
              ></v-btn>
            </v-toolbar>

            <!-- Chat History -->
            <div
              class="chat-history flex-grow-1 overflow-y-auto pa-6 d-flex flex-column gap-4"
              ref="chatContainer"
            >
              <div
                v-for="(msg, idx) in selectedConversation.messages"
                :key="msg.id + '-' + idx"
              >
                <!-- User Message Bubble -->
                <div class="d-flex justify-start mb-4">
                  <div class="bubble admin-bubble-wrapper">
                    <div class="bubble-content shadow-sm user-msg-style">
                      <div
                        class="text-caption text-primary font-weight-bold mb-1"
                        v-if="msg.subject && msg.subject !== 'null'"
                      >
                        {{ msg.subject }}
                      </div>
                      <div class="text-body-1">{{ msg.message }}</div>
                      <div class="text-micro mt-1 text-end opacity-60">
                        {{ formatFullDate(msg.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Admin Reply Bubble -->
                <div v-if="msg.reply_text" class="d-flex justify-end mb-4">
                  <div class="bubble admin-bubble-wrapper text-end">
                    <div
                      class="bubble-content shadow-sm admin-msg-style bg-primary text-white"
                    >
                      <div
                        class="text-caption opacity-80 mb-1 d-flex align-center justify-end"
                      >
                        <v-icon size="12" class="me-1"
                          >mdi-account-check</v-icon
                        >
                        {{ $t("messaging.admin_reply_label") }}
                      </div>
                      <div class="text-body-1 text-start">
                        {{ msg.reply_text }}
                      </div>
                      <div class="text-micro mt-1 opacity-60 text-end">
                        {{ formatFullDate(msg.replied_at) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chat Footer: Input Area -->
            <div class="pa-4 bg-white border-t">
              <!-- Templates Ribbon -->
              <div class="d-flex gap-2 overflow-x-auto mb-3 pb-2 ribbons">
                <v-chip
                  v-for="tpl in replyTemplates"
                  :key="tpl.title"
                  size="small"
                  variant="outlined"
                  color="primary"
                  class="cursor-pointer"
                  @click="applyTemplate(tpl.value)"
                >
                  {{ tpl.title }}
                </v-chip>
              </div>

              <v-form
                @submit.prevent="sendReply"
                class="d-flex align-end gap-3"
              >
                <v-textarea
                  v-model="replyText"
                  :placeholder="$t('messaging.reply_placeholder')"
                  variant="solo-filled"
                  flat
                  density="compact"
                  hide-details
                  rows="1"
                  auto-grow
                  rounded="lg"
                  class="flex-grow-1"
                  color="primary"
                  @keydown.ctrl.enter="sendReply"
                ></v-textarea>
                <v-btn
                  color="primary"
                  icon="mdi-send"
                  size="large"
                  elevation="4"
                  :loading="replying"
                  @click="sendReply"
                  :disabled="!replyText.trim()"
                ></v-btn>
              </v-form>
              <div class="text-micro text-grey mt-2 px-2">
                Ctrl + Enter للإرسال السريع
              </div>
            </div>
          </template>

          <template v-else>
            <div
              class="fill-height d-flex flex-column align-center justify-center text-grey-lighten-1"
            >
              <v-icon size="120">mdi-message-text-outline</v-icon>
              <h3 class="text-h5 mt-4">اختر محادثة للبدء في الرد</h3>
              <p>سيتم عرض تفاصيل الرسائل هنا</p>
            </div>
          </template>
        </v-col>
      </v-row>
    </v-container>

    <!-- Snackbar -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3000"
      location="top center"
    >
      {{ snackbar.text }}
      <template v-slot:actions>
        <v-btn variant="text" @click="snackbar.show = false">إغلاق</v-btn>
      </template>
    </v-snackbar>
  </div>
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
import { useI18n } from "vue-i18n";
import axios from "@/axios";

const { locale, t } = useI18n();
const emitter = inject("emitter");
const chatContainer = ref(null);

const messages = ref([]);
const loading = ref(true);
const selectedConvId = ref(null);
const replyText = ref("");
const replying = ref(false);
const searchQuery = ref("");

const snackbar = ref({ show: false, text: "", color: "success" });

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

// Group messages by email (and user_id if available)
const groupedConversations = computed(() => {
  const groups = {};
  messages.value.forEach((m) => {
    const key = m.user_id ? `u-${m.user_id}` : m.email;
    if (!groups[key]) {
      groups[key] = {
        id: key,
        name: m.name || "زائر",
        email: m.email,
        user_id: m.user_id,
        messages: [],
        hasUnread: false,
        lastMessage: "",
        time: m.created_at,
      };
    }
    groups[key].messages.push(m);
    if (!m.is_read) groups[key].hasUnread = true;
  });

  // Post-process groups
  const sorted = Object.values(groups)
    .map((g) => {
      g.messages.sort(
        (a, b) => new Date(a.created_at) - new Date(b.created_at)
      );
      const last = g.messages[g.messages.length - 1];
      g.lastMessage = last.message;
      g.time = last.created_at;
      return g;
    })
    .sort((a, b) => new Date(b.time) - new Date(a.time));

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    return sorted.filter(
      (c) =>
        c.name.toLowerCase().includes(q) || c.email.toLowerCase().includes(q)
    );
  }
  return sorted;
});

const selectedConversation = computed(() => {
  return (
    groupedConversations.value.find((c) => c.id === selectedConvId.value) ||
    null
  );
});

const selectConversation = async (conv) => {
  selectedConvId.value = conv.id;
  // Mark all messages in this conversation as read
  const unreadMsg = conv.messages.filter((m) => !m.is_read);
  if (unreadMsg.length > 0) {
    try {
      // Mark as read in parallel or sequence
      for (const m of unreadMsg) {
        await axios.put(`/admin/messages/${m.id}`);
        m.is_read = true;
      }
      emitter.emit("updateHeaderData");
    } catch (e) {
      console.error(e);
    }
  }
  nextTick(() => scrollChatToBottom());
};

const sendReply = async () => {
  if (!replyText.value.trim() || !selectedConversation.value) return;
  // Which message to reply to? We'll reply to the latest message in the thread
  const lastMsg =
    selectedConversation.value.messages[
      selectedConversation.value.messages.length - 1
    ];
  replying.value = true;
  try {
    const res = await axios.post(`/admin/messages/${lastMsg.id}/reply`, {
      reply_text: replyText.value,
    });
    // Update local data
    lastMsg.reply_text = res.data.msg.reply_text;
    lastMsg.replied_at = res.data.msg.replied_at;
    replyText.value = "";
    showMessage(t("messaging.reply_success"));
    nextTick(() => scrollChatToBottom());
  } catch (e) {
    showMessage(t("messaging.error_occurred"), "error");
  } finally {
    replying.value = false;
  }
};

const fetchMessages = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const res = await axios.get("/admin/messages");
    messages.value = res.data;
  } catch (err) {
    console.error("Error fetching messages:", err);
  } finally {
    if (showLoading) loading.value = false;
  }
};

const confirmDelete = async (conv) => {
  if (!confirm("هل أنت متأكد من حذف جميع رسائل هذا العميل؟")) return;
  try {
    for (const m of conv.messages) {
      await axios.delete(`/admin/messages/${m.id}`);
    }
    messages.value = messages.value.filter((m) => {
      const key = m.user_id ? `u-${m.user_id}` : m.email;
      return key !== conv.id;
    });
    selectedConvId.value = null;
    showMessage(t("messaging.delete_success"));
    emitter.emit("updateHeaderData");
  } catch (e) {
    showMessage(t("messaging.error_occurred"), "error");
  }
};

const applyTemplate = (val) => {
  replyText.value = val;
};

const scrollChatToBottom = () => {
  if (chatContainer.value) {
    nextTick(() => {
      if (chatContainer.value) {
        chatContainer.value.scrollTo({
          top: chatContainer.value.scrollHeight,
          behavior: "smooth",
        });
      }
    });
  }
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString(locale.value === "ar" ? "ar-EG" : "en-US", {
    month: "short",
    day: "numeric",
  });
};

const formatFullDate = (dateStr) => {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  return date.toLocaleString("ar-EG", { hour: "2-digit", minute: "2-digit" });
};

const replyTemplates = computed(() => [
  {
    title: t("messaging.template_thanks"),
    value: t("messaging.template_thanks_val"),
  },
  {
    title: t("messaging.template_order"),
    value: t("messaging.template_order_val"),
  },
  {
    title: t("messaging.template_tech"),
    value: t("messaging.template_tech_val"),
  },
  {
    title: t("messaging.template_suggestion"),
    value: t("messaging.template_suggestion_val"),
  },
]);

let pollInterval = null;
onMounted(() => {
  fetchMessages();
  pollInterval = setInterval(() => fetchMessages(false), 20000);
});
onUnmounted(() => clearInterval(pollInterval));

watch(selectedConvId, () => {
  nextTick(() => scrollChatToBottom());
});
</script>

<style scoped>
.admin-chat-view {
  height: calc(100vh - 64px);
  background-color: #f5f7f9;
}
.h-100 {
  height: 100% !important;
}
.conversations-list {
  background-color: transparent;
}
.conversation-item {
  cursor: pointer;
  transition: background 0.2s;
}
.conversation-item:hover {
  background-color: #f9f9f9;
}
.unread-item {
  border-right: 4px solid rgb(var(--v-theme-primary)) !important;
  background-color: #e3f2fd;
}
.chat-history {
  background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
}
.bubble {
  max-width: 75%;
}
.bubble-content {
  padding: 12px 16px;
  border-radius: 16px;
  display: inline-block;
}
.user-msg-style {
  background-color: white;
  color: #333;
  border-top-left-radius: 4px;
}
.admin-msg-style {
  border-top-right-radius: 4px;
}
.text-micro {
  font-size: 10px;
}
.gap-2 {
  gap: 8px;
}
.gap-3 {
  gap: 12px;
}
.gap-4 {
  gap: 16px;
}
.ribbons::-webkit-scrollbar {
  height: 4px;
}
.ribbons::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}
</style>
