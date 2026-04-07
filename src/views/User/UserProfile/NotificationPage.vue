<template>
  <v-container class="py-8" dir="rtl">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card elevation="3" class="rounded-xl overflow-hidden">
          <v-toolbar
            color="rgb(var(--v-theme-primary))"
            class="text-white px-2"
          >
            <v-icon class="mr-2">mdi-bell-ring-outline</v-icon>
            <v-toolbar-title class="font-weight-bold"
              >الإشعارات</v-toolbar-title
            >
            <v-spacer></v-spacer>
            <v-tooltip location="bottom">
              <template v-slot:activator="{ props }">
                <v-btn
                  icon
                  variant="text"
                  v-bind="props"
                  @click="markAllAsRead"
                  :disabled="unreadCount === 0"
                >
                  <v-icon>mdi-check-all</v-icon>
                </v-btn>
              </template>
              <span>تحديد الكل كمقروء</span>
            </v-tooltip>
          </v-toolbar>

          <v-list lines="three" class="bg-surface pa-0">
            <template v-for="(note, index) in notifications" :key="note.id">
              <v-list-item
                :value="note.id"
                class="pa-4 cursor-pointer transition-fast-in-fast-out"
                :class="{
                  'bg-blue-grey-lighten-5': !note.is_read,
                  'hover-bg': true,
                }"
                @click="openNotification(note)"
              >
                <template v-slot:prepend>
                  <v-avatar
                    size="48"
                    :color="note.color + '-lighten-4'"
                    class="ml-4"
                  >
                    <v-icon :color="note.color">{{ note.icon }}</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title
                  class="d-flex justify-space-between align-start mb-1"
                >
                  <span
                    class="text-subtitle-1"
                    :class="{
                      'font-weight-bold text-black': !note.is_read,
                      'font-weight-medium text-grey-darken-2': note.is_read,
                    }"
                  >
                    {{ note.title }}
                  </span>
                  <span
                    class="text-caption text-grey-darken-1 whitespace-nowrap mr-2"
                    style="white-space: nowrap"
                  >
                    {{ new Date(note.created_at).toLocaleString("ar-EG") }}
                  </span>
                </v-list-item-title>

                <v-list-item-subtitle
                  class="text-body-2"
                  :class="{
                    'text-black': !note.is_read,
                    'text-grey': note.is_read,
                  }"
                >
                  {{ note.description }}
                </v-list-item-subtitle>

                <template v-slot:append v-if="!note.is_read">
                  <v-badge dot color="error" class="mr-2 mt-2"></v-badge>
                </template>
              </v-list-item>

              <v-divider v-if="index < notifications.length - 1"></v-divider>
            </template>
          </v-list>

          <div
            v-if="notifications.length === 0"
            class="text-center pa-10 text-grey"
          >
            <v-icon size="60" color="grey-lighten-2" class="mb-4"
              >mdi-bell-off-outline</v-icon
            >
            <h3>لا توجد إشعارات حالياً</h3>
            <p class="text-caption mt-2">سنعلمك فور وجود أي تحديثات جديدة.</p>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, inject } from "vue";
import api from "@/axios";

const emitter = inject("emitter");

const notifications = ref([]);

// Fetch notifications
const fetchNotifications = async () => {
  try {
    const res = await api.get("/user/notifications");
    notifications.value = res.data;
  } catch (error) {
    console.error("Failed to fetch notifications:", error);
  }
};

const unreadCount = computed(() => {
  return notifications.value.filter((note) => !note.is_read).length;
});

const openNotification = async (note) => {
  if (!note.is_read) {
    try {
      await api.post(`/user/notifications/${note.id}/mark-read`);
      note.is_read = true;
      emitter.emit("updateHeaderData"); // تحديث العداد في الناف بار
    } catch (e) {
      console.error("Failed to mark as read", e);
    }
  }
};

const markAllAsRead = async () => {
  try {
    await api.post("/user/notifications/mark-read");
    notifications.value.forEach((note) => {
      note.is_read = true;
    });
    emitter.emit("updateHeaderData"); // تحديث العداد في الناف بار
  } catch (e) {
    console.error("Failed to mark all as read", e);
  }
};

onMounted(() => {
  fetchNotifications();
});
</script>

<style scoped>
.hover-bg:hover {
  background-color: #f5f5f5 !important;
}
/* تمييز الإشعار الجديد بخلفية خفيفة جداً */
.bg-blue-grey-lighten-5 {
  background-color: #f8fafc !important;
}
</style>
