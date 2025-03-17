<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection de Facture</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-6 max-w-lg w-full">
        <h2 class="text-xl font-bold mb-4">Sélectionnez une Facture à Imprimer</h2>
        
        <!-- Search Bar -->
        <input type="text" id="search" placeholder="Rechercher une facture..." 
               class="w-full px-3 py-2 border rounded mb-4" onkeyup="filterFactures()">
        
        @if($message)
            <div class="text-center text-red-500 font-bold">
                {{ $message }}
            </div>
        @else
            <div class="max-h-[500px] overflow-y-auto">
                <ul id="factureList" class="divide-y divide-gray-300">
                    @foreach ($factures as $facture)
                        <li class="py-3 flex justify-between items-center facture-item">
                            <div>
                                <div class="text-lg font-bold">Facture ID: {{ $facture->uid }}</div>
                                <div>Montant: {{ $facture->amount }} MAD</div>
                                <div>Statut: {{ $facture->statut }}</div>
                            </div>
                            <a href="{{ url('/facturation/print/' . $facture->id . '?uid=' . request()->id . '&docteur=' . request()->docteur) }}"
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Imprimer
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $factures->links() }}
            </div>
        @endif
    </div>

    <script>
        function filterFactures() {
            let input = document.getElementById("search").value.toLowerCase();
            let items = document.querySelectorAll(".facture-item");
            
            items.forEach(item => {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(input) ? "flex" : "none";
            });
        }
    </script>
</body>
</html>
