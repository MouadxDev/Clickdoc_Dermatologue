<!DOCTYPE html> 
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
    <style>
        @page {
            size: A4;
            margin: 0.3in;
            
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            background-color: #f8f9fa;
        }

        .receipt-container {
            width: 100%;
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-section .info {
            font-size: 0.9rem;
            width: 30%;
        }

        .header-section img {
            max-width: 100px;
            height: auto;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 2px solid #ddd;
        }

        .title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            color: #0092C5;
            text-transform: uppercase;
            margin-bottom: 40px;
            margin-top: 40px;
        }

        .details {
            margin-top: 20px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details th, 
        .details td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 0.9rem;
        }

        .details th {
            text-align: left;
            font-weight: bold;
            background: #f0f0f0;
        }

        .summary {
            margin-top: 20px;
            background: #0092C5;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary th, 
        .summary td {
            padding: 10px;
            color: white;
        }

        .summary th {
            text-align: left;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.85rem;
            color: #666;
        }

        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="receipt-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="info">
                <p><strong>{{ $doctor }}</strong></p>
                <p>Docteur en Médecine</p>
            </div>
            <div class="logo">
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info" style="text-align: right;">
                <p><strong>{{ $entity['name'] }}</strong></p>
                <p>{{ $entity['address'] }}, {{ $entity['city'] }}</p>
                <p>{{ $entity['email'] }}</p>
            </div>
        </div>

        <hr>

        <!-- Title -->
        <div class="title">
            Reçu de Paiement
        </div>

        <!-- Details Section -->
        <div class="details">
            <table>
                <tr>
                    <th>Date</th>
                    <td>{{ date('d/m/Y', strtotime($payment_date)) }}</td>
                </tr>
                <tr>
                    <th>Reçu Pour</th>
                    <td>{{ $patient }}</td>
                </tr>
                <tr>
                    <th>Montant Total</th>
                    <td>{{ number_format($payment_value, 2) }} MAD</td>
                </tr>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary">
            <table>
                <tr>
                    <th>Montant Total :</th>
                    <td>{{ number_format($payment_value, 2) }} MAD</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Merci pour votre paiement !</p>
            <p>Ce reçu est généré électroniquement et ne nécessite pas de signature.</p>
        </div>
    </div>
</body>
</html>
