<?php

class User {
    private ?int $Id = null;
    private $Password;
    private $Email;
    public $Name;
    public $Position; //professor ou coordenador ou bolsista e ect...

    public function __construct($Name,$Email,$Password,$Position){
        $this->Id = $c;
        $this->Name = $Name;
        $this->Email = $Email;
        $this->Password = $Password;
        $this->Position = $Position;
    }

    public function getId(){
    return $this->Id;
    }

    public function getName(){
        return $this->Name;
    }

    public function getEmail(){
        return $this->Email;
    }

    public function getPosition(){
        return $this->Position;
    }
}

/*
<?php

// Instancia o objeto (sem o ID ainda, já que ele não existe no banco)
$user = new User("João", "joao@email.com", "senha123", "Professor");

// Conexão com o banco de dados (PDO)
$pdo = new PDO("mysql:host=localhost;dbname=seu_banco", "usuario", "senha");

// Prepara a consulta SEM passar o ID
$sql = "INSERT INTO usuarios (nome, email, senha, cargo) VALUES (:nome, :email, :senha, :cargo)";
$stmt = $pdo->prepare($sql);

// Executa passando os dados
$stmt->execute([
    ':nome'  => $user->getName(),
    ':email' => $user->getEmail(),
    // Lembre-se de salvar o HASH da senha no banco, nunca a senha pura
    ':senha' => password_hash("senha123", PASSWORD_DEFAULT), 
    ':cargo' => $user->getPosition()
]);

// O PDO pega o ID gerado automaticamente pelo MySQL!
$idGerado = $pdo->lastInsertId();

echo "Usuário cadastrado com o ID: " . $idGerado;
*/