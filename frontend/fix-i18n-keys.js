const fs = require("fs");
const path = require("path");

const arLocalePath = path.join(__dirname, "src", "locales", "ar.json");
const enLocalePath = path.join(__dirname, "src", "locales", "en.json");

const arLocales = JSON.parse(fs.readFileSync(arLocalePath, "utf8"));
const enLocales = JSON.parse(fs.readFileSync(enLocalePath, "utf8"));

// Helper to set nested object value by path
function setNestedValue(obj, path, value) {
  const parts = path.split(".");
  let current = obj;
  for (let i = 0; i < parts.length - 1; i++) {
    if (!current[parts[i]]) current[parts[i]] = {};
    current = current[parts[i]];
  }
  current[parts[parts.length - 1]] = value;
}

const additions = [
  {
    key: "sales.orders.status.partially_returned",
    ar: "مرتجع جزئياً",
    en: "Partially Returned",
  },
  { key: "dashboard.close", ar: "إغلاق", en: "Close" },
  { key: "dashboard.confirm", ar: "تأكيد", en: "Confirm" },
  { key: "returns.type_th", ar: "النوع", en: "Type" },
  { key: "auth.register.email", ar: "البريد الإلكتروني", en: "Email" },
  {
    key: "auth.email_invalid",
    ar: "البريد الإلكتروني غير صالح",
    en: "Invalid Email",
  },
  { key: "auth.register.password", ar: "كلمة المرور", en: "Password" },
  { key: "settings.landing.show", ar: "إظهار", en: "Show" },
  { key: "settings.landing.hide", ar: "إخفاء", en: "Hide" },
  {
    key: "settings.landing.texts_title",
    ar: "نصوص الصفحة الرئيسية",
    en: "Landing Page Texts",
  },
  { key: "settings.landing.auto_hint", ar: "نص تلقائي", en: "Auto Hint" },
  { key: "close", ar: "إغلاق", en: "Close" },
];

additions.forEach((item) => {
  setNestedValue(arLocales, item.key, item.ar);
  setNestedValue(enLocales, item.key, item.en);
});

fs.writeFileSync(arLocalePath, JSON.stringify(arLocales, null, 2), "utf8");
fs.writeFileSync(enLocalePath, JSON.stringify(enLocales, null, 2), "utf8");

console.log("Added missing keys successfully.");
