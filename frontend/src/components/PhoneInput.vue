<template>
  <v-text-field
    v-model="phoneNumber"
    :label="label"
    :placeholder="placeholder"
    :rules="rules"
    variant="outlined"
    density="comfortable"
    color="primary"
    class="phone-input"
    type="tel"
  >
    <template v-slot:prepend-inner>
      <v-menu
        v-model="menu"
        :close-on-content-click="false"
        location="bottom start"
      >
        <template v-slot:activator="{ props }">
          <div
            v-bind="props"
            class="country-selector d-flex align-center cursor-pointer me-2"
          >
            <span class="text-body-2 font-weight-bold me-1">{{
              selectedCountry.flag
            }}</span>
            <span class="text-body-2 me-1">{{ selectedCountry.dialCode }}</span>
            <v-icon size="small">mdi-chevron-down</v-icon>
            <v-divider vertical class="mx-2 my-2"></v-divider>
          </div>
        </template>

        <v-list density="compact" max-height="300" class="rounded-lg">
          <v-list-item
            v-for="country in countries"
            :key="country.code"
            @click="selectCountry(country)"
            :active="selectedCountry.code === country.code"
          >
            <template v-slot:prepend>
              <span class="me-3">{{ country.flag }}</span>
            </template>
            <v-list-item-title
              >{{ country.name }} ({{ country.dialCode }})</v-list-item-title
            >
          </v-list-item>
        </v-list>
      </v-menu>
    </template>
  </v-text-field>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
  label: {
    type: String,
    default: "رقم الهاتف",
  },
  placeholder: {
    type: String,
    default: "0123456789",
  },
  rules: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:modelValue"]);

const countries = [
  { name: "مصر", code: "EG", dialCode: "+20", flag: "🇪🇬" },
  { name: "السعودية", code: "SA", dialCode: "+966", flag: "🇸🇦" },
  { name: "الإمارات", code: "AE", dialCode: "+971", flag: "🇦🇪" },
  { name: "الكويت", code: "KW", dialCode: "+965", flag: "🇰🇼" },
  { name: "قطر", code: "QA", dialCode: "+974", flag: "🇶🇦" },
  { name: "عمان", code: "OM", dialCode: "+968", flag: "🇴🇲" },
  { name: "البحرين", code: "BH", dialCode: "+973", flag: "🇧🇭" },
  { name: "الأردن", code: "JO", dialCode: "+962", flag: "🇯🇴" },
  { name: "فلسطين", code: "PS", dialCode: "+970", flag: "🇵🇸" },
  { name: "لبنان", code: "LB", dialCode: "+961", flag: "🇱🇧" },
  { name: "سوريا", code: "SY", dialCode: "+963", flag: "🇸🇾" },
  { name: "العراق", code: "IQ", dialCode: "+964", flag: "🇮🇶" },
  { name: "المغرب", code: "MA", dialCode: "+212", flag: "🇲🇦" },
  { name: "تونس", code: "TN", dialCode: "+216", flag: "🇹🇳" },
  { name: "الجزائر", code: "DZ", dialCode: "+213", flag: "🇩🇿" },
  { name: "ليبيا", code: "LY", dialCode: "+218", flag: "🇱🇾" },
  { name: "السودان", code: "SD", dialCode: "+249", flag: "🇸🇩" },
  { name: "اليمن", code: "YE", dialCode: "+967", flag: "🇾🇪" },
];

const menu = ref(false);
const selectedCountry = ref(countries[0]);

// Parse initial value if it contains a dial code
const getInitialValue = () => {
  if (!props.modelValue) return "";

  for (const country of countries) {
    if (props.modelValue.startsWith(country.dialCode)) {
      selectedCountry.value = country;
      return props.modelValue.slice(country.dialCode.length);
    }
  }
  return props.modelValue;
};

const phoneNumber = ref(getInitialValue());

const selectCountry = (country) => {
  selectedCountry.value = country;
  menu.value = false;
  updateValue();
};

const updateValue = () => {
  // Return the full number with dial code
  const fullNumber = selectedCountry.value.dialCode + phoneNumber.value;
  emit("update:modelValue", fullNumber);
};

watch(phoneNumber, updateValue);

// Sync with external modelValue if it's cleared or changed externally
watch(
  () => props.modelValue,
  (newVal) => {
    if (!newVal) {
      phoneNumber.value = "";
    }
  }
);
</script>

<style scoped>
.country-selector {
  height: 100%;
  padding-left: 8px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.country-selector:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.phone-input :deep(.v-field__prepend-inner) {
  padding-top: 0;
  align-items: center;
}
</style>
