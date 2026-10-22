# SARE_NH
sistema de agendamento e reserva de espaços

## Configuração local

O `bootstrap.php` centraliza os caminhos do projeto, a sessão, o fuso horário e as configurações do banco de dados. Os valores padrão apontam para o MySQL local (`sarenh`, usuário `root`, sem senha).

Para usar credenciais diferentes, crie `db_constants.php` na raiz do projeto. Esse arquivo é ignorado pelo Git:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'sarenh');
define('DB_CHARSET', 'utf8mb4');
define('DB_USERNAME', 'seu_usuario');
define('DB_PASSWORD', 'sua_senha');
```
