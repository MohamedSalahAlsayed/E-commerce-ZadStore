<template>
  <v-app theme="light">
    <v-locale-provider rtl>
      <v-main class="bg-grey-lighten-5">
        <v-container class="py-10">
          <div class="mb-6 d-flex align-center justify-space-between">
            <div>
              <h1 class="text-h4 font-weight-bold text-primary-darken-3">
                إضافة مودريتور
              </h1>
              <p class="text-grey-darken-1 mt-1">
                إضافة مودريتور جدد والتحكم في صلاحيات الفريق الحالي
              </p>
            </div>
            <v-btn variant="tonal" color="primary" prepend-icon="mdi-download"
              >تصدير القائمة</v-btn
            >
          </div>

          <v-expansion-panels class="mb-10 rounded-xl" elevation="2">
            <v-expansion-panel>
              <v-expansion-panel-title class="py-4">
                <div class="d-flex align-center">
                  <v-icon color="primary" class="me-3">mdi-account-plus</v-icon>
                  <span class="text-h6 font-weight-bold text-grey-darken-3"
                    >تسجيل مودريتور جديد</span
                  >
                </div>
              </v-expansion-panel-title>

              <v-expansion-panel-text class="pa-4">
                <v-row>
                  <v-col cols="12" md="12">
                    <v-row>
                      <v-col cols="12" md="6"
                        ><v-text-field
                          label="الاسم بالكامل"
                          variant="outlined"
                          density="comfortable"
                          color="primary"
                        ></v-text-field
                      ></v-col>
                      <v-col cols="12" md="6"
                        ><v-text-field
                          label="رقم الهاتف"
                          variant="outlined"
                          density="comfortable"
                          color="primary"
                        ></v-text-field
                      ></v-col>
                      <v-col cols="12"
                        ><v-text-field
                          label="البريد الإلكتروني"
                          variant="outlined"
                          density="comfortable"
                          color="primary"
                        ></v-text-field
                      ></v-col>
                      <v-col cols="12" md="6"
                        ><v-text-field
                          label="كلمة المرور"
                          type="password"
                          variant="outlined"
                          density="comfortable"
                          color="primary"
                        ></v-text-field
                      ></v-col>
                      <v-col cols="12" md="6"
                        ><v-select
                          label="الدور الوظيفي"
                          :items="['مودريتور', 'مودريتور', 'مودريتور']"
                          variant="outlined"
                          density="comfortable"
                          color="primary"
                        ></v-select
                      ></v-col>
                    </v-row>
                    <div class="mt-4">
                      <v-btn color="primary" size="large" class="px-6"
                        >حفظ البيانات</v-btn
                      >
                    </div>
                  </v-col>
                  <!-- <v-col cols="12" md="4">
                    <div
                      class="bg-primary-lighten-5 rounded-lg pa-4 h-100 border-indigo-dashed"
                    >
                      <div
                        class="text-subtitle-1 font-weight-bold text-primary mb-2"
                      >
                        صلاحيات سريعة
                      </div>
                      <v-checkbox
                        label="الوصول للطلبات"
                        color="primary"
                        hide-details
                        density="compact"
                      ></v-checkbox>
                      <v-checkbox
                        label="الوصول للمنتجات"
                        color="primary"
                        hide-details
                        density="compact"
                      ></v-checkbox>
                      <v-checkbox
                        label="الوصول للتقارير المالية"
                        color="primary"
                        hide-details
                        density="compact"
                      ></v-checkbox>
                      <v-checkbox
                        label="تعديل الإعدادات"
                        color="primary"
                        hide-details
                        density="compact"
                      ></v-checkbox>
                    </div>
                  </v-col> -->
                </v-row>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>

          <v-card class="rounded-xl border-0" elevation="2">
            <v-card-title
              class="pa-4 d-flex align-center flex-wrap gap-4 bg-white border-bottom"
            >
              <div class="d-flex align-center">
                <v-icon color="primary-darken-1" class="me-2"
                  >mdi-account-group</v-icon
                >
                <span class="text-h6 font-weight-bold text-grey-darken-3"
                  >فريق العمل الحالي</span
                >
                <v-chip
                  class="ms-3 font-weight-bold"
                  color="primary"
                  size="small"
                  variant="flat"
                >
                  {{ filteredModerators.length }} موظف
                </v-chip>
              </div>
              <v-spacer></v-spacer>
              <div style="width: 300px">
                <v-text-field
                  v-model="search"
                  density="compact"
                  variant="solo-filled"
                  label="بحث سريع..."
                  append-inner-icon="mdi-magnify"
                  single-line
                  hide-details
                  bg-color="grey-lighten-4"
                ></v-text-field>
              </div>
            </v-card-title>

            <v-table hover density="comfortable">
              <thead class="bg-primary-lighten-5">
                <tr>
                  <th class="text-start font-weight-bold text-primary-darken-4">
                    الموظف
                  </th>
                  <th class="text-start font-weight-bold text-primary-darken-4">
                    الدور الوظيفي
                  </th>
                  <th
                    class="text-center font-weight-bold text-primary-darken-4"
                  >
                    تاريخ التعيين
                  </th>
                  <th class="text-start font-weight-bold text-primary-darken-4">
                    حالة الحساب
                  </th>
                  <th class="text-end font-weight-bold text-primary-darken-4">
                    إجراءات
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in filteredModerators" :key="item.id">
                  <td class="py-3">
                    <div class="d-flex align-center">
                      <v-avatar
                        color="primary-lighten-4"
                        class="me-3 border"
                        size="40"
                      >
                        <span class="text-primary-darken-4 font-weight-bold">{{
                          item.initials
                        }}</span>
                      </v-avatar>
                      <div>
                        <div class="font-weight-bold text-subtitle-2">
                          {{ item.name }}
                        </div>
                        <div class="text-caption text-grey-darken-1">
                          {{ item.email }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <td>
                    <v-chip
                      :color="getRoleColor(item.role)"
                      size="small"
                      class="font-weight-bold"
                      variant="tonal"
                    >
                      {{ item.role }}
                    </v-chip>
                  </td>

                  <td class="text-center text-grey-darken-2">
                    {{ item.date }}
                  </td>

                  <td>
                    <div class="d-flex align-center">
                      <v-switch
                        v-model="item.isActive"
                        color="success"
                        hide-details
                        density="compact"
                        inset
                      ></v-switch>
                      <span
                        class="ms-2 text-caption font-weight-bold"
                        :class="item.isActive ? 'text-success' : 'text-grey'"
                      >
                        {{ item.isActive ? "نشط" : "محظور" }}
                      </span>
                    </div>
                  </td>

                  <td class="text-end">
                    <v-btn
                      icon="mdi-pencil-outline"
                      size="small"
                      variant="text"
                      color="blue-grey"
                      class="me-2"
                    ></v-btn>
                    <v-btn
                      icon="mdi-delete-outline"
                      size="small"
                      variant="text"
                      color="error"
                    ></v-btn>
                  </td>
                </tr>

                <tr v-if="filteredModerators.length === 0">
                  <td colspan="5" class="text-center py-6 text-grey">
                    لا يوجد نتائج مطابقة للبحث
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-card>
        </v-container>
      </v-main>
    </v-locale-provider>
  </v-app>
</template>

<script setup>
import { ref, computed } from "vue";

const search = ref("");

// بيانات وهمية للموظفين
const moderators = ref([
  {
    id: 1,
    name: "أحمد محمود",
    initials: "AM",
    email: "ahmed@company.com",
    role: "Admin",
    date: "2023-01-15",
    isActive: true,
  },
  {
    id: 2,
    name: "سارة علي",
    initials: "SA",
    email: "sara.ali@company.com",
    role: "Moderator",
    date: "2023-05-20",
    isActive: true,
  },
  {
    id: 3,
    name: "كريم حسن",
    initials: "KH",
    email: "kareem@company.com",
    role: "Support",
    date: "2023-08-10",
    isActive: false,
  },
  {
    id: 4,
    name: "منى السيد",
    initials: "MS",
    email: "mona@company.com",
    role: "Accountant",
    date: "2023-11-01",
    isActive: true,
  },
]);

// تصفية البيانات للبحث (لأننا نستخدم جدول عادي)
const filteredModerators = computed(() => {
  if (!search.value) return moderators.value;
  const q = search.value.toLowerCase();
  return moderators.value.filter(
    (item) =>
      item.name.toLowerCase().includes(q) || item.role.toLowerCase().includes(q)
  );
});

// دالة لتلوين الدور الوظيفي
const getRoleColor = (role) => {
  switch (role) {
    case "Admin":
      return "purple-darken-1";
    case "Moderator":
      return "indigo";
    case "Support":
      return "teal";
    case "Accountant":
      return "amber-darken-3";
    default:
      return "grey";
  }
};
</script>

<style scoped>
.border-indigo-dashed {
  border: 1px dashed #3949ab;
}
.border-bottom {
  border-bottom: 1px solid #e0e0e0;
}
/* تنسيق رؤوس الجدول */
th {
  white-space: nowrap;
}
</style>
