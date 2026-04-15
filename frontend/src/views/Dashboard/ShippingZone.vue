<template>
  <v-container fluid class="pa-6" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
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
            <v-icon color="white" size="28">mdi-truck-fast-outline</v-icon>
          </v-avatar>
          <h2
            class="text-h5 font-weight-black"
            style="color: rgb(var(--v-theme-primary))"
          >
            {{ $t("shipping.title") }}
          </h2>
        </div>

        <div
          class="d-flex align-center gap-3 flex-grow-1 flex-wrap justify-end"
          style="max-width: 500px"
        >
          <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            class="font-weight-bold px-6 rounded-lg shadow-btn"
            height="44"
            @click="openAddDialog"
          >
            {{ $t("shipping.add_zone") }}
          </v-btn>
        </div>
      </v-card-text>
    </v-card>

    <v-row class="mb-4">
      <v-col cols="12" md="6">
        <v-card
          class="rounded-xl pa-4 border-left-success h-100 d-flex flex-column justify-center"
          elevation="1"
        >
          <div class="d-flex justify-space-between align-center mb-3">
            <div class="d-flex align-center gap-2">
              <v-avatar color="green-lighten-4" size="40" class="text-success">
                <v-icon>mdi-gift-outline</v-icon>
              </v-avatar>
              <span class="text-subtitle-1 font-weight-bold">{{
                $t("shipping.free_shipping_settings")
              }}</span>
            </div>
            <v-switch
              v-model="isFreeShippingEnabled"
              color="success"
              hide-details
              density="compact"
            ></v-switch>
          </div>
          <div
            class="d-flex align-center gap-3"
            :class="{ 'opacity-50': !isFreeShippingEnabled }"
          >
            <span class="text-grey-darken-1 text-subtitle-2">{{
              $t("shipping.free_shipping_hint")
            }}</span>
            <div class="d-flex align-center" style="max-width: 150px">
              <v-text-field
                v-model.number="freeShippingThreshold"
                variant="outlined"
                density="compact"
                hide-details
                type="number"
                :suffix="$t('products.currency')"
                :disabled="!isFreeShippingEnabled"
                class="font-weight-bold bg-white"
              ></v-text-field>
            </div>
            <v-btn
              size="small"
              color="success"
              variant="tonal"
              class="font-weight-bold"
              :loading="savingSettings"
              @click="saveShippingSettings"
              >{{ $t("dashboard.save") }}</v-btn
            >
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card
          class="rounded-xl pa-4 d-flex align-center border-left-primary h-100"
          elevation="1"
        >
          <v-avatar
            color="primary-lighten-4"
            size="50"
            class="mr-3 text-primary"
          >
            <v-icon>mdi-map-marker-multiple-outline</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-grey-darken-1">
              {{ $t("shipping.coverage_zones") }}
            </div>
            <div class="text-h5 font-weight-black">
              {{ zones.length }}
              <span class="text-caption text-grey">{{
                $t("shipping.zones_unit")
              }}</span>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card
          class="rounded-xl pa-4 d-flex align-center border-left-warning h-100"
          elevation="1"
        >
          <v-avatar
            color="orange-lighten-4"
            size="50"
            class="mr-3 text-warning"
          >
            <v-icon>mdi-cash-fast</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-grey-darken-1">
              {{ $t("shipping.avg_cost") }}
            </div>
            <div class="text-h5 font-weight-black text-warning">
              {{ averageShippingCost }}
              <span class="text-caption text-grey">{{
                $t("products.currency")
              }}</span>
            </div>
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
                {{ $t("shipping.zone_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("shipping.governorates_assigned") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("shipping.total_methods") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("dashboard.status_th") }}
              </th>
              <th class="text-center font-weight-bold">
                {{ $t("dashboard.actions_th") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <template v-if="loading">
              <tr v-for="i in 5" :key="`skel-zone-${i}`">
                <td class="px-2 py-3">
                  <v-skeleton-loader type="list-item" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" width="120" class="mx-auto" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" width="60" class="mx-auto" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader type="text" width="40" class="mx-auto" />
                </td>
                <td class="px-2">
                  <v-skeleton-loader
                    type="actions"
                    width="100"
                    class="mx-auto"
                  />
                </td>
              </tr>
            </template>
            <tr v-else-if="zones.length === 0">
              <td colspan="5" class="text-center pa-6 text-grey-darken-1">
                {{ $t("shipping.no_zones") }}
              </td>
            </tr>

            <template v-for="zone in zones" :key="zone.id" v-else>
              <tr class="hover-row">
                <td
                  class="font-weight-bold py-3 text-subtitle-2"
                  style="color: rgb(var(--v-theme-primary))"
                >
                  <v-icon color="primary" size="20" class="mr-1 ml-2"
                    >mdi-map-marker-radius</v-icon
                  >
                  {{ zone.name }}
                </td>

                <td class="text-center">
                  <v-chip
                    v-if="zone.governorates?.length"
                    color="primary"
                    variant="tonal"
                    size="small"
                    class="font-weight-bold"
                    @click="openGovsDialog(zone)"
                  >
                    {{ zone.governorates.length }}
                    {{ $t("shipping.zones_unit") }}
                    <v-icon end size="14">mdi-chevron-down</v-icon>
                  </v-chip>
                  <v-btn
                    v-else
                    variant="text"
                    color="grey"
                    size="small"
                    prepend-icon="mdi-plus"
                    @click="openGovsDialog(zone)"
                  >
                    {{ $t("shipping.assign_govs") }}
                  </v-btn>
                </td>

                <td class="text-center">
                  <v-chip
                    color="orange"
                    variant="tonal"
                    size="small"
                    class="font-weight-bold"
                  >
                    {{ zone.methods?.length || 0 }}
                  </v-chip>
                </td>

                <td class="text-center">
                  <v-switch
                    v-model="zone.is_active"
                    color="success"
                    hide-details
                    density="compact"
                    class="d-inline-flex"
                    :disabled="processingIds.includes(zone.id)"
                    @change="toggleZoneStatus(zone)"
                  ></v-switch>
                </td>

                <td class="text-center">
                  <v-btn
                    icon
                    size="small"
                    color="primary"
                    variant="text"
                    @click="editItem(zone)"
                    :title="$t('dashboard.edit')"
                  >
                    <v-icon>mdi-pencil</v-icon>
                  </v-btn>
                  <v-btn
                    icon
                    size="small"
                    color="error"
                    variant="text"
                    :disabled="processingIds.includes(zone.id)"
                    @click="openDeleteDialog(zone)"
                    :title="$t('dashboard.delete')"
                  >
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </td>
              </tr>
              <!-- Sub-Table for Methods -->
              <tr v-if="zone.methods?.length" class="bg-grey-lighten-5">
                <td colspan="5" class="pa-0">
                  <v-expand-transition>
                    <div class="px-10 py-4 border-bottom">
                      <div
                        class="d-flex justify-space-between align-center mb-3"
                      >
                        <h4
                          class="text-subtitle-1 font-weight-bold text-primary"
                        >
                          <v-icon start size="20">mdi-truck-delivery</v-icon>
                          {{ $t("shipping.manage_methods") }}
                        </h4>
                        <v-btn
                          size="small"
                          color="primary"
                          prepend-icon="mdi-plus"
                          variant="flat"
                          @click="openAddMethod(zone)"
                        >
                          {{ $t("shipping.add_method") }}
                        </v-btn>
                      </div>
                      <v-table density="compact" class="bg-transparent">
                        <thead>
                          <tr>
                            <th class="text-right">
                              {{ $t("dashboard.name") }}
                            </th>
                            <th class="text-center">
                              {{ $t("shipping.driver") }}
                            </th>
                            <th class="text-center">
                              {{ $t("shipping.base_cost") }}
                            </th>
                            <th class="text-center">
                              {{ $t("shipping.weight_aware") }}
                            </th>
                            <th class="text-center">
                              {{ $t("dashboard.actions_th") }}
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="method in zone.methods" :key="method.id">
                            <td class="font-weight-medium">
                              {{
                                locale === "ar"
                                  ? method.name_ar
                                  : method.name_en
                              }}
                            </td>
                            <td class="text-center">
                              <v-chip
                                size="x-small"
                                label
                                color="grey-darken-1"
                                >{{ method.driver }}</v-chip
                              >
                            </td>
                            <td class="text-center font-weight-bold">
                              {{ method.fee }}
                              {{ $t("products.currency") }}
                            </td>
                            <td class="text-center">
                              <v-icon
                                :color="
                                  method.is_weight_aware ? 'success' : 'grey'
                                "
                              >
                                {{
                                  method.is_weight_aware
                                    ? "mdi-check-circle"
                                    : "mdi-minus-circle"
                                }}
                              </v-icon>
                            </td>
                            <td class="text-center">
                              <v-btn
                                icon
                                size="x-small"
                                color="primary"
                                variant="text"
                                @click="editMethod(zone, method)"
                                ><v-icon>mdi-pencil</v-icon></v-btn
                              >
                              <v-btn
                                icon
                                size="x-small"
                                color="error"
                                variant="text"
                                @click="removeMethod(method.id)"
                                ><v-icon>mdi-delete</v-icon></v-btn
                              >
                            </td>
                          </tr>
                        </tbody>
                      </v-table>
                    </div>
                  </v-expand-transition>
                </td>
              </tr>
              <tr v-else class="bg-grey-lighten-5">
                <td colspan="5" class="text-center py-4">
                  <v-btn
                    size="small"
                    variant="tonal"
                    color="primary"
                    prepend-icon="mdi-plus"
                    @click="openAddMethod(zone)"
                  >
                    {{ $t("shipping.add_method") }}
                  </v-btn>
                </td>
              </tr>
            </template>
          </tbody>
        </v-table>
      </div>
    </v-card>

    <!-- Zone Edit/Add Dialog -->
    <v-dialog v-model="dialog" max-width="500px" persistent>
      <v-card class="rounded-xl pa-2">
        <v-card-title
          class="text-h5 font-weight-bold pt-4 px-4"
          style="color: rgb(var(--v-theme-primary))"
        >
          {{
            isEditing
              ? $t("shipping.edit_zone_title")
              : $t("shipping.add_zone_title")
          }}
        </v-card-title>
        <v-divider class="my-3"></v-divider>
        <v-card-text>
          <v-form ref="form" v-model="validForm">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="editedItem.name"
                  :label="$t('shipping.zone_name_label')"
                  variant="outlined"
                  color="primary"
                  :rules="[(v) => !!v || $t('shipping.zone_name_required')]"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-switch
                  v-model="editedItem.is_active"
                  color="success"
                  :label="$t('shipping.activate_immediately')"
                ></v-switch>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-4 font-weight-bold"
            @click="closeDialog"
            >{{ $t("dashboard.cancel") }}</v-btn
          >
          <v-btn
            color="primary"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :disabled="!validForm || saving"
            :loading="saving"
            @click="saveItem"
          >
            {{ $t("dashboard.save") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Governorates Assignment Dialog -->
    <v-dialog v-model="dialogGovs" max-width="600px" scrollable>
      <v-card class="rounded-xl">
        <v-card-title class="pa-4 font-weight-bold d-flex align-center">
          <v-icon color="primary" class="mr-2 ml-2">mdi-google-maps</v-icon>
          {{ $t("shipping.assign_govs") }}: {{ activeZone?.name }}
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="pa-0" style="height: 400px">
          <v-list class="pa-2">
            <v-list-item
              v-for="gov in governorates"
              :key="gov.id"
              :active="selectedGovIds.includes(gov.id)"
            >
              <template v-slot:prepend>
                <v-checkbox
                  v-model="selectedGovIds"
                  :value="gov.id"
                  hide-details
                  density="compact"
                ></v-checkbox>
              </template>
              <v-list-item-title class="font-weight-medium">{{
                locale === "ar" ? gov.name_ar : gov.name_en
              }}</v-list-item-title>
              <v-list-item-subtitle
                v-if="
                  gov.shipping_zone_id &&
                  gov.shipping_zone_id !== activeZone?.id
                "
                class="text-error"
              >
                {{ $t("shipping.assigned_to_another") }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="tonal" @click="dialogGovs = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :loading="saving"
            @click="saveGovsAssignment"
            >{{ $t("dashboard.save") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Shipping Method Dialog -->
    <v-dialog v-model="dialogMethod" max-width="600px">
      <v-card class="rounded-xl">
        <v-card-title class="pa-4 font-weight-bold">
          {{ isEditing ? $t("dashboard.edit") : $t("shipping.add_method") }}
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text>
          <v-form ref="methodForm" v-model="validMethodForm">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="editedMethod.name_ar"
                  :label="$t('shipping.method_name_ar')"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Required']"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="editedMethod.name_en"
                  :label="$t('shipping.method_name_en')"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Required']"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="editedMethod.driver"
                  :items="['local', 'aramex', 'bosta']"
                  :label="$t('shipping.driver')"
                  variant="outlined"
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="editedMethod.fee"
                  :label="$t('shipping.base_cost')"
                  type="number"
                  variant="outlined"
                  prefix="EGP"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-switch
                  v-model="editedMethod.is_weight_aware"
                  :label="$t('shipping.weight_aware')"
                  color="primary"
                  hide-details
                ></v-switch>
              </v-col>
              <template v-if="editedMethod.is_weight_aware">
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model.number="editedMethod.base_weight"
                    :label="$t('shipping.base_weight')"
                    type="number"
                    variant="outlined"
                    suffix="KG"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="8">
                  <v-text-field
                    v-model.number="editedMethod.extra_weight_fee"
                    :label="$t('shipping.extra_weight_fee')"
                    type="number"
                    variant="outlined"
                    suffix="EGP/KG"
                  ></v-text-field>
                </v-col>
              </template>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="tonal" @click="dialogMethod = false">{{
            $t("dashboard.cancel")
          }}</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :loading="saving"
            @click="saveMethod"
            >{{ $t("dashboard.save") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDelete" max-width="400px">
      <v-card class="rounded-xl pa-4 text-center">
        <v-icon color="error" size="60" class="mb-3"
          >mdi-alert-circle-outline</v-icon
        >
        <v-card-title class="text-h6 font-weight-bold text-wrap">{{
          $t("shipping.delete_confirm_title")
        }}</v-card-title>
        <v-card-text class="text-grey-darken-1">{{
          $t("shipping.delete_confirm_msg")
        }}</v-card-text>
        <v-card-actions class="d-flex justify-center mt-4">
          <v-btn
            color="grey-darken-1"
            variant="tonal"
            class="px-6 font-weight-bold"
            @click="dialogDelete = false"
            >{{ $t("dashboard.cancel") }}</v-btn
          >
          <v-btn
            color="error"
            variant="flat"
            class="px-6 font-weight-bold shadow-btn"
            :loading="deleting"
            @click="confirmDelete"
            >{{ $t("shipping.confirm_delete_btn") }}</v-btn
          >
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
          $t("dashboard.close")
        }}</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import axios from "@/axios";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();

const zones = ref([]);
const governorates = ref([]);
const loading = ref(true);
const processingIds = ref([]);
const lastAction = ref("");

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

const isFreeShippingEnabled = ref(false);
const freeShippingThreshold = ref(0);
const savingSettings = ref(false);

const dialog = ref(false);
const dialogDelete = ref(false);
const dialogGovs = ref(false);
const dialogMethod = ref(false);

const isEditing = ref(false);
const validForm = ref(false);
const validMethodForm = ref(false);

const saving = ref(false);
const deleting = ref(false);
const itemToDelete = ref(null);

const activeZone = ref(null);
const selectedGovIds = ref([]);

const defaultItem = {
  id: null,
  name: "",
  is_active: true,
};
const editedItem = ref({ ...defaultItem });

const defaultMethod = {
  id: null,
  name_ar: "",
  name_en: "",
  driver: "local",
  is_active: true,
  is_weight_aware: false,
  fee: 0,
  extra_weight_fee: 0,
  base_weight: 0,
};
const editedMethod = ref({ ...defaultMethod });

// --- Polling & Visibility Logic ---
const pollingInterval = ref(null);

const startPolling = () => {
  stopPolling();
  pollingInterval.value = setInterval(() => {
    fetchZonesAndSettings(true);
  }, 60000);
};

const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
};

const handleVisibilityChange = () => {
  if (document.hidden) {
    stopPolling();
  } else {
    fetchZonesAndSettings(true);
    startPolling();
  }
};
// ----------------------------------

const fetchZonesAndSettings = async (quiet = false) => {
  if (!quiet) loading.value = true;
  try {
    const [zonesRes, settingsRes, govsRes] = await Promise.all([
      axios.get("/admin/shipping-zones"),
      axios.get("/admin/settings"),
      axios.get("/admin/governorates"),
    ]);
    zones.value = zonesRes.data;
    governorates.value = govsRes.data;

    if (settingsRes.data) {
      isFreeShippingEnabled.value =
        settingsRes.data.free_shipping_enabled === "true";
      freeShippingThreshold.value =
        Number(settingsRes.data.free_shipping_threshold) || 0;
    }
  } catch (error) {
    console.error("Error loading data:", error);
    showMessage(t("dashboard.error_loading"), "error");
  } finally {
    if (!quiet) loading.value = false;
  }
};

const averageShippingCost = computed(() => {
  if (zones.value.length === 0) return 0;
  let totalCost = 0;
  let methodCount = 0;
  zones.value.forEach((zone) => {
    if (zone.methods && zone.methods.length > 0) {
      zone.methods.forEach((m) => {
        totalCost += Number(m.fee);
        methodCount++;
      });
    }
  });
  return methodCount > 0 ? Math.round(totalCost / methodCount) : 0;
});

const saveShippingSettings = async () => {
  savingSettings.value = true;
  try {
    await axios.post("/admin/settings", {
      free_shipping_enabled: isFreeShippingEnabled.value ? "true" : "false",
      free_shipping_threshold: freeShippingThreshold.value,
    });
    showMessage(t("shipping.save_settings_success"));
  } catch (err) {
    console.error("Error saving settings", err);
    showMessage(t("shipping.save_settings_error"), "error");
  } finally {
    savingSettings.value = false;
  }
};

const openAddDialog = () => {
  isEditing.value = false;
  editedItem.value = { ...defaultItem };
  dialog.value = true;
};

const editItem = (item) => {
  isEditing.value = true;
  editedItem.value = { ...item };
  editedItem.value.is_active = !!editedItem.value.is_active;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  editedItem.value = { ...defaultItem };
};

const saveItem = async () => {
  if (!validForm.value) return;
  saving.value = true;
  try {
    const payload = {
      name: editedItem.value.name,
      is_active: editedItem.value.is_active,
    };

    if (isEditing.value) {
      const res = await axios.put(
        `/admin/shipping-zones/${editedItem.value.id}`,
        payload
      );
      const index = zones.value.findIndex((z) => z.id === editedItem.value.id);
      if (index !== -1)
        zones.value[index] = { ...zones.value[index], ...res.data };
      showMessage(t("shipping.update_success"));
    } else {
      const res = await axios.post("/admin/shipping-zones", payload);
      zones.value.unshift(res.data);
      showMessage(t("shipping.add_success"));
    }
    closeDialog();
  } catch (err) {
    console.error("Error saving zone:", err);
    showMessage(err.response?.data?.message || "Error saving zone", "error");
  } finally {
    saving.value = false;
  }
};

const toggleZoneStatus = async (zone) => {
  processingIds.value.push(zone.id);
  lastAction.value = "toggle";
  try {
    await axios.put(`/admin/shipping-zones/${zone.id}`, {
      name: zone.name,
      is_active: zone.is_active,
    });
    showMessage(
      zone.is_active
        ? t("shipping.zone_activated")
        : t("shipping.zone_deactivated"),
      zone.is_active ? "success" : "warning"
    );
  } catch (err) {
    console.error("Error toggling zone status:", err);
    zone.is_active = !zone.is_active;
    showMessage(t("dashboard.error_occurred"), "error");
  } finally {
    processingIds.value = processingIds.value.filter((id) => id !== zone.id);
  }
};

const openDeleteDialog = (item) => {
  itemToDelete.value = item;
  dialogDelete.value = true;
};

const confirmDelete = async () => {
  if (!itemToDelete.value) return;
  deleting.value = true;
  processingIds.value.push(itemToDelete.value.id);
  lastAction.value = "delete";
  try {
    await axios.delete(`/admin/shipping-zones/${itemToDelete.value.id}`);
    zones.value = zones.value.filter((z) => z.id !== itemToDelete.value.id);
    showMessage(t("shipping.delete_success"));
    dialogDelete.value = false;
    itemToDelete.value = null;
  } catch (error) {
    console.error("Error deleting zone:", error);
    showMessage(t("dashboard.delete_error"), "error");
  } finally {
    deleting.value = false;
    if (itemToDelete.value) {
      processingIds.value = processingIds.value.filter(
        (id) => id !== itemToDelete.value.id
      );
    }
  }
};

// --- Governorates & Methods Action ---
const openGovsDialog = (zone) => {
  activeZone.value = zone;
  selectedGovIds.value = zone.governorates
    ? zone.governorates.map((g) => g.id)
    : [];
  dialogGovs.value = true;
};

const saveGovsAssignment = async () => {
  saving.value = true;
  try {
    await axios.post("/admin/shipping-zones/assign-governorate", {
      zone_id: activeZone.value.id,
      governorate_ids: selectedGovIds.value,
    });
    showMessage(t("shipping.update_success"));
    fetchZonesAndSettings(true);
    dialogGovs.value = false;
  } catch (err) {
    console.error(err);
    showMessage(t("dashboard.error_occurred"), "error");
  } finally {
    saving.value = false;
  }
};

const openAddMethod = (zone) => {
  activeZone.value = zone;
  isEditing.value = false;
  editedMethod.value = { ...defaultMethod };
  dialogMethod.value = true;
};

const editMethod = (zone, method) => {
  activeZone.value = zone;
  isEditing.value = true;
  editedMethod.value = { ...method };
  dialogMethod.value = true;
};

const saveMethod = async () => {
  if (!validMethodForm.value) return;
  saving.value = true;
  try {
    await axios.post(`/admin/shipping-zones/${activeZone.value.id}/methods`, {
      ...editedMethod.value,
      id: isEditing.value ? editedMethod.value.id : null,
    });
    showMessage(t("shipping.update_success"));
    fetchZonesAndSettings(true);
    dialogMethod.value = false;
  } catch (err) {
    console.error(err);
    showMessage(t("dashboard.error_occurred"), "error");
  } finally {
    saving.value = false;
  }
};

const removeMethod = async (methodId) => {
  if (!confirm(t("shipping.delete_confirm_title"))) return;
  try {
    await axios.delete(`/admin/shipping-methods/${methodId}`);
    showMessage(t("shipping.delete_success"));
    fetchZonesAndSettings(true);
  } catch (err) {
    showMessage(t("dashboard.delete_error"), "error");
  }
};

onMounted(() => {
  fetchZonesAndSettings();
  startPolling();
  document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
  stopPolling();
  document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<style scoped>
.shadow-btn {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
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
.border-left-success {
  border-right: 4px solid #4caf50 !important;
}
.border-left-warning {
  border-right: 4px solid #fb8c00 !important;
}

.opacity-50 {
  opacity: 0.5;
  pointer-events: none;
  transition: opacity 0.3s ease;
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
