<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificat Médical</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    @page {
      size: A5 portrait;
      margin: 0;
    }

    html, body {
      width: 148mm;
      height: 210mm;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      -webkit-print-color-adjust: exact;
    }

    .a5-page {
      /* background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg"); */
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center top;
      width: 147mm;
      height: 205mm;
      position: relative;
      padding: 10mm;
      box-sizing: border-box;
    }

    .title {
      text-align: center;
      font-size: 1.3rem;
      font-weight: bold;
      text-decoration: underline;
      color: #2c31a5;
    }

    .date {
      text-align: center;
      margin-top: 150px;
      font-size: 12px;
      margin-bottom: 28px;
    }

    .certificate-content {
      margin-top: 50px;
      font-size: 17px;
      line-height: 1.6;
      text-align: justify;
    }

    .signature {
      margin-top: 65px;
      text-align: right;
      font-size: 13px;
      margin-right: 20px;
    }

    @media print {
      body {
        -webkit-print-color-adjust: exact;
      }
    }
  </style>

    @if(isset($branding_file))
    <style>
      .a5-page {
         background-image: url('{{ $branding_file }}');
      }
    </style>
  @endif
</head>
<body onload="window.print()">
  <div class="a5-page">
    <div class="date">
         <span id="currentDate"></span>
    </div>
    <div class="title" style="padding-top: 10px;">CERTIFICAT MÉDICAL</div>

    <div class="certificate-content" style="font-size: 18px; line-height: 1.8; text-align: justify;">
      <p style="text-indent: 2em;">
        Je soussigné(e), <str>{{ $docteur->name }}</str>, certifie avoir reçu ce jour&nbsp;
        @if(isset($patient))
          @if($patient->sex == 'M') M.
          @elseif($patient->sex == 'F') Mme
          @else Mlle
          @endif
        @endif
        <str>{{ $patient->surname ?? '' }} {{ $patient->name ?? '' }}</str>,
        titulaire de la carte d’identité nationale &nbsp;<str>{{ $patient->CIN ?? '' }}</str>, en consultation médicale.
      </p>
    
      <p style="text-indent: 2em; margin-top: 1em;">
        Le présent certificat est délivré à l’intéressé(e) pour justifier son absence en date d’aujourd’hui et servir et valoir ce que de droit.
      </p>
    </div>
    

    <div class="signature">
      Signature : _______________________
    </div>
  </div>

  <script>
    const today = new Date();
    const day = today.getDate();
    const month = today.toLocaleString('fr-FR', { month: 'long' });
    const year = today.getFullYear();
    document.getElementById('currentDate').textContent = `${day} ${month} ${year}`;
  </script>
</body>
</html>
