<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat d'Aptitude Physique</title>
    <style>
        @page { 
            size: A4;
            margin: 0.3in;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.6;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }
        .header-section .info {
            text-align: left;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .header-section .info span {
            display: block;
        }
        .header-section img {
            max-width: 80px;
            height: auto;
        }
        hr {
            margin: 10px 0;
            border: none;
            border-top: 2px solid #ddd;
            margin-bottom: 100px;

        }
        .title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 30px;
            text-decoration: underline;
        }
        .content {
            margin: 0 10px 30px;
            font-size: 1rem;
            line-height: 1.6;
            text-align: justify;
        }
        .content p {
            margin-bottom: 15px;
        }
        .signature {
            text-align: right;
            margin-top: 550px;
            font-size: 1rem;
        }
    </style>
</head>
<body onload="window.print()">
    <div>
        <!-- Header Section -->
        <div class="header-section">
            <div class="info">
                <span><strong>{{ $docteur->name }}</strong></span>
                <span>Docteur en Médecine</span>
                <span>{{ $docteur->mobile_number }}</span>
                <span>{{ $docteur->email }}</span>
            </div>
            <div class="logo">
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info" style="text-align: right;">
                <span><strong>{{ $entite->name }}</strong></span>
                <span>{{ $entite->adress }}, {{ $entite->city }}</span>
                <span>0676616626</span>
            </div>
        </div>

        <hr>

        <!-- Title -->
        <div class="title">
            CERTIFICAT D'APTITUDE PHYSIQUE
        </div>

        <!-- Content Section -->
        <div class="content">
            <p>
                Je soussigné, <b>{{ $docteur->name }}</b>, docteur en médecine, certifie avoir examiné ce jour,
                @if($patient->sex=='M') le @else la @endif dénommé@if($patient->sex!='M')e @endif 
                <b>{{ $patient->surname }} {{ $patient->name }}</b>.
            </p>
            <p>
                Et déclare, d’après son examen clinique et des examens complémentaires, qu’@if($patient->sex=='M')il @else elle @endif est <b>physiquement apte</b> pour l’emploi considéré.
            </p>
            <p>
                En foi de quoi, le présent certificat est délivré à l’intéressé@if($patient->sex!='M')e @endif pour servir et valoir ce que de droit.
            </p>
        </div>

        <!-- Signature Section -->
        <div class="signature">
            <p>
                Fait à {{ $entite->city }}, le : {{ date('d/m/Y') }}
            </p>
            <p>
                Signature : _______________________
            </p>
        </div>
    </div>
</body>
</html>
