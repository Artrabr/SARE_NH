<?php

require_once __DIR__ . "/Reservation.php";

class ReservationDB
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function isReservationConflicting(
        int $slotId,
        string $reservationDate,
        string $startTime,
        string $endTime
    ): bool
    {
        $conflictingReservations = $this->getReservationInThisRange(
            $slotId,
            $reservationDate,
            $startTime,
            $endTime
        );
        return !empty($conflictingReservations);
    }

    public function getReservationInThisRange(
        int $slotId,
        string $reservationDate,
        string $startTime,
        string $endTime
    ): array
    {
        $sql = "SELECT reserved_slot_id
            FROM `reservation`
            WHERE reserved_slot_slot_id = :slot_id
            AND reserved_slot_date = :reservation_date
            AND reserved_slot_status IN ('pending', 'confirmed')
            AND reserved_slot_start_time < :end_time
            AND reserved_slot_end_time > :start_time";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'slot_id' => $slotId,
            'reservation_date' => $reservationDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReservation(
        int $userId,
        int $slotId,
        string $topic,
        string $startTime,
        string $endTime,
        string $reservationDate,
        ?string $reason
        ): Reservation 
    //cria uma reserva
    {
        if ($userId < 1 || $slotId < 1 || trim($topic) === '') {
            throw new InvalidArgumentException('A valid user, room, and non-empty topic are required');
        }

        if (
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $startTime) ||
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $endTime) ||
            $endTime <= $startTime
        ) {
            throw new InvalidArgumentException('A valid reservation time range is required');
        }

        if (
            !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $reservationDate, $dateParts) ||
            !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])
        ) {
            throw new InvalidArgumentException('A valid reservation date is required');
        }

        if ($this->isReservationConflicting($slotId, $reservationDate, $startTime, $endTime)) {
            throw new DomainException('The selected time conflicts with an existing reservation');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO `reservation` (
                reserved_slot_user_id,
                reserved_slot_slot_id,
                reserved_slot_topic,
                reserved_slot_start_time,
                reserved_slot_end_time,
                reserved_slot_date,
                reserved_slot_reason
             ) VALUES (
                :user_id,
                :slot_id,
                :topic,
                :start_time,
                :end_time,
                :reservation_date,
                :reason
             )'
        );
        $stmt->execute([
            'user_id' => $userId,
            'slot_id' => $slotId,
            'topic' => trim($topic),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reservation_date' => $reservationDate,
            'reason' => $reason,
        ]);

        $reservationId = (int) $this->pdo->lastInsertId();

        return new Reservation(
            $reservationId,
            $userId,
            $slotId,
            trim($topic),
            $startTime,
            $endTime,
            $reservationDate,
            $reason,
            date('Y-m-d H:i:s'),
            'pending'
        );
    }

    public function updateReservationStatus(int $id, string $status): bool
    {
        $allowedStatuses = ['pending', 'confirmed', 'rejected', 'cancelled'];
        if ($id < 1 || !in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException('Invalid reservation ID or status');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE `reservation`
             SET reserved_slot_status = ?
             WHERE reserved_slot_id = ?'
        );
        $stmt->execute([$status, $id]);

        return $stmt->rowCount() > 0;
    }

    public function getReservationById(int $id): ?Reservation
    //retorna a reserva por id
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot.reserved_slot_id, reserved_slot.reserved_slot_user_id,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at, reserved_slot.reserved_slot_status
             FROM `reservation` AS reserved_slot
             WHERE reserved_slot.reserved_slot_id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Reservation(
            (int) $row['reserved_slot_id'],
            (int) $row['reserved_slot_user_id'],
            (int) $row['reserved_slot_slot_id'],
            $row['reserved_slot_topic'],
            $row['reserved_slot_start_time'],
            $row['reserved_slot_end_time'],
            $row['reserved_slot_date'],
            $row['reserved_slot_reason'],
            $row['reserved_slot_at'],
            $row['reserved_slot_status']
        );
    }

    public function getUserReservationByUserId(int $userId): array
    //retorna todas as reservas de um usuario ou um array vazio
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot.reserved_slot_id, reserved_slot.reserved_slot_user_id,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at, reserved_slot.reserved_slot_status
             FROM `reservation` AS reserved_slot
             WHERE reserved_slot.reserved_slot_user_id = ?
             ORDER BY reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_start_time'
        );

        $reservations = []; // vetor
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reservations[] = new Reservation(
                (int) $row['reserved_slot_id'],
                (int) $row['reserved_slot_user_id'],
                (int) $row['reserved_slot_slot_id'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time'],
                $row['reserved_slot_date'],
                $row['reserved_slot_reason'],
                $row['reserved_slot_at'],
                $row['reserved_slot_status']
            );
        }

        return $reservations;
    }

    public function getUserReservationByUserIdAndStatus(int $userId, string $status): array
    //retorna todas as reservas de um usuario ou um array vazio
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot.reserved_slot_id, reserved_slot.reserved_slot_user_id,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at, reserved_slot.reserved_slot_status
             FROM `reservation` AS reserved_slot
             WHERE reserved_slot.reserved_slot_user_id = ? 
             AND reserved_slot.reserved_slot_status = ?
             ORDER BY reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_start_time'
        );

        $reservations = []; // vetor
        $stmt->execute([$userId, $status]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reservations[] = new Reservation(
                (int) $row['reserved_slot_id'],
                (int) $row['reserved_slot_user_id'],
                (int) $row['reserved_slot_slot_id'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time'],
                $row['reserved_slot_date'],
                $row['reserved_slot_reason'],
                $row['reserved_slot_at'],
                $row['reserved_slot_status']
            );
        }
        return $reservations;
    }

    public function getUserReservationByUserIdAndStatusAndPeriod(
        int $userId,
        string $status,
        ?string $startDate = null,
        ?string $endDate = null
    ): array
    {
        if ($userId < 1 || !in_array($status, ['pending', 'confirmed', 'rejected', 'cancelled'], true)) {
            throw new InvalidArgumentException('Invalid user ID or reservation status');
        }

        if ($startDate !== null && $endDate !== null && $startDate > $endDate) {
            throw new InvalidArgumentException('The reservation date range is invalid');
        }

        $sql = 'SELECT reserved_slot.reserved_slot_id, reserved_slot.reserved_slot_user_id,
                       reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                       reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                       reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                       reserved_slot.reserved_slot_at, reserved_slot.reserved_slot_status
                FROM `reservation` AS reserved_slot
                WHERE reserved_slot.reserved_slot_user_id = ?
                AND reserved_slot.reserved_slot_status = ?';
        $parameters = [$userId, $status];

        if ($startDate !== null) {
            $sql .= ' AND reserved_slot.reserved_slot_date >= ?';
            $parameters[] = $startDate;
        }

        if ($endDate !== null) {
            $sql .= ' AND reserved_slot.reserved_slot_date <= ?';
            $parameters[] = $endDate;
        }

        $sql .= ' ORDER BY reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_start_time';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);

        $reservations = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reservations[] = new Reservation(
                (int) $row['reserved_slot_id'],
                (int) $row['reserved_slot_user_id'],
                (int) $row['reserved_slot_slot_id'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time'],
                $row['reserved_slot_date'],
                $row['reserved_slot_reason'],
                $row['reserved_slot_at'],
                $row['reserved_slot_status']
            );
        }

        return $reservations;
    }

    public function getAllReservation(): ?array
    //cria uma array com varios objetos reservation
    {
        $stmt = $this->pdo->query(
            'SELECT reserved_slot.reserved_slot_id, reserved_slot.reserved_slot_user_id,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at, reserved_slot.reserved_slot_status
             FROM `reservation` AS reserved_slot
             ORDER BY reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_start_time'
        );

        $reservations = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reservations[] = new Reservation(
                (int) $row['reserved_slot_id'],
                (int) $row['reserved_slot_user_id'],
                (int) $row['reserved_slot_slot_id'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time'],
                $row['reserved_slot_date'],
                $row['reserved_slot_reason'],
                $row['reserved_slot_at'],
                $row['reserved_slot_status']
            );
        }

        return $reservations ?? null;
    }
}
