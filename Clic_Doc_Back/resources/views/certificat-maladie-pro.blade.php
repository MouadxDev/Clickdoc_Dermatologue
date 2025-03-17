<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Déclaration de Maladie Professionnelle</title>
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
            margin-bottom: 50px;
        }
        .title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 50px;
        }
        .content {
            margin: 0 10px 30px;
            font-size: 1rem;
            line-height: 1.6;
            text-align: justify;
        }
        .content span {
            font-weight: bold;
        }
        .signature {
            margin-top: 450px;
            text-align: right;
            font-size: 1rem;
        }
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
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.1), 
                0 5px 15px rgba(0, 0, 0, 0.05);
            width: 90%;
            max-width: 400px;
        }
        .modal-content input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <div class="modal" id="dataModal">
        <div class="modal-content">
            <p>Veuillez compléter les informations suivantes :</p>
            <input type="text" id="patientAddressInput" placeholder="Adresse du malade"><br>
            <input type="text" id="patientJobInput" placeholder="Nature du travail effectué"><br>
            <input type="text" id="employerNameInput" placeholder="Nom et adresse de l'employeur"><br>
            <input type="text" id="symptomsInput" placeholder="Symptômes"><br>
            <input type="text" id="diseaseInput" placeholder="Nature de la maladie"><br>
            <input type="text" id="consequencesInput" placeholder="Conséquences"><br>
            <button onclick="setDataAndPrint()">Valider</button>
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
                <span>{{ $entite->contact_number }}</span>
            </div>
        </div>
        <hr>

        <!-- Title -->
        <div class="title">
            Certificat de Déclaration de Maladie Professionnelle
        </div>

        <!-- Content Section -->
        <div class="content">
            <p>
                Je soussigné <strong>{{ $docteur->name }}</strong>, demeurant à {{ $docteur->address }}.
            </p>
            <p>
                Après avoir examiné <strong>{{ $patient->surname }} {{ $patient->name }}</strong>, 
                demeurant à <span id="patientAddress">________________________</span>.
            </p>
            <p>
                Qui m’a déclaré être occupé en qualité de 
                <span id="patientJob">________________________</span> 
                chez <span id="employerName">________________________</span>.
            </p>
            <p>
                Certifie qu'il présente les symptômes suivants : <span id="symptoms">________________________</span>.
            </p>
            <p>
                Et estime qu’il serait atteint de <span id="disease">________________________</span>, 
                maladie mentionnée au tableau annexé à l’arrêté n°919-99 du 23-12-1999 du Ministre du développement social, de la solidarité, de l’emploi et de la formation professionnelle, pris pour l’exécution du dahir de 1943.
            </p>
            <p>
                Cette maladie paraît devoir entraîner les conséquences suivantes : <span id="consequences">________________________</span>.
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
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('dataModal');

            modal.style.display = 'flex';

            window.addEventListener('afterprint', function () {
                modal.style.display = 'flex';
            });
        });

        function setDataAndPrint() {
            const patientAddress = document.getElementById('patientAddressInput').value;
            const patientJob = document.getElementById('patientJobInput').value;
            const employerName = document.getElementById('employerNameInput').value;
            const symptoms = document.getElementById('symptomsInput').value;
            const disease = document.getElementById('diseaseInput').value;
            const consequences = document.getElementById('consequencesInput').value;

            if (!patientAddress || !patientJob || !employerName || !symptoms || !disease || !consequences) {
                alert('Veuillez remplir tous les champs.');
                return;
            }

            document.getElementById('patientAddress').textContent = patientAddress;
            document.getElementById('patientJob').textContent = patientJob;
            document.getElementById('employerName').textContent = employerName;
            document.getElementById('symptoms').textContent = symptoms;
            document.getElementById('disease').textContent = disease;
            document.getElementById('consequences').textContent = consequences;

            const modal = document.getElementById('dataModal');
            modal.style.display = 'none';

            window.print();
        }
    </script>
</body>
</html>
