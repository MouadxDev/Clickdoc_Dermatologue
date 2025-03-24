<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Certificat Médical de Repos</title>
    <style>
        @page {
            margin-top: 0.2in;
            margin-bottom: 0.1in;
            margin-right: 0.2in;
            margin-left: 0.2in;
            size: A5 portrait;
        }
        P{
            font-size: 11px
        }

        /* body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        } */
        body {
            font-family: Arial, sans-serif;
            font-size: 0.8rem;
            margin-bottom: 50px;
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
            margin-top: 30px;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .content {
            margin: 0 10px 30px;
            font-size: 1rem;
            line-height: 1.6;
            text-align: justify;
        }
        .signature {
            margin-top: 150px;
            text-align: right;
            font-size: 1rem;
        }
        .days-input {
            border: none;
            width: 50px;
            text-align: center;
            font-size: inherit;
            border-bottom: 1px solid black;
        }
        /* Modal Styles */
        .modal {
            display: none;
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
            backdrop-filter: blur(5px); /* Adds a blur effect to background */
        }
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
        }
        .liste-rtl {
            list-style-position: inside;
            padding-right: 5px;
            padding-left: 0;
            text-align: right;
            direction: rtl;
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
            page-break-after: always;
      
        }
    
        .footer-left {
        }
    
        .footer-right {
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 16px; /* Increased border radius for softer look */
            text-align: center;
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.1), 
                0 5px 15px rgba(0, 0, 0, 0.05); /* More sophisticated shadow */
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
            transition: border-color 0.3s ease;
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
            transition: 
            background-color 0.3s ease, 
            transform 0.2s ease,
            box-shadow 0.2s ease;
        }

        .date {
            text-align: center;
            /* margin-top: 20px; */
            /* padding-right: 50px; */
            font-size: 12px;
        }

        .modal-content button:hover {
            background-color: #2c7bad;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .modal-content p{
            font-family: arial;
            font-size: 14px;
        }

     

    </style>
</head>
<body>
    <div class="modal" id="daysModal">
        <div class="modal-content">
            <p>Veuillez indiquer le nombre de jours de repos nécessaires pour l'arrêt de travail :</p>
            <input type="number" id="daysInput" placeholder="Nombre de jours">
            
            <button onclick="setDaysAndPrint()">OK</button>
        </div>
    </div>

    <div>
        <!-- Header Section -->
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

        

        <!-- Title -->
        <div class="title text-[#2c31a5]">
            CERTIFICAT MÉDICAL DE REPOS
        </div>

        <div class="date">
            <p class="!text-md"> <strong> Béni Mellal le :</strong> <span id="currentDate"></span></p>
        </div>

        <!-- Content Section -->
        <div class="content" style="display: flex;flex-direction: column;gap: 5px">
            <p style="padding-top: 70px;" class="text-[15px]">
                Je, soussigné(e) <strong><?php echo e($docteur->name, false); ?></strong>, certifie avoir examiné ce jour
                <?php if(isset($patient)): ?>
                    <?php if($patient->sex == 'M'): ?>
                        M.
                    <?php elseif($patient->sex == 'F'): ?>
                        Mme
                    <?php else: ?>
                        Mlle
                    <?php endif; ?>
                <?php endif; ?>
                <strong><?php echo e($patient->surname, false); ?> <?php echo e($patient->name, false); ?></strong>.
            </p>
            <p class="text-[15px]">
                Après évaluation, l'état de santé du(de la) patient(e) nécessite un repos médical avec arrêt de travail d'une durée de 
                <input type="number" class="days-input" id="daysField" readonly placeholder="____"> 
                jours, sauf complications.
            </p>
            <p class="text-[15px]">
                En foi de quoi, le présent certificat est délivré à l’intéressé(e) pour servir et valoir ce que de droit.
            </p>
        </div>


        <!-- Signature Section -->
        <div class="signature text-[13px] ">
            
            <p>
                Signature : _______________________
            </p>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left flex">
            <p class="text-center"> Qr El Adarissa Rue 5 N° 64, 1er Étage, Beni Mellal 
                - <strong>GSM :</strong> 06.60.80.86.74 | <strong>Fixe :</strong> 05.23.42.00.82
                <strong>Email :</strong> elmoutaoui_lamiae2@yahoo.fr</p>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('daysModal');
            const modalContent = modal.querySelector('.modal-content');
            
            // Show modal on initial page load
            modal.style.display = 'flex';
            setTimeout(() => {
                modalContent.classList.add('show');
            }, 10);

            // Detect when print mode is cancelled or exited
            window.addEventListener('afterprint', function() {
                // Reset modal display
                modal.style.display = 'flex';
                setTimeout(() => {
                    modalContent.classList.add('show');
                }, 10);
            });

            function setDaysAndPrint() {
                const days = document.getElementById('daysInput').value;
                
                // Validate input
                if (!days || days <= 0) {
                    alert('Veuillez saisir un nombre de jours valide.');
                    return;
                }
            
                // Set days in main document
                document.getElementById('daysField').value = days;
            
                // Hide modal with animation
                modalContent.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    window.print();
                }, 300);
            }

            // Expose function to global scope
            window.setDaysAndPrint = setDaysAndPrint;
        });
        
    </script>
    
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
<?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\Clickdoc Dermatologue\Clic_Doc_Back\resources\views/certificat-medical.blade.php ENDPATH**/ ?>