# 🏗️ Guia de Build - FitZone Frontend

## 📋 Comandos Disponíveis

### Build para Produção
```bash
npm run build
```

Este comando irá:
1. ✅ Limpar a pasta `dist/` (se existir)
2. ✅ Copiar todos os arquivos HTML
3. ✅ Copiar assets (imagens, favicon)
4. ✅ Minificar todos os arquivos JavaScript
5. ✅ Minificar todos os arquivos CSS
6. ✅ Gerar estatísticas do build

### Desenvolvimento
```bash
npm run dev
```
Inicia um servidor local na porta 3000 para desenvolvimento.

### Limpar Build
```bash
npm run clean
```
Remove a pasta `dist/` completamente.

## 📁 Estrutura após Build

Após executar `npm run build`, a estrutura será:

```
dist/
├── css/
│   └── styles.css          (minificado)
├── js/
│   ├── app.js              (minificado)
│   ├── auth.js             (minificado)
│   ├── dashboard-cliente.js (minificado)
│   └── dashboard-personal.js (minificado)
├── index.html
├── login.html
├── dashboard-personal.html
├── dashboard-cliente.html
├── favicon.ico
└── logo.redonda.png
```

## 🚀 Publicação

Após o build, publique **apenas a pasta `dist/`** no servidor:

1. Execute `npm run build`
2. Faça upload de **todos os arquivos dentro de `dist/`** para a pasta `htdocs/` do servidor
3. **NÃO** publique a pasta `node_modules/` ou arquivos de desenvolvimento

## ⚙️ Ferramentas de Build

O build utiliza:
- **Terser**: Minificação de JavaScript
- **Clean-CSS**: Minificação de CSS
- **Node.js**: Scripts de build personalizados

## 📊 Otimizações

O build realiza as seguintes otimizações:
- ✅ Minificação de JavaScript (redução de ~30-50% no tamanho)
- ✅ Minificação de CSS (redução de ~20-40% no tamanho)
- ✅ Remoção de comentários
- ✅ Compressão de código

## 🔧 Troubleshooting

### Erro: "terser não encontrado"
```bash
npm install
```

### Erro: "cleancss não encontrado"
```bash
npm install
```

### Build falha
- Verifique se todas as dependências estão instaladas: `npm install`
- Verifique se os arquivos fonte existem nas pastas corretas
- Execute `npm run clean` antes de tentar novamente


