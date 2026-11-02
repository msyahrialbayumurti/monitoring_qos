@extends('layouts.app')







@section('title', 'Analisis QoS')







@section('styles')







<style>

.analysis-page .header {



        display: flex;



        justify-content: space-between;



        align-items: center;



        margin-bottom: 24px;



    }







.analysis-page .header h1 {



        margin: 0;



        font-size: 27px;



        color: #12376d;



    }







.analysis-page .header p {



        margin: 7px 0 0;



        color: #7183a0;



        font-size: 13px;



    }







.analysis-page .date-box {



        background: white;



        border: 1px solid #dfe6ef;



        padding: 11px 17px;



        border-radius: 9px;



        font-size: 13px;



        color: #516784;



    }







    /* FILTER */







.analysis-page .filter-card {



        background: white;



        border: 1px solid #e2e8f0;



        border-radius: 12px;



        padding: 20px;



        margin-bottom: 20px;



        box-shadow: 0 3px 12px rgba(20, 50, 90, .04);



    }







.analysis-page .filter-title {



        font-size: 14px;



        font-weight: bold;



        margin-bottom: 14px;



    }







.analysis-page .filter-form {



        display: flex;



        gap: 12px;



        align-items: end;



    }







.analysis-page .form-group {



        display: flex;



        flex-direction: column;



        gap: 7px;



        min-width: 280px;



    }







.analysis-page .form-group label {



        font-size: 12px;



        color: #647793;



    }







.analysis-page select {



        height: 40px;



        border: 1px solid #d6dfeb;



        border-radius: 8px;



        padding: 0 12px;



        color: #284467;



        background: white;



    }







.analysis-page .btn {



        height: 40px;



        padding: 0 20px;



        border: 0;



        border-radius: 8px;



        background: #1469dd;



        color: white;



        font-weight: bold;



        cursor: pointer;



        text-decoration: none;



        display: inline-flex;



        align-items: center;



        justify-content: center;



    }







.analysis-page .btn:hover {



        background: #0d58bd;



    }







.analysis-page .btn-reset {



        background: #eef3f9;



        color: #47617f;



    }







.analysis-page .btn-reset:hover {



        background: #e0e8f2;



    }







    /* SUMMARY */







.analysis-page .summary-grid {



        display: grid;



        grid-template-columns: repeat(4, 1fr);



        gap: 16px;



        margin-bottom: 20px;



    }







.analysis-page .summary-card {



        background: white;



        border: 1px solid #e2e8f0;



        border-radius: 12px;



        padding: 18px;



        box-shadow: 0 3px 12px rgba(20, 50, 90, .04);



    }







.analysis-page .summary-label {



        font-size: 11px;



        color: #7183a0;



        text-transform: uppercase;



    }







.analysis-page .summary-value {



        margin-top: 8px;



        font-size: 24px;



        font-weight: bold;



        color: #173d70;



    }







.analysis-page .summary-unit {



        font-size: 11px;



        font-weight: normal;



        color: #7183a0;



    }







    /* ANALYSIS CARDS */







.analysis-page .analysis-grid {



        display: grid;



        grid-template-columns: 1fr 1fr;



        gap: 20px;



        margin-bottom: 20px;



    }







.analysis-page .analysis-card {



        background: white;



        border: 1px solid #e2e8f0;



        border-radius: 12px;



        padding: 20px;



        box-shadow: 0 3px 12px rgba(20, 50, 90, .04);



    }







.analysis-page .analysis-card-title {



        font-size: 15px;



        font-weight: bold;



        color: #153b70;



        margin-bottom: 12px;



    }







.analysis-page .analysis-summary {



        font-size: 12px;



        color: #60738f;



        line-height: 1.6;



        margin-bottom: 15px;



    }







    .cause-item,



.analysis-page .recommendation-item {



        border: 1px solid #e6ebf2;



        border-radius: 8px;



        padding: 12px;



        margin-bottom: 9px;



        background: #fafcff;



    }







    .cause-item:last-child,



.analysis-page .recommendation-item:last-child {



        margin-bottom: 0;



    }







.analysis-page .item-title {



        font-size: 12px;



        font-weight: bold;



        color: #244b79;



        margin-bottom: 5px;



    }







.analysis-page .item-text {



        font-size: 11px;



        line-height: 1.55;



        color: #60738f;



    }







.analysis-page .priority {



        display: inline-block;



        margin-top: 7px;



        padding: 4px 7px;



        border-radius: 5px;



        font-size: 9px;



        font-weight: bold;



    }







