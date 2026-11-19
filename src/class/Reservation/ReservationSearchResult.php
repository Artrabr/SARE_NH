<?php
class ReservationSearchResult
{
    public function __construct(
        private string $userName,
        private string $userEmail,
        private string $slotName,
        private string $slotInstitutionalName,
        private string $topic,
        private string $startTime,
        private string $endTime
    ) {
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getSlotName(): string
    {
        return $this->slotName;
    }

    public function getSlotInstitutionalName(): string
    {
        return $this->slotInstitutionalName;
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
}
