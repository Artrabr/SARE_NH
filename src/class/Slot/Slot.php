<?php

class Slot{
    public int $id;
    public string $name;
    public string $institutionalName;
    public string $description;

    public function __construct(int $id, string $name, string $institutionalName, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->institutionalName = $institutionalName;
        $this->description = $description;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getInstitutionalName(): string{
        return $this->institutionalName;
    }

    public function getDescription(): string{
        return $this->description;
    }
}