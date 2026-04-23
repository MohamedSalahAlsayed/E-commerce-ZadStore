const fs = require("fs");
const path = require("path");

const viewsDir = path.join(__dirname, "src", "views", "Dashboard");
const arLocalePath = path.join(__dirname, "src", "locales", "ar.json");
const enLocalePath = path.join(__dirname, "src", "locales", "en.json");

const arLocales = JSON.parse(fs.readFileSync(arLocalePath, "utf8"));
const enLocales = JSON.parse(fs.readFileSync(enLocalePath, "utf8"));

// Helper to get nested object value by path (e.g. "dashboard.title")
function getNestedValue(obj, path) {
  if (!path) return undefined;
  return path.split(".").reduce((acc, part) => acc && acc[part], obj);
}

let allKeys = new Set();
const hardcodedArabicLines = [];

function checkFile(filePath) {
  const content = fs.readFileSync(filePath, "utf8");

  // Extract $t('key') or t('key') strictly.
  // Use \b or exact matching.
  const regex = /(?:\$t|\bt)\(\s*['"]([^'"]+)['"]/g;
  let match;
  while ((match = regex.exec(content)) !== null) {
    allKeys.add(match[1]);
  }

  // Check for hardcoded Arabic text in template part
  const templateRegex = /<template>([\s\S]*?)<\/template>/;
  const templateMatch = templateRegex.exec(content);
  if (templateMatch) {
    const templateContent = templateMatch[1];
    const tLines = templateContent.split("\n");
    tLines.forEach((line, index) => {
      if (line.trim().startsWith("<!--")) return;
      // Ignore some common patterns
      if (/locale\s*===\s*['"]ar['"]/.test(line)) return;
      if (/(?:\$t|\bt)\(\s*['"][^'"]+['"]\s*,/.test(line)) return;

      // Basic check for arabic characters outside of known attributes
      if (/[\u0600-\u06FF]/.test(line)) {
        hardcodedArabicLines.push({
          file: path.basename(filePath),
          line: index + 1,
          text: line.trim(),
        });
      }
    });
  }
}

const files = fs.readdirSync(viewsDir).filter((f) => f.endsWith(".vue"));
files.forEach((f) => checkFile(path.join(viewsDir, f)));

const missingInAr = [];
const missingInEn = [];

allKeys.forEach((key) => {
  if (getNestedValue(arLocales, key) === undefined) {
    missingInAr.push(key);
  }
  if (getNestedValue(enLocales, key) === undefined) {
    missingInEn.push(key);
  }
});

console.log("=== MISSING IN AR.JSON ===");
console.log(missingInAr);

console.log("\n=== MISSING IN EN.JSON ===");
console.log(missingInEn);

console.log("\n=== POTENTIAL HARDCODED ARABIC ===");
hardcodedArabicLines.forEach((h) => {
  console.log(`${h.file}:${h.line} - ${h.text}`);
});
