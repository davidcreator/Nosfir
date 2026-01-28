# 🤝 Guia de Contribuição - Nosfir

Obrigado por considerar contribuir com o **Nosfir**! Este documento fornece diretrizes para contribuir com o projeto.

## 📋 Índice

- [Código de Conduta](#código-de-conduta)
- [Como Posso Contribuir?](#como-posso-contribuir)
- [Configuração do Ambiente](#configuração-do-ambiente)
- [Padrões de Código](#padrões-de-código)
- [Processo de Pull Request](#processo-de-pull-request)
- [Reportando Bugs](#reportando-bugs)
- [Sugerindo Melhorias](#sugerindo-melhorias)

---

## 📜 Código de Conduta

Este projeto adota um [Código de Conduta](CODE_OF_CONDUCT.md). Ao participar, você concorda em seguir suas diretrizes.

---

## 🎯 Como Posso Contribuir?

### 🐛 Reportando Bugs

- Verifique se o bug já foi reportado nas [Issues](https://github.com/davidcreator/Nosfir/issues)
- Use o template de bug report
- Inclua o máximo de detalhes possível

### 💡 Sugerindo Melhorias

- Abra uma issue com a tag `enhancement`
- Descreva claramente a melhoria proposta
- Explique por que seria útil

### 💻 Contribuindo com Código

1. Fork o repositório
2. Crie uma branch para sua feature
3. Desenvolva e teste
4. Envie um Pull Request

### 📝 Melhorando a Documentação

- Correções de typos
- Exemplos adicionais
- Traduções

### 🌐 Traduções

- Acesse a pasta `languages/`
- Use o arquivo `.pot` como base
- Envie arquivos `.po` e `.mo`

---

## ⚙️ Configuração do Ambiente

### Requisitos

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+ (para desenvolvimento)
- Composer (opcional)

### Instalação para Desenvolvimento

```bash
# Clone o repositório
git clone https://github.com/davidcreator/Nosfir.git

# Entre na pasta
cd Nosfir

# Instale dependências (se usar npm)
npm install

# Compile assets (se usar build)
npm run build
```

## Estrutura de Desenvolvimento
```text
nosfir/
│
├── assets/                          # Recursos estáticos
│   ├── css/                         # Folhas de estilo
│   │   ├── style.css                # Estilos principais
│   │   ├── editor-style.css         # Estilos do editor
│   │   ├── responsive.css           # Media queries
│   │   ├── blocks.css               # Estilos do Gutenberg
│   │   ├── woocommerce.css          # Estilos WooCommerce
│   │   └── admin.css                # Estilos do painel
│   ├── js/                          # Scripts JavaScript
│   │   ├── navigation.js            # Menu responsivo
│   │   ├── customizer.js            # Preview do Customizer
│   │   ├── main.js                  # Scripts gerais
│   │   ├── filters.js               # Filtros de posts
│   │   ├── woocommerce.js           # Scripts da loja
│   │   └── admin.js                 # Scripts do painel
│   ├── images/                      # Imagens do tema
│   │   ├── logo.png                 # Logo padrão
│   │   ├── placeholder.jpg          # Imagem placeholder
│   │   └── icons/                   # Ícones SVG
│   ├── fonts/                       # Fontes locais
│   └── scss/                        # Arquivos SASS
│       ├── base/
│       ├── components/
│       ├── woocommerce/
│       └── layout/
│
├── inc/                             # Includes PHP
│   ├── core/
│   │   ├── theme-setup.php          # Configuração inicial
│   │   ├── scripts.php              # Scripts e estilos
│   │   └── image-sizes.php          # Tamanhos de imagem
│   ├── customizer/
│   │   ├── customizer.php           # Configurações do Customizer
│   │   ├── sections/
│   │   └── controls/
│   ├── template-functions.php       # Funções de template
│   ├── template-tags.php            # Tags de template
│   ├── post-organization.php        # Sistema de organização
│   ├── widgets/                     # Widgets personalizados
│   │   ├── recent-posts.php
│   │   ├── category-posts.php
│   │   ├── featured-products.php
│   │   └── social-links.php
│   ├── extras.php                   # Funções auxiliares
│   └── compatibility/               # Compatibilidade
│       ├── woocommerce.php          # Integração WooCommerce
│       ├── woocommerce-functions.php
│       └── plugins.php
│
├── woocommerce/                     # Templates WooCommerce
│   ├── archive-product.php
│   ├── single-product.php
│   ├── cart/
│   ├── checkout/
│   ├── loop/
│   └── single-product/
│
├── template-parts/                  # Partes de template
│   ├── content/
│   │   ├── content.php              # Conteúdo padrão
│   │   ├── content-none.php         # Nenhum conteúdo
│   │   ├── content-search.php       # Resultados de busca
│   │   ├── content-single.php       # Post único
│   │   ├── content-page.php         # Página
│   │   └── content-product.php      # Produto
│   ├── header/
│   │   ├── header-default.php
│   │   ├── header-centered.php
│   │   └── header-sticky.php
│   ├── footer/
│   │   ├── footer-default.php
│   │   └── footer-widgets.php
│   ├── loops/
│   │   ├── loop-blog.php
│   │   ├── loop-grid.php
│   │   ├── loop-list.php
│   │   └── loop-archive.php
│   └── shop/
│       ├── product-card.php
│       └── quick-view.php
│
├── languages/                       # Arquivos de tradução
│   ├── nosfir.pot                   # Template de tradução
│   ├── pt_BR.mo                     # Português Brasil
│   └── pt_BR.po
│
├── style.css                        # Stylesheet principal (metadados)
├── functions.php                    # Funções do tema
├── index.php                        # Template principal
├── header.php                       # Cabeçalho
├── footer.php                       # Rodapé
├── sidebar.php                      # Barra lateral
├── sidebar-shop.php                 # Sidebar da loja
├── single.php                       # Post único
├── page.php                         # Página
├── archive.php                      # Arquivo
├── category.php                     # Categoria
├── search.php                       # Busca
├── 404.php                          # Página não encontrada
├── comments.php                     # Comentários
├── screenshot.png                   # Screenshot do tema
├── README.md                        # Este arquivo
├── readme.txt                       # Readme para WordPress.org
├── LICENSE                          # Licença GPL v2
├── CHANGELOG.md                     # Histórico de alterações
├── CONTRIBUTING.md                  # Guia de contribuição
├── CODE_OF_CONDUCT.md               # Código de conduta
└── SECURITY.md                      # Política de segurança
```

## 📏 Padrões de Código
### PHP
Seguimos os WordPress Coding Standards:
```php
<?php
/**
 * Descrição da função.
 *
 * @since 1.0.0
 * @param string $param Descrição do parâmetro.
 * @return string Descrição do retorno.
 */
function nosfir_function_name( $param ) {
    if ( empty( $param ) ) {
        return '';
    }

    $result = sanitize_text_field( $param );

    return $result;
}
```

### CSS/SCSS
```scss
// Use BEM para nomenclatura
.nosfir-component {
    display: flex;

    &__element {
        margin: 0;
    }

    &--modifier {
        color: red;
    }
}
```

### JavaScript
```js
/**
 * Descrição da função.
 *
 * @since 1.0.0
 * @param {string} param - Descrição do parâmetro.
 * @returns {string} Descrição do retorno.
 */
function nosfirFunctionName( param ) {
    if ( ! param ) {
        return '';
    }

    return param.trim();
}
```

## Convenções de Nomenclatura
| Tipo          | Padrão               | Exemplo              |
|---------------|----------------------|----------------------|
| Funções PHP   | nosfir_nome_funcao   | nosfir_get_header()  |
| Classes PHP   | Nosfir_Nome_Classe   | Nosfir_Customizer    |
| Hooks         | nosfir_nome_hook     | nosfir_before_header |
| CSS Classes   | nosfir-nome-classe   | nosfir-post-card     |
| JS Functions  | nosfirNomeFuncao     | nosfirToggleMenu()   |

## 🔄 Processo de Pull Request
### 1. Preparação
```bash
# Atualize seu fork
git checkout main
git pull upstream main

# Crie uma branch
git checkout -b feature/minha-feature
```

### 2. Desenvolvimento
* Escreva código limpo e documentado
* Adicione comentários quando necessário
* Teste em múltiplos navegadores
* Verifique responsividade

### 3. Commits
```bash
# Formato
tipo: descrição breve

# Tipos
feat:     Nova funcionalidade
fix:      Correção de bug
docs:     Documentação
style:    Formatação (não afeta código)
refactor: Refatoração
test:     Testes
chore:    Manutenção

# Exemplos
git commit -m "feat: adiciona filtro de posts por categoria"
git commit -m "fix: corrige menu mobile no Safari"
git commit -m "docs: atualiza instruções de instalação"
```
### Push e Pull Request
```bash
# Envie para seu fork
git push origin feature/minha-feature
```
* Abra um Pull Request no GitHub
* Preencha o template
* Aguarde revisão

### 5. Revisão
* Responda aos comentários do revisor
* Faça ajustes se necessário
* Mantenha a branch atualizada

## 🐛 Reportando Bugs
### Template de Bugs Report
```md
## Descrição do Bug
Descrição clara e concisa do bug.

## Passos para Reproduzir
1. Vá para '...'
2. Clique em '...'
3. Role até '...'
4. Veja o erro

## Comportamento Esperado
O que deveria acontecer.

## Screenshots
Se aplicável, adicione screenshots.

## Ambiente
- WordPress: [versão]
- PHP: [versão]
- WooCommerce: [versão, se aplicável]
- Navegador: [nome e versão]
- Plugins ativos: [lista]

## Informações Adicionais
Qualquer outro contexto relevante.
```

## 💡 Sugerindo Melhorias
### Template de Feature Request
```md
## Descrição da Melhoria
Descrição clara da funcionalidade desejada.

## Problema Relacionado
Qual problema isso resolve?

## Solução Proposta
Como você imagina a implementação?

## Alternativas Consideradas
Outras soluções que você considerou.

## Contexto Adicional
Screenshots, exemplos, referências.
```

## ✅ Checklist do Contribuidor
Antes de enviar seu PR, verifique:

 * Código segue os padrões do WordPress
 * Funções estão documentadas
 * Strings estão prontas para tradução
 * Testado em WordPress 6.0+
 * Testado em PHP 8.0+
 * Responsivo (mobile, tablet, desktop)
 * Acessível (teclado, leitores de tela)
 * Sem erros no console
 * Sem warnings do PHP

 ## 🏆 Reconhecimento
 Todos os contribuidores serão reconhecidos no README.md e no arquivo CONTRIBUTORS.md.

## ❓ Dúvidas?
* Abra uma issue
* Envie email para contato@davidalmeida.xyz
* Visite davidalmeida.xyz

<p align="center"> Obrigado por contribuir! ❤️ </p>