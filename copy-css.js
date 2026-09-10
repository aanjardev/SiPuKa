const fs = require("fs");
const path = require("path");

// Source and destination directories
const sourceDir = path.join(__dirname, "resources", "css");
const destDir = path.join(__dirname, "public", "css");

// Create destination directory if it doesn't exist
if (!fs.existsSync(destDir)) {
    fs.mkdirSync(destDir, { recursive: true });
}

// Function to copy files recursively
function copyFilesRecursive(src, dest) {
    if (!fs.existsSync(src)) {
        console.error(`Source directory does not exist: ${src}`);
        return;
    }

    const files = fs.readdirSync(src);

    files.forEach((file) => {
        const srcPath = path.join(src, file);
        const destPath = path.join(dest, file);

        if (fs.statSync(srcPath).isDirectory()) {
            // Recursively copy subdirectories
            if (!fs.existsSync(destPath)) {
                fs.mkdirSync(destPath, { recursive: true });
            }
            copyFilesRecursive(srcPath, destPath);
        } else if (file.endsWith(".css")) {
            // Copy CSS files
            fs.copyFileSync(srcPath, destPath);
            console.log(`✓ Copied: ${srcPath} → ${destPath}`);
        }
    });
}

// Run the copy operation
console.log("Copying CSS files from resources/css to public/css...");
copyFilesRecursive(sourceDir, destDir);
console.log("✅ CSS files copied successfully!");
