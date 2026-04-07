<template>
  <v-container
    fluid
    class="py-8 bg-grey-lighten-4"
    :dir="locale === 'ar' ? 'rtl' : 'ltr'"
    style="min-height: 100vh"
  >
    <v-row class="mb-4 align-center">
      <v-col cols="12" md="6">
        <h2 class="text-h4 font-weight-bold text-primary mb-1">
          {{ $t("tager.welcome", { name: "متجر التقنية" }) }}
        </h2>
        <p class="text-subtitle-1 text-grey-darken-1">
          {{ $t("tager.overview_subtitle") }}
        </p>
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-md-end gap-3">
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          rounded="lg"
          elevation="2"
        >
          {{ $t("tager.add_product") }}
        </v-btn>
        <v-btn
          color="success"
          variant="tonal"
          prepend-icon="mdi-cash-multiple"
          rounded="lg"
        >
          {{ $t("tager.withdraw_profits") }}
        </v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col
        v-for="(stat, index) in stats"
        :key="index"
        cols="12"
        sm="6"
        lg="3"
      >
        <v-card
          elevation="2"
          class="rounded-xl pa-4 d-flex align-center stat-card transition-swing"
        >
          <v-avatar
            :color="stat.color + '-lighten-4'"
            size="64"
            :class="locale === 'ar' ? 'mr-4' : 'ml-4'"
            class="rounded-lg"
          >
            <v-icon :color="stat.color" size="32">{{ stat.icon }}</v-icon>
          </v-avatar>
          <div>
            <div
              class="text-caption text-grey-darken-1 font-weight-medium mb-1"
            >
              {{ stat.title }}
            </div>
            <div class="text-h5 font-weight-black">{{ stat.value }}</div>
            <div
              :class="stat.trend > 0 ? 'text-success' : 'text-error'"
              class="text-caption mt-1 font-weight-bold d-flex align-center"
            >
              <v-icon size="small">{{
                stat.trend > 0 ? "mdi-trending-up" : "mdi-trending-down"
              }}</v-icon>
              <span :class="locale === 'ar' ? 'ml-1' : 'mr-1'">{{
                $t("tager.stats.vs_last_week", {
                  percentage: Math.abs(stat.trend),
                })
              }}</span>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-6">
      <v-col cols="12" lg="8">
        <v-card elevation="2" class="rounded-xl h-100">
          <v-toolbar color="transparent" class="px-4">
            <v-toolbar-title class="font-weight-bold text-h6">{{
              $t("tager.recent_orders")
            }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn color="primary" variant="text" size="small">{{
              $t("tager.view_all")
            }}</v-btn>
          </v-toolbar>
          <v-divider></v-divider>

          <v-table hover>
            <thead>
              <tr>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.order_no") }}
                </th>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.customer") }}
                </th>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.date") }}
                </th>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.total") }}
                </th>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.status") }}
                </th>
                <th
                  :class="locale === 'ar' ? 'text-right' : 'text-left'"
                  class="font-weight-bold"
                >
                  {{ $t("tager.action") }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in recentOrders" :key="order.id">
                <td class="font-weight-bold text-primary">#{{ order.id }}</td>
                <td>{{ order.customer }}</td>
                <td class="text-grey-darken-1 text-body-2">{{ order.date }}</td>
                <td class="font-weight-bold">{{ order.total }} ج.م</td>
                <td>
                  <v-chip
                    :color="getStatusColor(order.status)"
                    size="small"
                    variant="flat"
                    class="font-weight-bold"
                  >
                    {{ getStatusText(order.status) }}
                  </v-chip>
                </td>
                <td>
                  <v-btn
                    icon="mdi-eye-outline"
                    size="small"
                    variant="text"
                    color="info"
                  ></v-btn>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>

      <v-col cols="12" lg="4">
        <v-card elevation="2" class="rounded-xl h-100">
          <v-toolbar color="transparent" class="px-4">
            <v-toolbar-title class="font-weight-bold text-h6">{{
              $t("tager.top_selling")
            }}</v-toolbar-title>
          </v-toolbar>
          <v-divider></v-divider>

          <v-list lines="two" class="pa-0">
            <template v-for="(product, index) in topProducts" :key="product.id">
              <v-list-item class="pa-4">
                <template v-slot:prepend>
                  <v-avatar
                    rounded="lg"
                    size="60"
                    :class="locale === 'ar' ? 'mr-3' : 'ml-3'"
                    class="border"
                  >
                    <v-img :src="product.image" cover></v-img>
                  </v-avatar>
                </template>
                <v-list-item-title class="font-weight-bold mb-1">{{
                  product.name
                }}</v-list-item-title>
                <v-list-item-subtitle class="text-success font-weight-bold"
                  >{{ product.price }} ج.م</v-list-item-subtitle
                >
                <template v-slot:append>
                  <div class="text-center">
                    <div class="text-caption text-grey">
                      {{ $t("tager.sales_count") }}
                    </div>
                    <div class="font-weight-bold">{{ product.sales }}</div>
                  </div>
                </template>
              </v-list-item>
              <v-divider
                v-if="index < topProducts.length - 1"
                inset
              ></v-divider>
            </template>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { locale, t } = useI18n();

// بيانات الإحصائيات
const stats = computed(() => [
  {
    title: t("tager.stats.total_sales"),
    value: "45,230 " + t("products.currency"),
    icon: "mdi-wallet-outline",
    color: "primary",
    trend: 12.5,
  },
  {
    title: t("tager.stats.new_orders"),
    value: "34",
    icon: "mdi-package-variant-closed",
    color: "info",
    trend: 5.2,
  },
  {
    title: t("tager.stats.active_products"),
    value: "128",
    icon: "mdi-storefront-outline",
    color: "warning",
    trend: -2.4,
  },
  {
    title: t("tager.stats.store_visits"),
    value: "3,492",
    icon: "mdi-eye-outline",
    color: "success",
    trend: 18.1,
  },
]);

// بيانات الطلبات الأخيرة
const recentOrders = ref([
  {
    id: "10045",
    customer: "أحمد محمود",
    date: "اليوم، 10:30 ص",
    total: "1,250",
    status: "قيد التجهيز",
  },
  {
    id: "10044",
    customer: "سارة خالد",
    date: "اليوم، 09:15 ص",
    total: "450",
    status: "بانتظار التأكيد",
  },
  {
    id: "10043",
    customer: "محمد علي",
    date: "الأمس، 08:00 م",
    total: "3,200",
    status: "تم الشحن",
  },
  {
    id: "10042",
    customer: "فاطمة حسن",
    date: "الأمس، 05:45 م",
    total: "850",
    status: "مكتمل",
  },
  {
    id: "10041",
    customer: "عمر ياسين",
    date: "الأمس، 02:20 م",
    total: "2,100",
    status: "ملغي",
  },
]);

// بيانات المنتجات الأكثر مبيعاً
const topProducts = ref([
  {
    id: 1,
    name: "سماعة أبل إيربودز برو",
    price: "6,500",
    sales: 124,
    image: "https://cdn.vuetifyjs.com/images/cards/halcyon.png",
  },
  {
    id: 2,
    name: "ساعة ذكية رياضية",
    price: "1,200",
    sales: 89,
    image: "https://cdn.vuetifyjs.com/images/cards/foster.jpg",
  },
  {
    id: 3,
    name: "باور بانك 20000mAh",
    price: "750",
    sales: 65,
    image: "https://cdn.vuetifyjs.com/images/cards/docks.jpg",
  },
]);

const getStatusText = (status) => {
  const texts = {
    "بانتظار التأكيد": t("tager.order_status.pending_confirmation"),
    "قيد التجهيز": t("tager.order_status.processing"),
    "تم الشحن": t("tager.order_status.shipped"),
    مكتمل: t("tager.order_status.completed"),
    ملغي: t("tager.order_status.cancelled"),
  };
  return texts[status] || status;
};

// دالة لتحديد لون حالة الطلب
const getStatusColor = (status) => {
  const colors = {
    "بانتظار التأكيد": "warning",
    "قيد التجهيز": "info",
    "تم الشحن": "primary",
    مكتمل: "success",
    ملغي: "error",
  };
  return colors[status] || "grey";
};
</script>

<style scoped>
.gap-3 {
  gap: 12px;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
}
</style>
