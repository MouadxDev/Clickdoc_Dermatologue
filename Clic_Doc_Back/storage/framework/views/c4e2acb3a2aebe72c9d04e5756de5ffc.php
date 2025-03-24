<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add JsBarcode for barcode generation -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <title>Ordonnance</title>
    <style>
        /* Page margins and sizes */
        @page {
            margin-top: 0.2in;
            margin-bottom: 0.1in;
            margin-right: 0.2in;
            margin-left: 0.2in;
            size: A5 portrait;
        }
    
        /* Body settings */
        body {
            font-family: Arial, sans-serif;
            font-size: 0.8rem;
        }
    
        /* Section for the header with logo and contact info */
        .section-entete {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            /* padding: 10px; */
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
            rotate: -2deg;
        }
    
        /* Styles for the list in the right-aligned section */
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
    
        /* Footer style with fixed position */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            background-color: #fff; 
            page-break-after: always;
      
        }
    
        .footer-left {
        }
    
        .footer-right {
        }
    
        /* Barcode container position */
        .barcode-container {
            text-align: center !important;
            position: absolute;
            bottom: 65px; 
            right: 10px;
            font-size: 10px;
        }
    
        /* Prevent content overlap by adding padding to the bottom */
        .content {
            padding-bottom: 50px; /* Add space for the footer */
        }
        .title {
            text-align: center;
            font-size: 1.5rem !important;
            font-weight: bold;
            text-decoration: underline;
            color: #1a56db;
        }
        .container-prescription{
            margin-bottom: 15px;
        }
    </style>
    
