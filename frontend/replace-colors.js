const fs = require("fs");
const path = require("path");

function replaceInDir(dir) {
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
      let newContent = content.replace(
        /rgb\(\s*14\s*,\s*22\s*,\s*67\s*\)/gi,
        "rgb(var(--v-theme-primary))"
      );
      newContent = newContent.replace(
        /#0e1643/gi,
        "rgb(var(--v-theme-primary))"
      );
      newContent = newContent.replace(
        /#0b2c8f/gi,
        "rgb(var(--v-theme-primary))"
      );
      if (content !== newContent) {
        fs.writeFileSync(fullPath, newContent, "utf8");
        console.log(`Updated ${fullPath}`);
      }
    }
  }
}
replaceInDir("e:/مشروع فيو/ZadStore/src");
