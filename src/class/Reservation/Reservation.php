<?php

class Reservation
{
    public function __construct(
        private int $id,
        private int $userId,
        private int $slotId,
        private string $topic,
        private string $startTime,
        private string $endTime,
        private string $reservationDate,
        private ?string $reason,
        private string $reservedAt,
        private string $status = 'pending'
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
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

    public function getReservationDate(): string
    {
        return $this->reservationDate;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getReservedAt(): string
    {
        return $this->reservedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
