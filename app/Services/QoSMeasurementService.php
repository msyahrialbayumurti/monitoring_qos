<?php

namespace App\Services;

class QoSMeasurementService
{
    /**
     * Melakukan pengukuran QoS.
     *
     * Sumber data:
     * - Throughput : Speedtest download
     * - Delay      : Speedtest latency
     * - Jitter     : Speedtest jitter
     * - Packet Loss: Speedtest packet loss
     * - Ping       : Ping ke target IP
     */
    public function measure(string $ip): array
    {
        // Pengukuran ping ke target jaringan
        $pingResult = $this->measurePing($ip);

        // Pengukuran internet menggunakan Speedtest
        $speedtestResult = $this->runSpeedtest();

        /*
         * Packet loss utama menggunakan hasil Speedtest.
         *
         * Jika Speedtest tidak memberikan packet loss,
         * gunakan packet loss dari ping target sebagai fallback.
         */
        $packetLoss = $speedtestResult['packet_loss'];

        if ($packetLoss === null) {
            $packetLoss = $pingResult['packet_loss'];
        }

        /*
         * Pengukuran dianggap berhasil jika Speedtest
         * menghasilkan result JSON.
         */
        $success = $speedtestResult['success'];

        return [
            'success' => $success,

            /*
             * Throughput dari hasil download Speedtest.
             */
            'throughput' => $speedtestResult['throughput'],

            /*
             * Ping menggunakan hasil ping ke target IP.
             */
            'ping' => $pingResult['ping'],

            /*
             * Delay menggunakan latency Speedtest.
             */
            'delay' => $speedtestResult['ping'],

            /*
             * Jitter dari Speedtest.
             */
            'jitter' => $speedtestResult['jitter'],

            /*
             * Packet loss utama.
             */
            'packet_loss' => $packetLoss,

            /*
             * Data tambahan untuk pemeriksaan teknis.
             */
            'target_ping' => $pingResult['ping'],

            'target_packet_loss' => $pingResult['packet_loss'],

            'target_success' => $pingResult['success'],

            /*
             * Simpan hasil mentah untuk debugging/audit.
             */
            'raw_output' => $this->buildRawOutput(
                $pingResult,
                $speedtestResult,
                $packetLoss
            ),
        ];
    }

    /**
     * Fungsi publik untuk melakukan ping saja.
     */
    public function ping(string $ip): array
    {
        return $this->measurePing($ip);
    }

    /**
     * Melakukan ping Windows sebanyak 4 kali.
     */
    private function measurePing(string $ip): array
    {
        $command = 'ping -n 4 ' . escapeshellarg($ip);

        $output = [];

        $resultCode = 1;

        exec(
            $command,
            $output,
            $resultCode
        );

        $outputText = implode(
            "\n",
            $output
        );

        /*
         * ==========================================
         * PACKET LOSS
         * ==========================================
         *
         * Contoh output Windows:
         *
         * Lost = 0 (0% loss),
         */

        $packetLoss = null;

        if (
            preg_match(
                '/\((\d+)%\s*loss\)/i',
                $outputText,
                $matches
            )
        ) {
            $packetLoss = (float) $matches[1];
        }

        /*
         * Fallback apabila format output berbeda.
         */
        if ($packetLoss === null) {

            if (
                preg_match(
                    '/(\d+)%\s*loss/i',
                    $outputText,
                    $matches
                )
            ) {
                $packetLoss = (float) $matches[1];
            }
        }

        /*
         * ==========================================
         * AVERAGE PING
         * ==========================================
         *
         * Contoh:
         *
         * Average = 1ms
         */

        $averagePing = null;

        if (
            preg_match(
                '/Average\s*=\s*(\d+)\s*ms/i',
                $outputText,
                $matches
            )
        ) {
            $averagePing = (float) $matches[1];
        }

        /*
         * ==========================================
         * DELAY SETIAP REPLY
         * ==========================================
         *
         * Contoh:
         *
         * time<1ms
         * time=1ms
         */

        $delays = [];

        foreach ($output as $line) {

            if (
                preg_match(
                    '/time[=<]\s*(\d+)\s*ms/i',
                    $line,
                    $matches
                )
            ) {
                $delays[] = (float) $matches[1];
            }
        }

        /*
         * Jika Average tidak ditemukan,
         * hitung dari nilai reply.
         */
        if (
            $averagePing === null &&
            count($delays) > 0
        ) {
            $averagePing =
                array_sum($delays)
                /
                count($delays);

            $averagePing = round(
                $averagePing,
                2
            );
        }

        /*
         * ==========================================
         * JITTER PING
         * ==========================================
         *
         * Jitter dihitung dari rata-rata
         * selisih absolut antar-delay.
         */

        $jitter = null;

        if (count($delays) >= 2) {

            $differences = [];

            for (
                $i = 1;
                $i < count($delays);
                $i++
            ) {
                $differences[] =
                    abs(
                        $delays[$i]
                        -
                        $delays[$i - 1]
                    );
            }

            if (count($differences) > 0) {

                $jitter =
                    array_sum($differences)
                    /
                    count($differences);

                $jitter = round(
                    $jitter,
                    2
                );
            }
        }

        return [
            'success' => $resultCode === 0,

            'ping' => $averagePing,

            'packet_loss' => $packetLoss,

            'jitter' => $jitter,

            'raw_output' => $outputText,
        ];
    }

