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
      background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg"); /* Change URL to your certificate template */
      background-size: cover;
      background-repeat: no-repeat;
      background-position: top center;
      width: 100%;
      height: 100%;
      padding: 10mm;
      padding-top: 191px;
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
      margin-top: 10px;
      font-size: 12px;
      margin-bottom: 28px;
    }

    .certificate-content {
      margin-top: 60px;
      font-size: 17px;
      line-height: 1.6;
      text-align: justify;
    }

    .signature {
      margin-top: 200px;
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
</head>
<body onload="window.print()">
  <div class="a5-page">
    <div class="date">
         <span id="currentDate"></span>
    </div>
    
    <div class="title">CERTIFICAT MÉDICAL</div>

    <div class="certificate-content">
      <p>
        Je soussigné(e), <strong><?php echo e($docteur->name, false); ?></strong>, certifie avoir reçu ce jour
        <?php if(isset($patient)): ?>
          <?php if($patient->sex == 'M'): ?> M.
          <?php elseif($patient->sex == 'F'): ?> Mme
          <?php else: ?> Mlle
          <?php endif; ?>
        <?php endif; ?>
        <b><?php echo e($patient->surname ?? '', false); ?> <?php echo e($patient->name ?? '', false); ?></b>,
        CIN : <b><?php echo e($patient->CIN ?? '', false); ?></b>, à ma consultation.
      </p>

      <p style="margin-top: 20px;">
        Ce certificat est délivré à l'intéressé(e) pour justifier son absence ce jour et servir et valoir ce que de droit.
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
<?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/certificat-aptitude.blade.php ENDPATH**/ ?>