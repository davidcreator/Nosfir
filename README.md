# Nosfir - Tema WordPress para Organização de Conteúdo e WooCommerce

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B-purple.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)

<p align="center">
  <img src="logo.png" alt="Nosfir Screenshot" width="600">
</p>

## 📋 Descrição

**Nosfir** é um tema WordPress moderno, responsivo e poderoso, desenvolvido com foco na **organização inteligente de postagens** e **integração completa com WooCommerce**. Ideal para blogs, portais de conteúdo e lojas virtuais que necessitam de uma estrutura organizada e profissional.

### ✨ Características Principais

- 🎨 **Design Moderno e Limpo** - Interface elegante e profissional
- 📱 **Totalmente Responsivo** - Perfeito em qualquer dispositivo
- 🛒 **WooCommerce Integrado** - Suporte completo para e-commerce
- 📂 **Organização Avançada** - Sistema inteligente de categorização de posts
- ⚡ **Alta Performance** - Código otimizado para velocidade máxima
- 🔧 **Customizer Completo** - Personalização sem código
- 🌐 **Pronto para Tradução** - Totalmente internacionalizado (i18n ready)
- ♿ **Acessível** - Segue diretrizes WCAG 2.1
- 🔍 **SEO Friendly** - Estrutura otimizada para buscadores
- 📝 **Gutenberg Ready** - Compatível com o editor de blocos
- 🎯 **Filtros Inteligentes** - Busca e filtragem avançada de conteúdo
- 📊 **Múltiplos Layouts** - Grid, Lista, Masonry e mais

---

## 🚀 Instalação

### Via Painel WordPress (Recomendado)

1. Acesse **Aparência > Temas > Adicionar Novo**
2. Clique em **Enviar Tema**
3. Selecione o arquivo `nosfir.zip`
4. Clique em **Instalar Agora**
5. Após a instalação, clique em **Ativar**

### Via FTP

1. Baixe e extraia o arquivo do tema
2. Conecte-se ao seu servidor via FTP
3. Faça upload da pasta `nosfir` para `/wp-content/themes/`
4. Acesse **Aparência > Temas** no painel WordPress
5. Localize o Nosfir e clique em **Ativar**

### Via Git (Desenvolvedores)

```bash
cd /caminho/para/wp-content/themes/
git clone https://github.com/davidcreator/Nosfir.git
cd Nosfir
```
## ⚙️ Configuração
Configuração Inicial
Após ativar o tema, siga estes passos:

1. Configurar Menus
  * Acesse Aparência > Menus
  * Crie menus para: Principal, Loja, Rodapé e Social

2. Configurar Widgets
  * Acesse Aparência > Widgets
  * Configure as áreas: Sidebar Blog, Sidebar Loja, Rodapé 1-4
3. Personalizar o Tema
  * Acesse Aparência > Personalizar
  * Configure: Logo, cores, tipografia, layout e WooCommerce
4. Configurar WooCommerce
  * Instale e ative o WooCommerce
  * Execute o assistente de configuração
  * O tema se adaptará automaticamente

## Locais de Menu Disponíveis
| Local     | Descrição                              |
|-----------|----------------------------------------|
| primary   | Menu principal no cabeçalho            |
| secondary | Menu secundário (categorias)           |
| shop      | Menu da loja                           |
| footer    | Menu no rodapé                         |
| social    | Links para redes sociais               |

## Áreas de Widget
| Área            | Descrição                              |
|-----------------|----------------------------------------|
| sidebar-blog    | Barra lateral do blog                  |
| sidebar-shop    | Barra lateral da loja                  |
| sidebar-product | Sidebar de produto único               |
| footer-1        | Primeira coluna do rodapé              |
| footer-2        | Segunda coluna do rodapé               |
| footer-3        | Terceira coluna do rodapé              |
| footer-4        | Quarta coluna do rodapé                |

## 🎨 Personalização
### Opções do Customizer
O tema oferece extensas opções de personalização:

### 🎨 Identidade do Site
  * Logo personalizado (normal e retina)
  * Título e descrição do site
  * Ícone do site (favicon)

### 🎨 Cores
  * Cor primária
  * Cor secundária
  * Cor de destaque
  * Cor do texto
  * Cor de fundo
  * Cores do WooCommerce (botões, preços, badges)

### ✏️ Tipografia
  * Família de fontes do título
  * Família de fontes do corpo
  * Tamanhos de fonte personalizáveis
  * Peso das fontes