.analysis-page .priority-kritis {



        background: #ffe2e2;



        color: #c13232;



    }







.analysis-page .priority-tinggi {



        background: #fff0df;



        color: #b96500;



    }







.analysis-page .priority-sedang {



        background: #fff4d7;



        color: #a87200;



    }







.analysis-page .priority-rendah {



        background: #e8f0fb;



        color: #47688e;



    }







.analysis-page .good-message {



        padding: 13px;



        border-radius: 8px;



        background: #e8f8ef;



        color: #187b50;



        font-size: 12px;



        line-height: 1.5;



    }







    /* TABLE */







.analysis-page .table-card {



        background: white;



        border: 1px solid #e2e8f0;



        border-radius: 12px;



        box-shadow: 0 3px 12px rgba(20, 50, 90, .04);



        overflow: hidden;



    }







.analysis-page .table-header {



        padding: 20px 22px;



        border-bottom: 1px solid #e8edf4;



        display: flex;



        justify-content: space-between;



        align-items: center;



    }







.analysis-page .table-title {



        font-size: 16px;



        font-weight: bold;



        color: #153b70;



    }







.analysis-page .table-info {



        font-size: 11px;



        color: #7b8ca5;



    }







.analysis-page .table-wrapper {



        overflow-x: auto;



    }







.analysis-page table {



        width: 100%;



        border-collapse: collapse;



        min-width: 1200px;



    }







.analysis-page th {



        background: #f8fafd;



        color: #72839d;



        font-size: 10px;



        font-weight: bold;



        text-align: left;



        padding: 14px 15px;



        border-bottom: 1px solid #e5ebf2;



        white-space: nowrap;



    }







.analysis-page td {



        padding: 15px;



        border-bottom: 1px solid #edf1f6;



        font-size: 12px;



        color: #3e5776;



        white-space: nowrap;



    }







.analysis-page tbody tr:hover {



        background: #fafcff;



    }







.analysis-page .school-name {



        font-weight: bold;



        color: #183d70;



    }







.analysis-page .number {



        font-weight: bold;



        color: #294e7d;



    }







    /* PING */







.analysis-page .ping-connected {



        color: #168658;



        font-weight: bold;



    }







.analysis-page .ping-timeout {



        color: #d63b3b;



        font-weight: bold;



    }







.analysis-page .ping-unknown {



        color: #7b8ca5;



    }







    /* INDEX */







.analysis-page .index-box {



        font-size: 14px;



        font-weight: bold;



        color: #173d70;



    }







.analysis-page .index-detail {



        margin-top: 4px;



        font-size: 10px;



        color: #7b8ca5;



    }







    /* STATUS */







.analysis-page .status {



        display: inline-block;



        padding: 5px 9px;



        border-radius: 6px;



        font-size: 10px;



        font-weight: bold;



    }







.analysis-page .status-baik {



        background: #e5f8ef;



        color: #168658;



    }







.analysis-page .status-sedang {



        background: #fff4d7;



        color: #b47a00;



    }







.analysis-page .status-buruk {



        background: #ffe5e5;



        color: #c13d3d;



    }







.analysis-page .status-null {



        background: #eef2f6;



        color: #718096;



    }







    /* PAGINATION */







.analysis-page .pagination-area {



        padding: 18px 22px;



        display: flex;



        justify-content: space-between;



        align-items: center;



    }







.analysis-page .pagination-info {



        font-size: 11px;



        color: #7b8ca5;



    }







.analysis-page .pagination {



        display: flex;



        gap: 5px;



    }







    .pagination a,



.analysis-page .pagination span {



        min-width: 32px;



        height: 32px;



        padding: 0 9px;



        display: flex;



        align-items: center;



        justify-content: center;



        border-radius: 6px;



        border: 1px solid #dce4ee;



        text-decoration: none;



        font-size: 11px;



        color: #48627f;



        background: white;



    }







.analysis-page .pagination .active {



        background: #1469dd;



        color: white;



        border-color: #1469dd;



    }







.analysis-page .pagination .disabled {



        opacity: .45;



    }







.analysis-page .empty {



        text-align: center;



        padding: 60px 20px;



        color: #7a8ca5;



    }







.analysis-page .empty-title {



        font-weight: bold;



        font-size: 15px;



        margin-bottom: 6px;



        color: #4d6685;



    }







.analysis-page .empty-text {



        font-size: 12px;



    }







    @media (max-width: 1100px) {



.analysis-page .summary-grid {



            grid-template-columns: repeat(2, 1fr);



        }







.analysis-page .analysis-grid {



            grid-template-columns: 1fr;



        }



    }

