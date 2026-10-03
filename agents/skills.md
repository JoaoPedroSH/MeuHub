# Skills do projeto

## Implementação

- Laravel concentra rotas, validação, autorização, persistência e geração de PDF.
- Vue + Inertia concentram a interação e os estados da interface.
- PostgreSQL é a fonte persistente dos dados.

## Revisão de interface

Verifique idioma pt-BR, responsividade, foco de teclado, estado vazio, feedback de sucesso/erro e consistência com `docs/padro-visual.md`.

## Segurança

Toda entidade de usuário deve ser filtrada pelo usuário autenticado. Ações de alteração e exclusão devem validar propriedade no servidor; nunca confiar somente na interface.

## Escopo

Novas funcionalidades devem justificar sua presença no MVP e evitar abstrações prematuras. Categorias futuras não devem aparecer como menus vazios.
