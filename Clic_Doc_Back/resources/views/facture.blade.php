<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturation</title>
    <style>
        @page {
            size: A4;
            margin: 0.3in;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding: 20px 0;
        }

        .header-section .info {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .header-section img {
            max-width: 100px;
            height: auto;
            margin: 0 20px;
        }

        .title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #333;
        }

        .content {
            font-size: 1rem;
            line-height: 1.6;
            text-align: left;
            margin-bottom: 30px;
            padding: 0 20px;
        }

        .table-section {
            margin: 20px 0;
            padding: 0 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        td {
            vertical-align: middle;
        }

        .number-column {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .subtotal-row {
            border-top: 2px solid #ddd;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 0.9rem;
            padding: 0 20px;
            position: relative;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body onload="window.print()">
    <div>
        <!-- Header Section -->
        <div class="header-section">
            <div class="info">
                <strong>{{ $docteur->name }}</strong><br>
                Médecin Dermatologue<br>
                {{ $docteur->mobile_number }}<br>
                {{ $docteur->email }}
            </div>
            <div>
                <img src="/cdn/logo.png" alt="Logo">
            </div>
            <div class="info" style="text-align: right;">
                <strong>{{ $entite->name }}</strong><br>
                {{ $entite->adress }}, {{ $entite->city }}<br>
                {{ $entite->contact_number }}
            </div>
        </div>

        <hr>

        <!-- Title -->
        <div class="title">Facturation</div>

        @if(isset($message))
            <div class="text-center text-red-500 font-bold">
                {{ $message }}
            </div>
        @else
            <!-- Patient Details -->
            <div class="content">
                <strong>Patient :</strong> {{ $patient->surname }} {{ $patient->name }}
                <br>
                <strong>Facture :</strong> {{ $facture->uid }} 
            </div>

            <!-- Articles Section -->
            <div class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="number-column" style="text-align: center">Quantité</th>
                            <th class="number-column" style="text-align: center">Prix Unitaire</th>
                            <th class="number-column" style="text-align: center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($articlesGrouped as $articles)
                            @foreach ($articles as $article)
                                <tr>
                                    <td>{{ $article->libelle }}</td>
                                    <td class="number-column" style="text-align: center">1</td>
                                    <td class="number-column">{{ number_format($article->prix, 2) }} MAD</td>
                                    <td class="number-column">{{ number_format($article->prix, 2) }} MAD</td>
                                    @php $total += $article->prix; @endphp
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="subtotal-row">
                            <td colspan="3" style="text-align: right;"><strong>Sous-total</strong></td>
                            <td class="number-column">{{ number_format($total, 2) }} MAD</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>Total à payer</strong></td>
                            <td class="number-column">{{ number_format($total, 2) }} MAD</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- Footer Section -->
        <div class="footer">
            Fait à {{ $entite->city }}, le : {{ date('d/m/Y') }}
        </div>
    </div>
</body>
</html>
