<?php

class User{
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $category;

    public function __construct(int $id, string $name, string $email, string $category)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->category = $category;
    }
    
    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getCategory(){
        return $this->category;
    }
}