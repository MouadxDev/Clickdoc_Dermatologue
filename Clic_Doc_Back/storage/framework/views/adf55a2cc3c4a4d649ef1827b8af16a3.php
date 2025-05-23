<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Analyse</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  <style>
    @page {
      size: A5 portrait;
      margin: 0;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      -webkit-print-color-adjust: exact;
    }

    .a5-page {
      /* background-image: url("https://clickdoc.webredirect.org/public/doc/ordonnance_01.jpg"); */
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
      width: 147mm;
      height: 205mm;
      position: relative;
      padding: 15mm; 
      box-sizing: border-box;
    }

    .analyses-container {
      font-size: 1rem;
      margin-top: 9mm;
    }

    .analyses-item {
      margin-bottom: 8px;
      padding: 6px 12px;
      border: 1px solid #ccc;
      background: rgba(255, 255, 255, 0.9);
      border-radius: 4px;
      page-break-inside: avoid;
    }
    .title {
            text-align: center;
            font-size: 1.5rem !important;
            font-weight: bold;
            text-decoration: underline;
            color: #1a56db;
        }

    @media print {
      html, body {
        width: 148mm;
        height: 210mm;
      }

      .a5-page {
        page-break-after: always;
      }
    }
  </style>

<?php if(isset($branding_file)): ?>
<style>
  .a5-page {
     background-image: url('<?php echo e($branding_file, false); ?>');
  }
</style>
<?php endif; ?>

</head>
<body onload="window.print()">
  <section class="a5-page">
    <div class="m-4 relative z-10" style="margin: 1rem 0.4in;">
        <div class="text-center" style="padding-top: 120px">
            <p style="font-size: 0.8rem;"> <?php echo e(date('d/m/Y'), false); ?></p>
          
          <h2 class="text-[15px] font-bold pt-[10px]">
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
      
      <div class="analyses-container">
        <ol style="padding-left: 20px; list-style-type: decimal;">
          <?php $__currentLoopData = $analyses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="py-1">
              <?php echo e($item->libelle, false); ?>

            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
      </div>

  </section>
</body>
</html>
<?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/analyses.blade.php ENDPATH**/ ?>