#!/bin/bash

# Atualiza informações sobre o repositório remoto
git fetch --all

# Obtém a lista de branches remotas
branches=$(git branch -r | grep -v '\->' | sed 's/origin\///')

# Atualiza cada branch local com as mudanças da respectiva branch remota
for branch in $branches; do
    echo "Atualizando a branch $branch..."
    git checkout $branch 2>/dev/null || git checkout -b $branch origin/$branch
    git pull origin $branch
done

# Retorna para a branch original (opcional)
echo "Retornando para a branch principal..."
git checkout main || git checkout master
