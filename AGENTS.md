# MeuHub

## 1. Visão do produto

O **MeuHub** será um sistema pessoal para centralizar diferentes áreas da vida do usuário em um único lugar.

A visão de longo prazo é transformar o MeuHub em um **hub pessoal modular**, onde diferentes áreas da vida possuem ferramentas específicas.

Exemplo futuro:

```text
MeuHub
├── Profissional
│   ├── Currículo
│   ├── Experiências
│   ├── Formação
│   └── Projetos
│
├── Pessoal
├── Saúde
├── Estudos
├── Finanças
├── Planejamento
└── outras áreas
```

Porém, isso é apenas a visão futura.

## O MVP deve ser extremamente pequeno.

A primeira versão do MeuHub deve possuir **somente uma categoria e uma ferramenta**:

```text
Profissional
└── Currículo
```

Não implementar outras áreas neste momento.

---

# 2. Objetivo do MVP

O objetivo inicial é criar uma ferramenta excelente para criação e gerenciamento de currículos.

O usuário deve conseguir entrar no MeuHub e:

1. criar um currículo;
2. editar o currículo;
3. salvar o currículo;
4. retornar posteriormente;
5. continuar a edição;
6. visualizar o currículo;
7. duplicar o currículo;
8. excluir o currículo;
9. exportar o currículo para PDF.

A experiência deve ser semelhante à de um **editor de documentos**, porém especializada em currículos.

A referência conceitual é:

> "Um Word simplificado especificamente para criar currículos."

Não é necessário reproduzir todas as funcionalidades de um editor de texto.

---

# 3. Stack

Utilizar obrigatoriamente:

- Laravel;
- Vue.js;
- Inertia.js;
- PostgreSQL.

A aplicação deve ser um **monólito Laravel + Vue + Inertia**.

Não criar frontend e backend como aplicações independentes.

Laravel deve ser responsável por:

- aplicação;
- autenticação;
- regras de negócio;
- persistência;
- autorização;
- validação;
- geração/exportação quando apropriado.

Vue deve ser responsável pela interface.

Inertia deve fazer a comunicação entre Laravel e Vue.

PostgreSQL deve ser o banco de dados principal.

---

# 4. Arquitetura

Apesar de o MVP possuir apenas Currículo, a arquitetura deve permitir que o MeuHub cresça futuramente.

Utilizar o conceito:

```text
MeuHub
  ↓
Categoria
  ↓
Ferramenta
  ↓
Dados
```

No MVP:

```text
MeuHub
  ↓
Profissional
  ↓
Currículo
```

No futuro:

```text
MeuHub
├── Profissional
│   ├── Currículo
│   ├── Experiências
│   └── Projetos
│
├── Saúde
│   ├── Treinos
│   └── Atividades
│
└── Planejamento
    ├── Tarefas
    └── Objetivos
```

Não implementar essas funcionalidades futuras agora.

---

# 5. Categorias pré-configuradas

As categorias do MeuHub devem existir internamente no sistema.

No futuro poderão existir:

- Profissional;
- Pessoal;
- Saúde;
- Estudos;
- Finanças;
- Planejamento;
- etc.

Porém, no MVP somente:

```text
Profissional
```

deve estar disponível.

O usuário não deve criar categorias livremente.

As categorias devem ser consideradas parte da estrutura interna do produto.

Isso permite que, no futuro, uma nova categoria seja adicionada pelo sistema sem alterar a arquitetura principal.

---

# 6. Profissional

A categoria Profissional deve conter inicialmente apenas:

```text
Currículo
```

A interface não deve apresentar funcionalidades que ainda não existem.

Não criar menus vazios como:

```text
Experiências
Formação
Projetos
Cursos
```

se eles ainda não forem ferramentas independentes.

Essas informações podem existir **dentro do currículo**.

---

# 7. Currículos

O usuário pode possuir vários currículos.

Exemplo:

```text
Meus currículos

Currículo Principal
Currículo Desenvolvedor
Currículo Gestão
Currículo Empresa X
```

Cada currículo deve ser independente.

Deve possuir:

- identificação;
- conteúdo;
- data de criação;
- data de atualização.

O usuário deve poder:

- criar;
- editar;
- duplicar;
- excluir;
- visualizar;
- exportar.

---

# 8. Editor de currículo

O principal diferencial do MVP deve ser o editor.

Não criar uma tela gigantesca composta apenas por inputs.

Criar uma experiência de edição semelhante a um documento.

O usuário deve conseguir editar o conteúdo do currículo de forma natural.

