<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat Médical de Repos</title>
    <style>
        @page {
            size: A4;
            margin: 0.3in;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
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
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .content {
            margin: 0 10px 30px;
            font-size: 1rem;
            line-height: 1.6;
            text-align: justify;
        }
        .signature {
            margin-top: 550px;
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

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 16px; /* Increased border radius for softer look */
            text-align: center;
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.1), 
                0 5px 15px rgba(0, 0, 0, 0.05); /* More sophisticated shadow */
            width: 90%;
            max-width: 400px;
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
        <div class="header-section">
            <div class="info">
                <span><strong>{{ $docteur->name }}</strong></span><br>
                <span>Docteur en Médecine</span><br>
                <span>{{ $docteur->mobile_number }}</span><br>
                <span>{{ $docteur->email }}</span>
            </div>
            <div class="logo">
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info" style="text-align: right;">
                <span><strong>{{ $entite->name }}</strong></span><br>
                <span>{{ $entite->adress }}, {{ $entite->city }}</span><br>
                <span>0676616626</span>
            </div>
        </div>

        <hr>

        <!-- Title -->
        <div class="title">
            CERTIFICAT MÉDICAL DE REPOS
        </div>

        <!-- Content Section -->
        <div class="content">
            <p>
                Je soussigné, <strong>{{ $docteur->name }}</strong>, docteur en médecine, certifie avoir examiné ce jour,
                @if($patient->sex=='M') le @else la @endif dénommé@if($patient->sex!='M')e @endif 
                <strong>{{ $patient->surname }} {{ $patient->name }}</strong>.
            </p>
            <p>
                Et déclare que son état de santé nécessite un repos avec arrêt de travail de 
                <input type="number" class="days-input" id="daysField" readonly placeholder="____"> 
                jours sauf complications.
            </p>
            <p>
                En foi de quoi, le présent certificat est délivré à l’intéressé@if($patient->sex!='M')e @endif pour servir et valoir ce que de droit.
            </p>
        </div>

        <!-- Signature Section -->
        <div class="signature">
            <p>
                Fait à {{ $entite->city }}, le : {{ date('d/m/Y') }}
            </p>
            <p>
                Signature : _______________________
            </p>
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
</body>
</html>
