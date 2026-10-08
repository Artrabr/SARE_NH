<?php

class Reservation
{
    public function __construct(
        private int $userId,
        private string $email,
        private int $slotId,
        private string $topic,
        private string $startTime,
        private string $endTime,
        private ?string $reason,
        private string $reservedAt
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSlot(): int
    {
        return $this->slotId;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getEndTime(): string
    {
        return $this->endTime;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getReservedAt(): string
    {
        return $this->reservedAt;
    }
}
