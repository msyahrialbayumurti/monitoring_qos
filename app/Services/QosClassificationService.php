<?php

namespace App\Services;

class QosClassificationService
{
    /**
     * Menentukan indeks TIPHON berdasarkan persentase throughput
     * terhadap kapasitas bandwidth.
     *
     * Kriteria berdasarkan proposal:
     *
     * 100%             = indeks 4
     * 75% - 99%        = indeks 3
     * 50% - 74%        = indeks 2
     * < 50%            = indeks 1
     */
    public function throughputIndex(
        ?float $throughput,
        ?float $bandwidth
    ): ?int {
        if (
            $throughput === null ||
            $bandwidth === null ||
            $bandwidth <= 0
        ) {
            return null;
        }

        $percentage = ($throughput / $bandwidth) * 100;

        if ($percentage >= 100) {
            return 4;
        }

        if ($percentage >= 75) {
            return 3;
        }

        if ($percentage >= 50) {
            return 2;
        }

        return 1;
    }


    /**
     * Menentukan indeks TIPHON berdasarkan delay.
     *
     * < 150 ms       = indeks 4
     * 150 - 300 ms   = indeks 3
     * 300 - 450 ms   = indeks 2
     * > 450 ms       = indeks 1
     */
    public function delayIndex(?float $delay): ?int
    {
        if ($delay === null) {
            return null;
        }

        if ($delay < 150) {
            return 4;
        }

        if ($delay <= 300) {
            return 3;
        }

        if ($delay <= 450) {
            return 2;
        }

        return 1;
    }


    /**
     * Menentukan indeks TIPHON berdasarkan jitter.
     *
     * 0 ms           = indeks 4
     * > 0 - 75 ms    = indeks 3
     * > 75 - 125 ms  = indeks 2
     * > 125 - 225 ms = indeks 1
     */
    public function jitterIndex(?float $jitter): ?int
    {
        if ($jitter === null) {
            return null;
        }

        if ($jitter == 0) {
            return 4;
        }

        if ($jitter <= 75) {
            return 3;
        }

        if ($jitter <= 125) {
            return 2;
        }

        if ($jitter <= 225) {
            return 1;
        }

        return 1;
    }


    /**
     * Menentukan indeks TIPHON berdasarkan packet loss.
     *
     * 0%          = indeks 4
     * > 0 - 3%    = indeks 3
     * > 3 - 15%   = indeks 2
     * > 15 - 25%  = indeks 1
     *
     * Packet loss > 25% juga dikategorikan indeks 1.
     */
    public function packetLossIndex(?float $packetLoss): ?int
    {
        if ($packetLoss === null) {
            return null;
        }

        if ($packetLoss == 0) {
            return 4;
        }

        if ($packetLoss <= 3) {
            return 3;
        }

        if ($packetLoss <= 15) {
            return 2;
        }

        return 1;
    }


    /**
     * Menghitung indeks QoS berdasarkan rata-rata
     * dari empat parameter QoS.
     */
    public function calculateIndex(
        ?int $throughputIndex,
        ?int $delayIndex,
        ?int $jitterIndex,
        ?int $packetLossIndex
    ): ?float {
        $indexes = [
            $throughputIndex,
            $delayIndex,
            $jitterIndex,
            $packetLossIndex,
        ];

        /*
        |--------------------------------------------------------------------------
        | Semua parameter harus tersedia
        |--------------------------------------------------------------------------
        */

        if (in_array(null, $indexes, true)) {
            return null;
        }

        return round(
            array_sum($indexes) / 4,
            2
        );
    }


    /**
     * Menentukan kondisi akhir QoS.
     *
     * 3.50 - 4.00 = BAIK
     * 2.50 - 3.49 = SEDANG
     * 1.00 - 2.49 = BURUK
     */
    public function classify(?float $index): ?string
    {
        if ($index === null) {
            return null;
        }

        if ($index >= 3.50) {
            return 'BAIK';
        }

        if ($index >= 2.50) {
            return 'SEDANG';
        }

        return 'BURUK';
    }


    /**
     * Menghasilkan seluruh hasil klasifikasi QoS.
     */
    public function analyze(
        ?float $throughput,
        ?float $bandwidth,
        ?float $delay,
        ?float $jitter,
        ?float $packetLoss
    ): array {
        $throughputIndex =
            $this->throughputIndex(
                $throughput,
                $bandwidth
            );

        $delayIndex =
            $this->delayIndex(
                $delay
            );

        $jitterIndex =
            $this->jitterIndex(
                $jitter
            );

        $packetLossIndex =
            $this->packetLossIndex(
                $packetLoss
            );

        $qosIndex =
            $this->calculateIndex(
                $throughputIndex,
                $delayIndex,
                $jitterIndex,
                $packetLossIndex
            );

        $condition =
            $this->classify(
                $qosIndex
            );

        return [
            'throughput_index' => $throughputIndex,
            'delay_index' => $delayIndex,
            'jitter_index' => $jitterIndex,
            'packet_loss_index' => $packetLossIndex,
            'qos_index' => $qosIndex,
            'condition' => $condition,
        ];
    }
}
