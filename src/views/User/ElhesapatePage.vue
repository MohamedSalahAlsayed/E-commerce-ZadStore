<template>
  <v-app theme="light">
    <v-locale-provider rtl>
      <v-main class="bg-grey-lighten-5">
        <v-container class="py-10">
          <div class="mb-6 d-flex align-center justify-space-between">
            <div>
              <h1 class="text-h4 font-weight-bold text-primary-darken-3">
                الحسابات المالية
              </h1>
              <p class="text-grey-darken-1 mt-1">
                إدارة طرق الدفع، وسجل العمليات، والعمولات
              </p>
            </div>
            <v-chip
              color="primary"
              variant="flat"
              size="large"
              class="font-weight-bold"
            >
              الرصيد الحالي: 1,250 ج.م
            </v-chip>
          </div>

          <v-card class="rounded-xl border-0 overflow-hidden" elevation="2">
            <v-tabs
              v-model="tab"
              color="primary-darken-2"
              bg-color="white"
              align-tabs="start"
              class="border-bottom"
              height="60"
            >
              <v-tab value="methods" class="font-weight-bold text-body-1">
                <v-icon start>mdi-card-account-details-outline</v-icon> طريقة
                الدفع
              </v-tab>
              <v-tab value="history" class="font-weight-bold text-body-1">
                <v-icon start>mdi-history</v-icon> سجل المدفوعات
              </v-tab>
              <v-tab value="due" class="font-weight-bold text-body-1">
                <v-icon start>mdi-cash-check</v-icon> مستحقة الدفع
              </v-tab>
              <v-tab value="pending" class="font-weight-bold text-body-1">
                <v-icon start>mdi-timer-sand</v-icon> تحت التحصيل
              </v-tab>
            </v-tabs>

            <v-card-text class="pa-0 bg-white" style="min-height: 400px">
              <v-window v-model="tab">
                <v-window-item value="methods">
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-card
                          variant="outlined"
                          class="h-100 rounded-lg border-indigo"
                          :class="{
                            'bg-primary-lighten-5': paymentType === 'cash',
                          }"
                        >
                          <v-card-title class="d-flex align-center">
                            <v-radio-group
                              v-model="paymentType"
                              hide-details
                              class="mt-0"
                            >
                              <v-radio value="cash" color="primary-darken-3">
                                <template v-slot:label>
                                  <span
                                    class="text-h6 font-weight-bold text-primary-darken-3 opacity-100"
                                    >محفظة كاش</span
                                  >
                                </template>
                              </v-radio>
                            </v-radio-group>
                            <v-spacer></v-spacer>
                            <v-icon color="primary" size="large"
                              >mdi-cellphone-nfc</v-icon
                            >
                          </v-card-title>
                          <v-card-text class="pt-2">
                            <p class="text-caption text-grey-darken-1 mb-4">
                              استقبال الأرباح عبر فودافون كاش، اتصالات كاش، أو
                              انستا باي.
                            </p>
                            <v-text-field
                              label="رقم المحفظة"
                              placeholder="01xxxxxxxxx"
                              variant="solo-filled"
                              prepend-inner-icon="mdi-phone"
                              :disabled="paymentType !== 'cash'"
                              v-model="cashNumber"
                            ></v-text-field>
                          </v-card-text>
                        </v-card>
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-card
                          variant="outlined"
                          class="h-100 rounded-lg border-grey"
                          :class="{
                            'bg-primary-lighten-5': paymentType === 'visa',
                          }"
                        >
                          <v-card-title class="d-flex align-center">
                            <v-radio-group
                              v-model="paymentType"
                              hide-details
                              class="mt-0"
                            >
                              <v-radio value="visa" color="primary-darken-3">
                                <template v-slot:label>
                                  <span
                                    class="text-h6 font-weight-bold text-primary-darken-3"
                                    >حساب بنكي / فيزا</span
                                  >
                                </template>
                              </v-radio>
                            </v-radio-group>
                            <v-spacer></v-spacer>
                            <v-icon color="blue-darken-4" size="large"
                              >mdi-credit-card-outline</v-icon
                            >
                          </v-card-title>
                          <v-card-text class="pt-2">
                            <p class="text-caption text-grey-darken-1 mb-4">
                              تحويل بنكي مباشر على رقم الحساب (IBAN) أو رقم
                              الكارت.
                            </p>
                            <v-text-field
                              label="اسم صاحب الحساب"
                              variant="solo-filled"
                              density="compact"
                              class="mb-2"
                              :disabled="paymentType !== 'visa'"
                            ></v-text-field>
                            <v-text-field
                              label="رقم الحساب / IBAN"
                              placeholder="EGxxxxxxxxxxxx"
                              variant="solo-filled"
                              prepend-inner-icon="mdi-bank"
                              :disabled="paymentType !== 'visa'"
                            ></v-text-field>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>

                    <div class="d-flex justify-end mt-6">
                      <v-btn
                        size="large"
                        color="primary"
                        elevation="2"
                        prepend-icon="mdi-content-save-check"
                      >
                        حفظ بيانات الدفع
                      </v-btn>
                    </div>
                  </v-container>
                </v-window-item>

                <v-window-item value="history">
                  <v-table hover>
                    <thead class="bg-grey-lighten-4">
                      <tr>
                        <th class="text-start font-weight-bold">كود العملية</th>
                        <th class="text-start font-weight-bold">طريقة الدفع</th>
                        <th class="text-center font-weight-bold">المبلغ</th>
                        <th class="text-center font-weight-bold">التاريخ</th>
                        <th class="text-center font-weight-bold">الإيصال</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="pay in paymentHistory" :key="pay.id">
                        <td class="text-grey-darken-2">#{{ pay.code }}</td>
                        <td>
                          <v-chip
                            size="small"
                            :color="
                              pay.method === 'visa'
                                ? 'blue-darken-1'
                                : 'orange-darken-2'
                            "
                            variant="tonal"
                            class="font-weight-bold"
                          >
                            <v-icon start size="small">{{
                              pay.method === "visa"
                                ? "mdi-bank"
                                : "mdi-cellphone"
                            }}</v-icon>
                            {{
                              pay.method === "visa" ? "حساب بنكي" : "محفظة كاش"
                            }}
                          </v-chip>
                        </td>
                        <td class="text-center font-weight-bold text-success">
                          {{ pay.amount }} ج.م
                        </td>
                        <td class="text-center text-grey">{{ pay.date }}</td>
                        <td class="text-center">
                          <v-btn
                            icon="mdi-file-eye-outline"
                            variant="text"
                            color="primary"
                            density="comfortable"
                          ></v-btn>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-window-item>

                <v-window-item value="due">
                  <v-alert
                    type="info"
                    variant="tonal"
                    class="mb-0 rounded-0 border-b-sm border-indigo"
                    density="compact"
                  >
                    هذه الأوردرات تم تسليمها وانتهت فترة الاسترجاع، يمكنك طلب
                    سحب أرباحها الآن.
                  </v-alert>
                  <v-table>
                    <thead>
                      <tr>
                        <th class="text-start">رقم الأوردر</th>
                        <th class="text-start">تاريخ الانتهاء</th>
                        <th class="text-center">حالة الأوردر</th>
                        <th class="text-end text-primary font-weight-bold">
                          قيمة العمولة
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="order in dueOrders" :key="order.id">
                        <td>{{ order.orderNo }}</td>
                        <td class="text-grey-darken-1">{{ order.date }}</td>
                        <td class="text-center">
                          <v-chip color="success" size="small" variant="flat"
                            >مكتمل</v-chip
                          >
                        </td>
                        <td
                          class="text-end font-weight-bold text-h6 text-primary-darken-2"
                        >
                          {{ order.commission }} ج.م
                        </td>
                      </tr>
                      <tr class="bg-primary-lighten-5">
                        <td
                          colspan="3"
                          class="text-start font-weight-bold text-primary-darken-4"
                        >
                          إجمالي المستحق للدفع
                        </td>
                        <td
                          class="text-end font-weight-bold text-h6 text-primary-darken-4"
                        >
                          1,250 ج.م
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                  <div class="pa-4 text-center">
                    <v-btn block color="primary" size="large" elevation="2"
                      >طلب سحب الرصيد (1,250 ج.م)</v-btn
                    >
                  </div>
                </v-window-item>

                <v-window-item value="pending">
                  <v-table>
                    <thead>
                      <tr>
                        <th class="text-start">رقم الأوردر</th>
                        <th class="text-start">تاريخ الطلب</th>
                        <th class="text-center">الحالة الحالية</th>
                        <th class="text-end">العمولة المتوقعة</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="order in pendingOrders" :key="order.id">
                        <td class="text-grey-darken-3">{{ order.orderNo }}</td>
                        <td class="text-grey">{{ order.date }}</td>
                        <td class="text-center">
                          <v-chip
                            v-if="order.status === 'shipping'"
                            color="info"
                            size="small"
                            prepend-icon="mdi-truck-fast"
                          >
                            خرج للشحن
                          </v-chip>
                          <v-chip
                            v-else-if="order.status === 'hold'"
                            color="orange"
                            size="small"
                            prepend-icon="mdi-clock-outline"
                          >
                            فترة الضمان
                          </v-chip>
                        </td>
                        <td
                          class="text-end font-weight-bold text-grey-darken-1"
                        >
                          {{ order.commission }} ج.م
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-window-item>
              </v-window>
            </v-card-text>
          </v-card>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref } from "vue";

