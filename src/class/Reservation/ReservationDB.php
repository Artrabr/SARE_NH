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
            'INSERT INTO `reserved_slot` (user_id, slot_id, topic, start_time, end_time, reason)
             VALUES (:user_id, :slot_id, :topic, :start_time, :end_time, :reason)'
        );
        $stmt->execute([
            'user_id' => (int) $userId,
            'slot_id' => $slotId,
            'topic' => trim($topic),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $reason,
        ]);

        $reservationStmt = $this->pdo->prepare(
            'SELECT reserved_at FROM `reserved_slot` WHERE user_id = ? AND slot_id = ?'
        );
        $reservationStmt->execute([(int) $userId, $slotId]);
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
            $reason,
            $reservedAt
        );
    }

    public function getReservationById(int $id): ?Reservation
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot.user_id, `user`.user_email, reserved_slot.slot_id,
                    reserved_slot.topic, reserved_slot.start_time, reserved_slot.end_time,
                    reserved_slot.reason, reserved_slot.reserved_at
             FROM `reserved_slot`
             JOIN `user` ON `user`.user_id = reserved_slot.user_id
             WHERE reserved_slot.user_id = ?
             ORDER BY reserved_slot.reserved_at DESC
             LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Reservation(
            (int) $row['user_id'],
            $row['user_email'],
            (int) $row['slot_id'],
            $row['topic'],
            $row['start_time'],
            $row['end_time'],
            $row['reason'],
            $row['reserved_at']
        );
    }
}
