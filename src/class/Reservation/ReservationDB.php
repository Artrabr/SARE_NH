<?php

require_once __DIR__ . "/Reservation.php";

class ReservationDB
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createReservation(
        string $email,
        int $slotId,
        string $topic,
        string $startTime,
        string $endTime,
        string $reservationDate,
        ?string $reason
    ): Reservation {
        if ($slotId < 1 || trim($topic) == '') {
            throw new InvalidArgumentException('A valid room and a non-empty topic are required.');
        }

        $userStmt = $this->pdo->prepare('SELECT user_id FROM `user` WHERE user_email = ?');
        $userStmt->execute([$email]);
        $userId = $userStmt->fetchColumn();

        if ($userId == false) {
            throw new InvalidArgumentException('The user email does not exist.');
        }

        $slotStmt = $this->pdo->prepare('SELECT slot_id FROM `slot` WHERE slot_id = ?');
        $slotStmt->execute([$slotId]);
        if ($slotStmt->fetchColumn() == false) {
            throw new InvalidArgumentException('The selected room does not exist.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO `reserved_slot` (
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
            'user_id' => (int) $userId,
            'slot_id' => $slotId,
            'topic' => trim($topic),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reservation_date' => $reservationDate,
            'reason' => $reason,
        ]);

        $reservationId = (int) $this->pdo->lastInsertId();
        $reservationStmt = $this->pdo->prepare(
            'SELECT reserved_slot_at FROM `reserved_slot` WHERE reserved_slot_id = ?'
        );
        $reservationStmt->execute([$reservationId]);
        $reservedAt = $reservationStmt->fetchColumn();

        if ($reservedAt === false) {
            throw new RuntimeException('The reservation was inserted but could not be loaded.');
        }

        return new Reservation(
            (int) $userId,
            $email,
            $slotId,
            trim($topic),
            $startTime,
            $endTime,
            $reservationDate,
            $reason,
            $reservedAt
        );
    }

    public function getReservationById(int $id): ?Reservation
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot.reserved_slot_user_id, `user`.user_email,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at
             FROM `reserved_slot`
             JOIN `user` ON `user`.user_id = reserved_slot.reserved_slot_user_id
             WHERE reserved_slot.reserved_slot_id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Reservation(
            (int) $row['reserved_slot_user_id'],
            $row['user_email'],
            (int) $row['reserved_slot_slot_id'],
            $row['reserved_slot_topic'],
            $row['reserved_slot_start_time'],
            $row['reserved_slot_end_time'],
            $row['reserved_slot_date'],
            $row['reserved_slot_reason'],
            $row['reserved_slot_at']
        );
    }

    public function getAllReservation(): array
    {
        $stmt = $this->pdo->query(
            'SELECT reserved_slot.reserved_slot_user_id, `user`.user_email,
                    reserved_slot.reserved_slot_slot_id, reserved_slot.reserved_slot_topic,
                    reserved_slot.reserved_slot_start_time, reserved_slot.reserved_slot_end_time,
                    reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_reason,
                    reserved_slot.reserved_slot_at
             FROM `reserved_slot`
             JOIN `user` ON `user`.user_id = reserved_slot.reserved_slot_user_id
             ORDER BY reserved_slot.reserved_slot_date, reserved_slot.reserved_slot_start_time'
        );

        $reservations = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reservations[] = new Reservation(
                (int) $row['reserved_slot_user_id'],
                $row['user_email'],
                (int) $row['reserved_slot_slot_id'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time'],
                $row['reserved_slot_date'],
                $row['reserved_slot_reason'],
                $row['reserved_slot_at']
            );
        }

        return $reservations;
    }
}