const tab = ref("methods"); // التبويب الافتراضي
const paymentType = ref("cash"); // للتبديل بين الكاش والفيزا
const cashNumber = ref("01012345678"); // مثال لرقم محفوظ

// 1. بيانات سجل المدفوعات
const paymentHistory = ref([
  { id: 1, code: "TRX-1001", method: "cash", amount: 500, date: "2023-10-01" },
  { id: 2, code: "TRX-1005", method: "visa", amount: 1200, date: "2023-10-15" },
  { id: 3, code: "TRX-1020", method: "cash", amount: 350, date: "2023-11-01" },
]);

// 2. بيانات العمولات المستحقة (انتهت وتستطيع سحبها)
const dueOrders = ref([
  { id: 1, orderNo: "#ORD-7788", date: "2023-11-05", commission: 250 },
  { id: 2, orderNo: "#ORD-7790", date: "2023-11-06", commission: 500 },
  { id: 3, orderNo: "#ORD-7801", date: "2023-11-07", commission: 500 },
]);

// 3. بيانات تحت التحصيل (لسه في الشحن أو فترة الضمان)
const pendingOrders = ref([
  {
    id: 10,
    orderNo: "#ORD-8800",
    date: "2023-11-10",
    status: "shipping",
    commission: 150,
  },
  {
    id: 11,
    orderNo: "#ORD-8802",
    date: "2023-11-11",
    status: "hold",
    commission: 300,
  }, // hold = فترة الاسترجاع 14 يوم مثلا
  {
    id: 12,
    orderNo: "#ORD-8805",
    date: "2023-11-12",
    status: "shipping",
    commission: 200,
  },
]);
</script>

<style scoped>
td {
  text-align: justify;
}
.border-indigo {
  border-color: #3949ab !important;
  border-width: 2px;
}
.border-grey {
  border-color: #e0e0e0 !important;
}
.border-bottom {
  border-bottom: 1px solid #e0e0e0;
}
/* تأثير بسيط عند تفعيل البطاقة */
.v-card--variant-outlined {
  transition: all 0.3s ease;
}
</style>
