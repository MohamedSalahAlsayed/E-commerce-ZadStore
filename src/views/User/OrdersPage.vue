<template>
  <div
    class="orders-page py-10 bg-grey-lighten-4"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    style="min-height: 100vh"
  >
    <v-container max-width="1100">
      <!-- رأس الصفحة المذهل -->
      <div
        class="d-flex align-center justify-space-between mb-8 flex-wrap gap-4"
      >
        <div>
          <div class="d-flex align-center mt-2">
            <v-avatar
              color="primary-lighten-5"
              size="50"
              class="mr-3 ml-4 rounded-lg"
            >
              <v-icon color="primary" size="28">mdi-shopping-outline</v-icon>
            </v-avatar>
            <h1
              class="text-h4 font-weight-black text-grey-darken-4 mb-0"
              style="letter-spacing: -0.5px"
            >
              {{ $t("orders.title") }}
            </h1>
          </div>
          <p class="text-subtitle-1 text-grey-darken-1 mt-2 mb-0 ms-16">
            {{ $t("orders.subtitle") }}
          </p>
        </div>
        <v-btn
          color="primary"
          class="rounded-pill px-6 font-weight-bold"
          height="46"
          elevation="2"
          prepend-icon="mdi-cart-arrow-right"
          to="/"
        >
          {{ $t("orders.continue_shopping") }}
        </v-btn>
      </div>

      <!-- أدوات الفلترة والبحث بتصميم زجاجي (Glassmorphism) -->
      <v-card
        class="mb-8 rounded-xl border-0 overflow-visible filter-card custom-shadow"
        elevation="0"
      >
        <v-card-text
          class="pa-5 d-flex flex-column flex-md-row justify-space-between align-md-center gap-4 bg-white"
        >
          <v-tabs v-model="tab" color="primary" class="modern-tabs" hide-slider>
            <v-tab
              value="all"
              class="rounded-pill px-6 mx-1 text-none font-weight-bold"
              :class="{ 'bg-primary-lighten-5 text-primary': tab === 'all' }"
              >{{ $t("orders.all_orders") }}</v-tab
            >
            <v-tab
              value="pending"
              class="rounded-pill px-6 mx-1 text-none font-weight-bold"
              :class="{
                'bg-primary-lighten-5 text-primary-darken-3': tab === 'pending',
              }"
              >{{ $t("orders.pending") }}</v-tab
            >
            <v-tab
              value="shipped"
              class="rounded-pill px-6 mx-1 text-none font-weight-bold"
              :class="{
                'bg-blue-lighten-5 text-blue-darken-3': tab === 'shipped',
              }"
              >{{ $t("orders.shipped") }}</v-tab
            >
            <v-tab
              value="cancelled"
              class="rounded-pill px-6 mx-1 text-none font-weight-bold"
              :class="{
                'bg-red-lighten-5 text-red-darken-3': tab === 'cancelled',
              }"
              >{{ $t("orders.cancelled") }}</v-tab
            >
          </v-tabs>

          <div class="d-flex gap-3 align-center wrapper-filters">
            <v-select
              v-model="selectedPeriod"
              :items="periods"
              item-title="title"
              item-value="value"
              variant="outlined"
              density="compact"
              hide-details
              bg-color="grey-lighten-5"
              class="modern-select rounded-lg"
              max-width="160"
              prepend-inner-icon="mdi-calendar-month-outline"
            ></v-select>

            <v-text-field
              v-model="searchQuery"
              variant="outlined"
              density="compact"
              :placeholder="$t('orders.search_placeholder')"
              append-inner-icon="mdi-magnify"
              hide-details
              bg-color="grey-lighten-5"
              class="modern-search rounded-lg"
              min-width="260"
            ></v-text-field>
          </div>
        </v-card-text>
      </v-card>

      <!-- قائمة الطلبات -->
      <v-expand-transition group>
        <div
          v-if="loading"
          key="loading"
          class="text-center py-16 bg-white rounded-xl mt-4"
        >
          <v-progress-circular
            indeterminate
            color="primary"
          ></v-progress-circular>
        </div>

        <div
          v-else-if="filteredOrders.length === 0"
          key="empty"
          class="text-center py-16 bg-white rounded-xl custom-shadow mt-4"
        >
          <v-avatar color="grey-lighten-4" size="120" class="mb-6">
            <v-icon size="60" color="grey-lighten-1"
              >mdi-package-variant-closed</v-icon
            >
          </v-avatar>
          <h3 class="text-h5 font-weight-bold text-grey-darken-3 mb-2">
            {{ $t("orders.no_orders_found") }}
          </h3>
          <p class="text-body-1 text-grey-darken-1 mb-6">
            {{ $t("orders.no_orders_hint") }}
          </p>
        </div>

        <v-card
          v-for="order in filteredOrders"
          v-else
          :key="order.id"
          class="mb-6 rounded-xl overflow-hidden order-card custom-shadow border-0"
          elevation="0"
        >
          <!-- رأس الطلب (مظهره عصري جداً) -->
          <div
            class="order-header pa-5 d-flex flex-wrap justify-space-between align-center"
            :class="getOrderTheme(order.status).headerClass"
          >
            <div class="d-flex align-center gap-6 flex-wrap">
              <div class="d-flex align-center gap-3">
                <v-avatar
                  size="44"
                  :color="getOrderTheme(order.status).iconBg"
                  class="rounded-lg"
                >
                  <v-icon
                    :color="getOrderTheme(order.status).iconColor"
                    size="24"
                    >{{ getOrderTheme(order.status).icon }}</v-icon
                  >
                </v-avatar>
                <div>
                  <div
                    class="text-caption text-grey-darken-1 text-uppercase font-weight-bold mb-1"
                  >
                    {{ $t("orders.order_status_label") }}
                  </div>
                  <div
                    class="text-subtitle-1 font-weight-black"
                    :class="getOrderTheme(order.status).textColor"
                  >
                    {{ getOrderTheme(order.status).text }}
                  </div>
                </div>
              </div>

              <v-divider
                vertical
                class="d-none d-md-block h-auto my-2 border-opacity-50"
              ></v-divider>

              <div>
                <div
                  class="text-caption text-grey-darken-1 text-uppercase font-weight-bold mb-1"
                >
                  {{ $t("orders.order_date_label") }}
                </div>
                <div
                  class="text-body-2 font-weight-bold text-grey-darken-3 d-flex align-center"
                >
                  <v-icon size="small" class="ml-1 text-grey"
                    >mdi-calendar-clock</v-icon
                  >
                  {{ formatDate(order.created_at) }}
                </div>
              </div>

              <v-divider
                vertical
                class="d-none d-md-block h-auto my-2 border-opacity-50"
              ></v-divider>

              <div>
                <div
                  class="text-caption text-grey-darken-1 text-uppercase font-weight-bold mb-1"
                >
                  {{ $t("orders.billed_total_label") }}
                </div>
                <div
                  class="text-body-1 font-weight-black text-primary-darken-1"
                >
                  {{ order.total }} {{ $t("products.currency") }}
                </div>
              </div>
            </div>

            <div class="d-flex flex-column align-end mt-4 mt-sm-0">
              <div
                class="text-caption text-grey-darken-1 text-uppercase font-weight-bold mb-1"
              >
                {{ $t("orders.order_id_label") }}
              </div>
              <div
                class="text-subtitle-2 font-weight-bold bg-white px-3 py-1 rounded-md border text-grey-darken-3 mb-3"
              >
                # {{ order.order_number }}
              </div>
              <div class="d-flex gap-2">
                <v-btn
                  variant="text"
                  color="primary"
                  size="small"
                  class="font-weight-bold rounded-lg px-2"
                  prepend-icon="mdi-text-box-outline"
                  @click="printInvoice(order)"
                  >{{ $t("orders.download_invoice") }}</v-btn
                >
              </div>
            </div>
          </div>

          <!-- جسم الطلب - تفاصيل المنتجات -->
          <div class="pa-6 bg-white">
            <div class="d-flex flex-column flex-lg-row gap-6">
              <!-- قائمة المنتجات -->
              <div class="flex-grow-1 order-items-list">
                <h4
                  class="text-subtitle-1 font-weight-bold mb-4 text-grey-darken-3 d-flex align-center"
                >
                  <v-icon
                    size="small"
                    :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                    color="grey-darken-1"
                    >mdi-package-variant</v-icon
                  >
                  {{
                    $t("orders.order_items_count", {
                      count: order.items?.length || 0,
                    })
                  }}
                </h4>

                <div
                  v-for="(item, i) in order.items"
                  :key="i"
                  class="product-row d-flex gap-4 pa-3 rounded-lg transition-all mb-3 position-relative"
                  :class="{ 'bg-grey-lighten-5': true }"
                >
                  <div
                    class="image-wrapper rounded-lg overflow-hidden border bg-white flex-shrink-0"
                    style="width: 100px; height: 100px"
                  >
                    <v-img
                      :src="
                        item.image ||
                        (item.product ? item.product.thumbnail : '')
                      "
                      width="100%"
                      height="100%"
                      cover
                      class="product-img"
                    ></v-img>
                  </div>

                  <div
                    class="py-1 d-flex flex-column justify-center flex-grow-1"
                  >
                    <h5
                      class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1 line-clamp-2"
                      style="line-height: 1.3"
                    >
                      {{ item.name }}
                    </h5>
                    <div class="text-caption text-grey-darken-1 mb-2">
                      {{ $t("checkout.quantity") }} {{ item.quantity || 1 }}
                    </div>

                    <div class="mt-auto d-flex align-center gap-2">
                      <v-btn
                        color="primary"
                        variant="tonal"
                        height="32"
                        class="rounded-pill font-weight-bold px-4 text-caption"
                        elevation="0"
                        :to="`/product-details/${item.product_id}`"
                      >
                        {{ $t("orders.buy_again") }}
                        <v-icon
                          size="small"
                          :class="locale === 'ar' ? 'mr-1' : 'ml-1'"
                          >mdi-refresh</v-icon
                        >
                      </v-btn>
                    </div>
                  </div>

                  <!-- سعر العنصر -->
                  <div
                    class="d-none d-sm-block"
                    :class="locale === 'ar' ? 'ml-4' : 'mr-4'"
                    style="text-align: left; padding-top: 4px"
                  >
                    <span
                      class="font-weight-black text-body-1 text-primary-darken-1"
                      >{{ item.price || 0 }} {{ $t("products.currency") }}</span
                    >
                  </div>
                </div>
              </div>

              <!-- لوحة التحكم الجانبية للإجراءات -->
              <div
                class="action-panel bg-grey-lighten-5 rounded-xl p-5 border d-flex flex-column justify-center mt-4 mt-lg-0"
              >
                <h4
                  class="text-subtitle-2 font-weight-bold mb-4 text-center text-grey-darken-2"
                >
                  {{ $t("orders.order_actions") }}
                </h4>
                <div class="d-flex flex-column gap-3 w-100">
                  <v-btn
                    v-if="order.status === 'shipped'"
                    block
                    color="primary"
                    class="rounded-lg font-weight-bold text-body-2"
                    height="44"
                    elevation="2"
                    @click="trackOrder(order)"
                  >
                    <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                      >mdi-map-marker-path</v-icon
                    >
                    {{ $t("orders.live_track") }}
                  </v-btn>

                  <v-btn
                    v-if="order.status === 'pending'"
                    block
                    color="primary"
                    class="rounded-lg font-weight-bold text-body-2 text-white"
                    height="44"
                    elevation="2"
                    :loading="actionLoading === 'expedite_' + order.id"
                    :disabled="order.is_urgent"
                    @click="expediteOrder(order)"
                  >
                    <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                      >mdi-fire</v-icon
                    >
                    {{
                      order.is_urgent
                        ? $t("orders.expedited_success")
                        : $t("orders.expedite_shipping")
                    }}
                  </v-btn>

                  <v-btn
                    v-if="order.status === 'pending'"
                    block
                    variant="outlined"
                    color="error"
                    class="rounded-lg font-weight-bold text-body-2 bg-white"
                    height="44"
                    :loading="actionLoading === 'cancel_' + order.id"
                    @click="cancelOrder(order)"
                  >
                    <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                      >mdi-cancel</v-icon
                    >
                    {{ $t("orders.cancel_order") }}
                  </v-btn>

                  <v-btn
                    block
                    variant="outlined"
                    color="primary"
                    class="rounded-lg font-weight-bold text-body-2 bg-white"
                    height="44"
                    v-if="
                      ['completed', 'shipped', 'delivered'].includes(
                        order.status
                      )
                    "
                    :loading="actionLoading === 'return_' + order.id"
                    :disabled="
                      order.return_request_status === 'pending' ||
                      order.return_request_status === 'approved'
                    "
                    @click="openReturnDialog(order)"
                  >
                    <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                      >mdi-undo-variant</v-icon
                    >
                    {{
                      order.return_request_status === "pending"
                        ? $t("orders.return_review")
                        : $t("orders.return_request")
                    }}
                  </v-btn>

                  <v-btn
                    block
                    variant="tonal"
                    color="blue-grey-darken-2"
                    class="rounded-lg font-weight-bold text-body-2 mt-2"
                    height="44"
                    v-if="
                      order.status === 'delivered' ||
                      order.status === 'completed'
                    "
                    @click="rateOrder(order)"
                  >
                    <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
                      >mdi-message-star-outline</v-icon
                    >
                    {{ $t("orders.rate_experience") }}
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
        </v-card>
      </v-expand-transition>
    </v-container>

    <!-- Track Order Dialog -->
    <v-dialog v-model="trackDialog" max-width="500">
      <v-card class="rounded-xl pa-4">
        <v-card-title
          class="text-h6 font-weight-bold text-primary mb-4 d-flex align-center"
        >
          <v-icon :class="locale === 'ar' ? 'ml-2' : 'mr-2'"
            >mdi-truck-fast</v-icon
          >
          {{
            $t("orders.track_order_title", { no: selectedOrder?.order_number })
          }}
        </v-card-title>
        <v-card-text>
          <v-timeline align="start" density="compact" truncate-line="both">
            <v-timeline-item
              :dot-color="
                [
                  'pending',
                  'processing',
                  'shipped',
                  'delivered',
                  'completed',
                ].includes(selectedOrder?.status)
                  ? 'success'
                  : 'grey-lighten-2'
              "
              size="small"
            >
              <div class="mb-4">
                <div
                  class="font-weight-bold"
                  :class="
                    selectedOrder?.status === 'pending' ? 'text-primary' : ''
                  "
                >
                  {{ $t("orders.confirmed_status") }}
                </div>
                <div class="text-caption text-grey">
                  {{ $t("orders.confirmed_hint") }}
                </div>
              </div>
            </v-timeline-item>

            <v-timeline-item
              :dot-color="
                ['processing', 'shipped', 'delivered', 'completed'].includes(
                  selectedOrder?.status
                )
                  ? 'success'
                  : 'grey-lighten-2'
              "
              size="small"
            >
              <div class="mb-4">
                <div
                  class="font-weight-bold"
                  :class="
                    selectedOrder?.status === 'processing' ? 'text-primary' : ''
                  "
                >
                  {{ $t("orders.processing_status") }}
                </div>
                <div class="text-caption text-grey">
                  {{ $t("orders.processing_hint") }}
                </div>
              </div>
            </v-timeline-item>

            <v-timeline-item
              :dot-color="
                ['shipped', 'delivered', 'completed'].includes(
                  selectedOrder?.status
                )
                  ? 'primary'
                  : 'grey-lighten-2'
              "
              size="large"
              icon="mdi-truck"
            >
              <div class="mb-4">
                <div
                  class="font-weight-bold"
                  :class="
                    selectedOrder?.status === 'shipped' ? 'text-primary' : ''
                  "
                >
                  {{ $t("orders.shipped_status") }}
                </div>
                <div class="text-caption text-grey">
                  {{ $t("orders.shipped_hint") }}
                </div>
              </div>
            </v-timeline-item>

            <v-timeline-item
              :dot-color="
                ['delivered', 'completed'].includes(selectedOrder?.status)
                  ? 'success'
                  : 'grey-lighten-2'
              "
              size="small"
            >
              <div class="mb-4">
                <div
                  class="font-weight-bold"
                  :class="
                    ['delivered', 'completed'].includes(selectedOrder?.status)
                      ? 'text-primary'
                      : 'text-grey'
                  "
                >
                  {{ $t("orders.delivered_status") }}
                </div>
                <div class="text-caption text-grey">
                  {{ $t("orders.delivered_hint") }}
                </div>
              </div>
            </v-timeline-item>
          </v-timeline>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            @click="trackDialog = false"
            >{{ $t("orders.close_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Rate Dialog -->
    <v-dialog v-model="rateDialog" max-width="600">
      <v-card class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-bold text-primary mb-4">
          {{ $t("orders.rate_products_title") }}
        </v-card-title>
        <v-card-text>
          <div
            v-for="item in selectedOrder?.items"
            :key="item.id"
            class="mb-6 p-4 border rounded-lg bg-grey-lighten-5"
          >
            <div class="d-flex align-center gap-4 mb-3">
              <v-avatar size="50" rounded>
                <img
                  :src="
                    item.image || (item.product ? item.product.thumbnail : '')
                  "
                />
              </v-avatar>
              <div class="text-subtitle-2 font-weight-bold">
                {{ item.name }}
              </div>
            </div>
            <v-rating
              v-model="reviews[item.product_id]"
              color="warning"
              active-color="warning"
              half-increments
              hover
            ></v-rating>
            <v-textarea
              v-model="comments[item.product_id]"
              :placeholder="$t('orders.rate_comment_placeholder')"
              variant="outlined"
              density="compact"
              rows="2"
              class="mt-2"
              hide-details
            ></v-textarea>
            <v-btn
              class="mt-3 font-weight-bold"
              color="primary"
              size="small"
              :loading="actionLoading === 'rate_' + item.product_id"
              @click="submitRating(item.product_id)"
              >{{ $t("orders.publish_rating") }}</v-btn
            >
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            @click="rateDialog = false"
            >{{ $t("orders.close_btn") }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Return Request Dialog -->
    <v-dialog v-model="returnDialog" max-width="500">
      <v-card v-if="selectedOrder" class="rounded-xl pa-4">
        <v-card-title class="text-h6 font-weight-bold text-primary mb-2">
          {{
            $t("orders.return_request_title", {
              no: selectedOrder.order_number,
            })
          }}
        </v-card-title>
        <v-card-text>
          <p class="text-body-2 text-grey-darken-1 mb-4">
            {{ $t("orders.return_hint") }}
          </p>
          <v-select
            v-model="returnReasonCode"
            :items="returnReasonOptions"
            item-title="title"
            item-value="value"
            :label="$t('orders.return_reason_label')"
            variant="outlined"
            class="mb-4 rounded-lg"
            hide-details
          ></v-select>
          <v-textarea
            v-model="returnReasonDetails"
            :label="$t('orders.return_additional_details')"
            :placeholder="$t('orders.return_notes_placeholder')"
            variant="outlined"
            rows="3"
            auto-grow
            class="rounded-lg"
            hide-details
          ></v-textarea>
        </v-card-text>
        <v-card-actions class="pb-2">
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            @click="returnDialog = false"
            class="font-weight-bold"
          >
            {{ $t("orders.cancel_btn") }}
          </v-btn>
          <v-btn
            color="primary"
            variant="elevated"
            class="px-6 rounded-lg font-weight-bold"
            @click="submitReturnRequest"
            :loading="actionLoading === 'return_' + selectedOrder.id"
            :disabled="!returnReasonCode"
          >
            {{ $t("orders.submit_request") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, inject, watch } from "vue";
import { useI18n } from "vue-i18n";
const { locale, t } = useI18n();
import axios from "@/axios";
import { useRouter } from "vue-router";

const router = useRouter();
const emitter = inject("emitter");

const tab = ref("all");
const selectedPeriod = ref("all");

const periods = computed(() => [
  { title: t("orders.period_all_times"), value: "all" },
  { title: t("orders.period_last_30_days"), value: "last_30_days" },
  { title: t("orders.period_last_3_months"), value: "last_3_months" },
  { title: "2025", value: "2025" },
  { title: "2024", value: "2024" },
]);
const searchQuery = ref("");

const orders = ref([]);
const loading = ref(true);
const actionLoading = ref(null);
const trackDialog = ref(false);
const rateDialog = ref(false);
const selectedOrder = ref(null);
const reviews = ref({});
const comments = ref({});
const returnDialog = ref(false);
const returnReasonCode = ref(null);
const returnReasonDetails = ref("");
const returnReasonOptions = computed(() => [
  { title: t("orders.reason_wrong_item"), value: "WRONG_ITEM" },
  { title: t("orders.reason_damaged"), value: "DAMAGED" },
  { title: t("orders.reason_size_issue"), value: "SIZE_ISSUE" },
  { title: t("orders.reason_not_as_described"), value: "NOT_AS_DESCRIBED" },
  { title: t("orders.reason_quality_issue"), value: "QUALITY_ISSUE" },
  { title: t("orders.reason_changed_mind"), value: "CHANGED_MIND" },
  { title: t("orders.reason_other"), value: "OTHER" },
]);

const fetchOrders = async (showLoading = true) => {
  if (showLoading) loading.value = true;
  try {
    const params = {};
    if (tab.value !== "all") params.status = tab.value;

    if (selectedPeriod.value !== "all") {
      params.period = selectedPeriod.value;
    }

    const res = await axios.get("/user/orders", { params });
    orders.value = res.data;
  } catch (err) {
    console.error("Error fetching orders:", err);
    if (err.response && err.response.status === 401) {
      router.push("/Auth/LogIn");
    }
  } finally {
    if (showLoading) loading.value = false;
  }
};

let pollInterval = null;

const startPolling = () => {
  if (pollInterval) clearInterval(pollInterval);
  pollInterval = setInterval(() => fetchOrders(false), 20000); // 20 seconds
};

onMounted(() => {
  fetchOrders();
  startPolling();

  document.addEventListener("visibilitychange", () => {
    if (!document.hidden) {
      fetchOrders(false);
      startPolling();
    } else {
      if (pollInterval) clearInterval(pollInterval);
    }
  });
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});

const getOrderTheme = (status) => {
  if (status === "delivered" || status === "completed")
    return {
      text: t("orders.delivered"),
      headerClass: "bg-green-lighten-5 border-green-lighten-4 border-b",
      iconBg: "green-lighten-4",
      iconColor: "green-darken-3",
      icon: "mdi-check-decagram",
      textColor: "text-green-darken-4",
    };
  if (status === "shipped")
    return {
      text: t("orders.shipped"),
      headerClass: "bg-blue-lighten-5 border-blue-lighten-4 border-b",
      iconBg: "blue-lighten-4",
      iconColor: "blue-darken-3",
      icon: "mdi-truck-fast",
      textColor: "text-blue-darken-4",
    };
  if (status === "cancelled")
    return {
      text: t("orders.cancelled"),
      headerClass: "bg-red-lighten-5 border-red-lighten-4 border-b",
      iconBg: "red-lighten-4",
      iconColor: "red-darken-3",
      icon: "mdi-cancel",
      textColor: "text-red-darken-4",
    };
  if (status === "returned")
    return {
      text: t("orders.returned"),
      headerClass: "bg-grey-lighten-3 border-grey-lighten-2 border-b",
      iconBg: "grey-lighten-2",
      iconColor: "grey-darken-3",
      icon: "mdi-keyboard-return",
      textColor: "text-grey-darken-4",
    };
  // default / pending
  return {
    text: t("orders.pending"),
    headerClass: "bg-primary-lighten-5 border-primary-lighten-4 border-b",
    iconBg: "primary-lighten-4",
    iconColor: "primary-darken-3",
    icon: "mdi-package-variant-closed",
    textColor: "text-primary-darken-4",
  };
};

const formatDate = (dateString) => {
  if (!dateString) return "";
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(
    locale.value === "ar" ? "ar-EG" : "en-US",
    options
  );
};

const filteredOrders = computed(() => {
  let result = orders.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (order) =>
        (order.order_number &&
          order.order_number.toLowerCase().includes(query)) ||
        (order.items &&
          order.items.some((item) => item.name.toLowerCase().includes(query)))
    );
  }

  // Backend already filters by status and period if we call it correctly.
  // But we allow local tab filtering for better UX if we didn't re-fetch.
  // Actually it's better to watch tab/period and refetch.

  return result;
});

// Watch for changes to refetch

watch([tab, selectedPeriod], () => {
  fetchOrders();
});

const printInvoice = () => {
  window.print();
};

const trackOrder = (order) => {
  selectedOrder.value = order;
  trackDialog.value = true;
};

const rateOrder = (order) => {
  selectedOrder.value = order;
  reviews.value = {};
  comments.value = {};
  rateDialog.value = true;
};

const expediteOrder = async (order) => {
  actionLoading.value = "expedite_" + order.id;
  try {
    const res = await axios.post(`/user/orders/${order.id}/expedite`);
    emitter.emit("showMessage", {
      text: res.data.message || "تم تسجيل التعجيل بنجاح!",
      color: "success",
    });
    order.is_urgent = true;
  } catch (error) {
    emitter.emit("showMessage", {
      text: error.response?.data?.message || "حدث خطأ في تقديم الطلب",
      color: "error",
    });
  } finally {
    actionLoading.value = null;
  }
};

const cancelOrder = async (order) => {
  if (!confirm("هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟")) return;
  actionLoading.value = "cancel_" + order.id;
  try {
    const res = await axios.post(`/user/orders/${order.id}/cancel`);
    emitter.emit("showMessage", {
      text: res.data.message || "تم إلغاء الطلب بنجاح!",
      color: "success",
    });
    order.status = "cancelled";
  } catch (error) {
    emitter.emit("showMessage", {
      text: error.response?.data?.message || "حدث خطأ في إلغاء الطلب",
      color: "error",
    });
  } finally {
    actionLoading.value = null;
  }
};

const openReturnDialog = (order) => {
  selectedOrder.value = order;
  returnReasonCode.value = null;
  returnReasonDetails.value = "";
  returnDialog.value = true;
};

const submitReturnRequest = async () => {
  if (!selectedOrder.value || !returnReasonCode.value) return;
  const order = selectedOrder.value;
  actionLoading.value = "return_" + order.id;
  try {
    const res = await axios.post(
      `/user/orders/${selectedOrder.value.id}/return`,
      {
        reason: returnReasonCode.value + ": " + returnReasonDetails.value,
      }
    );
    order.return_request_status = "pending";
    returnDialog.value = false;
    emitter.emit("showMessage", {
      text: res.data.message || "تم تسجيل طلب الاسترجاع بنجاح!",
      color: "success",
    });
  } catch (err) {
    emitter.emit("showMessage", {
      text: "حدث خطأ في تقديم الطلب",
      color: "error",
    });
  } finally {
    actionLoading.value = null;
  }
};

const submitRating = async (productId) => {
  if (!reviews.value[productId]) {
    return emitter.emit("showMessage", {
      text: "يرجى تحديد التقييم بالنجوم أولاً",
      color: "warning",
    });
  }
  actionLoading.value = "rate_" + productId;
  try {
    await axios.post(`/products/${productId}/reviews`, {
      rating: reviews.value[productId],
      comment: comments.value[productId],
    });
    emitter.emit("showMessage", {
      text: "تم تسجيل تقييمك بنجاح! نشكرك على ثقتك.",
      color: "success",
    });
  } catch (err) {
    emitter.emit("showMessage", {
      text: "حدث خطأ أثناء إرسال التقييم",
      color: "error",
    });
  } finally {
    actionLoading.value = null;
  }
};
</script>

<style scoped>
.orders-page {
  font-family: inherit;
}

.custom-shadow {
  box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.06) !important;
}

.modern-tabs {
  background: transparent !important;
}
:deep(.v-slide-group__content) {
  justify-content: flex-start;
}

.modern-select :deep(.v-field__outline),
.modern-search :deep(.v-field__outline) {
  --v-field-border-opacity: 0;
  border: 1px solid #edf2f7;
  border-radius: 12px;
  transition: all 0.3s ease;
}
.modern-select:hover :deep(.v-field__outline),
.modern-search:hover :deep(.v-field__outline) {
  border-color: rgba(var(--v-theme-primary), 0.3);
}
.modern-search :deep(.v-field--focused .v-field__outline) {
  border-color: rgb(var(--v-theme-primary));
  box-shadow: 0 0 0 3px rgba(var(--v-theme-primary), 0.1);
}

.order-card {
  border: 1px solid #f0f0f0 !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.order-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1) !important;
}

.order-header {
  border-bottom-width: 1px !important;
  border-bottom-style: solid !important;
}

.border-opacity-50 {
  border-color: rgba(0, 0, 0, 0.08) !important;
}

.product-row {
  border: 1px solid transparent;
}
.product-row:hover {
  background-color: #f8fafc !important;
  border-color: #edf2f7;
}

.product-img {
  transition: transform 0.5s ease;
}
.image-wrapper:hover .product-img {
  transform: scale(1.1);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.action-panel {
  min-width: 300px;
  padding: 24px;
}

@media (max-width: 960px) {
  .action-panel {
    min-width: 100%;
    border-left: none;
    border-top: 1px dashed #e2e8f0;
    margin-top: 16px;
    padding-top: 24px;
  }
  .wrapper-filters {
    width: 100%;
    flex-direction: column;
    align-items: stretch !important;
  }
  .modern-search,
  .modern-select {
    max-width: 100% !important;
  }
}
</style>
