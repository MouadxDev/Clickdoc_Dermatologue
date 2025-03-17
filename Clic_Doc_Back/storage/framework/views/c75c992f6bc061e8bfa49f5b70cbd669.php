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
        
        <?php if($message): ?>
            <div class="text-center text-red-500 font-bold">
                <?php echo e($message, false); ?>

            </div>
        <?php else: ?>
            <div class="max-h-[500px] overflow-y-auto">
                <ul id="factureList" class="divide-y divide-gray-300">
                    <?php $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="py-3 flex justify-between items-center facture-item">
                            <div>
                                <div class="text-lg font-bold">Facture ID: <?php echo e($facture->uid, false); ?></div>
                                <div>Montant: <?php echo e($facture->amount, false); ?> MAD</div>
                                <div>Statut: <?php echo e($facture->statut, false); ?></div>
                            </div>
                            <a href="<?php echo e(url('/facturation/print/' . $facture->id . '?uid=' . request()->id . '&docteur=' . request()->docteur), false); ?>"
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Imprimer
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            
            <!-- Pagination Links -->
            <div class="mt-4">
                <?php echo e($factures->links(), false); ?>

            </div>
        <?php endif; ?>
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
<?php /**PATH C:\Users\user\Documents\CLICK DOC WEB APP\backend-code\backend-final\resources\views/facture-selection.blade.php ENDPATH**/ ?>