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

    public function confirmReservation(int $id): bool
    {
        return $this->updatePendingReservationStatus($id, 'confirmed');
    }

    public function rejectReservation(int $id): bool
    {
        return $this->updatePendingReservationStatus($id, 'rejected');
    }

    public function cancelReservation(int $id): bool
    {
        return $this->updatePendingReservationStatus($id, 'cancelled');
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

    public function getAllReservation(): array
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

        return $reservations;
    }

    private function updatePendingReservationStatus(int $id, string $status): bool
    //atualiza para padding o pedido de reserva ou oq tu por no status
    //retorna true ou false se der certo ou errado
    {
        $stmt = $this->pdo->prepare(
            'UPDATE `reservation`
             SET reserved_slot_status = ?
             WHERE reserved_slot_id = ? AND reserved_slot_status = ?'
        );
        $stmt->execute([$status, $id, 'pending']);

        return $stmt->rowCount() > 0;
    }
}
