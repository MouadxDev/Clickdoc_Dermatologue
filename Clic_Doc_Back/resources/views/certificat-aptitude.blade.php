<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Certificat Médical</title>
    <style>
        @page {
            margin-top: 0.2in;
            margin-bottom: 0.1in;
            margin-right: 0.2in;
            margin-left: 0.2in;
            size: A5 portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 0.8rem;
            margin-bottom: 50px;
        }
        .section-entete {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px;
        }
        .section-entete .info {
            text-align: left;
        }
        .section-entete .info-ar {
            text-align: right;
            direction: rtl;
        }
        .section-entete img {
            max-width: 80px;
            height: auto;
        }
        .liste-rtl {
            list-style-position: inside;
            padding-right: 5px;
            padding-left: 0;
            text-align: right;
            direction: rtl;
        }
        p {
            font-size: 11px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            background-color: #fff;
        }
        .content {
            padding-bottom: 50px;
        }
        .title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
            color: #1a56db;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
            padding-right: 50px;
        }
        .date {
            text-align: right;
            margin-top: 20px;
            padding-right: 50px;
            font-size: 12px;
        }
        .certificate-content {
            margin: 30px 20px;
            line-height: 1.5;
            font-size: 12px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="section-entete">
        <div class="info text-left">
            <span class="font-bold text-blue-600 text-sm">Dr. Lamiae EL MOUTAOUI</span><br>
            <span class="text-[10px]">Médecin spécialiste en maladies <br> de la peau, des ongles et des cheveux</span>
            <ul class="list-disc pl-5 text-[10px]">
                <li>Allergies cutanées</li>
                <li>Dermatologie pédiatrique</li>
                <li>Chirurgie dermatologique</li>
                <li>Maladies sexuellement transmissibles</li>
                <li>Dermatologie esthétique : (Laser, comblement, peeling, Botox, PRP)</li>
            </ul>
        </div>
        <div class="logo">
            <img src="/cdn/logo_cust.png" alt="Logo" class="mr-[10px]">
        </div>
        <div class="info-ar">
            <span class="font-bold text-blue-600 text-sm">د. لمياء المطاوي</span><br>
            <span class="text-[10px]">طبيبة متخصصة في أمراض <br> الجلد، الشعر والأظافر</span>
            <ul class="list-disc liste-rtl text-[10px]">
                <li>الحساسية الجلدية</li>
                <li>أمراض الجلد عند الأطفال</li>
                <li>الجراحة الجلدية</li>
                <li>الأمراض المنقولة جنسياً</li>
                <li>طب الجلد التجميلي : (الليزر، التقشير، البوتوكس، البلازما المجددة)</li>
            </ul>
        </div>
    </div>
    
    <div class="title">
        CERTIFICAT MÉDICAL
    </div>
    
    <div class="date">
        Béni Mellal, le <span id="currentDate"></span>
    </div>
    
    <div class="certificate-content " style="padding-top: 70px">
        <p>
            Je soussigné(e), <b> Dr. Lamiae EL MOUTAOUI</b>, certifie avoir reçu ce jour 
            @if(isset($patient) && $patient->sex=='M') M. @else Mme @endif 
            <b>{{ $patient->surname ?? '' }} {{ $patient->name ?? '' }}</b>
            CIN : <b>{{ $patient->CIN ?? '' }}</b>, à ma consultation.
        </p>
        <p style="margin-top: 20px;">
            Ce certificat est délivré à l'intéressé(e) pour justifier son absence ce jour et servir et valoir ce que de droit.
        </p>
    </div>
    
    <div class="signature">
        <p>
            Cachet et signature :
        </p>
        <div style="height: 60px;"></div>
    </div>
    
    <div class="footer">
        <div class="footer-left flex">
            <p class="text-center"> Qr El Adarissa Rue 5 N° 64, 1er Étage, Beni Mellal 
                - <strong>GSM :</strong> 06.60.80.86.74 | <strong>Fixe :</strong> 05.23.42.00.82
                <strong>Email :</strong> elmoutaoui_lamiae2@yahoo.fr</p>
        </div>
    </div>
    
    <script>
        const today = new Date();
        const day = today.getDate();
        const month = today.toLocaleString('fr-FR', { month: 'long' });
        const year = today.getFullYear();
        const formattedDate = day + ' ' + month + ' ' + year;
        document.getElementById('currentDate').textContent = formattedDate;
    </script>
</body>
</html>