<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ordonnance</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  <style>
    @page {
        margin: 0.2in;
        size: A5 portrait;
    }

    body {
        margin: 0;
        font-family: Arial, sans-serif;
        font-size: 0.8rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .title {
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
      text-decoration: underline;
      color: #1a56db;
    }

    .container-prescription {
      margin-bottom: 15px;
    }

    .new-page {
      page-break-before: always;
    }

    .barcode-container {
      text-align: center;
      margin-top: 20px;
    }

    .first-page {
        width: 147mm;
        height: 205mm;
        background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
    }

    .first-page-content {
        position: relative;
        z-index: 2;
        padding: 30mm 15mm 15mm 15mm;
    }

    .second-page {
        page-break-before: always;
        padding: 15mm;
    }

    @media print {
      .first-page-bg {
        display: block;
      }
      
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
    }
  </style>
</head>
<body>

  <div class="first-page">
    <div class="first-page-bg"></div>

    <div class="m-4 relative z-10" style="margin: 1rem 0.4in;">
      <div class="text-center" style="padding-top: 190px">
        <p class="!text-md"><strong></strong> <?php echo e(date('d/m/Y'), false); ?></p>
        <h2 class="text-[15px] font-bold mt-[15px] text-[#354b88] ">
          <?php if(isset($patient)): ?>
            <?php if($patient->sex == 'M'): ?> M.
            <?php elseif($patient->sex == 'F'): ?> Mme
            <?php else: ?> Mlle
            <?php endif; ?>
          <?php endif; ?>
          <?php echo e($patient->surname, false); ?> <?php echo e($patient->name, false); ?>

        </h2>
      </div>
    </div>

    <div id="first-page-content" class="relative z-10" style="margin: 1rem 0.4in;"></div>
  </div>

  <div id="second-page" class="second-page"></div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ordonnanceItems = <?php echo json_encode($ordonnance, 15, 512) ?>;
      const firstPageContent = document.getElementById('first-page-content');
      const secondPage = document.getElementById('second-page');
      const firstPageHeightLimit = 350;
      let currentHeight = 0;
      let counter = 1;
      let firstPageItems = [];
      let secondPageItems = [];
      ordonnanceItems.forEach(item => {
        const estimatedHeight = 
          (item.medicament ? 30 : 0) + 
          (item.frequency || item.administration_mode || item.unit || (item.duration_value && item.duration_unit) ? 30 : 0) +
          (item.commentaire ? 25 : 0);
        if (currentHeight + estimatedHeight <= firstPageHeightLimit) {
          firstPageItems.push({...item, counter: counter++});
          currentHeight += estimatedHeight;
        } else {
          secondPageItems.push({...item, counter: counter++});
        }
      });
      firstPageItems.forEach(item => {
        firstPageContent.appendChild(createPrescriptionElement(item));
      });
      secondPageItems.forEach(item => {
        secondPage.appendChild(createPrescriptionElement(item));
      });
      function createPrescriptionElement(item) {
        const container = document.createElement('div');
        container.className = 'container-prescription';
        let html = '';
        if (item.medicament) {
          html += `<p class="font-bold text-[15px]">${item.counter}. ${item.medicament}</p>`;
        }
        html += `<p class="ml-5 text-[14px]">`;
        if (item.frequency) {
          html += `${item.frequency.replace(/[\[\]"]/g, '')} `;
        }
        if (item.administration_mode) {
          const mode = item.administration_mode.toLowerCase().replace(/[\[\]"]/g, '');
          html += mode === 'orale' || mode === 'sublinguale' ? `par voie ${mode} ` : `sur ${mode} `;
        }
        if (item.application_site) {
          html += `, appliquer sur ${item.application_site.toLowerCase().replace(/[\[\]"]/g, '')} `;
        }
        if (item.unit) {
          html += `- ${item.unit.replace(/[\[\]"]/g, '')} `;
        }
        if (item.duration_value && item.duration_unit) {
          html += `pendant ${item.duration_value} ${item.duration_unit.toLowerCase().replace(/[\[\]"]/g, '')} `;
        }
        html += `</p>`;
        if (item.commentaire) {
          const cleanComment = item.commentaire.replace(/[\[\]"]/g, '');
          if (cleanComment.trim().length > 0) {
            html += `<p class="ml-5 italic">${cleanComment}</p>`;
          }
        }
        container.innerHTML = html;
        return container;
      }
      setTimeout(() => window.print(), 500);
    });
  </script>
</body>
</html><?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/ordonnance.blade.php ENDPATH**/ ?>