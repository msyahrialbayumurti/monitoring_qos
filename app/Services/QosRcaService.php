<?php

namespace App\Services;

class QosRcaService
{
    /**
     * Rule-Based Root Cause Analysis
     *
     * Indeks:
     * 4 = Sangat Baik
     * 3 = Baik
     * 2 = Sedang
     * 1 = Buruk
     */
    public function analyze(
        array $classification,
        ?float $targetPacketLoss = null,
        bool $pingTimeoutRepeated = false
    ): array {
        $condition =
            $classification['condition']
            ?? null;

        $throughputIndex =
            $classification['throughput_index']
            ?? null;

        $delayIndex =
            $classification['delay_index']
            ?? null;

        $jitterIndex =
            $classification['jitter_index']
            ?? null;

        $packetLossIndex =
            $classification['packet_loss_index']
            ?? null;


        /*
        |--------------------------------------------------------------------------
        | Validasi data
        |--------------------------------------------------------------------------
        */

        if (
            $condition === null ||
            $throughputIndex === null ||
            $delayIndex === null ||
            $jitterIndex === null ||
            $packetLossIndex === null
        ) {
            return [
                'required' => false,
                'condition' => $condition,
                'summary' =>
                    'Data QoS belum cukup untuk melakukan RCA.',
                'causes' => [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Jika QoS BAIK
        |--------------------------------------------------------------------------
        */

        if ($condition === 'BAIK') {
            return [
                'required' => false,
                'condition' => 'BAIK',
                'summary' =>
                    'Kualitas jaringan berada pada kondisi baik.',
                'causes' => [],
            ];
        }


        $causes = [];


        /*
        |--------------------------------------------------------------------------
        | STATUS PARAMETER
        |--------------------------------------------------------------------------
        */

        $jitterBuruk =
            $jitterIndex === 1;

        $delayBurukAtauSedang =
            in_array(
                $delayIndex,
                [1, 2],
                true
            );

        $throughputBaik =
            in_array(
                $throughputIndex,
                [3, 4],
                true
            );


        /*
        |--------------------------------------------------------------------------
        | RULE 1
        |--------------------------------------------------------------------------
        |
        | Jitter Buruk
        | AND
        | Delay Buruk/Sedang
        | AND
        | Throughput Baik
        |
        */

        if (
            $jitterBuruk &&
            $delayBurukAtauSedang &&
            $throughputBaik
        ) {
            $causes[] = [
                'parameter' =>
                    'Jitter + Delay',

                'rule' =>
                    'JITTER = BURUK AND DELAY = BURUK/SEDANG AND THROUGHPUT = BAIK',

                'cause' =>
                    'Kepadatan trafik pada access point atau interferensi frekuensi wireless pada jam sibuk.',

                'severity' =>
                    'TINGGI',

                'recommendation' =>
                    'Alihkan sebagian perangkat ke access point cadangan dan evaluasi kanal frekuensi Wi-Fi.',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RULE 2
        |--------------------------------------------------------------------------
        |
        | Packet Loss QoS Buruk
        | AND
        | Throughput Buruk/Sedang
        |
        | Catatan:
        | Rule ini menggunakan packet loss hasil Speedtest,
        | bukan packet loss ping target.
        |
        */

        $packetLossBuruk =
            $packetLossIndex === 1;

        $throughputBurukAtauSedang =
            in_array(
                $throughputIndex,
                [1, 2],
                true
            );

        if (
            $packetLossBuruk &&
            $throughputBurukAtauSedang
        ) {
            $causes[] = [
                'parameter' =>
                    'Packet Loss + Throughput',

                'rule' =>
                    'PACKET LOSS = BURUK AND THROUGHPUT = BURUK/SEDANG',

                'cause' =>
                    'Bandwidth congestion atau kualitas sinyal fisik yang lemah seperti redaman dan jarak terhadap access point.',

                'severity' =>
                    'TINGGI',

                'recommendation' =>
                    'Batasi jumlah perangkat per access point, periksa penempatan access point, dan pertimbangkan penambahan bandwidth.',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RULE 3
        |--------------------------------------------------------------------------
        |
        | Delay Buruk
        | AND
        | Jitter Baik
        | AND
        | Packet Loss Baik
        | AND
        | Throughput Baik
        |
        */

        $delayBuruk =
            $delayIndex === 1;

        $jitterBaik =
            in_array(
                $jitterIndex,
                [3, 4],
                true
            );

        $packetLossBaik =
            in_array(
                $packetLossIndex,
                [3, 4],
                true
            );

        if (
            $delayBuruk &&
            $jitterBaik &&
            $packetLossBaik &&
            $throughputBaik
        ) {
            $causes[] = [
                'parameter' =>
                    'Delay',

                'rule' =>
                    'DELAY = BURUK AND JITTER = BAIK AND PACKET LOSS = BAIK AND THROUGHPUT = BAIK',

                'cause' =>
                    'Gangguan pada sisi penyedia layanan internet (ISP) atau jalur backbone.',

                'severity' =>
                    'TINGGI',

                'recommendation' =>
                    'Lakukan pengecekan status layanan ke ISP dan jalankan pengujian ping ke beberapa titik pembanding.',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RULE 4
        |--------------------------------------------------------------------------
        |
        | Ping timeout berulang
        | OR
        | Packet loss ping target mendekati 100%.
        |
        | Data yang digunakan berasal dari target_ping,
        | bukan packet loss Speedtest.
        |
        */

        $targetPacketLossMendekati100 =
            $targetPacketLoss !== null &&
            $targetPacketLoss >= 90;

        if (
            $pingTimeoutRepeated ||
            $targetPacketLossMendekati100
        ) {
            $causes[] = [
                'parameter' =>
                    'Ping / Packet Loss Target',

                'rule' =>
                    'PING TIMEOUT BERULANG OR PACKET LOSS TARGET >= 90%',

                'cause' =>
                    'Perangkat jaringan seperti access point atau router mati, kabel terputus, atau IP target tidak terjangkau.',

                'severity' =>
                    'KRITIS',

                'recommendation' =>
                    'Periksa kondisi fisik perangkat keras, lakukan restart perangkat jaringan, dan cek konfigurasi IP target.',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | RULE 5
        |--------------------------------------------------------------------------
        |
        | Semua parameter QoS baik.
        |
        */

        $semuaBaik =
            $throughputBaik &&
            in_array(
                $delayIndex,
                [3, 4],
                true
            ) &&
            $jitterBaik &&
            $packetLossBaik;

        if (empty($causes) && $semuaBaik) {
            return [
                'required' => false,
                'condition' => $condition,
                'summary' =>
                    'Tidak ditemukan indikasi gangguan pada parameter QoS.',
                'causes' => [],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Tidak ada rule spesifik
        |--------------------------------------------------------------------------
        */

        if (empty($causes)) {
    $indexes = [
        'Throughput' => $throughputIndex,
        'Delay' => $delayIndex,
        'Jitter' => $jitterIndex,
        'Packet Loss' => $packetLossIndex,
    ];

    // Cari parameter dengan indeks terendah.
    $lowestIndex = min($indexes);
    $lowestParameters = array_keys(
        array_filter(
            $indexes,
            fn ($index) => $index === $lowestIndex
        )
    );

    $parameter = implode(', ', $lowestParameters);

    $causes[] = [
        'parameter' => $parameter,
        'rule' => 'PARAMETER DENGAN INDEKS TERENDAH',
        'cause' => 'Indikasi gangguan diprioritaskan pada parameter '
            . $parameter
            . ' karena memiliki indeks QoS terendah. Pemeriksaan lanjutan diperlukan untuk memastikan penyebab sebenarnya.',
        'severity' => $condition === 'BURUK'
            ? 'TINGGI'
            : 'SEDANG',
        'recommendation' => 'Periksa parameter '
            . $parameter
            . ', evaluasi kondisi perangkat dan jalur jaringan, lalu lakukan pengukuran ulang untuk memastikan sumber gangguan.',
    ];
}


        /*
        |--------------------------------------------------------------------------
        | Ringkasan RCA
        |--------------------------------------------------------------------------
        */

        $parameterNames =
            array_map(
                function ($cause) {
                    return $cause['parameter'];
                },
                $causes
            );

        $summary =
            'Indikasi masalah ditemukan pada parameter: ' .
            implode(', ', $parameterNames) .
            '.';


        return [
            'required' => true,
            'condition' => $condition,
            'summary' => $summary,
            'causes' => $causes,
        ];
    }
}
