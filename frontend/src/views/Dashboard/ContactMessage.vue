<template>
  <div :dir="locale === 'ar' ? 'rtl' : 'ltr'">
    <v-container fluid class="pa-6">
      <v-card class="mb-6 rounded-xl" elevation="1">
        <v-card-text
          class="d-flex align-center justify-space-between flex-wrap gap-4 pa-4"
        >
          <div class="d-flex align-center gap-3">
            <v-avatar
              color="rgb(var(--v-theme-primary))"
              size="48"
              class="rounded-lg"
            >
              <v-icon color="white" size="28">mdi-email-newsletter</v-icon>
            </v-avatar>
            <h2
              class="text-h5 font-weight-black"
              style="color: rgb(var(--v-theme-primary))"
            >
              {{ $t("messaging.visitor_messages") }}
            </h2>
          </div>

          <div
            class="d-flex align-center gap-3 flex-grow-1 flex-wrap"
            style="max-width: 500px"
          >
            <v-text-field
              v-model="searchQuery"
              prepend-inner-icon="mdi-magnify"
              :label="$t('messaging.search_contacts_placeholder')"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg flex-grow-1"
            ></v-text-field>

            <v-select
              v-model="statusFilter"
              :items="statusOptions"
              item-title="title"
              item-value="value"
              variant="outlined"
              density="compact"
              hide-details
              class="bg-white rounded-lg"
              style="max-width: 180px"
            ></v-select>
          </div>
        </v-card-text>
      </v-card>

      <v-row class="mb-4">
        <v-col cols="12" md="6">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-error"
            elevation="1"
          >
            <v-avatar color="red-lighten-4" size="50" class="mr-3 text-error">
              <v-badge
                :content="unreadCount"
                color="error"
                v-if="unreadCount > 0"
              >
                <v-icon>mdi-email-alert-outline</v-icon>
              </v-badge>
              <v-icon v-else>mdi-email-open-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                {{ $t("messaging.new_unread_badge") }}
              </div>
              <div class="text-h5 font-weight-black text-error">
                {{ unreadCount }}
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" md="6">
          <v-card
            class="rounded-xl pa-4 d-flex align-center border-left-primary"
            elevation="1"
          >
            <v-avatar
              color="primary-lighten-4"
              size="50"
              class="mr-3 text-primary"
            >
              <v-icon>mdi-forum-outline</v-icon>
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-grey-darken-1">
                {{ $t("messaging.total_messages") }}
              </div>
              <div class="text-h5 font-weight-black">{{ messages.length }}</div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <v-card class="rounded-xl overflow-hidden" elevation="1">
        <div class="table-responsive">
          <v-table class="custom-table pa-2">
            <thead class="bg-grey-lighten-4">
              <tr>
                <th class="text-right font-weight-bold">
                  {{ $t("messaging.sender_th") }}
                </th>
                <th class="text-right font-weight-bold">
                  {{ $t("messaging.subject_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("messaging.date_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("messaging.status_th") }}
                </th>
                <th class="text-center font-weight-bold">
                  {{ $t("messaging.actions_th") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-if="loading">
                <tr v-for="i in 5" :key="`skel-msg-${i}`">
                  <td class="px-2 py-3">
                    <v-skeleton-loader type="list-item-avatar" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="text" style="max-width: 200px" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="text"
                      style="max-width: 100px"
                      class="mx-auto"
                    />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader type="chip" class="mx-auto" />
                  </td>
                  <td class="px-2">
                    <v-skeleton-loader
                      type="actions"
                      class="mx-auto"
                      style="min-width: 100px"
                    />
                  </td>
                </tr>
              </template>

              <tr v-else-if="filteredMessages.length === 0">
                <td colspan="5" class="text-center pa-8 text-grey-darken-1">
                  <v-icon size="50" color="grey-lighten-2" class="mb-3"
                    >mdi-mailbox-open-outline</v-icon
                  >
                  <br />{{ $t("messaging.no_messages_found") }}
                </td>
              </tr>

              <tr
                v-for="msg in filteredMessages"
                :key="msg.id"
                class="hover-row"
                :class="{ 'bg-blue-grey-lighten-5': !msg.is_read }"
                v-else
              >
                <td class="py-3">
                  <div class="d-flex align-center gap-3">
                    <v-avatar
                      size="36"
                      :color="msg.is_read ? 'grey-lighten-2' : 'primary'"
                      :class="{ 'text-white': !msg.is_read }"
                    >
                      {{ msg.name ? msg.name.charAt(0) : "?" }}
                    </v-avatar>
                    <div>
                      <div
                        class="text-subtitle-2"
                        :class="
                          msg.is_read
                            ? 'font-weight-medium'
                            : 'font-weight-black text-primary'
                        "
                      >
                        {{ msg.name }}
                      </div>
                      <div class="text-caption text-grey-darken-1">
                        {{ msg.email }}
                      </div>
                    </div>
                  </div>
                </td>

                <td style="max-width: 250px" class="text-truncate">
                  <span
                    :class="
                      msg.is_read
                        ? 'text-grey-darken-2'
                        : 'font-weight-bold text-black'
                    "
                  >
                    {{ msg.subject || $t("messaging.no_subject") }}
                  </span>
                  <span class="text-caption text-grey ml-2" v-if="msg.message"
                    >-
                    {{
                      msg.message.length > 30
                        ? msg.message.substring(0, 30) + "..."
                        : msg.message
                    }}</span
                  >
                </td>

                <td
                  class="text-center text-caption text-grey-darken-1 font-weight-medium"
                >
                  {{ formatDate(msg.created_at) }}
                </td>

                <td class="text-center">
                  <v-chip
                    size="small"
                    :color="msg.reply_text ? 'success' : 'warning'"
                    variant="outlined"
                    class="font-weight-bold"
                  >
                    {{
                      msg.reply_text
                        ? $t("messaging.replied_chip")
                        : $t("messaging.pending_chip")
                    }}
                  </v-chip>
                </td>

                <td class="text-center">
                  <v-btn
                    icon
                    size="small"
                    color="primary"
                    variant="text"
                    @click="openMessage(msg)"
                    :title="$t('messaging.read_message_title')"
                  >
                    <v-icon>mdi-eye-outline</v-icon>
                  </v-btn>
                  <v-btn
                    icon
                    size="small"
                    color="error"
                    variant="text"
                    :disabled="processingIds.includes(msg.id)"
                    @click="deleteMessage(msg.id)"
                    :title="$t('messaging.delete_title')"
                  >
                    <v-progress-circular
                      v-if="processingIds.includes(msg.id)"
                      indeterminate
                      size="16"
                    ></v-progress-circular>
                    <v-icon v-else>mdi-delete-outline</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </div>
      </v-card>

      <v-dialog v-model="dialog" max-width="600px">
        <v-card class="rounded-xl" v-if="selectedMessage">
          <v-card-title
            class="d-flex align-center justify-space-between pt-4 px-4 pb-0"
          >
            <div class="d-flex align-center gap-2">
              <v-icon color="primary">mdi-email-open-outline</v-icon>
              <span
                class="text-h6 font-weight-bold"
                style="color: rgb(var(--v-theme-primary))"
                >{{ $t("messaging.read_message_title") }}</span
              >
            </div>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="dialog = false"
            ></v-btn>
          </v-card-title>

          <v-divider class="my-3"></v-divider>

          <v-card-text class="pt-0">
            <v-row class="mb-4 bg-grey-lighten-4 rounded-lg pa-2 mx-0">
              <v-col cols="12" sm="6" class="py-1">
                <div class="text-caption text-grey-darken-1">
                  {{ $t("messaging.sender_name_label") }}
                </div>
                <div class="font-weight-bold">{{ selectedMessage.name }}</div>
              </v-col>
              <v-col cols="12" class="py-1">
                <div class="text-caption text-grey-darken-1">
                  {{ $t("messaging.email_label") }}
                </div>
                <div class="font-weight-bold text-primary">
                  {{ selectedMessage.email }}
                </div>
              </v-col>
              <v-col cols="12" class="py-1">
                <div class="text-caption text-grey-darken-1">
                  {{ $t("messaging.sent_at_label") }}
                </div>
                <div class="font-weight-bold">
                  {{ formatFullDate(selectedMessage.created_at) }}
                </div>
              </v-col>
            </v-row>

            <div class="mb-2 text-subtitle-2 text-grey-darken-1">
              {{ $t("messaging.message_subject_label") }}
            </div>
            <h3 class="text-h6 font-weight-bold mb-4">
              {{ selectedMessage.subject || $t("messaging.no_subject") }}
            </h3>

            <div
              class="bg-blue-grey-lighten-5 pa-4 rounded-lg text-body-1"
              style="line-height: 1.8; white-space: pre-wrap"
            >
              {{ selectedMessage.message }}
            </div>

            <div v-if="selectedMessage.reply_text" class="mt-6">
              <div
                class="d-flex align-center gap-2 mb-2 text-success font-weight-bold"
              >
                <v-icon size="18">mdi-reply-all</v-icon>
                <span>{{ $t("messaging.admin_reply_label") }}</span>
                <span class="text-caption text-grey ml-auto">{{
                  formatFullDate(selectedMessage.replied_at)
                }}</span>
              </div>
              <div
                class="bg-success-lighten-5 pa-4 rounded-lg text-body-2 border-left-success"
              >
                {{ selectedMessage.reply_text }}
              </div>
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4 bg-grey-lighten-4 justify-space-between">
            <v-btn
              color="error"
              variant="text"
              class="font-weight-bold"
              prepend-icon="mdi-delete"
              @click="
                deleteMessage(selectedMessage.id);
                dialog = false;
              "
            >
              {{ $t("messaging.delete_title") }}
            </v-btn>
            <div class="gap-2 d-flex">
              <v-btn
                color="grey-darken-1"
                variant="tonal"
                class="px-4 font-weight-bold"
                @click="dialog = false"
                >{{ $t("messaging.close_btn") }}</v-btn
              >
              <v-btn
                color="primary"
                variant="flat"
                class="px-6 font-weight-bold shadow-btn"
                prepend-icon="mdi-reply"
                @click="replyDialog = true"
              >
                {{ $t("messaging.reply_to_user_btn") }}
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Reply Dialog -->
      <v-dialog v-model="replyDialog" max-width="500px">
        <v-card class="rounded-xl">
          <v-card-title class="bg-primary text-white pt-4 px-4 pb-4">
            {{ $t("messaging.reply_to_user_btn") }} {{ selectedMessage?.name }}
          </v-card-title>
          <v-card-text class="pt-6">
            <v-select
              v-model="selectedTemplate"
              :items="replyTemplates"
              item-title="title"
              item-value="value"
              :label="$t('messaging.quick_reply_label')"
              variant="outlined"
              density="compact"
              class="mb-4"
              @update:model-value="applyTemplate"
            ></v-select>
            <v-textarea
              v-model="replyText"
              :label="$t('messaging.reply_placeholder')"
              variant="outlined"
              rows="5"
            ></v-textarea>
          </v-card-text>
          <v-card-actions class="pa-4 bg-grey-lighten-4 justify-end">
            <v-btn color="grey" variant="text" @click="replyDialog = false">
              {{ $t("messaging.cancel_btn") }}
            </v-btn>
            <v-btn
              color="primary"
              variant="flat"
              :loading="replying"
              @click="sendReply"
            >
              {{ $t("messaging.send_reply_btn") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Snackbar for notifications -->
      <v-snackbar
        v-model="snackbar.show"
        :color="snackbar.color"
        timeout="3000"
        location="top center"
        class="font-weight-bold"
      >
        {{ snackbar.text }}
        <template v-slot:actions>
          <v-btn variant="text" @click="snackbar.show = false">{{
            $t("messaging.close_btn")
          }}</v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from "vue";
import { useI18n } from "vue-i18n";
import axios from "@/axios";

const { locale, t } = useI18n();
const emitter = inject("emitter");

const messages = ref([]);
const loading = ref(true);
const processingIds = ref([]);

const snackbar = ref({
  show: false,
  text: "",
  color: "success",
});

const showMessage = (text, color = "success") => {
  snackbar.value.text = text;
  snackbar.value.color = color;
  snackbar.value.show = true;
};

const searchQuery = ref("");
const statusFilter = ref("all");

const statusOptions = computed(() => [
  { title: t("messaging.status_filter_all"), value: "all" },
  { title: t("messaging.status_filter_unread"), value: "unread" },
  { title: t("messaging.status_filter_read"), value: "read" },
]);

const dialog = ref(false);
const replyDialog = ref(false);
const selectedMessage = ref(null);
const replyText = ref("");
const replying = ref(false);
const selectedTemplate = ref(null);

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

const applyTemplate = (val) => {
  if (val) {
    let finalVal = val;
    // إذا كان هناك رقم طلب متاح للعميل، نقوم باستبدال التمبليت به
    const orderNumber = selectedMessage.value?.user?.orders?.[0]?.order_number;
    if (orderNumber) {
      finalVal = finalVal.replace(
        /#\d+|رقم الطلب|#\.\.\.|\(\.\.\.\)/g,
        `#${orderNumber}`
      );
    }
    replyText.value = finalVal;
  }
};

const unreadCount = computed(
  () => messages.value.filter((m) => !m.is_read).length
);

const filteredMessages = computed(() => {
  return messages.value.filter((msg) => {
    const matchSearch =
      (msg.name || "")
        .toLowerCase()
        .includes(searchQuery.value.toLowerCase()) ||
      (msg.email || "").toLowerCase().includes(searchQuery.value.toLowerCase());

    let matchStatus = true;
    if (statusFilter.value === "unread") matchStatus = !msg.is_read;
    if (statusFilter.value === "read") matchStatus = msg.is_read;

    return matchSearch && matchStatus;
  });
});

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString(locale.value === "ar" ? "ar-EG" : "en-US", {
    month: "short",
    day: "numeric",
  });
};

const formatFullDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleString(locale.value === "ar" ? "ar-EG" : "en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
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

const openMessage = async (msg) => {
  selectedMessage.value = msg;
  dialog.value = true;

  if (!msg.is_read) {
    try {
      await axios.put(`/admin/messages/${msg.id}`);
      const index = messages.value.findIndex((m) => m.id === msg.id);
      if (index !== -1) messages.value[index].is_read = true;
      emitter.emit("updateHeaderData");
    } catch (err) {
      console.error("Error marking message read:", err);
    }
  }
};

const deleteMessage = async (id) => {
  processingIds.value.push(id);
  try {
    await axios.delete(`/admin/messages/${id}`);
    messages.value = messages.value.filter((m) => m.id !== id);
    showMessage(t("messaging.delete_success"));
    emitter.emit("updateHeaderData");
  } catch (err) {
    console.error("Error deleting message:", err);
    showMessage(t("messaging.error_occurred"), "error");
  } finally {
    processingIds.value = processingIds.value.filter((itemId) => itemId !== id);
  }
};

const sendReply = async () => {
  if (!replyText.value.trim()) {
    showMessage(t("messaging.write_reply_error"), "error");
    return;
  }
  replying.value = true;
  try {
    const res = await axios.post(
      `/admin/messages/${selectedMessage.value.id}/reply`,
      {
        reply_text: replyText.value,
      }
    );
    showMessage(t("messaging.reply_success"));
    replyDialog.value = false;
    dialog.value = false;
    replyText.value = "";

    const index = messages.value.findIndex(
      (m) => m.id === selectedMessage.value.id
    );
    if (index !== -1) {
      messages.value[index].reply_text = res.data.msg.reply_text;
      messages.value[index].replied_at = res.data.msg.replied_at;
    }
  } catch (e) {
    showMessage(t("messaging.error_occurred"), "error");
  } finally {
    replying.value = false;
  }
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchMessages(false), 20000); // 20 seconds
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
.shadow-btn {
  box-shadow: 0 4px 12px rgba(24, 103, 192, 0.3) !important;
  transition: transform 0.2s ease;
}
.shadow-btn:hover {
  transform: translateY(-2px);
}

.custom-table th {
  color: rgb(var(--v-theme-primary)) !important;
  font-size: 14px !important;
  border-bottom: 2px solid #eee !important;
  white-space: nowrap;
}

.custom-table td {
  border-bottom: 1px solid #f5f5f5 !important;
  vertical-align: middle !important;
}

.hover-row {
  transition: background-color 0.2s ease;
}
.hover-row:hover {
  background-color: #fcfcfc;
}

.border-left-primary {
  border-right: 4px solid rgb(var(--v-theme-primary)) !important;
}
.border-left-error {
  border-right: 4px solid #e53935 !important;
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
</style>