@media (max-width: 800px) {

.analysis-page .header {



            align-items: flex-start;



            gap: 15px;



        }







.analysis-page .filter-form {



            flex-direction: column;



            align-items: stretch;



        }







.analysis-page .form-group {



            min-width: 100%;



        }







.analysis-page .summary-grid {



            grid-template-columns: 1fr;



        }



    }



    /* NETMONITOR Modern Blue - refinement for Analysis QoS */
    .analysis-page { color: #18365f; }
    .analysis-page .header,
    .analysis-page .filter-card,
    .analysis-page .summary-card,
    .analysis-page .analysis-card,
    .analysis-page .table-card {
        border: 1px solid rgba(190, 212, 244, .9);
        border-radius: 18px;
        box-shadow: 0 12px 30px rgba(33, 78, 137, .07);
    }
    .analysis-page .header {
        padding: 26px 28px;
        background: radial-gradient(circle at 88% 10%, rgba(91, 157, 255, .22), transparent 36%), linear-gradient(120deg, #fff 0%, #f4f8ff 58%, #e6f0ff 100%);
        position: relative;
        overflow: hidden;
    }
    .analysis-page .header h1 { font-size: clamp(25px, 2.2vw, 32px); letter-spacing: -.6px; font-weight: 800; }
    .analysis-page .header p { font-size: 13px; line-height: 1.65; }
    .analysis-page .date-box { border-color: #d9e7ff; border-radius: 12px; box-shadow: 0 5px 15px rgba(38, 91, 164, .06); white-space: nowrap; }
    .analysis-page .filter-card { padding: 22px; background: rgba(255,255,255,.94); }
    .analysis-page .filter-title { color: #153b70; font-size: 15px; }
    .analysis-page select { min-height: 44px; border-color: #d7e3f5; background: #fbfdff; transition: border-color .18s, box-shadow .18s; }
    .analysis-page select:focus { outline: none; border-color: #3984f6; box-shadow: 0 0 0 4px rgba(57,132,246,.12); }
    .analysis-page .btn { min-height: 42px; border-radius: 10px; box-shadow: 0 6px 14px rgba(20,105,221,.15); transition: transform .18s, box-shadow .18s, background .18s; }
    .analysis-page .btn:hover { transform: translateY(-1px); box-shadow: 0 9px 18px rgba(20,105,221,.2); }
    .analysis-page .btn-reset { box-shadow: none; }
    .analysis-page .summary-grid { gap: 16px; }
    .analysis-page .summary-card { position: relative; overflow: hidden; padding: 21px; background: linear-gradient(145deg,#fff,#f8fbff); transition: transform .18s, box-shadow .18s; }
    .analysis-page .summary-card::after { content: ''; position: absolute; width: 90px; height: 90px; right: -34px; top: -40px; border-radius: 50%; background: rgba(66, 133, 244, .09); pointer-events: none; }
    .analysis-page .summary-card:hover { transform: translateY(-2px); box-shadow: 0 16px 30px rgba(33,78,137,.1); }
    .analysis-page .summary-label { letter-spacing: 1px; font-weight: 800; }
    .analysis-page .summary-value { font-size: clamp(23px, 2vw, 29px); letter-spacing: -.5px; }
    .analysis-page .analysis-card { padding: 22px; background: linear-gradient(150deg,#fff,#fbfdff); }
    .analysis-page .analysis-card-title { font-size: 16px; }
    .analysis-page .cause-item, .analysis-page .recommendation-item { border-radius: 11px; border-color: #e1eaf7; background: #f8fbff; padding: 14px; }
    .analysis-page .good-message { border: 1px solid #c9efdc; border-radius: 11px; }
    .analysis-page .table-card { background: #fff; }
    .analysis-page .table-header { background: linear-gradient(110deg,#fff,#f5f9ff); padding: 21px 24px; }
    .analysis-page .table-title { font-size: 17px; }
    .analysis-page th { background: #f0f6ff; color: #5d79a4; letter-spacing: .7px; }
    .analysis-page td { padding-top: 16px; padding-bottom: 16px; }
    .analysis-page tbody tr { transition: background .15s; }
    .analysis-page tbody tr:hover { background: #f5f9ff; }
    .analysis-page .status { border-radius: 999px; padding: 6px 10px; letter-spacing: .3px; }
    .analysis-page .pagination-area { gap: 14px; flex-wrap: wrap; }
    .analysis-page .pagination a, .analysis-page .pagination span { border-radius: 9px; }
    .analysis-page .pagination a:hover { background: #edf5ff; border-color: #b9d4ff; }
    .analysis-page .empty { background: linear-gradient(180deg,#fff,#f7faff); }
    @media (max-width: 800px) {
        .analysis-page .header { flex-direction: column; align-items: flex-start; padding: 22px; }
        .analysis-page .date-box { align-self: flex-start; }
        .analysis-page .filter-card { padding: 17px; }
        .analysis-page .pagination-area { align-items: flex-start; }
    }
</style>







@endsection











@section('content')







<div class="analysis-page">







    <!-- CONTENT -->







    <div class="analysis-content">







    <!-- HEADER -->







    <div class="header">







        <div>



            <h1>



                Analisis QoS



            </h1>







            <p>



                Analisis kualitas jaringan berdasarkan



                parameter QoS dan klasifikasi TIPHON.



            </p>



        </div>







        <div class="date-box">



            {{ now()->format('d F Y') }}



        </div>







    </div>











    <!-- FILTER -->







    <div class="filter-card">







        <div class="filter-title">



            Pilih Sekolah



        </div>







        <form



            method="GET"



            action="{{ route('qos.analysis') }}"



            class="filter-form"



        >







            <div class="form-group">







                <label for="school_id">



                    Sekolah



                </label>







                <select



                    name="school_id"



                    id="school_id"



                >







                    <option value="">



                        Semua Sekolah



                    </option>







                    @foreach ($schools as $school)







                        <option



                            value="{{ $school->id }}"



                            {{ (string) $selectedSchoolId === (string) $school->id ? 'selected' : '' }}



                        >



                            {{ $school->nama_sekolah }}



                        </option>







                    @endforeach







                </select>







            </div>







            <button



                type="submit"



                class="btn"



            >



                Analisis



            </button>







            <a



                href="{{ route('qos.analysis') }}"



                class="btn btn-reset"



            >



                Reset



            </a>







        </form>







    </div>











    <!-- DATA TERBARU -->







    @php



        $latestClassification =



            $latestMeasurement



                ? $latestMeasurement->qos_classification



                : null;







        $latestRca =



            $latestMeasurement



                ? $latestMeasurement->rca



                : null;







        $latestDss =



            $latestMeasurement



                ? $latestMeasurement->dss



                : null;



    @endphp











    @if ($latestMeasurement)







        <!-- SUMMARY -->







        <div class="summary-grid">







            <div class="summary-card">







                <div class="summary-label">



                    Throughput



                </div>







                <div class="summary-value">







                    {{ $latestMeasurement->throughput !== null



                        ? number_format((float) $latestMeasurement->throughput, 2)



                        : '--'



                    }}







                    <span class="summary-unit">



                        Mbps



                    </span>







                </div>







            </div>











            <div class="summary-card">







                <div class="summary-label">



                    Delay



                </div>







                <div class="summary-value">







                    {{ $latestMeasurement->delay !== null



                        ? number_format((float) $latestMeasurement->delay, 2)



                        : '--'



                    }}







                    <span class="summary-unit">



                        ms



                    </span>







                </div>







            </div>











            <div class="summary-card">







                <div class="summary-label">



                    Jitter



                </div>







                <div class="summary-value">







                    {{ $latestMeasurement->jitter !== null



                        ? number_format((float) $latestMeasurement->jitter, 2)



                        : '--'



                    }}







                    <span class="summary-unit">



                        ms



                    </span>







                </div>







            </div>











            <div class="summary-card">







                <div class="summary-label">



                    Indeks QoS



                </div>







                <div class="summary-value">







                    {{ $latestClassification['qos_index'] ?? '--' }}







                    @if ($latestClassification['condition'] ?? null)







                        <span class="summary-unit">



                            {{ $latestClassification['condition'] }}



                        </span>







                    @endif







                </div>







            </div>







        </div>











        <!-- RCA + DSS -->







        <div class="analysis-grid">







            <!-- RCA -->







            <div class="analysis-card">







                <div class="analysis-card-title">



                    Analisis Penyebab Gangguan (RCA)



                </div>







                @if (



                    $latestRca &&



                    ($latestRca['required'] ?? false) === true



                )







                    <div class="analysis-summary">



                        {{ $latestRca['summary'] ?? 'Analisis penyebab tersedia.' }}



                    </div>







                    @if (!empty($latestRca['causes']))







                        @foreach ($latestRca['causes'] as $cause)







                            <div class="cause-item">







                                <div class="item-title">



                                    {{ $cause['parameter'] ?? 'QoS' }}



                                </div>







                                <div class="item-text">



                                    <strong>Penyebab:</strong><br>



                                    {{ $cause['cause'] ?? '-' }}



                                </div>







                                @if (!empty($cause['severity']))







                                    <span



                                        class="priority



                                        priority-{{ strtolower($cause['severity']) }}"



                                    >



                                        {{ $cause['severity'] }}



                                    </span>







                                @endif







                            </div>







                        @endforeach







                    @else







                        <div class="analysis-summary">



                            Kondisi QoS memerlukan perhatian,



                            tetapi belum ditemukan rule RCA yang spesifik.



                        </div>







                    @endif







                @else







                    <div class="good-message">



                        {{ $latestRca['summary']



                            ?? 'Tidak ditemukan indikasi gangguan pada parameter QoS.'



                        }}



                    </div>







                @endif







            </div>











            <!-- DSS -->







            <div class="analysis-card">







                <div class="analysis-card-title">



                    Rekomendasi Tindakan (DSS)



                </div>







                @if (



                    $latestDss &&



                    ($latestDss['required'] ?? false) === true



                )







                    <div class="analysis-summary">



                        {{ $latestDss['summary'] ?? 'Rekomendasi tersedia.' }}



                    </div>







                    @if (!empty($latestDss['recommendations']))







                        @foreach (



                            $latestDss['recommendations']



                            as $recommendation



                        )







                            <div class="recommendation-item">







                                <div class="item-title">



                                    {{ $recommendation['parameter'] ?? 'QoS' }}



                                </div>







                                <div class="item-text">



                                    {{ $recommendation['action'] ?? '-' }}



                                </div>







                                @if (!empty($recommendation['priority']))







                                    <span



                                        class="priority



                                        priority-{{ strtolower($recommendation['priority']) }}"



                                    >



                                        {{ $recommendation['priority'] }}



                                    </span>







                                @endif







                            </div>







                        @endforeach







                    @else







                        <div class="analysis-summary">



                            Belum terdapat rekomendasi tindakan spesifik.



                        </div>







                    @endif







                @else







                    <div class="good-message">



                        {{ $latestDss['summary']



                            ?? 'Tidak diperlukan tindakan khusus.'



                        }}



                    </div>







                @endif







            </div>







        </div>







    @endif











    <!-- TABLE -->







    <div class="table-card">







        <div class="table-header">







            <div class="table-title">



                Hasil Analisis QoS



            </div>







            <div class="table-info">



                {{ $measurements->total() }}



                data



            </div>







        </div>











        @if ($measurements->count() > 0)







            <div class="table-wrapper">







                <table>







                    <thead>







                        <tr>







                            <th>



                                WAKTU



                            </th>







                            <th>



                                SEKOLAH



                            </th>







                            <th>



                                THROUGHPUT



                            </th>







                            <th>



                                DELAY



                            </th>







                            <th>



                                JITTER



                            </th>







                            <th>



                                PACKET LOSS



                            </th>







                            <th>



                                PING



                            </th>







                            <th>



                                INDEKS QoS



                            </th>







                            <th>



                                KATEGORI TIPHON



                            </th>







                        </tr>







                    </thead>











                    <tbody>







                        @foreach ($measurements as $measurement)







                            @php







                                $classification =



                                    $measurement->qos_classification;







                                $condition =



                                    $classification['condition']



                                    ?? null;







                                $qosIndex =



                                    $classification['qos_index']



                                    ?? null;







                            @endphp







                            <tr>







                                <!-- WAKTU -->







                                <td>







                                    {{ $measurement->measured_at



                                        ? $measurement->measured_at



                                            ->format('d/m/Y H:i:s')



                                        : '-'



                                    }}







                                </td>











                                <!-- SEKOLAH -->







                                <td>







                                    <div class="school-name">







                                        {{ $measurement->school



                                            ? $measurement->school->nama_sekolah



                                            : '-'



                                        }}







                                    </div>







                                </td>











                                <!-- THROUGHPUT -->







                                <td>







                                    <span class="number">







                                        {{ $measurement->throughput !== null



                                            ? number_format(



                                                (float) $measurement->throughput,



                                                2



                                            )



                                            : '-'



                                        }}







                                    </span>







                                    Mbps







                                </td>











                                <!-- DELAY -->







                                <td>







                                    <span class="number">







                                        {{ $measurement->delay !== null



                                            ? number_format(



                                                (float) $measurement->delay,



                                                2



                                            )



                                            : '-'



                                        }}







                                    </span>







                                    ms







                                </td>











                                <!-- JITTER -->







                                <td>







                                    <span class="number">







                                        {{ $measurement->jitter !== null



                                            ? number_format(



                                                (float) $measurement->jitter,



                                                2



                                            )



                                            : '-'



                                        }}







                                    </span>







                                    ms







                                </td>











                                <!-- PACKET LOSS -->







                                <td>







                                    <span class="number">







                                        {{ $measurement->packet_loss !== null



                                            ? number_format(



                                                (float) $measurement->packet_loss,



                                                2



                                            )



                                            : '-'



                                        }}







                                    </span>







                                    %







                                </td>











                                <!-- PING -->







                                <td>







                                    @if (



                                        ($measurement->ping_status ?? null)



                                        === 'TERHUBUNG'



                                    )







                                        <span class="ping-connected">







                                            {{ $measurement->ping !== null



                                                ? number_format(



                                                    (float) $measurement->ping,



                                                    2



                                                ) . ' ms'



                                                : 'TERHUBUNG'



                                            }}







                                        </span>







                                    @elseif (



                                        ($measurement->ping_status ?? null)



                                        === 'TIMEOUT'



                                    )







                                        <span class="ping-timeout">



                                            TIMEOUT



                                        </span>







                                    @elseif (



                                        $measurement->ping !== null



                                    )







                                        <span class="number">







                                            {{ number_format(



                                                (float) $measurement->ping,



                                                2



                                            ) }}







                                            ms







                                        </span>







                                    @else







                                        <span class="ping-unknown">



                                            -



                                        </span>







                                    @endif







                                </td>











                                <!-- INDEKS -->







                                <td>







                                    @if ($qosIndex !== null)







                                        <div class="index-box">







                                            {{ number_format(



                                                (float) $qosIndex,



                                                2



                                            ) }}







                                        </div>







                                        <div class="index-detail">



                                            1 - 4



                                        </div>







                                    @else







                                        <span class="status status-null">



                                            BELUM TERSEDIA



                                        </span>







                                    @endif







                                </td>











                                <!-- KATEGORI -->







                                <td>







                                    @if ($condition === 'BAIK')







                                        <span class="status status-baik">



                                            BAIK



                                        </span>







                                    @elseif ($condition === 'SEDANG')







                                        <span class="status status-sedang">



                                            SEDANG



                                        </span>







                                    @elseif ($condition === 'BURUK')







                                        <span class="status status-buruk">



                                            BURUK



                                        </span>







                                    @else







                                        <span class="status status-null">



                                            BELUM DIKLASIFIKASI



                                        </span>







                                    @endif







                                </td>







                            </tr>







                        @endforeach







                    </tbody>







                </table>







            </div>











            <!-- PAGINATION -->







            <div class="pagination-area">







                <div class="pagination-info">







                    Menampilkan







                    {{ $measurements->firstItem() }}







                    -







                    {{ $measurements->lastItem() }}







                    dari







                    {{ $measurements->total() }}







                    data







                </div>











                <div class="pagination">







                    @if ($measurements->onFirstPage())







                        <span class="disabled">



                            ‹



                        </span>







                    @else







                        <a



                            href="{{ $measurements->previousPageUrl() }}"



                        >



                            ‹



                        </a>







                    @endif











                    @foreach (



                        $measurements->getUrlRange(



                            max(



                                1,



                                $measurements->currentPage() - 2



                            ),



                            min(



                                $measurements->lastPage(),



                                $measurements->currentPage() + 2



                            )



                        ) as $page => $url



                    )







                        @if (



                            $page ==



                            $measurements->currentPage()



                        )







                            <span class="active">



                                {{ $page }}



                            </span>







                        @else







                            <a href="{{ $url }}">



                                {{ $page }}



                            </a>







                        @endif







                    @endforeach











                    @if ($measurements->hasMorePages())







                        <a



                            href="{{ $measurements->nextPageUrl() }}"



                        >



                            ›



                        </a>







                    @else







                        <span class="disabled">



                            ›



                        </span>







                    @endif







                </div>







            </div>







        @else







            <div class="empty">







                <div class="empty-title">



                    Belum ada data monitoring



                </div>







                <div class="empty-text">



                    Data analisis akan muncul setelah



                    sistem melakukan pengukuran QoS.



                </div>







            </div>







        @endif







    </div>







    </div>



</div>



@endsection
