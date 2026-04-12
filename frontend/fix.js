const fs = require("fs");
const path = require("path");

const dirs = ["src/components", "src/views/User"];
let fixedCount = 0;

for (const dir of dirs) {
  const dirPath = path.join(__dirname, dir);
  if (!fs.existsSync(dirPath)) continue;

  const files = fs.readdirSync(dirPath).filter((f) => f.endsWith(".vue"));

  for (const file of files) {
    const filePath = path.join(dirPath, file);
    let content = fs.readFileSync(filePath, "utf8");
    const originalContent = content;

    // Target the specific v-card-actions block for product cards
    content = content.replace(
      /<v-card-actions[^>]*class="px-3 pb-4 pt-2 d-flex flex-column gap-2 w-100"[^>]*>/g,
      '<div class="px-3 pb-4 pt-3 d-flex flex-column gap-2">'
    );

    // Only replace </v-card-actions> that are directly preceding </v-card> in product cards
    content = content.replace(
      /<\/v-card-actions>\s*<\/v-card>/g,
      "</div>\n            </v-card>"
    );

    // Fix buttons classes to avoid overflow
    content = content.replace(
      /class="font-weight-bold mb-1 w-100"/g,
      'class="font-weight-bold mb-1 mx-0"'
    );
    content = content.replace(
      /class="font-weight-bold w-100"/g,
      'class="font-weight-bold mx-0"'
    );

    if (content !== originalContent) {
      fs.writeFileSync(filePath, content, "utf8");
      console.log("Fixed", file);
      fixedCount++;
    }
  }
}
console.log("Fixed count:", fixedCount);