### 📐 Layout
  * Largura do container (1140px - 1400px)
  * Posição da sidebar (esquerda/direita/sem)
  * Layout do blog (grid/lista/masonry/misto)
  * Colunas do grid (2, 3 ou 4)

### 📰 Opções de Posts
  * Exibir data
  * Exibir autor
  * Exibir categorias
  * Exibir tempo de leitura
  * Exibir contador de visualizações
  * Posts relacionados
  * Exibir tags
  * Navegação entre posts

### 🛒 WooCommerce
  * Produtos por página
  * Colunas da loja (3 ou 4)
  * Estilo dos cards de produto
  * Exibir avaliações
  * Quick View
  * Wishlist
  * Comparar produtos

### 📱 Header
  * Estilo do menu (padrão/centralizado/hamburger)
  * Header fixo (sticky)
  * Header transparente
  * Barra de busca
  * Carrinho no header
  * Conta do usuário

### 🦶 Footer
  * Número de colunas de widgets (1-4)
  * Texto de copyright
  * Menu de rodapé
  * Links sociais
  * Newsletter
  * Selos de pagamento

  # 📁 Estrutura do Tema
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

## 📂 Sistema de Organização de Posts
### Recursos de Organização
O Nosfir inclui um sistema avançado para organização de conteúdo:

### 📌 Categorias Destacadas
  * Defina categorias principais para exibição na home
  * Cores personalizadas por categoria
  * Ícones para categorias

### 🏷️ Sistema de Tags Inteligente
  * Tags relacionadas
  * Nuvem de tags estilizada
  * Sugestão automática de tags
  
### 📊 Filtros Avançados
  * Filtro por categoria
  * Filtro por data
  * Filtro por autor
  * Ordenação múltipla (data, popularidade, comentários)
  * Filtros AJAX sem reload
  
### 🔍 Busca Aprimorada
  * Busca em tempo real
  * Busca por categoria
  * Histórico de buscas
  * Sugestões automáticas

## 📄 Shortcodes Disponíveis
```php
// Grid de posts por categoria
[nosfir_posts category="noticias" posts="6" columns="3"]

// Lista de posts recentes
[nosfir_recent_posts count="5" show_thumbnail="true"]

// Posts em destaque
[nosfir_featured_posts ids="1,2,3,4"]

// Posts por tag
[nosfir_tag_posts tag="tecnologia" posts="4"]

// Slider de posts
[nosfir_post_slider category="destaques" posts="5"]

// Produtos em destaque
[nosfir_products category="ofertas" count="4"]
```

## 🛒 Integração WooCommerce
### Recursos de E-commerce
O Nosfir oferece integração profunda com WooCommerce:

### 🛍️ Loja
  * Layout de loja personalizável
  * Sidebar de filtros
  * Ordenação de produtos
  * Visualização rápida (Quick View)
  * Lista de desejos
  * Comparar produtos
  
### 📦 Produto
  * Galeria com zoom
  * Abas de informações
  * Produtos relacionados
  * Upsells e Cross-sells
  * Avaliações estilizadas
  
### 🛒 Carrinho e Checkout
  * Mini carrinho no header
  * Carrinho flutuante
  * Checkout otimizado
  * Múltiplos métodos de pagamento
  
### 📱 Mobile Commerce
  * Filtros responsivos
  * Carrinho mobile-friendly
  * Touch-friendly gallery

## Configurações WooCommerce
```php
// Alterar produtos por página
add_filter( 'nosfir_products_per_page', function() {
    return 12;
});

// Alterar colunas da loja
add_filter( 'nosfir_shop_columns', function() {
    return 4;
});
```
## 🔧 Hooks Disponíveis
### Actions
```php
// Antes do header
do_action( 'nosfir_before_header' );

// Após o header
do_action( 'nosfir_after_header' );

// Antes do conteúdo
do_action( 'nosfir_before_content' );

// Após o conteúdo
do_action( 'nosfir_after_content' );

// Antes do footer
do_action( 'nosfir_before_footer' );

// Após o footer
do_action( 'nosfir_after_footer' );

// Antes do loop
do_action( 'nosfir_before_loop' );

// Após o loop
do_action( 'nosfir_after_loop' );

// Antes do card de post
do_action( 'nosfir_before_post_card' );

// Após o card de post
do_action( 'nosfir_after_post_card' );

// Antes do card de produto
do_action( 'nosfir_before_product_card' );

// Após o card de produto
do_action( 'nosfir_after_product_card' );
```
## Filters
```php
// Modificar classes do body
apply_filters( 'nosfir_body_classes', $classes );

// Modificar classes do post
apply_filters( 'nosfir_post_classes', $classes );

// Texto do "Leia mais"
apply_filters( 'nosfir_read_more_text', $text );

// Tamanho do excerpt
apply_filters( 'nosfir_excerpt_length', $length );

// Mais texto do excerpt
apply_filters( 'nosfir_excerpt_more', $more );

// Thumbnail padrão
apply_filters( 'nosfir_default_thumbnail', $url );

// Colunas da loja
apply_filters( 'nosfir_shop_columns', $columns );

// Posts relacionados
apply_filters( 'nosfir_related_posts_count', $count );
```

