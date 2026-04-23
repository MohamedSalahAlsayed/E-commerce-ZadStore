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
        <div v-if="loading" key="loading" class="mt-4">
          <v-card
            v-for="i in 3"
            :key="`skel-order-${i}`"
            class="mb-6 rounded-xl overflow-hidden border-0"
            elevation="0"
          >
            <!-- Skeleton Header -->
            <div
              class="pa-5 d-flex flex-wrap justify-space-between align-center bg-grey-lighten-4"
            >
              <div class="d-flex align-center gap-6">
                <v-skeleton-loader
                  type="list-item-avatar-two-line"
                  width="200"
                  class="bg-transparent"
                />
                <v-skeleton-loader
                  type="text"
                  width="100"
                  class="bg-transparent d-none d-md-block"
                />
                <v-skeleton-loader
                  type="text"
                  width="100"
                  class="bg-transparent d-none d-md-block"
                />
              </div>
              <v-skeleton-loader
                type="text"
                width="120"
                class="bg-transparent"
              />
            </div>
            <!-- Skeleton Body -->
            <div class="pa-6 bg-white d-flex flex-column flex-lg-row gap-6">
              <div class="flex-grow-1">
                <div
                  v-for="j in 2"
                  :key="j"
                  class="d-flex gap-4 mb-3 pa-3 bg-grey-lighten-5 rounded-lg"
                >
                  <v-skeleton-loader
                    type="image"
                    width="100"
                    height="100"
                    class="rounded-lg"
                  />
                  <div class="flex-grow-1">
                    <v-skeleton-loader type="text" width="80%" class="mb-2" />
                    <v-skeleton-loader type="text" width="40%" />
                  </div>
                </div>
              </div>
              <div
                class="bg-grey-lighten-5 rounded-xl p-5 border d-flex flex-column gap-3"
                style="min-width: 200px"
              >
                <v-skeleton-loader type="button" block />
                <v-skeleton-loader type="button" block />
              </div>
            </div>
          </v-card>
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
          <!-- رأس الطلب (تصميم احترافي ومنظم) -->
          <div
            class="order-header pa-6 bg-white border-b"
            :class="getOrderTheme(order.status).headerBorder"
          >
            <div
              class="d-flex flex-column flex-md-row justify-space-between align-start align-md-center gap-4"
            >
              <div class="d-flex align-center gap-4">
                <v-chip
                  :color="getOrderTheme(order.status).iconColor"
                  :variant="
                    getOrderTheme(order.status).headerClass ? 'tonal' : 'flat'
                  "
                  class="font-weight-bold px-4"
                  size="large"
                >
                  <v-icon start size="18">{{
                    getOrderTheme(order.status).icon
                  }}</v-icon>
                  {{ getOrderTheme(order.status).text }}
                  <v-icon
                    v-if="order.is_urgent"
                    color="error"
                    size="16"
                    class="mr-2 pulse-icon"
                    >mdi-fire</v-icon
                  >
                </v-chip>

                <div class="d-flex flex-column">
                  <span
                    class="text-caption text-grey-darken-1 font-weight-bold mb-n1"
                  >
                    {{ $t("orders.order_id_label") }}
                  </span>
                  <span
                    class="text-subtitle-2 font-weight-black text-grey-darken-4"
                  >
                    #{{ order.order_number }}
                  </span>
                </div>
              </div>

              <div class="d-flex align-center gap-3 flex-wrap">
                <div class="d-flex flex-column align-md-end mr-md-6">
                  <span
                    class="text-caption text-grey-darken-1 font-weight-bold mb-n1"
                  >
                    {{ $t("orders.order_date_label") }}
                  </span>
                  <span
                    class="text-body-2 font-weight-medium text-grey-darken-3"
                  >
                    {{ formatDate(order.created_at) }}
                  </span>
                </div>

                <v-divider vertical class="mx-2 d-none d-md-block h-6" />

                <div class="d-flex flex-column align-md-end mr-md-6">
                  <span
                    class="text-caption text-grey-darken-1 font-weight-bold mb-n1"
                  >
                    {{ $t("orders.billed_total_label") }}
                  </span>
                  <span class="text-body-1 font-weight-black text-primary">
                    {{ order.total }} {{ $t("products.currency") }}
                  </span>
                </div>

                <v-btn
                  variant="outlined"
                  color="grey-darken-1"
                  size="small"
                  class="rounded-lg ml-md-4"
                  prepend-icon="mdi-printer-outline"
                  @click="printInvoice(order)"
                >
                  {{ $t("orders.download_invoice") }}
                </v-btn>
              </div>
            </div>
          </div>

          <!-- Professional Invoice Template (Hidden by default, visible on print) -->
          <div :id="'invoice-' + order.id" class="invoice-container d-none">
            <div class="invoice-header">
              <div class="d-flex justify-space-between align-center mb-6">
                <div>
                  <h1 class="text-h4 font-weight-black text-primary">
                    ZadStore
                  </h1>
                  <p class="text-subtitle-2 text-grey-darken-1">
                    رؤية جديدة للتسوق
                  </p>
                </div>
                <div
                  class="${locale === 'ar' ? 'text-left' : 'text-right'}"
                  style="text-align: $ {locale==='ar'?'left': 'right'}"
                >
                  <h2 class="text-h6 font-weight-bold">فاتورة ضريبية</h2>
                  <p class="text-body-2">
                    رقم الطلب: #{{ order.order_number }}
                  </p>
                  <p class="text-body-2">
                    التاريخ: {{ formatDate(order.created_at) }}
                  </p>
                </div>
              </div>
              <v-divider class="mb-6"></v-divider>
              <v-row>
                <v-col cols="6">
                  <h3 class="text-subtitle-2 font-weight-black mb-2">
                    بيانات العميل:
                  </h3>
                  <p class="mb-1">
                    <strong>الاسم:</strong> {{ order.customer_name }}
                  </p>
                  <p class="mb-1"><strong>الهاتف:</strong> {{ order.phone }}</p>
                  <p class="mb-1">
                    <strong>العنوان:</strong> {{ order.address }}
                  </p>
                </v-col>
                <v-col
                  cols="6"
                  class="${locale === 'ar' ? 'text-left' : 'text-right'}"
                  style="text-align: $ {locale==='ar'?'left': 'right'}"
                >
                  <h3 class="text-subtitle-2 font-weight-black mb-2">
                    حالة الدفع:
                  </h3>
                  <p class="mb-1">
                    <strong>الطريقة:</strong>
                    {{ order.payment_method?.toUpperCase() }}
                  </p>
                  <v-chip
                    size="small"
                    :color="
                      order.payment_status === 'paid' ? 'success' : 'error'
                    "
                    class="mt-1"
                  >
                    {{
                      order.payment_status === "paid"
                        ? "تم الدفع"
                        : "لم يتم الدفع"
                    }}
                  </v-chip>
                </v-col>
              </v-row>
            </div>

            <!-- Invoice Items Table -->
            <table
              class="invoice-table"
              style="
                width: 100%;
                border-collapse: collapse;
                margin: 30px 0;
                border: 1px solid #eee;
              "
            >
              <thead>
                <tr style="background: #f8fafc">
                  <th
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: right;
                    "
                  >
                    {{ locale === "ar" ? "المنتج" : "Product" }}
                  </th>
                  <th
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: center;
                    "
                  >
                    {{ locale === "ar" ? "الكمية" : "Qty" }}
                  </th>
                  <th
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: left;
                    "
                  >
                    {{ locale === "ar" ? "السعر" : "Price" }}
                  </th>
                  <th
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: left;
                    "
                  >
                    {{ locale === "ar" ? "الإجمالي" : "Total" }}
                  </th>
                  <th
                    v-if="order.return_request_status"
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: center;
                    "
                  >
                    {{ locale === "ar" ? "الحالة" : "Status" }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="item in order.items"
                  :key="item.id"
                  :style="
                    isItemReturned(order, item)
                      ? 'opacity:0.5;background:#fff8f8;'
                      : ''
                  "
                >
                  <td
                    style="padding: 12px; border: 1px solid #eee"
                    :style="
                      isItemReturned(order, item)
                        ? 'text-decoration:line-through;'
                        : ''
                    "
                  >
                    {{ item.name }}
                  </td>
                  <td
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: center;
                    "
                  >
                    {{ item.quantity }}
                  </td>
                  <td
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: left;
                    "
                  >
                    {{ item.price }} {{ $t("products.currency") }}
                  </td>
                  <td
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: left;
                      font-weight: bold;
                    "
                  >
                    {{ item.price * item.quantity }}
                    {{ $t("products.currency") }}
                  </td>
                  <td
                    v-if="order.return_request_status"
                    style="
                      padding: 12px;
                      border: 1px solid #eee;
                      text-align: center;
                    "
                  >
                    <span
                      v-if="isItemReturned(order, item)"
                      style="color: #d32f2f; font-size: 12px; font-weight: bold"
                      >{{ locale === "ar" ? "⮐ مُرجَع" : "⮐ Returned" }}</span
                    >
                    <span v-else style="color: #2e7d32; font-size: 12px">{{
                      locale === "ar" ? "✓ نشط" : "✓ Active"
                    }}</span>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="invoice-footer d-flex flex-column align-end">
              <div
                class="summary-line d-flex justify-space-between w-100"
                style="max-width: 250px"
              >
                <span>{{ locale === "ar" ? "المجموع:" : "Subtotal:" }}</span>
                <span
                  >{{
                    order.subtotal || order.total - (order.shipping_fee || 0)
                  }}
                  {{ $t("products.currency") }}</span
                >
              </div>
              <div
                class="summary-line d-flex justify-space-between w-100"
                style="max-width: 250px"
              >
                <span>{{ locale === "ar" ? "الشحن:" : "Shipping:" }}</span>
                <span
                  >{{ order.shipping_fee || 0 }}
                  {{ $t("products.currency") }}</span
                >
              </div>
              <!-- Refund line for returned/partially returned orders -->
              <div
                v-if="
                  ['returned', 'partially_returned'].includes(order.status) &&
                  order.return_request_status === 'approved'
                "
                class="summary-line d-flex justify-space-between w-100"
                style="max-width: 250px; color: #d32f2f"
              >
                <span style="font-weight: bold">{{
                  locale === "ar" ? "المبلغ المُسترد:" : "Refunded:"
                }}</span>
                <span style="font-weight: bold"
                  >- {{ getRefundAmount(order) }}
                  {{ $t("products.currency") }}</span
                >
              </div>
              <v-divider class="my-2" style="width: 250px"></v-divider>
              <div
                class="summary-line final-total d-flex justify-space-between w-100 text-h6 font-weight-black text-primary"
                style="max-width: 250px"
              >
                <span>{{ locale === "ar" ? "الإجمالي:" : "Total:" }}</span>
                <span>{{ order.total }} {{ $t("products.currency") }}</span>
              </div>
            </div>
            <div class="mt-16 text-center text-caption text-grey">
              {{
                locale === "ar"
                  ? "شكراً لتسوقكم من زاد ستور - نتمنى لكم يوماً سعيداً"
                  : "Thank you for shopping at ZadStore - Have a great day!"
              }}
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
                  class="product-row d-flex align-center gap-4 pa-4 rounded-xl border mb-3 bg-white"
                >
                  <div
                    class="image-wrapper rounded-lg overflow-hidden border bg-grey-lighten-5 flex-shrink-0"
                    style="width: 80px; height: 80px"
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

                  <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-center gap-2 flex-wrap">
                      <h5
                        class="text-subtitle-2 font-weight-bold text-grey-darken-4 mb-0 line-clamp-1"
                        :style="
                          isItemReturned(order, item)
                            ? 'text-decoration:line-through;opacity:0.6;'
                            : ''
                        "
                      >
                        {{ item.name }}
                      </h5>
                      <v-chip
                        v-if="isItemReturned(order, item)"
                        size="x-small"
                        color="error"
                        variant="tonal"
                        class="ms-1"
                      >
                        {{ locale === "ar" ? "مُرجَع" : "Returned" }}
                      </v-chip>
                    </div>
                    <div class="d-flex align-center gap-4 mt-1">
                      <span class="text-caption text-grey-darken-1">
                        {{ $t("checkout.quantity") }} {{ item.quantity || 1 }}
                      </span>
                      <span class="text-caption font-weight-black text-primary">
                        {{ item.price || 0 }} {{ $t("products.currency") }}
                      </span>
                    </div>
                  </div>

                  <v-btn
                    color="primary"
                    variant="text"
                    size="small"
                    class="rounded-pill font-weight-bold"
                    :loading="actionLoading === 'add_to_cart_' + item.id"
                    @click="buyAgain(item)"
                  >
                    {{ $t("orders.buy_again") }}
                    <v-icon size="small" class="ms-1">mdi-refresh</v-icon>
                  </v-btn>
                </div>
              </div>

              <!-- لوحة التحكم الجانبية للإجراءات (تجميع ذكي) -->
              <div
                class="action-panel bg-grey-lighten-5 rounded-xl pa-6 border-s-md d-flex flex-column"
                style="min-width: 250px"
              >
                <div class="d-flex flex-column gap-3">
                  <!-- الإجراءات الأساسية -->
                  <v-btn
                    v-if="order.status === 'shipped'"
                    block
                    color="primary"
                    class="rounded-xl font-weight-bold"
                    height="48"
                    elevation="2"
                    prepend-icon="mdi-map-marker-path"
                    @click="trackOrder(order)"
                  >
                    {{ $t("orders.live_track") }}
                  </v-btn>

                  <v-btn
                    v-if="order.status === 'pending'"
                    block
                    color="primary"
                    class="rounded-xl font-weight-bold text-white"
                    height="48"
                    elevation="2"
                    prepend-icon="mdi-fire"
                    :loading="actionLoading === 'expedite_' + order.id"
                    :disabled="order.is_urgent"
                    @click="expediteOrder(order)"
                  >
                    {{
                      order.is_urgent
                        ? $t("orders.expedited_success")
                        : $t("orders.expedite_shipping")
                    }}
                  </v-btn>

                  <v-divider class="my-2 border-opacity-50" />

                  <!-- الإجراءات الثانوية -->
                  <div class="d-flex flex-column gap-2">
                    <v-btn
                      v-if="order.status === 'pending'"
                      block
                      variant="text"
                      color="error"
                      class="rounded-lg font-weight-bold justify-start"
                      size="small"
                      prepend-icon="mdi-cancel"
                      :loading="actionLoading === 'cancel_' + order.id"
                      @click="cancelOrder(order)"
                    >
                      {{ $t("orders.cancel_order") }}
                    </v-btn>

                    <v-btn
                      v-if="
                        ['completed', 'shipped', 'delivered'].includes(
                          order.status
                        )
                      "
                      block
                      variant="text"
                      color="grey-darken-2"
                      class="rounded-lg font-weight-bold justify-start"
                      size="small"
                      prepend-icon="mdi-undo-variant"
                      :loading="actionLoading === 'return_' + order.id"
                      :disabled="
                        order.return_request_status === 'pending' ||
                        order.return_request_status === 'approved'
                      "
                      @click="openReturnDialog(order)"
                    >
                      {{
                        order.return_request_status === "pending"
                          ? $t("orders.return_review")
                          : $t("orders.return_request")
                      }}
                    </v-btn>

                    <v-btn
                      v-if="
                        order.status === 'delivered' ||
                        order.status === 'completed'
                      "
                      block
                      variant="tonal"
                      color="primary"
                      class="rounded-xl font-weight-bold mt-2"
                      height="40"
                      prepend-icon="mdi-message-star-outline"
                      @click="rateOrder(order)"
                    >
                      {{ $t("orders.rate_experience") }}
                    </v-btn>
                  </div>
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
              v-for="(st, i) in orderStory"
              :key="i"
              :dot-color="
                isStepCompleted(st.value) ? 'success' : 'grey-lighten-2'
              "
              :size="st.value === selectedOrder?.status ? 'large' : 'small'"
              :icon="st.value === selectedOrder?.status ? st.icon : ''"
            >
              <div class="mb-4">
                <div
                  class="font-weight-bold"
                  :class="
                    isStepCompleted(st.value) ? 'text-primary' : 'text-grey'
                  "
                >
                  {{ st.title }}
                </div>
                <div class="text-caption text-grey">
                  {{ st.hint }}
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
    <v-dialog v-model="returnDialog" max-width="600">
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

          <v-radio-group v-model="returnType" inline class="mb-4">
            <v-radio
              :label="$t('orders.return_full_order', 'إرجاع كامل الطلب')"
              value="full"
            ></v-radio>
            <v-radio
              :label="$t('orders.return_specific_items', 'إرجاع منتجات محددة')"
              value="partial"
            ></v-radio>
          </v-radio-group>

          <v-expand-transition>
            <div v-if="returnType === 'partial'" class="mb-4">
              <div class="text-subtitle-2 font-weight-bold mb-2">
                {{
                  $t(
                    "orders.select_return_items",
                    "اختر المنتجات المراد إرجاعها"
                  )
                }}
              </div>
              <v-list
                class="bg-grey-lighten-5 rounded-lg border"
                density="compact"
              >
                <v-list-item
                  v-for="item in selectedOrder.items"
                  :key="item.id"
                  class="px-2"
                >
                  <template v-slot:prepend>
                    <v-checkbox-btn
                      v-model="selectedReturnItems"
                      :value="item.id"
                      color="primary"
                    ></v-checkbox-btn>
                  </template>
                  <div class="d-flex align-center gap-3 w-100">
                    <v-avatar size="40" rounded class="border bg-white">
                      <img
                        :src="
                          item.image ||
                          (item.product ? item.product.thumbnail : '')
                        "
                        alt=""
                        style="width: 100%; height: 100%; object-fit: cover"
                      />
                    </v-avatar>
                    <div>
                      <div class="text-subtitle-2 line-clamp-1">
                        {{ item.name }}
                      </div>
                      <div class="text-caption text-primary font-weight-bold">
                        {{ item.price }} {{ $t("products.currency") }}
                      </div>
                    </div>
                  </div>
                </v-list-item>
              </v-list>
            </div>
          </v-expand-transition>

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
            :disabled="
              !returnReasonCode ||
              (returnType === 'partial' && selectedReturnItems.length === 0)
            "
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
import { AddInCart } from "../../store/Cart";

const cartStore = AddInCart();
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
const returnType = ref("full");
const selectedReturnItems = ref([]);
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

const orderStory = computed(() => [
  {
    title: t("orders.confirmed_status"),
    hint: t("orders.confirmed_hint"),
    value: "pending",
    icon: "mdi-check-decagram",
  },
  {
    title: t("orders.processing_status"),
    hint: t("orders.processing_hint"),
    value: "processing",
    icon: "mdi-cog-sync",
  },
  {
    title: t("orders.shipped_status"),
    hint: t("orders.shipped_hint"),
    value: "shipped",
    icon: "mdi-truck-fast",
  },
  {
    title: t("orders.delivered_status"),
    hint: t("orders.delivered_hint"),
    value: "completed",
    icon: "mdi-package-variant-closed-check",
  },
]);

const isStepCompleted = (statusValue) => {
  if (!selectedOrder.value) return false;
  const currentStatusEn = selectedOrder.value.status;
  const statusLevels = {
    pending: 1,
    processing: 2,
    shipped: 3,
    delivered: 4,
    completed: 4,
  };
  return statusLevels[statusValue] <= statusLevels[currentStatusEn];
};

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
      headerClass: "bg-green-lighten-5",
      headerBorder: "border-green-lighten-4",
      iconBg: "green-lighten-4",
      iconColor: "green-darken-3",
      icon: "mdi-check-decagram",
      textColor: "text-green-darken-4",
    };
  if (status === "shipped")
    return {
      text: t("orders.shipped"),
      headerClass: "bg-blue-lighten-5",
      headerBorder: "border-blue-lighten-4",
      iconBg: "blue-lighten-4",
      iconColor: "blue-darken-3",
      icon: "mdi-truck-fast",
      textColor: "text-blue-darken-4",
    };
  if (status === "processing")
    return {
      text: t("orders.processing"),
      headerClass: "bg-indigo-lighten-5",
      headerBorder: "border-indigo-lighten-4",
      iconBg: "indigo-lighten-4",
      iconColor: "indigo-darken-3",
      icon: "mdi-cog-sync",
      textColor: "text-indigo-darken-4",
    };
  if (status === "cancelled")
    return {
      text: t("orders.cancelled"),
      headerClass: "bg-red-lighten-5",
      headerBorder: "border-red-lighten-4",
      iconBg: "red-lighten-4",
      iconColor: "red-darken-3",
      icon: "mdi-cancel",
      textColor: "text-red-darken-4",
    };
  if (status === "returned")
    return {
      text: t("orders.returned"),
      headerClass: "bg-grey-lighten-3",
      headerBorder: "border-grey-lighten-2",
      iconBg: "grey-lighten-2",
      iconColor: "grey-darken-3",
      icon: "mdi-keyboard-return",
      textColor: "text-grey-darken-4",
    };
  if (status === "partially_returned")
    return {
      text: t("orders.partially_returned", "مرتجع جزئياً"),
      headerClass: "bg-orange-lighten-5",
      headerBorder: "border-orange-lighten-4",
      iconBg: "orange-lighten-4",
      iconColor: "orange-darken-3",
      icon: "mdi-keyboard-return",
      textColor: "text-orange-darken-4",
    };
  // default / pending
  return {
    text: t("orders.pending"),
    headerClass: "bg-primary-lighten-5",
    headerBorder: "border-primary-lighten-4",
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

const printInvoice = (order) => {
  const invoiceHtml = document.getElementById(`invoice-${order.id}`).innerHTML;
  const printWindow = window.open("", "_blank", "height=600,width=800");

  printWindow.document.write(`
    <html dir="${locale.value === "ar" ? "rtl" : "ltr"}">
      <head>
        <title>Invoice - ${order.order_number}</title>
        <style>
          @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');
          body { 
            font-family: 'Cairo', sans-serif; 
            padding: 40px; 
            color: #333;
            background: #fff;
          }
          .invoice-container { display: block !important; }
          .text-primary { color: #1976D2 !important; }
          .invoice-header { margin-bottom: 40px; }
          .d-flex { display: flex; }
          .justify-space-between { justify-content: space-between; }
          .align-center { align-items: center; }
          .mb-6 { margin-bottom: 24px; }
          .invoice-table { width: 100%; border-collapse: collapse; margin: 30px 0; border: 1px solid #eee; }
          .invoice-table th { background: #f8fafc; padding: 15px; border: 1px solid #eee; text-align: ${
            locale.value === "ar" ? "right" : "left"
          }; font-weight: bold; }
          .invoice-table td { padding: 15px; border: 1px solid #eee; text-align: ${
            locale.value === "ar" ? "right" : "left"
          }; }
          .text-center { text-align: center !important; }
          .text-left { text-align: ${
            locale.value === "ar" ? "left" : "right"
          } !important; }
          .invoice-footer { margin-top: 40px; }
          .summary-line { padding: 10px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; }
          .final-total { font-size: 24px; font-weight: 800; color: #1976D2 !important; border-bottom: none; }
          @media print {
            body { padding: 20px; }
          }
        </style>
      </head>
      <body>
        ${invoiceHtml}
        <script>
          window.onload = function() { 
            setTimeout(() => {
              window.print(); 
              window.close(); 
            }, 500);
          };
        </scrip' + 't>
      </body>
    </html>
  `);
  printWindow.document.close();
};

const buyAgain = async (item) => {
  actionLoading.value = "add_to_cart_" + item.id;
  try {
    // AddInCartItem expects a product object
    await cartStore.AddInCartItem({
      id: item.product_id,
      title: item.name,
      price: item.price,
      thumbnail: item.image,
      quantity: 1,
    });
    emitter.emit("showMessage", {
      text: t("orders.add_to_cart_success"),
      color: "success",
    });
  } catch (error) {
    console.error(error);
  } finally {
    actionLoading.value = null;
  }
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
  returnType.value = "full";
  selectedReturnItems.value = [];
  returnReasonCode.value = null;
  returnReasonDetails.value = "";
  returnDialog.value = true;
};

const submitReturnRequest = async () => {
  if (!selectedOrder.value || !returnReasonCode.value) return;
  if (returnType.value === "partial" && selectedReturnItems.value.length === 0)
    return;

  const order = selectedOrder.value;
  actionLoading.value = "return_" + order.id;
  try {
    const res = await axios.post(
      `/user/orders/${selectedOrder.value.id}/return`,
      {
        return_type: returnType.value,
        item_ids:
          returnType.value === "partial" ? selectedReturnItems.value : [],
        reason_code: returnReasonCode.value,
        reason: returnReasonDetails.value,
      }
    );
    order.return_request_status = "pending";
    order.return_type = returnType.value;
    returnDialog.value = false;
    emitter.emit("showMessage", {
      text: res.data.message || "تم تسجيل طلب الاسترجاع بنجاح!",
      color: "success",
    });
  } catch (err) {
    emitter.emit("showMessage", {
      text: err.response?.data?.message || "حدث خطأ في تقديم الطلب",
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

/**
 * Check if a specific item is targeted for return in this order.
 * Works for both partial returns (checks item_ids) and full returns.
 */
const isItemReturned = (order, item) => {
  if (
    !order.return_request_status ||
    order.return_request_status !== "approved"
  )
    return false;
  if (order.return_type === "full" || order.status === "returned") return true;
  if (order.return_type === "partial" && order.return_target_items) {
    const targetIds = Array.isArray(order.return_target_items)
      ? order.return_target_items
      : JSON.parse(order.return_target_items || "[]");
    return targetIds.includes(item.id);
  }
  return item.is_returned === true || item.is_returned === 1;
};

/**
 * Calculate total refund amount for an order based on returned items.
 */
const getRefundAmount = (order) => {
  if (!order.items) return 0;
  if (order.return_type === "full" || order.status === "returned") {
    return order.subtotal || order.total;
  }
  const targetIds = (() => {
    if (!order.return_target_items) return [];
    return Array.isArray(order.return_target_items)
      ? order.return_target_items
      : JSON.parse(order.return_target_items || "[]");
  })();
  return order.items
    .filter((item) => targetIds.includes(item.id) || item.is_returned)
    .reduce((sum, item) => sum + item.price * item.quantity, 0)
    .toFixed(2);
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

.pulse-icon {
  animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
  0% {
    transform: scale(0.95);
    opacity: 0.7;
  }
  50% {
    transform: scale(1.1);
    opacity: 1;
  }
  100% {
    transform: scale(0.95);
    opacity: 0.7;
  }
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

/* Print Styles */
@media print {
  .v-app-bar,
  .v-footer,
  .orders-page > .v-container > *:not(.order-card),
  .action-panel,
  .modern-tabs,
  .filter-card,
  .order-header v-btn,
  .order-items-list h4,
  .product-row v-btn,
  .no-print {
    display: none !important;
  }

  .orders-page {
    background-color: white !important;
    padding: 0 !important;
  }

  .order-card {
    box-shadow: none !important;
    border: none !important;
    transform: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .invoice-container {
    display: block !important;
    padding: 20px;
    background: white;
  }
}
</style>
