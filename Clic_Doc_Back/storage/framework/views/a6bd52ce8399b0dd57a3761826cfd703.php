<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Certificat Médical de Repos</title>
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
      background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg");
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
      font-size: 1.2rem;
      font-weight: bold;
      margin-top: 30px;
      margin-bottom: 10px;
      text-decoration: underline;
      color: #2c31a5;
    }

    .date {
      text-align: center;
      font-size: 12px;
    }

    .content {
      /* margin: 20px; */
      font-size: 17px;
      line-height: 1.6;
      text-align: justify;
    }

    .signature {
      text-align: right;
      font-size: 13px;
      margin-top: 150px;
      margin-right: 30px;
    }

    .days-input {
      border: none;
      width: 50px;
      text-align: center;
      font-size: inherit;
      border-bottom: 1px solid black;
    }

    .modal {
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
    }

    .modal-content {
      background-color: white;
      padding: 30px;
      border-radius: 16px;
      text-align: center;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
      width: 90%;
      max-width: 450px;
      position: relative;
      transform: scale(0.9);
      opacity: 0;
      transition: all 0.3s ease;
      border-left: 7px solid #3d93c2;
    }

    .modal-content.show {
      transform: scale(1);
      opacity: 1;
    }

    .modal-content input {
      width: 300px;
      padding: 10px;
      margin-top: 15px;
      border: 2px solid #3d93c2;
      border-radius: 8px;
      outline: none;
    }

    .modal-content input:focus {
      border-color: #2c7bad;
      box-shadow: 0 0 0 3px rgba(61, 147, 194, 0.2);
    }

    .modal-content button {
      margin-top: 20px;
      padding: 12px 25px;
      border: none;
      background-color: #3d93c2;
      color: white;
      cursor: pointer;
      border-radius: 8px;
    }

    .modal-content button:hover {
      background-color: #2c7bad;
    }

    @media print {
      .modal {
        display: none !important;
      }

      .a5-page {
        page-break-after: always;
      }
    }
  </style>

</head>
<body>
    <script src="
        https://cdn.jsdelivr.net/npm/n2words@1.21.0/dist/n2words.min.js
    "></script>
  <!-- Modal -->
  <div class="modal" id="daysModal">
    <div class="modal-content">
      <p>Veuillez indiquer le nombre de jours de repos nécessaires pour l'arrêt de travail :</p>
      <input type="number" id="daysInput" placeholder="Nombre de jours" />
      <button onclick="setDaysAndPrint()">OK</button>
    </div>
  </div>

  <!-- Printable Page -->
  <section class="a5-page">
    <div class="text-center" style="padding-top: 151px;">
      <p class="text-sm"> <span id="currentDate"></span></p>
      <div class="title pb-[50px] ">CERTIFICAT MÉDICAL DE REPOS</div>
    </div>

    <div class="content">
      <p>
        Je, soussigné(e) <strong><?php echo e($docteur->name, false); ?></strong>, certifie auni reçu ce jour du  <strong id="currentDate2" class=""></strong>
        <?php if(isset($patient)): ?>
          <?php if($patient->sex == 'M'): ?> M.
          <?php elseif($patient->sex == 'F'): ?> Mme
          <?php else: ?> Mlle
          <?php endif; ?>
        <?php endif; ?>
        <strong><?php echo e($patient->surname, false); ?> <?php echo e($patient->name, false); ?></strong> et que son état de santé nécessite un repos de
        <input type="number" class="days-input" id="daysField" readonly placeholder="____" />
        jours <span id="daysWords" class="italic text-gray-600"></span>  à compter de ce jour.

        </p>
        
      <p>
        Ce certificat est délivré à l'intéressé pour servir et valoir ce que de droit.
      </p>
    </div>

    <div class="signature">
      <p>Signature : _______________________</p>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('daysModal');
      const modalContent = modal.querySelector('.modal-content');
  
      modal.style.display = 'flex';
      setTimeout(() => modalContent.classList.add('show'), 10);
  
      window.addEventListener('afterprint', () => {
        modal.style.display = 'flex';
        setTimeout(() => modalContent.classList.add('show'), 10);
      });
  
      window.setDaysAndPrint = function () {
        const days = document.getElementById('daysInput').value;
        if (!days || days <= 0) {
          alert('Veuillez saisir un nombre de jours valide.');
          return;
        }
  
        // Set the numeric value
        document.getElementById('daysField').value = days;

        
        // Convert number to French words
        const daysInWords = n2words(days, { lang: 'fr' });
        document.getElementById('daysWords').textContent = `(${daysInWords} jours)`;
  
        // Hide modal and print
        modalContent.classList.remove('show');
        setTimeout(() => {
          modal.style.display = 'none';
          window.print();
        }, 300);
      };
  
      // Set today's date
      const today = new Date();
      const day = today.getDate();
      const month = today.toLocaleString('fr-FR', { month: 'long' });
      const year = today.getFullYear();
      document.getElementById('currentDate').textContent = `${day} ${month} ${year}`;
      document.getElementById('currentDate2').textContent = `${day} ${month} ${year}`;

    });
  </script>
  
  
</body>
</html>
<?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/certificat-medical.blade.php ENDPATH**/ ?>