    /**
     * Menjalankan Ookla Speedtest CLI.
     */
    private function runSpeedtest(): array
    {
        $speedtestPath = env(
            'SPEEDTEST_PATH',
            'C:/Users/Lenovo/Downloads/ookla-speedtest-1.2.0-win64/speedtest.exe'
        );

        /*
         * Pastikan file Speedtest tersedia.
         */
        if (!file_exists($speedtestPath)) {

            return [
                'success' => false,

                'throughput' => null,

                'ping' => null,

                'jitter' => null,

                'packet_loss' => null,

                'raw_output' =>
                    'Speedtest tidak ditemukan: '
                    .
                    $speedtestPath,
            ];
        }

        /*
         * Jalankan Speedtest menggunakan JSON.
         */
       
$command =
    escapeshellarg($speedtestPath)
    . ' --server-id=70715'
    . ' --accept-license'
    . ' --accept-gdpr'
    . ' --format=json';
    

        $output = [];

        $resultCode = 1;

        exec(
            $command,
            $output,
            $resultCode
        );

        $outputText = implode(
            "\n",
            $output
        );

        /*
         * Speedtest dapat menghasilkan beberapa
         * baris JSON.
         *
         * Kita hanya mengambil:
         *
         * "type":"result"
         */

        $resultData = null;

        foreach ($output as $line) {

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $decoded = json_decode(
                $line,
                true
            );

            if (
                is_array($decoded)
                &&
                isset($decoded['type'])
                &&
                $decoded['type'] === 'result'
            ) {
                $resultData = $decoded;

                break;
            }
        }

        /*
         * Jika result JSON tidak ditemukan,
         * pengukuran dianggap gagal.
         */
        if (!is_array($resultData)) {

            return [
                'success' => false,

                'throughput' => null,

                'ping' => null,

                'jitter' => null,

                'packet_loss' => null,

                'raw_output' =>
                    $outputText !== ''
                    ? $outputText
                    : 'Speedtest tidak menghasilkan data.',
            ];
        }

        /*
         * ==========================================
         * THROUGHPUT
         * ==========================================
         *
         * Speedtest memberikan bandwidth
         * dalam byte per second.
         *
         * byte/s × 8 / 1.000.000 = Mbps
         */

        $downloadBandwidth = null;

        if (
            isset(
                $resultData['download']['bandwidth']
            )
        ) {
            $downloadBandwidth =
                (float)
                $resultData['download']['bandwidth'];
        }

        $throughput = null;

        if ($downloadBandwidth !== null) {

            $throughput =
                ($downloadBandwidth * 8)
                /
                1000000;

            $throughput = round(
                $throughput,
                2
            );
        }

        /*
         * ==========================================
         * SPEEDTEST LATENCY
         * ==========================================
         */

        $ping = null;

        if (
            isset(
                $resultData['ping']['latency']
            )
        ) {
            $ping =
                round(
                    (float)
                    $resultData['ping']['latency'],
                    2
                );
        }

        /*
         * ==========================================
         * SPEEDTEST JITTER
         * ==========================================
         */

        $jitter = null;

        if (
            isset(
                $resultData['ping']['jitter']
            )
        ) {
            $jitter =
                round(
                    (float)
                    $resultData['ping']['jitter'],
                    2
                );
        }

        /*
         * ==========================================
         * PACKET LOSS
         * ==========================================
         */

        $packetLoss = null;

        if (
            isset(
                $resultData['packetLoss']
            )
        ) {
            $packetLoss =
                round(
                    (float)
                    $resultData['packetLoss'],
                    2
                );
        }

        return [
            'success' => true,

            'throughput' => $throughput,

            'ping' => $ping,

            'jitter' => $jitter,

            'packet_loss' => $packetLoss,

            'raw_output' => $outputText,
        ];
    }

    /**
     * Menggabungkan hasil ping dan Speedtest
     * untuk disimpan sebagai raw_output.
     */
    private function buildRawOutput(
        array $pingResult,
        array $speedtestResult,
        ?float $finalPacketLoss
    ): string {

        return json_encode(
            [
                'target_ping' => $pingResult,

                'speedtest' => $speedtestResult,

                'final_packet_loss' =>
                    $finalPacketLoss,
            ],
            JSON_PRETTY_PRINT
            |
            JSON_UNESCAPED_SLASHES
        );
    }
}
