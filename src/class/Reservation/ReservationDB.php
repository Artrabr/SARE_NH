<?php

require_once __DIR__ . "/Reservation.php";
require_once __DIR__ . "/ReservationSearchResult.php";

class ReservationDB
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* Retorna os intervalos de 45 minutos livres para uma sala na data informada*/
    public function availableTimes(string $date, int $slotId): array
    {
        if (
            $slotId < 1 ||
            !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $dateParts) ||
            !checkdate((int) $dateParts[2], (int) $dateParts[3], (int) $dateParts[1])
        ) {
            throw new InvalidArgumentException('a data ou o id estao errados');
        }

        $stmt = $this->pdo->prepare(
            "SELECT reserved_slot_start_time, reserved_slot_end_time
             FROM `reservation`
             WHERE reserved_slot_date = :date
             AND reserved_slot_slot_id = :slot_id
             AND reserved_slot_status IN ('pending', 'confirmed')"
        );
        $stmt->execute([
            'date' => $date,
            'slot_id' => $slotId,
        ]);

        $results = [];
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $startMinutes = 7 * 60 + 30;
        $endLimitMinutes = 22 * 60;
        $durationMinutes = 45;

        for (
            $start = $startMinutes;
            $start + $durationMinutes <= $endLimitMinutes;
            $start += $durationMinutes
        ) {
            $end = $start + $durationMinutes;
            $isAvailable = true;

            foreach ($reservations as $reservation) {
                [$reservedStartHour, $reservedStartMinute] = array_map(
                    'intval',
                    explode(':', substr($reservation['reserved_slot_start_time'], 0, 5))
                );
                [$reservedEndHour, $reservedEndMinute] = array_map(
                    'intval',
                    explode(':', substr($reservation['reserved_slot_end_time'], 0, 5))
                );
                $reservedStart = $reservedStartHour * 60 + $reservedStartMinute;
                $reservedEnd = $reservedEndHour * 60 + $reservedEndMinute;

                if ($start < $reservedEnd && $end > $reservedStart) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $results[] = [
                    'start_time' => sprintf('%02d:%02d', intdiv($start, 60), $start % 60),
                    'end_time' => sprintf('%02d:%02d', intdiv($end, 60), $end % 60),
                ];
            }
        }

        return $results;
    }

    /*
     Busca reservas que comecam dentro do intervalo informado.
    
     Diferente dos metodos que retornam linhas associativas do PDO, este
     metodo transforma cada resultado em um ReservationSearchResult.
     Assim, os dados retornados nao dependem diretamente dos nomes das
     colunas usadas na consulta SQL.
    
     Exemplo de uso:
    
     $reservationDB = new ReservationDB(Connection::Connect());
     $reservations = $reservationDB->findOccupiedReservations('12:00', '15:00');
    
     foreach ($reservations as $reservation) {
         echo $reservation->getUserName();
         echo $reservation->getSlotName();
         echo $reservation->getTopic();
     }
    
     Os horarios devem estar no formato HH:MM. Se nao houver reservas,
     o retorno sera um array vazio.
    
     se divirta filhao :D
     
     @return ReservationSearchResult[]
     */
    public function findOccupiedReservations(string $timeStart, string $endTime): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                user.user_name,
                user.user_email,
                slot.slot_name,
                slot.slot_institutional_name,
                reservation.reserved_slot_topic,
                reservation.reserved_slot_start_time,
                reservation.reserved_slot_end_time
             FROM `user`
             INNER JOIN reservation
                ON user.user_id = reservation.reserved_slot_user_id
             INNER JOIN slot
                ON slot.slot_id = reservation.reserved_slot_slot_id
             WHERE reservation.reserved_slot_start_time BETWEEN ? AND ?
             ORDER BY reservation.reserved_slot_start_time'
        );
        $stmt->execute([$timeStart, $endTime]);

        $results = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $results[] = new ReservationSearchResult(
                $row['user_name'],
                $row['user_email'],
                $row['slot_name'],
                $row['slot_institutional_name'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time']
            );
        }

        return $results;
    }

    /*
        Busca as reservas de um usuario que comecam dentro do intervalo informado.
        
        Exemplo de uso:
        
        $reservationDB = new ReservationDB(Connection::Connect());
        $reservations = $reservationDB->findOccupiedReservationsByUserId(12, '12:00', '15:00');
        
        foreach ($reservations as $reservation) {
            echo $reservation->getUserName();
            echo $reservation->getSlotName();
            echo $reservation->getTopic();
        }
        
        O primeiro argumento e o ID do usuario. Os horarios devem estar no
        formato HH:MM. Se nao houver reservas, o retorno sera um array vazio.
        
        @return ReservationSearchResult[]
    */
    public function findOccupiedReservationsByUserId(int $userId,string $timeStart,string $endTime): array 
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('A valid user ID is required');
        }

        $stmt = $this->pdo->prepare(
            'SELECT
                user.user_name,
                user.user_email,
                slot.slot_name,
                slot.slot_institutional_name,
                reservation.reserved_slot_topic,
                reservation.reserved_slot_start_time,
                reservation.reserved_slot_end_time
             FROM `user`
             INNER JOIN reservation
                ON user.user_id = reservation.reserved_slot_user_id
             INNER JOIN slot
                ON slot.slot_id = reservation.reserved_slot_slot_id
             WHERE reservation.reserved_slot_start_time BETWEEN ? AND ?
             AND reservation.reserved_slot_user_id = ?
             ORDER BY reservation.reserved_slot_start_time'
        );
        $stmt->execute([$timeStart, $endTime, $userId]);

        $results = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $results[] = new ReservationSearchResult(
                $row['user_name'],
                $row['user_email'],
                $row['slot_name'],
                $row['slot_institutional_name'],
                $row['reserved_slot_topic'],
                $row['reserved_slot_start_time'],
                $row['reserved_slot_end_time']
            );
        }

        return $results;
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

    public function getReservationInThisRange( //apagar esse codigo
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
            'start_time' => $startTime . ':00',
            'end_time' => $endTime . ':00',
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
        $sql = 'SELECT
                reserved_slot_id,
                reserved_slot_user_id,
                reserved_slot_slot_id,
                reserved_slot_topic,
                reserved_slot_start_time,
                reserved_slot_end_time,
                reserved_slot_date,
                reserved_slot_reason,
                reserved_slot_at,
                reserved_slot_status
            FROM `reservation`
            ORDER BY
                CASE
                    WHEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time) >= ? THEN 0
                    ELSE 1
                END ASC,
                CASE
                    WHEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time) >= ?
                    THEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time)
                END DESC,
                CASE
                    WHEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time) < ?
                    THEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time)
                END DESC,
                reserved_slot_id ASC';
        $stmt = $this->pdo->prepare($sql);
        $now = date('Y-m-d H:i:s');
        $stmt->execute([$now, $now, $now]);

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


    /*
    Retorna o ID da reserva mais próxima do momento atual.
    Em caso de empate, prioriza a reserva futura.
    Retorna null quando não há reservas.
    */
    public function getClosestReservation(): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT reserved_slot_id
             FROM `reservation`
             ORDER BY
                ABS(TIMESTAMPDIFF(
                    SECOND,
                    ?,
                    TIMESTAMP(reserved_slot_date, reserved_slot_start_time)
                )) ASC,
                CASE
                    WHEN TIMESTAMP(reserved_slot_date, reserved_slot_start_time) >= ? THEN 0
                    ELSE 1
                END ASC,
                reserved_slot_id ASC
             LIMIT 1'
        );
        $now = date('Y-m-d H:i:s');
        $stmt->execute([$now, $now]);
        $reservationId = $stmt->fetchColumn();

        return $reservationId === false ? null : (int) $reservationId;
    }
}
