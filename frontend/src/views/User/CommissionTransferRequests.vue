<template>
  <v-app theme="light">
    <v-locale-provider rtl>
      <v-main class="bg-grey-lighten-5">
        <v-container class="py-10">
          <div class="mb-6">
            <h1 class="text-h4 font-weight-bold text-primary-darken-3">
              طلب تحويل رصيد
            </h1>
            <p class="text-grey-darken-1">
              حدد الطلبات التي تريد سحب أرباحها من الجدول أدناه
            </p>
          </div>

          <v-card class="rounded-xl border-0 mb-8" elevation="2">
            <v-table hover density="comfortable">
              <thead class="bg-primary-lighten-5">
                <tr>
                  <th
                    class="font-weight-bold text-primary-darken-4 text-center"
                    width="50"
                  >
                    <v-checkbox-btn
                      v-model="selectAll"
                      @update:model-value="toggleAll"
                    ></v-checkbox-btn>
                  </th>
                  <th class="font-weight-bold text-primary-darken-4 text-start">
                    رقم الأوردر
                  </th>
                  <th
                    class="font-weight-bold text-primary-darken-4 text-center"
                  >
                    تاريخ التحصيل
                  </th>
                  <th
                    class="font-weight-bold text-primary-darken-4 text-center"
                  >
                    الحالة
                  </th>
                  <th
                    class="font-weight-bold text-primary-darken-4 text-center"
                  >
                    العمولة
                  </th>
                </tr>
              </thead>
              <tbody>
                <template v-if="loading">
                  <tr v-for="i in 5" :key="`skel-com-${i}`">
                    <td class="text-center">
                      <v-skeleton-loader type="list-item" />
                    </td>
                    <td><v-skeleton-loader type="text" width="100" /></td>
                    <td class="text-center">
                      <v-skeleton-loader
                        type="text"
                        width="120"
                        class="mx-auto"
                      />
                    </td>
                    <td class="text-center">
                      <v-skeleton-loader type="chip" class="mx-auto" />
                    </td>
                    <td class="text-center">
                      <v-skeleton-loader
                        type="text"
                        width="80"
                        class="mx-auto"
                      />
                    </td>
                  </tr>
                </template>
                <template v-else>
                  <tr v-for="item in orders" :key="item.id">
                    <td class="text-center">
                      <v-checkbox-btn
                        v-model="selectedItems"
                        :value="item.id"
                        color="primary"
                      ></v-checkbox-btn>
                    </td>
                    <td class="font-weight-medium">{{ item.orderNo }}</td>
                    <td class="text-center text-grey-darken-1">
                      {{ item.date }}
                    </td>
                    <td class="text-center">
                      <span
                        class="text-success font-weight-bold text-caption border px-2 py-1 rounded"
                      >
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="text-center font-weight-bold text-primary">
                      {{ item.commission }} ج.م
                    </td>
                  </tr>
                </template>
              </tbody>
            </v-table>
          </v-card>

          <v-card class="mb-8 rounded-xl bg-white border pa-6" flat>
            <div
              class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
              <div>
                <div class="text-caption text-grey-darken-1 mb-1">
                  إجمالي المبلغ المحدد
                </div>
                <div class="text-h4 font-weight-bold text-primary-darken-3">
                  {{ totalAmount }} <span class="text-h6 text-grey">ج.م</span>
                </div>
              </div>

              <v-btn
                color="primary"
                size="large"
                class="px-8 rounded-lg font-weight-bold"
                elevation="3"
                prepend-icon="mdi-cash-check"
                :disabled="selectedItems.length === 0"
              >
                تأكيد التحويل
              </v-btn>
            </div>
          </v-card>

          <div class="mt-8">
            <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-4">
              سجل التحويلات السابق
            </h3>
            <v-card class="rounded-xl border-0" elevation="1">
              <v-table density="compact">
                <thead class="bg-grey-lighten-4">
                  <tr>
                    <th class="text-start">رقم العملية</th>
                    <th class="text-center">المبلغ</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-end">التاريخ</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-if="loading">
                    <tr v-for="i in 3" :key="`skel-hist-${i}`">
                      <td><v-skeleton-loader type="text" width="60" /></td>
                      <td class="text-center">
                        <v-skeleton-loader
                          type="text"
                          width="80"
                          class="mx-auto"
                        />
                      </td>
                      <td class="text-center">
                        <v-skeleton-loader type="chip" class="mx-auto" />
                      </td>
                      <td class="text-end">
                        <v-skeleton-loader
                          type="text"
                          width="100"
                          class="ms-auto"
                        />
                      </td>
                    </tr>
                  </template>
                  <template v-else>
                    <tr v-for="history in historyList" :key="history.id">
                      <td>#{{ history.id }}</td>
                      <td class="text-center font-weight-bold">
                        {{ history.amount }} ج.م
                      </td>
                      <td class="text-center">
                        <v-chip
                          size="x-small"
                          :color="history.status === 'تم' ? 'green' : 'orange'"
                        >
                          {{ history.status }}
                        </v-chip>
                      </td>
                      <td class="text-end text-grey">{{ history.date }}</td>
                    </tr>
                  </template>
                </tbody>
              </v-table>
            </v-card>
          </div>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

// البيانات
const orders = ref([]);
const historyList = ref([]);
const loading = ref(true);

// محاكاة جلب البيانات
const fetchData = async () => {
  loading.value = true;
  setTimeout(() => {
    orders.value = [
      {
        id: 1,
        orderNo: "N857875",
        status: "مكتمل",
        commission: 500,
        date: "2023-10-01",
      },
      {
        id: 2,
        orderNo: "N857876",
        status: "مكتمل",
        commission: 150,
        date: "2023-10-02",
      },
      {
        id: 3,
        orderNo: "N857877",
        status: "مكتمل",
        commission: 300,
        date: "2023-10-03",
      },
      {
        id: 4,
        orderNo: "N857880",
        status: "مكتمل",
        commission: 500,
        date: "2023-10-05",
      },
    ];
    historyList.value = [
      { id: 101, amount: 650, status: "تم", date: "02/02/2022" },
      { id: 102, amount: 120, status: "معلق", date: "05/02/2022" },
    ];
    loading.value = false;
  }, 1000);
};

onMounted(() => {
  fetchData();
});

// المتغيرات الخاصة بالتحديد
const selectedItems = ref([]); // يحمل IDs العناصر المختارة
const selectAll = ref(false);

// دالة تحديد الكل
const toggleAll = () => {
  if (selectAll.value) {
    selectedItems.value = orders.value.map((item) => item.id);
  } else {
    selectedItems.value = [];
  }
};

// حساب المجموع تلقائياً
const totalAmount = computed(() => {
  return orders.value
    .filter((item) => selectedItems.value.includes(item.id))
    .reduce((sum, item) => sum + item.commission, 0);
});
</script>
