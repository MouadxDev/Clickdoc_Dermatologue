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

    /* First page container */
    .first-page {
        width: 147mm;
        height: 205mm;
        /* background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg"); */
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
    }
    

        /* Content over image */
        .first-page-content {
        position: relative;
        z-index: 2;
        padding: 30mm 15mm 15mm 15mm; /* Adjust as needed to leave space at top */
        }

        /* Second page */
        .second-page {
        page-break-before: always;
        padding: 15mm;
        }

        /* Container spacing */
        .container-prescription {
        margin-bottom: 15px;
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

  <?php if(isset($branding_file)): ?>
    <style>
      .first-page {
         background-image: url('<?php echo e($branding_file, false); ?>');
      }
    </style>
  <?php endif; ?>

</head>
<body>

  <!-- First Page with Background -->
  <div class="first-page">
    <!-- Background Image only for First Page -->
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

    <!-- First page content will be placed here by JavaScript -->
    <div id="first-page-content" class="relative z-10" style="margin: 1rem 0.4in;"></div>
  </div>

  <!-- Second Page without Background -->
  <div id="second-page" class="second-page">
    <!-- Second page content will be placed here by JavaScript -->
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ordonnanceItems = <?php echo json_encode($ordonnance, 15, 512) ?>;
      
      // Calculate how many items can fit on the first page
      const firstPageContent = document.getElementById('first-page-content');
      const secondPage = document.getElementById('second-page');
      
      // A5 page height considering margins (approximate)
      const firstPageHeightLimit = 350; // adjust based on your A5 size needs
      
      let currentHeight = 0;
      let counter = 1;
      let firstPageItems = [];
      let secondPageItems = [];
      
      // Process each ordonnance item
      ordonnanceItems.forEach(item => {
        // Estimate height based on content length (this is approximate)
        const estimatedHeight = 
          (item.medicament ? 30 : 0) + 
          (item.frequency || item.administration_mode || item.unit || (item.duration_value && item.duration_unit) ? 30 : 0) +
          (item.commentaire && JSON.parse(item.commentaire).length > 0 ? 25 : 0);
        
        // If it fits on first page, add it there
        if (currentHeight + estimatedHeight <= firstPageHeightLimit) {
          firstPageItems.push({...item, counter: counter++});
          currentHeight += estimatedHeight;
        } else {
          // Otherwise, add to second page
          secondPageItems.push({...item, counter: counter++});
        }
      });
      
      // Render first page items
      firstPageItems.forEach(item => {
        firstPageContent.appendChild(createPrescriptionElement(item));
      });
      
      // Render second page items
      secondPageItems.forEach(item => {
        secondPage.appendChild(createPrescriptionElement(item));
      });
      
      // Function to create a prescription element
      function createPrescriptionElement(item) {
        const container = document.createElement('div');
        container.className = 'container-prescription';
        
        let html = '';
        
        if (item.medicament) {
          html += `<p class="font-bold text-[15px]">${item.counter}. ${item.medicament}</p>`;
        }
        
        html += `<p class="ml-5 text-[14px]">`;
        
        if (item.frequency) {
          html += `${capitalizeFirstLetter(item.frequency)} `;
        }
        
        if (item.administration_mode) {
          const mode = item.administration_mode.toLowerCase();
          if(['orale', 'sublinguale'].includes(mode)) {
            html += `par voie ${mode} `;
          } else if(['topique', 'visage', 'corps', 'lésion', 'zones inflammées', 'cuir chevelu', 'plis cutanés', 'mains', 'pieds', 'ongles', 'zones exposées au soleil', 'application occlusse', 'sous pansement'].includes(mode)) {
            html += `sur ${mode} `;
          } else if(mode === 'visage uniquement') {
            html += `sur le visage uniquement `;
          } else {
            html += `sur ${mode} `;
          }
        }
        
        if (item.application_site) {
          html += `, appliquer sur ${item.application_site.toLowerCase()} `;
        }
        
        if (item.unit) {
          html += `- ${item.unit} `;
        }
        
        if (item.duration_value && item.duration_unit) {
          html += `pendant ${item.duration_value} ${item.duration_unit.toLowerCase()} `;
        }
        
        html += `</p>`;
        
        if (item.commentaire) {
          const comments = JSON.parse(item.commentaire);
          if (comments && comments.length > 0) {
            html += `<p class="ml-5 italic">${comments}</p>`;
          }
        }
        
        container.innerHTML = html;
        return container;
      }
      
      function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
      }
      
      // For easy printing
      setTimeout(() => window.print(), 500);
    });
  </script>

</body>
</html><?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/ordonnance.blade.php ENDPATH**/ ?>