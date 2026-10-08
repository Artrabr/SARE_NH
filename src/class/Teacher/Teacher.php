<?php

class Teacher extends User {
    private string $subject;

    public function __construct(int $id, string $name, string $email, string $category, string $subject)
    {
        parent::__construct($id, $name, $email, $category);
        $this->subject = $subject;
    }

    public function getSubject(): string {
        return $this->subject;
    }
    
    //devolve o assinto padrao de reserva
    public function getDefaultReservationTopic(): string {
        return "Aula de {$this->subject}";
    }
}