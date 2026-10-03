# MeuHub — Hub Pessoal Modular (MVP)

O **MeuHub** é um sistema pessoal construído como um monólito moderno com **Laravel**, **Vue.js 3**, **Inertia.js** e **PostgreSQL**, executando 100% em **containers Docker**.

No MVP, o sistema foca com excelência na categoria **Profissional** e ferramenta de **Currículos**, oferecendo uma experiência de documento inspirada em editores modernos ("um Word simplificado para currículos"), com duplicação, gerenciamento de múltiplas versões e exportação em PDF.

---

## 🚀 Arquitetura Docker

O ambiente é orquestrado via `docker-compose.yml` com 4 serviços:
- **`app`**: PHP 8.4-FPM com extensões `pdo_pgsql`, `gd`, `zip`, `bcmath`, `intl`, `pcntl` e Composer.
- **`web`**: Nginx servindo a porta `8000`.
- **`db`**: PostgreSQL 16 (porta `5432`).
- **`node`**: Node.js 22 com Vite dev server e Hot Module Replacement na porta `5173`.

---

## 🛠️ Como Iniciar o Sistema

### 1. Iniciar os containers
```bash
docker compose up -d
```

### 2. Rodar migrations e seeds
```bash
docker compose exec app php artisan migrate --seed
```

### 3. Acessar a aplicação
- Aplicação: [http://localhost:8000](http://localhost:8000)
- Vite Dev Server: [http://localhost:5173](http://localhost:5173)

---

## 🔑 Credenciais de Demonstração

O banco de dados já conta com um usuário de demonstração e um currículo completo pré-cadastrado:

- **E-mail:** `demo@meuhub.local`
- **Senha:** `senha123`

Você também pode criar novos usuários diretamente pela tela de registro.

---

## 🧪 Executando os Testes Automatizados

O sistema conta com suíte de testes cobrindo todo o fluxo de autenticação, CRUD de currículos, duplicação, geração de PDF e isolamento estrito de permissões (ResumePolicy):

```bash
docker compose exec app php artisan test
```

---

## 📄 Funcionalidades do MVP (Currículo)

- **Criação e Gestão**: Crie múltiplas versões independentes de currículos.
- **Editor de Documento**: Experiência fluida dividida em seções:
  - Dados Pessoais e Links (LinkedIn, GitHub, Portfólio);
  - Resumo Profissional;
  - Experiência Profissional (reordenação, cargo atual, realizações);
  - Formação Acadêmica (grau, datas, instituição);
  - Habilidades (tags interativas);
  - Idiomas com proficiência;
  - Cursos & Certificações;
  - Informações Adicionais livres.
- **Duplicação rápida**: Crie clones instantâneos para adaptar currículos para vagas específicas.
- **Visualização**: Modo documento para leitura e impressão (`Ctrl + P`).
- **Exportação para PDF**: Geração de PDF profissional em padrão A4, com tipografia legível e compatível com ATS.
