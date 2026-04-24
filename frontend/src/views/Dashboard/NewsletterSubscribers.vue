<template>
  <v-container fluid class="pa-6">
    <!-- Header Section -->
    <div class="d-flex align-center justify-space-between mb-8">
      <div>
        <h1 class="text-h4 font-weight-black text-primary mb-2">
          {{ $t("dashboard.newsletter_subscribers") }}
        </h1>
        <p class="text-subtitle-1 text-grey-darken-1">
          {{ $t("dashboard.newsletter_subtitle") }}
        </p>
      </div>
      <v-btn
        color="primary"
        prepend-icon="mdi-export"
        class="rounded-lg shadow-btn"
        @click="exportToCSV"
        :disabled="subscribers.length === 0"
      >
        {{ $t("dashboard.export_csv") || "تصدير القائمة" }}
      </v-btn>
    </div>

    <!-- Stats Section -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="4">
        <v-card class="rounded-xl border-0 bg-white" elevation="2">
          <v-card-text class="pa-6">
            <div class="d-flex align-center justify-space-between mb-4">
              <div class="icon-box bg-primary-lighten-5 rounded-lg pa-3">
                <v-icon color="primary" size="24"
                  >mdi-email-multiple-outline</v-icon
                >
              </div>
            </div>
            <div class="text-h4 font-weight-black mb-1">
              {{ subscribers.length }}
            </div>
            <div class="text-subtitle-2 text-grey-darken-1">
              {{ $t("dashboard.total_subscribers") }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Table Section -->
    <v-card class="rounded-xl border-0 overflow-hidden" elevation="3">
      <v-data-table
        :headers="headers"
        :items="subscribers"
        :loading="loading"
        :search="search"
        class="subscribers-table"
        hover
      >
        <!-- Custom Top Section -->
        <template v-slot:top>
          <v-toolbar flat class="bg-white px-4 py-2 border-b">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              :label="$t('dashboard.search_email')"
              variant="outlined"
              density="compact"
              hide-details
              rounded="lg"
              class="max-width-300"
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn
              icon="mdi-refresh"
              variant="text"
              color="grey-darken-1"
              @click="fetchSubscribers"
              :loading="loading"
            ></v-btn>
          </v-toolbar>
        </template>

        <!-- Date Column -->
        <template #[`item.created_at`]="{ item }">
          <span class="text-body-2 text-grey-darken-1">
            {{ formatDate(item.created_at) }}
          </span>
        </template>

        <!-- Actions Column -->
        <template #[`item.actions`]="{ item }">
          <v-btn
            icon="mdi-delete-outline"
            variant="text"
            color="error"
            size="small"
            @click="openDeleteDialog(item)"
          ></v-btn>
        </template>

        <!-- No Data -->
        <template v-slot:no-data>
          <div class="text-center pa-12">
            <v-icon size="64" color="grey-lighten-2" class="mb-4"
              >mdi-email-off-outline</v-icon
            >
            <p class="text-h6 text-grey-darken-1">
              {{ $t("dashboard.no_subscribers") }}
            </p>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h5 font-weight-bold text-center">
          {{ $t("dashboard.confirm_delete") }}
        </v-card-title>
        <v-card-text class="text-center text-body-1 py-4">
          {{ $t("dashboard.delete_subscriber_confirm") }}
          <br />
          <strong>{{ subscriberToDelete?.email }}</strong>
        </v-card-text>
        <v-card-actions class="justify-center gap-4">
          <v-btn
            color="grey-lighten-1"
            variant="flat"
            class="rounded-lg px-6"
            @click="deleteDialog = false"
          >
            {{ $t("dashboard.cancel") }}
          </v-btn>
          <v-btn
            color="error"
            variant="flat"
            class="rounded-lg px-6"
            @click="confirmDelete"
            :loading="deleting"
          >
            {{ $t("dashboard.delete") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import api from "@/axios";
import { useI18n } from "vue-i18n";

const { locale } = useI18n();
const emitter = inject("emitter");

const subscribers = ref([]);
const loading = ref(false);
const search = ref("");
const deleteDialog = ref(false);
const subscriberToDelete = ref(null);
const deleting = ref(false);

const headers = [
  { title: "البريد الإلكتروني", align: "start", key: "email", sortable: true },
  {
    title: "تاريخ الاشتراك",
    align: "center",
    key: "created_at",
    sortable: true,
  },
  { title: "الإجراءات", align: "end", key: "actions", sortable: false },
];

const fetchSubscribers = async () => {
  loading.value = true;
  try {
    const res = await api.get("/admin/subscribers");
    subscribers.value = res.data;
  } catch (error) {
    console.error("Error fetching subscribers:", error);
    emitter.emit("showMessage", {
      text: "فشل تحميل قائمة المشتركين",
      color: "error",
    });
  } finally {
    loading.value = false;
  }
};

const openDeleteDialog = (item) => {
  subscriberToDelete.value = item;
  deleteDialog.value = true;
};

const confirmDelete = async () => {
  if (!subscriberToDelete.value) return;
  deleting.value = true;
  try {
    await api.delete(`/admin/subscribers/${subscriberToDelete.value.id}`);
    emitter.emit("showMessage", { text: "تم الحذف بنجاح", color: "success" });
    subscribers.value = subscribers.value.filter(
      (s) => s.id !== subscriberToDelete.value.id
    );
    deleteDialog.value = false;
  } catch (error) {
    console.error("Error deleting subscriber:", error);
    emitter.emit("showMessage", {
      text: "حدث خطأ أثناء الحذف",
      color: "error",
    });
  } finally {
    deleting.value = false;
  }
};

const formatDate = (dateString) => {
  const options = {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  return new Date(dateString).toLocaleDateString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    options
  );
};

const exportToCSV = () => {
  const headers = ["Email", "Subscribed At"];
  const rows = subscribers.value.map((s) => [s.email, s.created_at]);

  let csvContent =
    "data:text/csv;charset=utf-8," +
    headers.join(",") +
    "\n" +
    rows.map((e) => e.join(",")).join("\n");

  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute(
    "download",
    `newsletter_subscribers_${new Date().toISOString().split("T")[0]}.csv`
  );
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

onMounted(fetchSubscribers);
</script>

<style scoped>
.shadow-btn {
  box-shadow: 0 4px 12px rgba(24, 103, 192, 0.3) !important;
}

.max-width-300 {
  max-width: 300px;
}

.subscribers-table {
  background: white !important;
}

.subscribers-table :deep(th) {
  font-weight: 800 !important;
  color: rgb(var(--v-theme-primary)) !important;
  text-transform: uppercase;
  font-size: 0.85rem !important;
  letter-spacing: 0.5px;
}

.gap-4 {
  gap: 16px;
}

.icon-box {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