Estrutura inicial:

```text
Dados pessoais

Resumo profissional

Experiência profissional

Formação acadêmica

Cursos

Certificações

Habilidades

Idiomas

Informações adicionais
```

Cada seção deve poder ser editada.

O usuário deve conseguir:

- adicionar itens;
- editar itens;
- remover itens;
- reorganizar itens;
- alterar conteúdo;
- formatar textos quando necessário.

---

# 9. Dados pessoais

Suportar inicialmente:

- nome;
- telefone;
- e-mail;
- cidade;
- foto opcional;
- LinkedIn;
- GitHub;
- outros links.

Não exigir informações que não sejam necessárias.

---

# 10. Experiência profissional

Cada experiência deve permitir:

- empresa;
- cargo;
- localização;
- data inicial;
- data final;
- experiência atual;
- descrição.

A descrição deve permitir texto formatado.

---

# 11. Formação

Cada formação deve possuir:

- instituição;
- curso;
- grau/tipo;
- data inicial;
- data final;
- descrição opcional.

---

# 12. Cursos e certificações

Permitir cadastrar:

### Curso

- nome;
- instituição;
- data;
- descrição.

### Certificação

- nome;
- instituição;
- data;
- validade opcional;
- código opcional;
- link opcional.

---

# 13. Habilidades

Permitir cadastrar uma lista de habilidades.

Exemplo:

```text
PHP
Laravel
Vue.js
PostgreSQL
Git
Docker
```

A interface pode permitir reorganizar a ordem.

---

# 14. Idiomas

Cada idioma deve possuir:

- idioma;
- nível.

Exemplo:

```text
Português — Nativo
Inglês — Intermediário
Espanhol — Básico
```

---

# 15. Informações adicionais

Permitir uma seção de texto livre para informações que não se encaixem nas outras categorias.

---

# 16. Templates

No MVP deve existir apenas **um template de currículo**.

Esse template deve ser:

- profissional;
- limpo;
- legível;
- adequado para processos seletivos;
- responsivo na visualização;
- adequado para impressão/PDF.

A arquitetura deve permitir futuramente:

```text
Currículo
├── Template moderno
├── Template clássico
├── Template executivo
└── Template minimalista
```

Mas não implementar múltiplos templates agora.

---

# 17. Exportação PDF

O usuário deve conseguir exportar o currículo para PDF.

O resultado deve:

- possuir boa aparência;
- respeitar a hierarquia visual;
- funcionar em múltiplas páginas;
- evitar cortes de conteúdo;
- preservar acentuação;
- ser adequado para envio profissional.

Avaliar a melhor solução para geração do PDF dentro do ecossistema Laravel.

---

# 18. Dashboard

O dashboard do MeuHub deve ser simples.

Como existe apenas uma ferramenta no MVP, não tentar criar um dashboard cheio de gráficos.

Exemplo:

```text
Olá, João

Profissional

┌──────────────────────────────┐
│ Currículos                   │
│                              │
│ 2 currículos                 │
│                              │
│ Última atualização           │
│ Currículo Principal          │
│ Hoje às 10:30                │
│                              │
│ [ Criar currículo ]          │
└──────────────────────────────┘
```

O dashboard deve levar rapidamente o usuário à ação principal.

---

# 19. Navegação

A navegação deve ser preparada para o crescimento do MeuHub.

No MVP:

```text
MeuHub

Dashboard

Profissional
└── Currículos

Configurações
```

Não criar navegação para funcionalidades inexistentes.

---

# 20. Design

O MeuHub deve possuir uma identidade visual moderna.

Priorizar:

- simplicidade;
- elegância;
- boa tipografia;
- espaçamento;
- hierarquia visual;
- responsividade;
- consistência;
- acessibilidade.

Evitar aparência de:

- sistema administrativo;
- ERP;
- painel cheio de tabelas;
- formulário governamental.

A sensação deve ser de um produto pessoal moderno.

---

# 21. Responsividade

O sistema deve funcionar em:

- desktop;
- notebook;
- tablet;
- celular.

O editor deve possuir uma experiência adequada para telas menores.

---

# 22. Persistência

Todos os dados do currículo devem ser persistidos no PostgreSQL.

O usuário deve conseguir:

```text
Criar
 ↓
Editar
 ↓
Salvar
 ↓
Sair
 ↓
Voltar
 ↓
Continuar editando
```

Nenhuma informação deve depender apenas do estado do frontend.

---

# 23. Segurança

Currículos possuem informações pessoais.

