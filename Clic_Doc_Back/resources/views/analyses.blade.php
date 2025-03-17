<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Analyses</title>
    <style>
        @page {
            margin: 0.5in;
            size: A4 portrait;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            height: 100vh;
        }

        .container {
            margin: 0.5in;
        }

        /* Header Section Styling */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            text-align: center;
            border-bottom: 2px solid #ddd;
        }

        .header-section .info {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .header-section img {
            max-width: 80px;
            height: auto;
        }

        .content-title {
            font-size: 1.8em;
            font-weight: bold;
            text-align: center;
            margin: 30px 0;
        }

        .patient-info {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Analyses Section Styling */
        .analyses-container {
            font-size: 1em;
            line-height: 1.6;
            margin-top: 20px;
        }

        .analyses-item {
            padding: 10px 15px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9fafc;
        }

        /* Print Specific Styles */
        @media print {
            .header-section {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: white;
                border-bottom: 1px solid #ddd;
            }

            .content {
                margin-top: 3in; /* Adjust for fixed header */
            }

            .analyses-item {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="info">
                <span class="font-bold">{{ $docteur->name }}</span><br>
                Médecin Dermatologue<br>
                {{ $docteur->mobile_number }}<br>
                {{ $docteur->email }}
            </div>
            <div>
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info">
                <span class="font-bold">{{ $entite->name }}</span><br>
                {{ $entite->adress }}, {{ $entite->city }}<br>
                0676616626
            </div>
        </div>

        <!-- Title Section -->
        <div class="content">
            <div class="content-title">Analyses</div>
            <div class="patient-info">
                Patient : {{ $patient->surname }} {{ $patient->name }}
            </div>

            <!-- Analyses Section -->
            <div class="analyses-container">
                @foreach ($analyses as $item)
                <div class="analyses-item">
                    {{ $item->libelle }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>
