# Arquitetura do MeuHub

O produto é um monólito Laravel + Vue + Inertia com PostgreSQL. O domínio segue a direção `Categoria → Ferramenta → Dados`.

No estado atual, a categoria Profissional contém Currículos; o espaço pessoal também oferece Anotações e Agenda, entidades independentes e preparadas para vínculos futuros. A agenda pode apontar opcionalmente para uma anotação, mas notas não possuem subgrupos.

Modelos devem manter relações pequenas e explícitas. Use políticas ou verificações de propriedade no servidor, migrations reversíveis e testes de feature para fluxos de usuário.
