<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Ordonnance</title>
    <style>
        @page {
            margin-top: 0.2in;
            margin-bottom: 1in;
            margin-right: 0.2in;
            margin-left: 0.2in;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
        }
        .table-custom {
            border-collapse: collapse;
            width: 100%;
        }
        .table-custom th,
        .table-custom td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        .table-custom th {
            background-color: #f0f4f8;
            font-weight: bold;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }
        .header-section .info {
            text-align: center;
            font-size: 0.9rem;
        }
        .header-section .info span {
            display: block;
        }
        .header-section img {
            max-width: 100px;
            height: auto;
        }
        .record-container {
            /* margin-bottom: 20px; */
            /* border: 1px solid #e0e0e0; */
            border-radius: 8px;
            padding: 5px;
        }
        .record-container th {
            width: 40%;
            background-color: #f9fafc;
        }
        .record-container td {
            width: 60%;
        }
        @media print {
            .header-section, .record-container {
                page-break-inside: avoid;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div>
        <!-- Header Section -->
        <div class="header-section">
            <div class="info">
                <span class="font-bold">{{ $docteur->name }}</span>
                <span>Médecin Dermatologue</span>
                <span>{{ $docteur->mobile_number }}</span>
                <span>{{ $docteur->email }}</span>
            </div>
            <div class="logo">
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info">
                <span class="font-bold">{{ $entite->name }}</span>
                <span>{{ $entite->adress }}, {{ $entite->city }}</span>
                <span>{{ $entite->contact_number }}</span>
            </div>
        </div>

        <hr class="m-4 border-2">

        <!-- Title Section -->
        <div class="m-8">
            <div class="text-center">
                <h1 class="text-2xl font-bold">Ordonnance</h1>
                <h2 class="text-xl font-bold mt-4">Patient : {{ $patient->surname }} {{ $patient->name }}</h2>
            </div>
        </div>

        <!-- Dynamic Data -->
        <div class="m-8">
            @foreach ($ordonnance as $item)
            <div class="record-container">
                <table class="table-custom">
                    <tr>
                        <th>Médicament</th>
                        <td>{{ $item->medicament }}</td>
                    </tr>
                    <tr>
                        <th>Mode d'administration</th>
                        <td>{{ $item->administration_mode }}</td>
                    </tr>
                    <tr>
                        <th>Fréquence</th>
                        <td>{{ $item->frequency }}</td>
                    </tr>
                    <tr>
                        <th>Durée</th>
                        <td>{{ $item->duration_value }} {{ $item->duration_unit }}</td>
                    </tr>
                    <tr>
                        <th>Commentaire</th>
                        <td>{{ $item->commentaire }}</td>
                    </tr>
                    <tr>
                        <th>Contre-indications</th>
                        <td>
                            @if (is_array($item->contraindications))
                                {{ implode(', ', $item->contraindications) }}
                            @else
                                {{ $item->contraindications }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Timing</th>
                        <td>
                            Matin: {{ $item->matin }}, Midi: {{ $item->midi }}, Soir: {{ $item->soir }}, Au coucher: {{ $item->au_coucher }}
                        </td>
                    </tr>
                </table>
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
