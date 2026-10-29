<?php

class User{
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $category;
    protected bool $admin;

    public function __construct(int $id, string $name, string $email, string $category, bool $admin = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->category = $category;
        $this->admin = $admin;
    }
    
    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getCategory(): string{
        return $this->category;
    }

    public function isAdmin(): bool{
        return $this->admin;
    }
}