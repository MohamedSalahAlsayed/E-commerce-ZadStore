const fs = require("fs");
const path = require("path");

function replaceInDir(dir) {
  if (!fs.existsSync(dir)) return;
  const files = fs.readdirSync(dir);
  for (const file of files) {
    const fullPath = path.join(dir, file);
    if (fs.statSync(fullPath).isDirectory()) {
      replaceInDir(fullPath);
    } else if (
      fullPath.endsWith(".vue") ||
      fullPath.endsWith(".js") ||
      fullPath.endsWith(".css")
    ) {
      let content = fs.readFileSync(fullPath, "utf8");

      let newContent = content.replace(/text-indigo/g, "text-primary");
      newContent = newContent.replace(/bg-indigo/g, "bg-primary");
      newContent = newContent.replace(/color="indigo"/g, 'color="primary"');
      newContent = newContent.replace(/color="indigo/g, 'color="primary');

      if (content !== newContent) {
        fs.writeFileSync(fullPath, newContent, "utf8");
        console.log(`Updated indigo -> primary in ${fullPath}`);
      }
    }
  }
}
replaceInDir("e:/مشروع فيو/ZadStore/src");
