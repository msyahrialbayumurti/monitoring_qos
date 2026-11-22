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
        bool $pingTimeoutRepeated = false,
        bool $speedtestSucceeded = true
    ): array {
        $condition = $classification['condition'] ?? null;
        $throughputIndex = $classification['throughput_index'] ?? null;
        $delayIndex = $classification['delay_index'] ?? null;
        $jitterIndex = $classification['jitter_index'] ?? null;
        $packetLossIndex = $classification['packet_loss_index'] ?? null;

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
                'summary' => 'Data QoS belum cukup untuk melakukan RCA.',
                'causes' => [],
            ];
        }

        if ($condition === 'BAIK') {
            return [
                'required' => false,
                'condition' => 'BAIK',
                'summary' => 'Seluruh parameter QoS berada pada kategori BAIK. Tidak ditemukan indikasi gangguan.',
                'causes' => [],
            ];
        }

        $causes = [];

        $throughputBuruk = $throughputIndex === 1;
        $throughputBurukAtauSedang = in_array($throughputIndex, [1, 2], true);
        $throughputBaik = in_array($throughputIndex, [3, 4], true);

        $delayBuruk = $delayIndex === 1;
        $delayBurukAtauSedang = in_array($delayIndex, [1, 2], true);
        $delayBaik = in_array($delayIndex, [3, 4], true);

        $jitterBuruk = $jitterIndex === 1;
        $jitterBurukAtauSedang = in_array($jitterIndex, [1, 2], true);
        $jitterBaik = in_array($jitterIndex, [3, 4], true);

        $packetLossBuruk = $packetLossIndex === 1;
        $packetLossBurukAtauSedang = in_array($packetLossIndex, [1, 2], true);
        $packetLossBaik = in_array($packetLossIndex, [3, 4], true);

        /*
        |--------------------------------------------------------------------------
        | Rule 1 - Jitter + Delay
        |--------------------------------------------------------------------------
        */
        if ($jitterBuruk && $delayBurukAtauSedang && $throughputBaik) {
            $causes[] = $this->cause(
                1,
                'Jitter + Delay',
                'Jitter + Delay',
                'JITTER = BURUK AND DELAY = BURUK/SEDANG AND THROUGHPUT = BAIK',
                'Kepadatan trafik pada access point atau interferensi frekuensi wireless pada jam sibuk.',
                'TINGGI',
                'Alihkan sebagian perangkat ke access point cadangan dan evaluasi kanal frekuensi Wi-Fi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 2 - Packet Loss + Throughput
        |--------------------------------------------------------------------------
        */
        if ($packetLossBuruk && $throughputBurukAtauSedang) {
            $causes[] = $this->cause(
                2,
                'Packet Loss + Throughput',
                'Packet Loss + Throughput',
                'PACKET LOSS = BURUK AND THROUGHPUT = BURUK/SEDANG',
                'Bandwidth congestion atau kualitas sinyal fisik yang lemah seperti redaman dan jarak terhadap access point.',
                'TINGGI',
                'Batasi jumlah perangkat per access point, periksa penempatan access point, dan pertimbangkan penambahan bandwidth.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 3 - Delay
        |--------------------------------------------------------------------------
        */
        if ($delayBuruk && $jitterBaik && $packetLossBaik && $throughputBaik) {
            $causes[] = $this->cause(
                3,
                'Delay',
                'Delay',
                'DELAY = BURUK AND JITTER = BAIK AND PACKET LOSS = BAIK AND THROUGHPUT = BAIK',
                'Gangguan pada sisi penyedia layanan internet (ISP) atau jalur backbone.',
                'TINGGI',
                'Lakukan pengecekan status layanan ke ISP dan jalankan pengujian ping ke beberapa titik pembanding.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 4 - Ping / Packet Loss Target
        |--------------------------------------------------------------------------
        |
        | Ping timeout saja tidak cukup jika speedtest masih berhasil, karena
        | target ping bisa saja tidak terjangkau tanpa internet benar-benar mati.
        |--------------------------------------------------------------------------
        */
        $targetPacketLossMendekati100 =
            $targetPacketLoss !== null &&
            $targetPacketLoss >= 90;

        $pingOutage =
            $pingTimeoutRepeated &&
            ! $speedtestSucceeded;

        if ($targetPacketLossMendekati100 || $pingOutage) {
            $causes[] = $this->cause(
                4,
                'Ping / Packet Loss Target',
                'Ping / Packet Loss Target',
                'PING TIMEOUT BERULANG OR PACKET LOSS TARGET >= 90%',
                'Perangkat jaringan seperti access point atau router mati, kabel terputus, atau IP target tidak terjangkau.',
                'KRITIS',
                'Periksa kondisi fisik perangkat keras, lakukan restart perangkat jaringan, dan cek konfigurasi IP target.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 5 - Seluruh parameter BAIK
        |--------------------------------------------------------------------------
        */
        $semuaBaik =
            $throughputBaik &&
            $delayBaik &&
            $jitterBaik &&
            $packetLossBaik;

        if (empty($causes) && $semuaBaik) {
            return [
                'required' => false,
                'condition' => $condition,
                'summary' => 'Seluruh parameter QoS berada pada kategori BAIK. Tidak ditemukan indikasi gangguan.',
                'causes' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Rule tambahan untuk kombinasi yang belum dicakup Tabel 3.7
        |--------------------------------------------------------------------------
        */
        if (empty($causes)) {
            if ($throughputBurukAtauSedang && $delayBaik && $jitterBaik && $packetLossBaik) {
                $causes[] = $this->cause(
                    6,
                    'Throughput',
                    'Throughput',
                    'THROUGHPUT = BURUK/SEDANG AND DELAY = BAIK AND JITTER = BAIK AND PACKET LOSS = BAIK',
                    'Bandwidth tidak sesuai dengan paket langganan, kongesti lokal, atau pembatasan/limit dari ISP.',
                    $throughputBuruk ? 'TINGGI' : 'SEDANG',
                    'Uji speedtest langsung di router, cek QoS/limiter dan beban perangkat, bandingkan hasil dengan bandwidth paket, lalu pertimbangkan penambahan bandwidth.'
                );
            } elseif ($throughputBurukAtauSedang && $delayBurukAtauSedang && $jitterBaik && $packetLossBaik) {
                $causes[] = $this->cause(
                    7,
                    'Throughput + Delay',
                    'Throughput + Delay',
                    'THROUGHPUT = BURUK/SEDANG AND DELAY = BURUK/SEDANG AND JITTER = BAIK AND PACKET LOSS = BAIK',
                    'Kongesti jaringan lokal atau penurunan kualitas jalur ISP yang menyebabkan throughput rendah dan delay tinggi.',
                    'TINGGI',
                    'Cek beban trafik dan QoS/limiter, uji speedtest langsung di router, lalu eskalasi ke ISP jika delay tetap tinggi.'
                );
            } elseif ($jitterBuruk && $throughputBurukAtauSedang) {
                $causes[] = $this->cause(
                    8,
                    'Jitter + Throughput',
                    'Jitter + Throughput',
                    'JITTER = BURUK AND THROUGHPUT = BURUK/SEDANG',
                    'Interferensi frekuensi wireless atau kepadatan trafik access point disertai keterbatasan bandwidth.',
                    'TINGGI',
                    'Evaluasi kanal Wi-Fi, pindahkan sebagian perangkat ke access point cadangan, kurangi beban AP, dan cek bandwidth uplink.'
                );
            } elseif ($packetLossIndex === 2 && $throughputBurukAtauSedang) {
                $causes[] = $this->cause(
                    9,
                    'Packet Loss + Throughput',
                    'Packet Loss + Throughput',
                    'PACKET LOSS = SEDANG AND THROUGHPUT = BURUK/SEDANG',
                    'Kongesti bandwidth atau penurunan kualitas sinyal fisik yang mulai menyebabkan packet loss.',
                    'SEDANG',
                    'Batasi jumlah perangkat per access point, cek kabel dan penempatan AP, lalu pertimbangkan penambahan bandwidth.'
                );
            } elseif ($jitterBuruk && $delayBaik && $throughputBaik && $packetLossBaik) {
                $causes[] = $this->cause(
                    10,
                    'Jitter',
                    'Jitter',
                    'JITTER = BURUK AND DELAY = BAIK AND THROUGHPUT = BAIK AND PACKET LOSS = BAIK',
                    'Interferensi frekuensi wireless atau kepadatan trafik pada access point.',
                    'SEDANG',
                    'Evaluasi kanal Wi-Fi, pindahkan perangkat ke access point cadangan, dan cek beban access point.'
                );
            } elseif ($packetLossBurukAtauSedang && $throughputBaik && $delayBaik && $jitterBaik) {
                $causes[] = $this->cause(
                    11,
                    'Packet Loss',
                    'Packet Loss',
                    'PACKET LOSS = BURUK/SEDANG AND DELAY = BAIK AND JITTER = BAIK AND THROUGHPUT = BAIK',
                    'Gangguan fisik jalur, kualitas sinyal wireless, atau kongesti sesaat pada jaringan.',
                    $packetLossBuruk ? 'TINGGI' : 'SEDANG',
                    'Cek kabel dan konektor, cek penempatan/interferensi access point, lalu ping beberapa target untuk memastikan lokasi gangguan.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Rule 12 - Kombinasi parameter lain
        |--------------------------------------------------------------------------
        */
        if (empty($causes)) {
            $badParameters = [];

            if ($throughputBurukAtauSedang) {
                $badParameters[] = 'Throughput';
            }

            if ($delayBurukAtauSedang) {
                $badParameters[] = 'Delay';
            }

            if ($jitterBurukAtauSedang) {
                $badParameters[] = 'Jitter';
            }

            if ($packetLossBurukAtauSedang) {
                $badParameters[] = 'Packet Loss';
            }

            $parameter = empty($badParameters)
                ? 'QoS'
                : implode(' + ', $badParameters);

            $causes[] = $this->cause(
                12,
                'Kombinasi Parameter',
                $parameter,
                'TIDAK ADA RULE SPESIFIK; PARAMETER TIDAK BAIK: ' . $parameter,
                'Kombinasi penurunan beberapa parameter QoS: '
                    . $parameter
                    . '. Penyebab belum dapat dipastikan dari satu rule saja.',
                $condition === 'BURUK' ? 'TINGGI' : 'SEDANG',
                'Lakukan pemeriksaan bertahap pada parameter '
                    . $parameter
                    . '; cek beban perangkat/AP, jalur kabel, dan kualitas layanan ISP.',
                true
            );
        }

        $parameterNames = array_values(array_unique(array_map(
            fn (array $cause) => $cause['parameter'],
            $causes
        )));

        $ruleLabels = array_values(array_unique(array_map(
            fn (array $cause) => $cause['rule_label'],
            $causes
        )));

        return [
            'required' => true,
            'condition' => $condition,
            'summary' => 'Indikasi masalah ditemukan pada parameter: '
                . implode(', ', $parameterNames)
                . ' (' . implode(', ', $ruleLabels) . ').',
            'causes' => $causes,
        ];
    }

    private function cause(
        int $ruleNumber,
        string $ruleName,
        string $parameter,
        string $condition,
        string $cause,
        string $severity,
        string $recommendation,
        bool $isFallback = false
    ): array {
        return [
            'rule_number' => $ruleNumber,
            'rule_label' => 'Rule ' . $ruleNumber,
            'rule_name' => $ruleName,
            'parameter' => $parameter,
            'condition' => $condition,
            'rule' => $condition,
            'cause' => $cause,
            'severity' => $severity,
            'recommendation' => $recommendation,
            'is_fallback' => $isFallback,
        ];
    }
}
