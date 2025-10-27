#!/bin/bash

# Script de configuração dos Git Hooks para o projeto CONTEX
# Este script configura automaticamente os hooks necessários

echo "🔧 Configurando Git Hooks para CONTEX..."

# Verificar se estamos na raiz do projeto
if [ ! -f "application/config/constants.php" ]; then
    echo "❌ Erro: Execute este script da raiz do projeto CONTEX!"
    exit 1
fi

# Verificar se .git existe
if [ ! -d ".git" ]; then
    echo "❌ Erro: Este não é um repositório Git!"
    exit 1
fi

# Criar diretório hooks se não existir
mkdir -p .git/hooks

# Hook pre-commit para versionamento automático
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/bash

# Hook pre-commit para versionamento automático do CONTEX

echo "🔄 Executando versionamento automático..."

# Caminho para o arquivo constants.php
CONSTANTS_FILE="application/config/constants.php"

# Verificar se o arquivo existe
if [ ! -f "$CONSTANTS_FILE" ]; then
    echo "❌ Arquivo constants.php não encontrado!"
    exit 1
fi

# Extrair versão atual
CURRENT_VERSION=$(grep '$version' "$CONSTANTS_FILE" | grep -o '[0-9]\+')

if [ -z "$CURRENT_VERSION" ]; then
    echo "❌ Não foi possível encontrar a versão atual!"
    exit 1
fi

# Incrementar versão
NEW_VERSION=$((CURRENT_VERSION + 1))

echo "📈 Versão atual: $CURRENT_VERSION → Nova versão: $NEW_VERSION"

# Atualizar arquivo constants.php
sed -i "s/\$version       = '$CURRENT_VERSION';/\$version       = '$NEW_VERSION';/" "$CONSTANTS_FILE"

# Adicionar arquivo modificado ao commit
git add "$CONSTANTS_FILE"

echo "✅ Versionamento automático concluído!"
EOF

# Tornar executável
chmod +x .git/hooks/pre-commit

echo "✅ Git Hooks configurados com sucesso!"
echo ""
echo "📋 Hooks ativos:"
echo "   • pre-commit: Versionamento automático"
echo ""
echo "🎯 Como testar:"
echo "   1. Faça qualquer modificação no código"
echo "   2. Execute: git add . && git commit -m 'teste'"
echo "   3. A versão será incrementada automaticamente!"
echo ""
echo "📁 Backup: Este script está em scripts/setup-hooks.sh"
echo "   Para reconfigurar: ./scripts/setup-hooks.sh"