## 🖼️ Tamanhos de Imagem
```php
// Tamanhos registrados pelo tema
'nosfir-featured'     => 1200 x 628   // Imagem destacada
'nosfir-large'        => 800 x 450    // Posts grandes
'nosfir-thumbnail'    => 350 x 230    // Thumbnail do blog
'nosfir-small'        => 150 x 150    // Miniatura
'nosfir-widget'       => 100 x 70     // Widget de posts
'nosfir-product'      => 400 x 400    // Produto da loja
'nosfir-product-lg'   => 800 x 800    // Produto único
```

## 🌍 Tradução
O tema está preparado para tradução. Para traduzir:

### Usando Loco Translate (Recomendado)
1. Instale o plugin Loco Translate
1. Vá em Loco Translate > Temas
1. Selecione Nosfir
1. Clique em New Language
1. Traduza as strings
1. Salve

### Manualmente com Poedit
1. Baixe o Poedit
1. Abra o arquivo languages/nosfir.pot
1. Crie uma nova tradução
1. Salve como nosfir-pt_BR.po (para português do Brasil)
1. Faça upload para wp-content/languages/themes/

## Locales Disponíveis

| Idioma       | Locale  | Status        |
|--------------|---------|---------------|
| Inglês (US)  | en_US   | ✅ Completo   |
| Português (BR) | pt_BR | ✅ Completo   |
| Espanhol     | es_ES   | 🔄 Em progresso |

## Plugins Recomendados
O tema funciona perfeitamente sozinho, mas recomendamos:

| Plugin                        | Finalidade                  |
|-------------------------------|-----------------------------|
| WooCommerce                   | E-commerce completo         |
| Yoast SEO                     | Otimização SEO              |
| WP Super Cache                | Cache e performance         |
| Contact Form 7                | Formulários de contato      |
| YITH WooCommerce Wishlist     | Lista de desejos            |
| YITH WooCommerce Quick View   | Visualização rápida         |
| Regenerate Thumbnails         | Regenerar miniaturas        |
| Smush                         | Otimização de imagens       |

## ♿ Acessibilidade
O tema segue as diretrizes WCAG 2.1 nível AA:

### Recursos de Acessibilidade
✅ Skip links para navegação
✅ Landmarks ARIA apropriados
✅ Contraste de cores adequado (4.5:1 mínimo)
✅ Navegação completa por teclado
✅ Textos alternativos em imagens
✅ Formulários acessíveis
✅ Focus states visíveis
✅ Compatível com leitores de tela

## Testando Acessibilidade
```bash
# Ferramentas recomendadas
- WAVE Web Accessibility Evaluator
- axe DevTools
- Lighthouse (Chrome DevTools)
- NVDA / VoiceOver (Screen Readers)
```

## ❓ FAQ
<details> <summary><strong>Como altero as cores do tema?</strong></summary>
Acesse Aparência > Personalizar > Cores e modifique as cores desejadas. As alterações são aplicadas em tempo real.

</details><details> <summary><strong>Como adiciono um logo?</strong></summary>
Acesse Aparência > Personalizar > Identidade do Site e clique em "Selecionar Logo". O tamanho recomendado é 250x60px.

</details><details> <summary><strong>O tema é compatível com WooCommerce?</strong></summary>
Sim! O Nosfir foi desenvolvido com integração profunda ao WooCommerce, oferecendo layouts personalizados para loja, produtos, carrinho e checkout.

</details><details> <summary><strong>Como configuro os filtros de posts?</strong></summary>
Os filtros são habilitados automaticamente nas páginas de arquivo. Para customizar, acesse Aparência > Personalizar > Organização de Posts.

