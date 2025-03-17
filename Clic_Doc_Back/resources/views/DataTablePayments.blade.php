<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0092C5;
            --primary-light: #00AAD8;
            --primary-dark: #007AAB;
            --light-bg: #F8FBFE;
        }

        body {
            background-color: var(--light-bg);
            color: #2D3748;
        }

        .container {
            /* max-width: 1200px; */
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 146, 197, 0.1);
        }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .form-control, .form-select {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 146, 197, 0.1);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-success {
            background: #10B981;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-success:hover {
            background: #059669;
        }
        .btn-success:hover,
        .btn-primary:hover,
        .btn-secondary:hover {
            transform: translateY(-1px);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--light-bg);
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid #E2E8F0;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .transaction-type {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .type-entrees {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .type-sorties {
            background: rgba(239, 68, 68, 0.1);
            color: #DC2626;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2 class="m-0">Transactions pour le Médecin : {{ $doctor }}</h2>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('DataTableFactures.display', ['doctor_id' => $doctor_id]) }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Date de Début</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Date de Fin</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="type_filter" class="form-label">Type</label>
                        <select id="type_filter" name="type_filter" class="form-select">
                            <option value="Tout" {{ request('type_filter') === 'Tout' ? 'selected' : '' }}>Tout</option>
                            <option value="Entrées" {{ request('type_filter') === 'Entrées' ? 'selected' : '' }}>Entrées</option>
                            <option value="Sorties" {{ request('type_filter') === 'Sorties' ? 'selected' : '' }}>Sorties</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('DataTableFactures.display', ['doctor_id' => $doctor_id]) }}" class="btn btn-secondary" style="display: flex;align-items: center;">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                        <a href="{{ route('transactions.export', ['doctor_id' => $doctor_id]) }}" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Exporter en Excel
                        </a>
                    
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>UID de Facture</th>
                        <th>Patient</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->facture_uid ?? 'N/A' }}</td>
                            <td>{{ $transaction->patient_name ?? 'N/A' }}</td>
                            <td>{{ $transaction->amount }} MAD</td>
                            <td>
                                <span class="transaction-type {{ $transaction->type === 'Sorties' ?  'type-sorties' : 'type-entrees'  }}">
                                    {{ $transaction->type }}
                                </span>
                            </td>
                            <td>{{ $transaction->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="m-0">Aucune transaction trouvée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>