Garantir:

- autenticação;
- autorização;
- isolamento dos dados por usuário;
- validação;
- proteção contra acesso indevido;
- políticas do Laravel quando apropriado.

Um usuário jamais deve conseguir visualizar ou alterar o currículo de outro usuário.

---

# 24. Modelo conceitual

Uma estrutura inicial pode seguir:

```text
User
 │
 └── Resume
      ├── PersonalInformation
      ├── Experiences
      ├── Educations
      ├── Courses
      ├── Certifications
      ├── Skills
      ├── Languages
      └── AdditionalInformation
```

A estrutura final deve ser definida após analisar o projeto e considerar as necessidades do editor.

Evitar colocar todo o currículo em um único campo JSON se isso prejudicar consultas, validações ou evolução do sistema.

Por outro lado, não normalizar excessivamente sem necessidade.

Escolher uma solução equilibrada.

---

# 25. Futuro

O MeuHub futuramente poderá crescer para:

```text
MeuHub
│
├── Profissional
│   ├── Currículo
│   ├── Carreira
│   └── Projetos
│
├── Pessoal
│
├── Saúde
│   ├── Treinos
│   ├── Atividades físicas
│   └── Hábitos
│
├── Estudos
│
├── Finanças
│
└── Planejamento
    ├── Tarefas
    ├── Objetivos
    └── Agenda
```

Mas essas áreas **não devem fazer parte do MVP**.

Quando forem implementadas, deverão seguir o mesmo conceito:

```text
Categoria
    ↓
Ferramentas especializadas
    ↓
Dados persistidos
```

---

# 26. O que NÃO fazer

Não implementar neste momento:

- categorias personalizadas;
- módulos personalizados;
- IA;
- integração com LinkedIn;
- integração com Google;
- agenda;
- tarefas;
- objetivos;
- hábitos;
- saúde;
- finanças;
- notificações;
- colaboração;
- múltiplos usuários administrativos;
- permissões complexas;
- marketplace;
- múltiplos templates de currículo.

Não criar funcionalidades apenas porque elas fazem parte da visão futura.

---

# 27. Ordem de implementação

## Etapa 1 — Análise

Antes de escrever código:

- analisar o projeto;
- verificar estrutura existente;
- verificar dependências;
- verificar banco;
- verificar autenticação;
- identificar padrões;
- identificar o que pode ser reutilizado.

Não substituir tecnologias ou arquitetura existentes sem necessidade.

---

## Etapa 2 — Fundação

Implementar:

- Laravel;
- Vue;
- Inertia;
- PostgreSQL;
- autenticação;
- layout;
- navegação;
- dashboard.

---

## Etapa 3 — Currículo

Implementar:

- criação;
- listagem;
- edição;
- visualização;
- exclusão;
- duplicação;
- persistência.

---

## Etapa 4 — Editor

Implementar a experiência de documento:

- seções;
- itens;
- edição;
- ordenação;
- formatação;
- preview.

---

## Etapa 5 — PDF

Implementar:

- template;
- renderização;
- exportação.

---

## Etapa 6 — Refinamento

Revisar:

- UX;
- responsividade;
- acessibilidade;
- validações;
- mensagens;
- estados vazios;
- performance;
- testes;
- segurança.

---

# 28. Critério de sucesso

O MVP estará concluído quando um usuário conseguir realizar todo este fluxo:

```text
Entrar no MeuHub
       ↓
Dashboard
       ↓
Profissional
       ↓
Currículos
       ↓
Criar currículo
       ↓
Preencher informações
       ↓
Editar o documento
       ↓
Salvar
       ↓
Voltar posteriormente
       ↓
Continuar edição
       ↓
Visualizar
       ↓
Exportar PDF
```

Essa experiência deve estar sólida antes de qualquer expansão do produto.

---

# 29. Diretriz principal para o agente

Não construa o Life OS inteiro.

Construa o **MeuHub MVP**.

O MeuHub deve começar com uma única coisa, mas fazê-la muito bem:

> **Criação, edição, persistência e exportação de currículos.**

A arquitetura deve estar preparada para receber novas categorias e ferramentas futuramente, mas o produto atual deve permanecer simples.

Priorize:

**Qualidade > quantidade**

**UX > complexidade**

**Produto funcional > arquitetura excessivamente abstrata**

**Código simples > abstrações prematuras**

Antes de cada implementação, pergunte:

> "Isso é necessário para o MVP do MeuHub?"

Se a resposta for não, deixe para uma etapa futura.