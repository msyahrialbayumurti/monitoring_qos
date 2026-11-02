<?php

namespace App\Services;

class QosDssService
{
    /**
     * Decision Support System (DSS)
     *
     * Menghasilkan rekomendasi tindakan berdasarkan
     * hasil Rule-Based Root Cause Analysis (RCA).
     */
    public function recommend(array $rca): array
    {
        /*
        |--------------------------------------------------------------------------
        | Jika RCA tidak diperlukan
        |--------------------------------------------------------------------------
        */

        if (
            isset($rca['required']) &&
            $rca['required'] === false
        ) {
            return [
                'required' => false,

                'summary' =>
                    'Tidak diperlukan tindakan khusus karena kualitas jaringan berada pada kondisi baik.',

                'recommendations' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Jika RCA diperlukan tetapi tidak menemukan
        | rule penyebab yang spesifik
        |--------------------------------------------------------------------------
        */

        if (
            !isset($rca['causes']) ||
            empty($rca['causes'])
        ) {
            return [
                'required' => true,

                'summary' =>
                    'Kondisi QoS memerlukan perhatian, tetapi belum ditemukan rekomendasi tindakan yang spesifik.',

                'recommendations' => [
                    [
                        'parameter' => 'QoS',

                        'priority' => 'SEDANG',

                        'action' =>
                            'Lakukan pemeriksaan parameter QoS secara menyeluruh dan evaluasi kondisi jaringan untuk mengidentifikasi sumber gangguan.',
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Array rekomendasi
        |--------------------------------------------------------------------------
        */

        $recommendations = [];

        /*
        |--------------------------------------------------------------------------
        | Ambil rekomendasi dari hasil RCA
        |--------------------------------------------------------------------------
        |
        | Setiap rule RCA yang aktif dapat memiliki:
        |
        | - parameter
        | - severity
        | - recommendation
        |
        | DSS menggunakan hasil tersebut sebagai dasar
        | pengambilan keputusan.
        |
        */

        foreach ($rca['causes'] as $cause) {

            $parameter =
                $cause['parameter'] ?? 'QoS';

            $priority =
                $cause['severity'] ?? 'SEDANG';

            $action =
                $cause['recommendation']
                ?? 'Lakukan pemeriksaan lebih lanjut terhadap kondisi jaringan.';
            
            $recommendations[] = [
                'parameter' =>
                    $parameter,

                'priority' =>
                    $priority,

                'action' =>
                    $action,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Hilangkan rekomendasi duplikat
        |--------------------------------------------------------------------------
        */

        $uniqueRecommendations = [];

        foreach ($recommendations as $recommendation) {

            $key =
                $recommendation['parameter'] .
                '|' .
                $recommendation['action'];

            $uniqueRecommendations[$key] =
                $recommendation;
        }

        $recommendations =
            array_values(
                $uniqueRecommendations
            );

        /*
        |--------------------------------------------------------------------------
        | Jika ternyata tidak ada rekomendasi
        |--------------------------------------------------------------------------
        */

        if (empty($recommendations)) {

            return [
                'required' => true,

                'summary' =>
                    'Kondisi QoS memerlukan perhatian, tetapi belum ditemukan rekomendasi tindakan yang spesifik.',

                'recommendations' => [
                    [
                        'parameter' => 'QoS',

                        'priority' => 'SEDANG',

                        'action' =>
                            'Lakukan pemeriksaan parameter QoS secara menyeluruh dan evaluasi kondisi jaringan.',
                    ],
                ],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Urutkan rekomendasi berdasarkan prioritas
        |--------------------------------------------------------------------------
        |
        | KRITIS  → paling tinggi
        | TINGGI
        | SEDANG
        | RENDAH
        |
        */

        $priorityOrder = [
            'KRITIS' => 1,
            'TINGGI' => 2,
            'SEDANG' => 3,
            'RENDAH' => 4,
        ];

        usort(
            $recommendations,
            function ($a, $b) use ($priorityOrder) {

                $priorityA =
                    $priorityOrder[
                        $a['priority']
                    ] ?? 99;

                $priorityB =
                    $priorityOrder[
                        $b['priority']
                    ] ?? 99;

                return $priorityA <=> $priorityB;
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Buat ringkasan DSS
        |--------------------------------------------------------------------------
        */

        $parameters =
            array_map(
                function ($recommendation) {
                    return $recommendation['parameter'];
                },
                $recommendations
            );

        $parameters =
            array_unique(
                $parameters
            );

        $summary =
            'Rekomendasi tindakan dibuat berdasarkan hasil Rule-Based RCA pada parameter: ' .
            implode(', ', $parameters) .
            '.';

        /*
        |--------------------------------------------------------------------------
        | Hasil DSS
        |--------------------------------------------------------------------------
        */

        return [
            'required' => true,

            'summary' =>
                $summary,

            'recommendations' =>
                $recommendations,
        ];
    }
}