</details><details> <summary><strong>Como configuro o menu de redes sociais?</strong></summary>
Acesse Aparência > Menus
Crie um novo menu
Adicione links personalizados para suas redes sociais
Atribua ao local "Social Menu"
</details><details> <summary><strong>Posso usar com construtores de página?</strong></summary>
Sim, o tema é compatível com Elementor, Beaver Builder, Divi e outros construtores populares.

</details><details> <summary><strong>Como exibo produtos na página inicial?</strong></summary>
Use o shortcode [nosfir_products category="destaque" count="4"] ou configure através do Aparência > Personalizar > Página Inicial.

</details><details> <summary><strong>O tema suporta modo escuro?</strong></summary>
Sim! Ative em Aparência > Personalizar > Cores > Habilitar Modo Escuro.

</details>

## 🐛 Suporte e Bugs
### Reportar um Bug
1. Verifique se o bug já foi reportado nas Issues
1. Se não, abra uma nova issue
1. Inclua:
- Descrição clara do bug
- Passos para reproduzir
- Comportamento esperado
- Comportamento atual
- Capturas de tela (se aplicável)
- Versão do WordPress
- Versão do WooCommerce (se aplicável)
- Versão do PHP
- Plugins ativos
- Passos para reproduzir
- Screenshots (se aplicável)

## Solicitar uma Feature
Abra uma issue com a tag "enhancement"

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor, leia nosso [Guia de Contribuição](docs/CONTRIBUTING.md) e o [Código de Conduta](docs/CODE_OF_CONDUCT.md) antes de enviar um Pull Request.

### Como Contribuir
1. Faça um Fork do repositório
1. Crie uma branch para sua feature (git checkout -b feature/MinhaFeature)
1. Commit suas mudanças (git commit -m 'Adiciona MinhaFeature')
1. Push para a branch (git push origin feature/MinhaFeature)
1. Abra um Pull Request

## Padrões de Código
  * Siga os WordPress Coding Standards
  * Use comentários descritivos
  * Escreva código acessível e semântico

## 💬 Suporte
### Canais de Suporte
| Canal         | Link                          | Tempo de Resposta |
|---------------|-------------------------------|-------------------|
| GitHub Issues | [Abrir Issue](https://github.com/davidcreator/Nosfir/issues) | 24–48 h           |
| Email         | [contato@davidalmeida.xyz](mailto:contato@davidalmeida.xyz) | 24–48 h           |
| Website       | [davidalmeida.xyz](https://davidalmeida.xyz) | —                 |

## Antes de Pedir Suporte
✅ Verifique a documentação
✅ Consulte as FAQ
✅ Busque em issues existentes
✅ Teste com tema/plugins padrão

## 📜 Changelog
### Versão 1.0.0 (2024)
🎉 Lançamento inicial
✨ Sistema de organização de posts
🛒 Integração completa com WooCommerce
🎨 Customizer com opções avançadas
📱 Design totalmente responsivo
♿ Acessibilidade WCAG 2.1 AA

## 📜 Licença
Este tema é distribuído sob a licença GNU General Public License v2.0 ou posterior.

Consulte o arquivo LICENSE para mais detalhes.

## 👨‍💻 Autor
David L. Almeida

🌐 Website: davidalmeida.xyz
💼 GitHub: @davidcreator
📧 Email: contato@davidalmeida.xyz

## 🙏 Créditos
| Recurso        | Autor                    |
|----------------|--------------------------|
| Starter Theme  | WordPress Developer Resources |
| Normalize.css  | Nicolas Gallagher        |
| Font Awesome   | Fonticons, Inc.          |
| Google Fonts   | Google                   |
| WooCommerce    | Automattic               |

## ⭐ Apoie o Projeto
Se este tema foi útil para você, considere:

⭐ Dar uma estrela no GitHub
🐛 Reportar bugs encontrados
💡 Sugerir melhorias
🤝 Contribuir com código
📢 Compartilhar com a comunidade

<p align="center"> <strong>Nosfir</strong> - Organização e E-commerce para WordPress </p><p align="center"> Feito com ❤️ por <a href="https://davidalmeida.xyz">David L. Almeida</a> </p><p align="center"> <a href="#nosfir---tema-wordpress-para-organização-de-conteúdo-e-woocommerce">⬆️ Voltar ao topo</a> </p> ```