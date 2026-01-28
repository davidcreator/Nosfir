# 🔒 Política de Segurança - Nosfir

## Versões Suportadas

As seguintes versões do Nosfir estão atualmente recebendo atualizações de segurança:

| Versão | Suportada          |
|--------|-------------------|
| 1.0.x  | ✅ Sim            |
| < 1.0  | ❌ Não            |

---

## 🚨 Reportando uma Vulnerabilidade

A segurança do Nosfir é levada a sério. Se você descobrir uma vulnerabilidade de segurança, por favor, siga os passos abaixo:

### ⚠️ NÃO Faça

- ❌ Não abra uma issue pública no GitHub
- ❌ Não divulgue publicamente antes da correção
- ❌ Não explore a vulnerabilidade além do necessário para demonstrá-la

### ✅ Faça

1. **Envie um email para**: contato@davidalmeida.xyz
2. **Assunto**: [SEGURANÇA] Descrição breve
3. **Inclua**:
   - Descrição detalhada da vulnerabilidade
   - Passos para reproduzir
   - Impacto potencial
   - Sugestão de correção (se tiver)

---

## 📧 Informações para o Relatório

---

## ⏱️ Processo de Resposta

| Etapa | Tempo Estimado |
|-------|----------------|
| Confirmação de recebimento | 24 horas |
| Avaliação inicial | 48 horas |
| Confirmação da vulnerabilidade | 7 dias |
| Desenvolvimento da correção | 14-30 dias |
| Lançamento da correção | Conforme gravidade |

---

## 🏷️ Classificação de Gravidade

### 🔴 Crítica
- Execução remota de código
- SQL Injection
- Bypass de autenticação

**Tempo de resposta**: 24-48 horas

### 🟠 Alta
- XSS armazenado
- CSRF em ações sensíveis
- Escalação de privilégios

**Tempo de resposta**: 7 dias

### 🟡 Média
- XSS refletido
- Divulgação de informações sensíveis
- CSRF em ações não críticas

**Tempo de resposta**: 14 dias

### 🟢 Baixa
- Problemas de configuração
- Informações de versão expostas

**Tempo de resposta**: 30 dias

---

## 🔐 Boas Práticas de Segurança

### Para Usuários do Tema

1. **Mantenha Atualizado**
   - Sempre use a versão mais recente do tema
   - Atualize WordPress e plugins regularmente

2. **Configuração Segura**
   - Use senhas fortes
   - Limite tentativas de login
   - Habilite autenticação de dois fatores

3. **Backups**
   - Faça backups regulares
   - Teste a restauração periodicamente

4. **Hospedagem**
   - Use hospedagem confiável
   - Habilite SSL/HTTPS
   - Configure firewall

### Para Desenvolvedores

1. **Sanitização e Validação**
   ```php
   // Sempre sanitize inputs
   $input = sanitize_text_field( $_POST['input'] );
   
   // Valide dados
   if ( ! wp_verify_nonce( $_POST['nonce'], 'action' ) ) {
       die( 'Segurança falhou' );
   }
2. **Escape de Output**
    ```php
    // Escape HTML
    echo esc_html( $variavel );

    // Escape atributos
    echo esc_attr( $atributo );

    // Escape URLs
    echo esc_url( $url );
    ```
3. **Prepared Statements**
    ```php
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->posts} WHERE ID = %d",
        $post_id
    );
    ```

## 🏆 Reconhecimento
Agradecemos aos pesquisadores de segurança que reportam vulnerabilidades de forma responsável. Contribuidores serão reconhecidos (se desejarem) após a correção ser liberada.

## 🏆 Hall da Fama
| Pesquisador | Vulnerabilidade | Data |
|-------------|---------------|------|
| - | - | - |

## 📚 Recursos
WordPress Security Best Practices
OWASP Top 10
WordPress Hardening

## 📞 Contato
Email de Segurança: contato@davidalmeida.xyz
Website: davidalmeida.xyz
PGP Key: [Disponível mediante solicitação]

<p align="center"> Segurança é responsabilidade de todos! 🛡️ </p> ```