const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

console.log('🚀 Iniciando build do FitZone...\n');

const distDir = path.join(__dirname, 'dist');

// Criar pasta dist se não existir
if (!fs.existsSync(distDir)) {
  fs.mkdirSync(distDir, { recursive: true });
}

// Função para copiar arquivo
function copyFile(src, dest) {
  const destDir = path.dirname(dest);
  if (!fs.existsSync(destDir)) {
    fs.mkdirSync(destDir, { recursive: true });
  }
  fs.copyFileSync(src, dest);
}

// Função para copiar diretório recursivamente
function copyDir(src, dest) {
  if (!fs.existsSync(dest)) {
    fs.mkdirSync(dest, { recursive: true });
  }
  const files = fs.readdirSync(src);
  files.forEach(file => {
    const srcPath = path.join(src, file);
    const destPath = path.join(dest, file);
    const stat = fs.statSync(srcPath);
    if (stat.isDirectory()) {
      copyDir(srcPath, destPath);
    } else {
      copyFile(srcPath, destPath);
    }
  });
}

try {
  // 1. Copiar arquivos HTML
  console.log('📄 Copiando arquivos HTML...');
  const htmlFiles = ['index.html', 'login.html', 'dashboard-personal.html', 'dashboard-cliente.html'];
  htmlFiles.forEach(file => {
    if (fs.existsSync(path.join(__dirname, file))) {
      copyFile(path.join(__dirname, file), path.join(distDir, file));
      console.log(`   ✓ ${file}`);
    }
  });

  // 2. Copiar assets (imagens, favicon)
  console.log('\n🖼️  Copiando assets...');
  const assets = ['favicon.ico', 'logo.nova.png'];
  assets.forEach(asset => {
    if (fs.existsSync(path.join(__dirname, asset))) {
      copyFile(path.join(__dirname, asset), path.join(distDir, asset));
      console.log(`   ✓ ${asset}`);
    }
  });

  // 3. Minificar e copiar JavaScript
  console.log('\n📦 Minificando JavaScript...');
  const jsDir = path.join(distDir, 'js');
  if (!fs.existsSync(jsDir)) {
    fs.mkdirSync(jsDir, { recursive: true });
  }

  const jsFiles = ['app.js', 'auth.js', 'dashboard-cliente.js', 'dashboard-personal.js'];
  jsFiles.forEach(file => {
    const srcPath = path.join(__dirname, 'js', file);
    if (fs.existsSync(srcPath)) {
      const destPath = path.join(jsDir, file);
      try {
        // Minificar com terser
        const minified = execSync(
          `npx terser "${srcPath}" -c -m --comments false`,
          { encoding: 'utf-8', stdio: 'pipe' }
        );
        fs.writeFileSync(destPath, minified);
        const originalSize = fs.statSync(srcPath).size;
        const minifiedSize = fs.statSync(destPath).size;
        const reduction = ((1 - minifiedSize / originalSize) * 100).toFixed(1);
        console.log(`   ✓ ${file} (${(originalSize / 1024).toFixed(1)}KB → ${(minifiedSize / 1024).toFixed(1)}KB, -${reduction}%)`);
      } catch (error) {
        // Se terser falhar, copiar o arquivo original
        console.log(`   ⚠ ${file} (copiado sem minificação - erro: ${error.message})`);
        copyFile(srcPath, destPath);
      }
    }
  });

  // 4. Minificar e copiar CSS
  console.log('\n🎨 Minificando CSS...');
  const cssDir = path.join(distDir, 'css');
  if (!fs.existsSync(cssDir)) {
    fs.mkdirSync(cssDir, { recursive: true });
  }

  const cssFile = path.join(__dirname, 'css', 'styles.css');
  if (fs.existsSync(cssFile)) {
    const destCssFile = path.join(cssDir, 'styles.css');
    try {
      // Minificar com clean-css
      execSync(
        `npx cleancss -o "${destCssFile}" "${cssFile}"`,
        { encoding: 'utf-8', stdio: 'pipe' }
      );
      const originalSize = fs.statSync(cssFile).size;
      const minifiedSize = fs.statSync(destCssFile).size;
      const reduction = ((1 - minifiedSize / originalSize) * 100).toFixed(1);
      console.log(`   ✓ styles.css (${(originalSize / 1024).toFixed(1)}KB → ${(minifiedSize / 1024).toFixed(1)}KB, -${reduction}%)`);
    } catch (error) {
      // Se clean-css falhar, copiar o arquivo original
      console.log(`   ⚠ styles.css (copiado sem minificação - erro: ${error.message})`);
      copyFile(cssFile, destCssFile);
    }
  }

  // 5. Estatísticas finais
  console.log('\n📊 Estatísticas do build:');
  const distFiles = getAllFiles(distDir);
  const totalSize = distFiles.reduce((total, file) => {
    return total + fs.statSync(file).size;
  }, 0);
  console.log(`   • Total de arquivos: ${distFiles.length}`);
  console.log(`   • Tamanho total: ${(totalSize / 1024).toFixed(1)}KB`);

  console.log('\n✅ Build concluído com sucesso!');
  console.log(`📁 Arquivos prontos para publicação em: ${distDir}\n`);

} catch (error) {
  console.error('\n❌ Erro durante o build:', error.message);
  process.exit(1);
}

function getAllFiles(dir) {
  const files = [];
  const items = fs.readdirSync(dir);
  items.forEach(item => {
    const fullPath = path.join(dir, item);
    const stat = fs.statSync(fullPath);
    if (stat.isDirectory()) {
      files.push(...getAllFiles(fullPath));
    } else {
      files.push(fullPath);
    }
  });
  return files;
}

