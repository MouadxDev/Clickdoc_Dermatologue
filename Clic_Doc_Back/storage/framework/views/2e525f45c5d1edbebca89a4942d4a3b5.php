<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <title>Ordonnance</title>
    
    <!-- Import handwriting fonts from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Kalam:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        /* Page margins and sizes */
        @page {
            margin-top: 0.2in;
            margin-bottom: 0.1in;
            margin-right: 0.2in;
            margin-left: 0.2in;
            size: A5 portrait;
        }
    
        /* Body settings with handwriting font */
        body {
            font-family: 'Kalam', cursive;
            font-size: 0.9rem;
            margin-bottom: 50px;
            /* background-color: #fffdf9;
            color: #283845; */
        }
        
        /* Handwritten header - keep structure but change style */
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
    
        /* Handwritten lists */
        .liste-rtl {
            list-style-position: inside;
            padding-right: 5px;
            padding-left: 0;
            text-align: right;
            direction: rtl;
        }
        
        ul li {
            margin-bottom: 3px;
            position: relative;
        }
        
        ul li::before {
            
            position: absolute;
            left: -15px;
            color: #3a6ea5;
        }
        
        .liste-rtl li::before {
           
            position: absolute;
            right: -15px;
            color: #3a6ea5;
        }
    
        p {
            font-size: 14px;
            line-height: 1.5;
        }
    
        /* Footer with more subtle styling */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            border-top: 1px dashed #3a6ea5;
            font-size: 9px;
            background-color: #fffdf9;
            font-family: Arial, sans-serif;
            color: #465b73;
        }
    
        /* Position barcode */
        .barcode-container {
            text-align: center;
            position: absolute;
            bottom: 65px;
            right: 10px;
            font-size: 10px;
            font-family: Arial, sans-serif;
        }
    
        /* Content area with spacing for footer */
        .content {
            padding-bottom: 60px;
            position: relative;
        }
        
        /* Handwritten title with ink style */
        .title {
            text-align: center;
            font-size: 1.8rem !important;
            font-weight: bold;
            color: #0c4d9c;
            font-family: 'Caveat', cursive;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-decoration: underline;
            text-decoration-style: wavy;
            text-decoration-thickness: 1px;
            transform: rotate(-1deg);
        }
        
        /* Prescription items with handwritten style */
        .container-prescription {
            padding: 5px 0;
            position: relative;
        }
        
        .container-prescription p {
            margin-bottom: 5px;
            position: relative;
        }
        
        /* This gives each prescription item a more handwritten look */
        .container-prescription p.font-bold {
            font-size: 16px;
            color: #14365d;
            margin-left: 5px;
            position: relative;
            font-family: 'Caveat', cursive;
            font-weight: 700;
            transform: rotate(-0.5deg);
            text-decoration: underline;
            text-decoration-thickness: 1px;
            text-decoration-color: #14365d;
        }
        
        /* Style for prescriptions text */
        .container-prescription p:not(.font-bold) {
            margin-left: 25px !important;
            font-size: 14px;
            line-height: 1.4;
            color: #1f4060;
            transform: rotate(-0.2deg);
            position: relative;
        }
        
        /* Special style for commentaires - slanted like doctor's notes */
        .container-prescription p.italic {
            font-style: italic;
            color: #3b5170;
            transform: rotate(-1deg);
            font-size: 13px;
        }
        
        /* Warning text stands out */
        .container-prescription p.text-red-600 {
            color: #b92d2d !important;
            font-weight: 500;
            text-decoration: underline;
            text-decoration-style: wavy;
            text-decoration-color: #b92d2d;
        }
        
        /* Custom line breaks between prescriptions */
        .container-prescription hr {
            border: none;
            height: 1px;
            background-image: linear-gradient(to right, transparent, #afc0d5, transparent);
            margin: 8px 0;
        }
        
        /* Date style like doctor's handwriting */
        .date-text {
            text-align: right;
            margin-right: 40px;
            font-weight: bold;
            color: #1f4060;
            font-size: 14px;
            margin-bottom: 15px;
            transform: rotate(-1deg);
        }
        
        /* Patient name styling */
        h2.text-[11px] {
            font-size: 16px !important;
            margin-bottom: 10px;
            font-weight: 600;
            color: #0d3b78;
            font-family: 'Caveat', cursive;
            text-decoration: underline;
            text-align: left;
            margin-left: 20px;
            transform: rotate(-0.5deg);
        }
    </style>
</head>
<body onload="window.print()">

    <div class="section-entete">
        <div class="info text-left">
            <span class="font-bold text-blue-600 text-sm" style="font-family: Arial, sans-serif; cursive; font-size: 16px; color: #0c4d9c;">Dr. Lamiae EL MOUTAOUI</span><br>
            <span class="text-[11px]" style="font-family: Arial, sans-serif; font-size: 10px;">Médecin spécialiste en maladies <br> de la peau, des ongles et des cheveux</span>
            <ul class="list-disc pl-5 text-[10px]" style="font-family: Arial, sans-serif; font-size: 10px;">
                <li>Allergies cutanées</li>
                <li>Dermatologie pédiatrique</li>
                <li>Chirurgie dermatologique</li>
                <li>Maladies sexuellement transmissibles</li>
                <li>Dermatologie esthétique : (Laser, comblement, peeling, Botox, PRP)</li>
            </ul>
        </div>
        <div class="logo">
            <img src="/cdn/logo_cust.png" alt="Logo" class="mr-[10px]" style="transform: rotate(1deg);">
        </div>
        <div class="info-ar">
            <span class="font-bold text-blue-600 text-sm" style="font-family: 'Caveat', cursive; font-size: 16px; color: #0c4d9c;">د. لمياء المطاوي</span><br>
            <span class="text-[11px]" style="font-family: Arial, sans-serif; font-size: 10px;">طبيبة متخصصة في أمراض <br> الجلد، الشعر والأظافر</span>
            <ul class="list-disc liste-rtl text-[10px]" style="font-family: Arial, sans-serif; font-size: 10px;">
                <li>الحساسية الجلدية</li>
                <li>أمراض الجلد عند الأطفال</li>
                <li>الجراحة الجلدية</li>
                <li>الأمراض المنقولة جنسياً</li>
                <li>طب الجلد التجميلي : (الليزر، التقشير، البوتوكس، البلازما المجددة)</li>
            </ul>
        </div>
    </div>

    <div class="m-6">
        <div>
            <h1 class="title">Ordonnance</h1>
            <h2 class="text-[11px] font-bold mt-2">Patient : <?php echo e($patient->surname, false); ?> <?php echo e($patient->name, false); ?></h2>
            
            <div class="date-text">
                <p class="!text-md"> <strong>Béni Mellal le :</strong> <?php echo e(date('d/m/Y'), false); ?></p>
            </div>

        </div>
    </div>

    <div class="m-6 content" style="font-family: 'Kalam', cursive;">
        <?php
            $counter = 1; // Initialize the counter
        ?>
        <?php $__currentLoopData = $ordonnance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="container-prescription">
            <?php if($item->medicament): ?>
                <p class="font-bold"><?php echo e($counter, false); ?>. <?php echo e($item->medicament, false); ?></p>
                <?php
                    $counter++; // Increment the counter for the next item
                ?>
            <?php endif; ?>
            
            <p class="ml-5">
                <?php if($item->frequency): ?>
                    <?php echo e($item->frequency, false); ?>

                <?php endif; ?>
                
                <?php if($item->matin || $item->midi || $item->soir || $item->au_coucher): ?>
                    (
                    <?php if($item->matin): ?> <?php echo e($item->matin, false); ?> le matin <?php endif; ?>
                    <?php if($item->matin && $item->midi): ?> - <?php endif; ?>
                    <?php if($item->midi): ?> <?php echo e($item->midi, false); ?> à midi <?php endif; ?>
                    <?php if(($item->matin || $item->midi) && $item->soir): ?> - <?php endif; ?>
                    <?php if($item->soir): ?> <?php echo e($item->soir, false); ?> le soir <?php endif; ?>
                    <?php if(($item->matin || $item->midi || $item->soir) && $item->au_coucher): ?> - <?php endif; ?>
                    <?php if($item->au_coucher): ?> <?php echo e($item->au_coucher, false); ?> au coucher <?php endif; ?>
                    )
                <?php endif; ?>
                
                <?php if($item->administration_mode): ?>
                    <?php echo e($item->administration_mode, false); ?>

                <?php endif; ?>
                
                <?php if($item->duration_value && $item->duration_unit): ?>
                    pendant <?php echo e($item->duration_value, false); ?> <?php echo e($item->duration_unit, false); ?>

                <?php endif; ?>
            </p>
            
            <?php if($item->commentaire): ?>
                <p class="ml-5 italic"><?php echo e($item->commentaire, false); ?></p>
            <?php endif; ?>
            
            <?php if($item->contraindications): ?>
                <p class="ml-5 text-red-600 text-sm">
                    <strong>Attention:</strong> 
                    <?php if(is_array($item->contraindications)): ?>
                        <?php echo e(implode(', ', $item->contraindications), false); ?>

                    <?php else: ?>
                        <?php echo e($item->contraindications, false); ?>

                    <?php endif; ?>
                </p>
            <?php endif; ?>
            
            <hr class="my-3">
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <div class="signature" style="text-align: right; margin-top: 30px; margin-right: 30px;display: none" >
            <p style="font-family: 'Caveat', cursive; font-weight: bold; font-size: 18px; transform: rotate(-3deg); color: #0c4d9c;">Dr. Lamiae</p>
            <!-- Optional: Add a small scribble-like signature -->
            <div style="margin-top: -5px; text-align: right; margin-right: 10px;">
                <svg width="70" height="20" viewBox="0 0 70 20">
                    <path d="M5,10 C10,5 20,15 30,10 C40,5 50,15 60,10" stroke="#0c4d9c" fill="none" stroke-width="1.5"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="barcode-container">
        <svg id="barcode"></svg>
        <p id="barcode_text"></p>
    </div>

    <!-- Footer with contact information -->
    <div class="footer">
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
                width: 1,
                displayValue: false,
                margin: 5
            });
            document.getElementById('barcode_text').innerText = paddedId;
        });
        
        // Page break handling for multi-page prescriptions
        document.addEventListener('DOMContentLoaded', function() {
            const prescriptionContainer = document.querySelector('.m-6.content');
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
                
                // Add "Suite" text to indicate continuation with handwritten style
                const suiteText = document.createElement('div');
                suiteText.className = 'm-6';
                suiteText.innerHTML = '<div class="text-center"><p class="title" style="font-size: 1.5rem !important; margin-bottom: 20px;">Suite de l\'ordonnance</p></div>';
                newPage.appendChild(suiteText);
                
                // Create a new container for prescriptions
                const newContainer = document.createElement('div');
                newContainer.className = 'm-6 content';
                newContainer.style.fontFamily = "'Kalam', cursive";
                
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
                
                // Add signature to the second page too
                const signature = document.createElement('div');
                signature.className = 'signature';
                signature.style = "text-align: right; margin-top: 30px; margin-right: 30px;";
                signature.innerHTML = `
                    <p style="font-family: 'Caveat', cursive; font-weight: bold; font-size: 18px; transform: rotate(-3deg); color: #0c4d9c;">Dr. Lamiae</p>
                    <div style="margin-top: -5px; text-align: right; margin-right: 10px;">
                        <svg width="70" height="20" viewBox="0 0 70 20">
                            <path d="M5,10 C10,5 20,15 30,10 C40,5 50,15 60,10" stroke="#0c4d9c" fill="none" stroke-width="1.5"/>
                        </svg>
                    </div>
                `;
                newContainer.appendChild(signature);
                
                newPage.appendChild(newContainer);
                
                // Clone the footer and barcode
                const barcode = document.querySelector('.barcode-container').cloneNode(true);
                const footer = document.querySelector('.footer').cloneNode(true);
                
                newPage.appendChild(barcode);
                newPage.appendChild(footer);
                
                // Add the new page to the document
                document.body.appendChild(newPage);
                
                // Generate barcode for the new page
                let firstId = 091035279;
                let paddedId = firstId.toString().padStart(9, '0');
                
                JsBarcode(newPage.querySelector("#barcode"), paddedId, {
                    format: "CODE128",
                    height: 30,
                    width: 1,
                    displayValue: false,
                    margin: 5
                });
                newPage.querySelector('#barcode_text').innerText = paddedId;
            }
            
            // Run the function after a small delay to ensure content is rendered
            setTimeout(checkAndSplitContent, 100);
        });
    </script>
</body>
</html><?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\backend-code\backend-final\resources\views/ordonnance.blade.php ENDPATH**/ ?>