# 💬 Suporte - Nosfir

Bem-vindo ao centro de suporte do Nosfir! Este documento vai ajudá-lo a encontrar as respostas que precisa.

---

## 📋 Índice

- [Antes de Pedir Ajuda](#antes-de-pedir-ajuda)
- [Canais de Suporte](#canais-de-suporte)
- [Perguntas Frequentes](#perguntas-frequentes)
- [Solução de Problemas](#solução-de-problemas)
- [Reportando Bugs](#reportando-bugs)

---

## ✅ Antes de Pedir Ajuda

Antes de abrir uma solicitação de suporte, verifique:

1. **📖 Leia a Documentação**
   - [README.md](../README.md) - Visão geral e instalação
   - [FAQ](#perguntas-frequentes) - Perguntas comuns

2. **🔍 Pesquise Issues Existentes**
   - [Issues abertas](https://github.com/davidcreator/Nosfir/issues)
   - [Issues fechadas](https://github.com/davidcreator/Nosfir/issues?q=is%3Aissue+is%3Aclosed)

3. **🔄 Atualize Tudo**
   - WordPress na última versão
   - Tema Nosfir atualizado
   - Plugins atualizados
   - PHP 8.0+

4. **🧪 Teste com Configuração Limpa**
   - Desative todos os plugins
   - Verifique se o problema persiste
   - Reative um por um para identificar conflitos

---

## 📞 Canais de Suporte

| Canal | Uso | Tempo de Resposta |
|-------|-----|-------------------|
| [GitHub Issues](https://github.com/davidcreator/Nosfir/issues) | Bugs e features | 24-48h |
| [Email](mailto:contato@davidalmeida.xyz) | Suporte geral | 24-48h |
| [Site](https://davidalmeida.xyz) | Informações | - |

### 🐛 Para Bugs
Use [GitHub Issues](https://github.com/davidcreator/Nosfir/issues) com o template de bug.

### 💡 Para Features
Use [GitHub Issues](https://github.com/davidcreator/Nosfir/issues) com o template de feature.

### ❓ Para Dúvidas Gerais
Envie email para contato@davidalmeida.xyz

---

## ❓ Perguntas Frequentes

<details>
<summary><strong>Como instalo o tema?</strong></summary>

1. Baixe o arquivo `nosfir.zip`
2. Acesse **Aparência > Temas > Adicionar Novo**
3. Clique em **Enviar Tema**
4. Selecione o arquivo e clique **Instalar**
5. Clique em **Ativar**

</details>

<details>
<summary><strong>O tema é compatível com WooCommerce?</strong></summary>

Sim! O Nosfir foi desenvolvido com integração completa ao WooCommerce. Basta instalar o WooCommerce e o tema se adaptará automaticamente.

</details>

<details>
<summary><strong>Como altero as cores do tema?</strong></summary>

Acesse **Aparência > Personalizar > Cores** e modifique as cores desejadas. As alterações são visualizadas em tempo real.

</details>

<details>
<summary><strong>Como adiciono um logo?</strong></summary>

Acesse **Aparência > Personalizar > Identidade do Site** e clique em "Selecionar Logo". Tamanho recomendado: 250x60px.

</details>

<details>
<summary><strong>Como configuro os menus?</strong></summary>

1. Acesse **Aparência > Menus**
2. Crie um novo menu
3. Adicione os itens desejados
4. Atribua a um local (Primary, Footer, Social)
5. Salve

</details>

<details>
<summary><strong>Como configuro a página inicial?</strong></summary>

1. Crie uma página para ser a home
2. Acesse **Configurações > Leitura**
3. Selecione "Uma página estática"
4. Escolha sua página como "Página inicial"
5. Salve

</details>

<details>
<summary><strong>O tema funciona com Elementor?</strong></summary>

Sim! O Nosfir é compatível com Elementor e outros page builders populares.

</details>

<details>
<summary><strong>Como traduzo o tema?</strong></summary>

Use o plugin **Loco Translate** ou traduza manualmente usando o arquivo `.pot` na pasta `languages/`.

</details>

<details>
<summary><strong>Posso usar em múltiplos sites?</strong></summary>

Sim! O tema é licenciado sob GPL, você pode usar em quantos sites quiser.

</details>

<details>
<summary><strong>Como atualizo o tema?</strong></summary>

Se baixou do GitHub:
```bash
cd wp-content/themes/Nosfir
git pull origin main
```

Se instalou via ZIP, baixe a nova versão e faça upload novamente.
</details>

## 🔧 Solução de Problemas
### Problema: Estilos não carregam corretamente
Soluções:

1. Limpe o cache do navegador (Ctrl+Shift+R)
1. Limpe o cache do WordPress (se usar plugin de cache)
1. Verifique se não há erros no console (F12)
1. Regenere os permalinks (Configurações > Links Permanentes > Salvar)

## Problema: Menu não aparece
Soluções:

1. Verifique se criou um menu em Aparência > Menus
1. Confirme que atribuiu o menu ao local correto
1. Verifique se o menu tem itens

## Problema: Imagens não aparecem
Soluções:

1. Verifique permissões da pasta uploads (755)
1. Regenere thumbnails com plugin "Regenerate Thumbnails"
1. Verifique limite de upload do PHP

## Problema: WooCommerce não estilizado
Soluções:

1. Verifique se o WooCommerce está ativo
1. Limpe o cache
1. Desative e reative o tema
1. Atualize ambos (tema e WooCommerce)

## Problema: Erro 500 ou tela branca
Soluções:

1. Ative debug no wp-config.php
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```
1. Verifique wp-content/debug.log
1. Aumente limite de memória PHP
1. Verifique versão do PHP (mínimo 8.0)

## Problema: Conflito com plugin
Soluções:

1. Desative todos os plugins
1. Ative um por um
1. Identifique o conflitante
1. Reporte como issue com detalhes

## 🐛 Reportando Bugs
### Informações Necessárias
```text
## Ambiente
- WordPress: [versão]
- PHP: [versão]
- Nosfir: [versão]
- WooCommerce: [versão, se aplicável]
- Navegador: [nome e versão]

## Descrição
[Descreva o bug]

## Passos para Reproduzir
1. [Passo 1]
2. [Passo 2]
3. [Passo 3]

## Comportamento Esperado
[O que deveria acontecer]

## Comportamento Atual
[O que acontece]

## Screenshots
[Se aplicável]

## Plugins Ativos
[Liste os plugins]
```

## Como Encontrar Informações
* Versão WordPress: Painel > Atualizações
* Versão PHP: Ferramentas > Saúde do Site > Informações > Servidor
* Versão do Tema: Aparência > Temas > Nosfir

## 🆘 Suporte de Emergência
Para problemas críticos que afetam produção:

1. Reverta para versão anterior (se possível)
1. Ative tema padrão (Twenty Twenty-Four)
1. Documente o problema com detalhes
1. Envie email com assunto [URGENTE]

## 📚 Recursos Adicionais
* WordPress Codex
* WooCommerce Docs
* WordPress Support Forums
* Stack Overflow - WordPress

<p align="center"> Estamos aqui para ajudar! 💜 </p> ```