</head>
<body>

    <div class="section-entete">
        <div class="info text-left">
            <span class="font-bold text-[#2c31a5] text-sm">Dr. Lamiae EL MOUTAOUI</span><br>
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
            <span class="font-bold text-[#2c31a5] text-sm">د. لمياء المطاوي</span><br>
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

    <div class="m-8">
        <div class="text-center">
            <h1 class="text-[14px] font-bold text-[#2c31a5]  title">Ordonnance</h1>
            
            <h2 class="text-[11px] font-bold mt-2"> 
                <?php if(isset($patient)): ?>
                    <?php if($patient->sex == 'M'): ?>
                        M.
                    <?php elseif($patient->sex == 'F'): ?>
                        Mme
                    <?php else: ?>
                        Mlle
                    <?php endif; ?>
                <?php endif; ?>

                 <?php echo e($patient->surname, false); ?> <?php echo e($patient->name, false); ?></h2>

            <div class="text-center">
                <p class="!text-md"> <strong> Béni Mellal le :</strong> <?php echo e(date('d/m/Y'), false); ?></p>
            </div>

        </div>
    </div>

    <div class="m-8" style="font-family: 'Arial', sans-serif;">
        <?php $counter = 1; ?> 

        <?php $__currentLoopData = $ordonnance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="container-prescription">
                
                <?php if($item->medicament): ?>
                    <p class="font-bold text-[15px]"><?php echo e($counter, false); ?>. <?php echo e($item->medicament, false); ?></p>
                    <?php $counter++; ?>
                <?php endif; ?>
        
                <p class="ml-5 text-[14px]">
                    
                    <?php if($item->frequency): ?> 
                        <?php echo e(ucfirst($item->frequency), false); ?>

                    <?php endif; ?>
        
                    
                    <?php if($item->administration_mode): ?>
                        <?php
                            $administration_mode = strtolower($item->administration_mode);
                        ?>
        
                        
                        <?php if($administration_mode === 'orale' || $administration_mode === 'sublinguale'): ?>
                            par voie <?php echo e($administration_mode, false); ?>

                        
                        
                        <?php elseif(in_array($administration_mode, ['topique', 'visage', 'corps', 'lésion', 'zones inflammées', 'cuir chevelu', 'plis cutanés', 'mains', 'pieds', 'ongles', 'zones exposées au soleil', 'application occlusse', 'sous pansement'])): ?>
                            sur <?php echo e($administration_mode, false); ?>

                        
                        
                        <?php elseif($administration_mode === 'visage uniquement'): ?>
                            sur le visage uniquement
                        
                        
                        <?php else: ?>
                            sur <?php echo e($administration_mode, false); ?>

                        <?php endif; ?>
                    <?php endif; ?>
        
                    
                    <?php if($item->application_site): ?>
                        , appliquer sur <?php echo e(strtolower($item->application_site), false); ?>

                    <?php endif; ?>
        
                    
                    <?php if($item->unit): ?> 
                        - <?php echo e($item->unit, false); ?>

                    <?php endif; ?>
        
                    
                    <?php if($item->duration_value && $item->duration_unit): ?> 
                        pendant <?php echo e($item->duration_value, false); ?> <?php echo e(strtolower($item->duration_unit), false); ?> 
                    <?php endif; ?>
                </p>
        
                
                <?php
                    $comments = json_decode($item->commentaire, true);
                ?>
                <?php if(!empty($comments) && is_array($comments)): ?>
                    <p class="ml-5 italic"><?php echo e(implode(', ', $comments), false); ?></p>
                <?php endif; ?>
            </div>
        
      
            
            
            
            
            <div class="my-[16px]">

        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>





    <!-- Footer with contact information -->

    <div class="footer">

        <div class="barcode-container" style="gap: 0 !important;display: flex;flex-direction: column;align-items: center;">
            <svg id="barcode"></svg>
            <p id="barcode_text"></p>
        </div>

        <div class="footer-left flex">
            <p class="text-center"> Qr El Adarissa Rue 5 N° 64, 1er Étage, Beni Mellal 
                - <strong>GSM :</strong> 06.60.80.86.74 | <strong>Fixe :</strong> 05.23.42.00.82
                <strong>Email :</strong> elmoutaoui_lamiae2@yahoo.fr</p>
        </div>

    </div>

    <script>
        // Generate barcode when page loads
        document.addEventListener('DOMContentLoaded', function() {
            let firstId = 091035279;
            let paddedId = firstId.toString().padStart(9, '0');

            JsBarcode("#barcode", paddedId, {
                format: "CODE128",
                height: 30,
                width:1,
                displayValue: false,
                margin: 5
            });
            document.getElementById('barcode_text').innerText = paddedId;

            setTimeout(function() {
            window.print();
        }, 500);
        });
        document.addEventListener('DOMContentLoaded', function() {
    const prescriptionContainer = document.querySelector('.m-8');
    const prescriptions = document.querySelectorAll('.container-prescription');
    const maxHeight = 200; // Maximum height in pixels
    
    // Function to check the height of the container
    function checkAndSplitContent() {
        let currentHeight = 0;
        let newPageItems = [];
        let currentPageItems = [];
        
        // Check each prescription item
        prescriptions.forEach(item => {
            const itemHeight = item.offsetHeight;
            
            // If adding this item would exceed the max height
            if (currentHeight + itemHeight > maxHeight) {
                newPageItems.push(item);
                item.style.display = 'none'; // Hide from current page
            } else {
                currentHeight += itemHeight;
                currentPageItems.push(item);
            }
        });
        
        // If we have items for a new page
        if (newPageItems.length > 0) {
            // Clone the entire document to create a new page
            createNewPage(newPageItems);
        }
    }
    
    // Function to create a new page with the remaining items
    function createNewPage(items) {
        // Create a new page element
        const newPage = document.createElement('div');
        newPage.className = 'new-page';
        
        // Add page break before new page
        newPage.style.pageBreakBefore = 'always';
        
        // Clone the header section
        const header = document.querySelector('.section-entete').cloneNode(true);
        newPage.appendChild(header);
        
        // Add "Suite" text to indicate continuation
        const suiteText = document.createElement('div');
        suiteText.className = 'm-8';
        suiteText.innerHTML = '<div class="text-center"><p class="text-md font-bold">Suite de l\'ordonnance</p></div>';
        newPage.appendChild(suiteText);
        
        // Create a new container for prescriptions
        const newContainer = document.createElement('div');
        newContainer.className = 'm-8';
        newContainer.style.fontFamily = 'Arial, sans-serif';
        
        // Continue the counter from the first page
        let startCounter = document.querySelectorAll('.container-prescription:not([style*="display: none"]) p.font-bold').length + 1;
        
        // Add the remaining items to the new container
        items.forEach((item, index) => {
            // Clone the item so we can show it on the new page
            const clonedItem = item.cloneNode(true);
            clonedItem.style.display = 'block';
            
            // Update the counter for the continued items
            const medicamentElement = clonedItem.querySelector('p.font-bold');
            if (medicamentElement) {
                const medicamentText = medicamentElement.textContent;
                const updatedText = medicamentText.replace(/^\d+\./, `${startCounter}.`);
                medicamentElement.textContent = updatedText;
                startCounter++;
            }
            
            newContainer.appendChild(clonedItem);
        });
        
        newPage.appendChild(newContainer);
        
        // Clone the footer and barcode
        const footer = document.querySelector('.footer').cloneNode(true);
        
        const existingBarcode = footer.querySelector('.barcode-container');
        if (existingBarcode) {
            existingBarcode.remove();
        }
        const barcode = document.querySelector('.barcode-container').cloneNode(true);
        
        newPage.appendChild(barcode);
        newPage.appendChild(footer);
        
        // Add the new page to the document
        document.body.appendChild(newPage);
    }
    
    // Run the function after a small delay to ensure content is rendered
    setTimeout(checkAndSplitContent, 100);
});
    </script>

</body>
</html><?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/ordonnance.blade.php ENDPATH**/ ?>