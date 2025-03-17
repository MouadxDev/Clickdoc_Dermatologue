<script lang="ts" setup>
import { Ref, ref, onBeforeMount, computed } from 'vue';
import { Stock } from '../../core/Clients/Stock';
import { eventBus } from '../utils/eventBus';

const client = new Stock();
const newStock = ref(false);
const showDetails = ref(false);
const stockItem: Ref<any> = ref({
    name: "",
    stock: 0,
    expiration_date: ""
});
const stockDetails: Ref<Array<any>> = ref([]);
const table: Ref<any> = ref(null);
const filterExpiring = ref(false);
const originalData = ref([]);

// Function to determine days until expiration
function getDaysUntilExpiration(expirationDate: string): number {
    if (!expirationDate) return Infinity;
    const expDate = new Date(expirationDate);
    const today = new Date();
    return Math.ceil((expDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
}

// Function to get expiry status for styling
function getExpiryStatus(expirationDate: string): string {
    const daysLeft = getDaysUntilExpiration(expirationDate);
    if (daysLeft < 0) return 'expired';
    if (daysLeft <= 7) return 'critical';
    if (daysLeft <= 30) return 'warning';
    return 'ok';
}

async function show(n: any) {
    stockDetails.value = await client.getByID(n);
    showDetails.value = true;
}



const fields = [
    { prop: 'status_icon', label: '', width: '60px' },
    { prop: 'name', label: 'Name' },
    { prop: 'stock', label: 'Stock' },
    { prop: 'expiration_date', label: "Date d'expiration" },
    { prop: 'days_left', label: 'Jours restants', width: '120px' }
];

const actions = [
    {
        icon: "View",
        text: "View",
        action: (n: any) => { show(n.id); }
    },
    {
        icon: "Delete",
        text: "Supprimer",
        action: (n: any) => { show(n.id); }
    }

];

// Computed property to get count of expiring items
const expiringCount = computed(() => {
    return originalData.value.filter((item: any) => 
        getDaysUntilExpiration(item.expiration_date) <= 30
    ).length;
});

// Filter data to show only expiring items
function toggleExpiringFilter() {
    filterExpiring.value = !filterExpiring.value;

    if (filterExpiring.value) {
        eventBus.setFilter('🔴');
    } else {
        eventBus.setFilter(''); 
    }
}


// Computed property to check if the currently viewed item is expiring soon
const isExpiringStockDetail = computed(() => {
    if (stockDetails.value && stockDetails.value.expiration_date) {
        const status = getExpiryStatus(stockDetails.value.expiration_date);
        return {
            status,
            daysLeft: getDaysUntilExpiration(stockDetails.value.expiration_date)
        };
    }
    return { status: 'ok', daysLeft: Infinity };
});

onBeforeMount(async () => {
    await getData();
});


async function getData() {
    const response = await client.getAll();
    originalData.value = response.data;

    if (table.value) {
        table.value.setData({
            data: response.data, // No need for transformation!
            current_page: response.current_page || 1,
            total: response.total || response.data.length,
            per_page: response.per_page || response.data.length
        });
    }
}


async function addStockItem() {
    await client.add(stockItem.value);
    stockItem.value = {
        name: "",
        stock: 0,
        expiration_date: ""
    };
    newStock.value = false;
    await getData();
}

async function UpdateStockItem() {
    await client.update(stockDetails.value);
    stockDetails.value = {
        name: "",
        stock: 0,
        expiration_date: ""
    };
    newStock.value = false;
    showDetails.value = false;
    await getData();
}
</script>

<template>
    <main-layout>
        <div class="container mx-auto">
            <!-- Enhanced Alert banner with action buttons -->
            <el-alert
                v-if="expiringCount > 0"
                :title="`⚠️ Alerte d'expiration: ${expiringCount} produits`"
                type="error"
                :description="`Vous avez ${expiringCount} produits qui expirent bientôt ou sont déjà expirés.`"
                show-icon
                :closable="false"
                class="mb-4 expiry-alert"
            >
                <template #default>
                    <div class="mt-2">
                        <el-button 
                            type="danger" 
                            size="small" 
                            @click="toggleExpiringFilter"
                        >
                            {{ filterExpiring ? 'Afficher tous les produits' : 'Afficher seulement les produits expirants' }}
                        </el-button>
                    </div>
                </template>
            </el-alert>

            <!-- Filter indicator -->
            <div v-if="filterExpiring" class="filter-indicator mb-4">
                <el-tag type="danger">Filtré: Affichage des produits expirants uniquement</el-tag>
                <el-button size="small" @click="toggleExpiringFilter">
                    Réinitialiser le filtre
                </el-button>
            </div>

            <ui-table 
                :is-main="true" 
                :hasButton="true" 
                :client="client" 
                :add="true" 
                :onAdd="() => { newStock = true; }"
                title="Stock List" 
                :fields="fields" 
                :actions="actions" 
                ref="table"
            />

            <el-dialog title="Nouvel article en stock" v-model="newStock">
                <el-form label-position="top">
                    <el-form-item label="Name">
                        <el-input v-model="stockItem.name" />
                    </el-form-item>
                    <el-form-item label="Stock">
                        <el-input type="number" v-model="stockItem.stock" />
                    </el-form-item>
                    <el-form-item label="Date d'expiration">
                        <el-date-picker v-model="stockItem.expiration_date" type="date" style="min-width: 100%;" />
                    </el-form-item>
                </el-form>
                <template #footer>
                    <span class="dialog-footer">
                        <el-button type="primary" @click="async () => { await addStockItem(); }">
                            Sauvegarder
                        </el-button>
                    </span>
                </template>
            </el-dialog>
            
            <el-dialog title="Détails de l'article" v-model="showDetails">
                <!-- Expiration warning in details dialog -->
                <el-alert 
                    v-if="isExpiringStockDetail.status === 'expired'"
                    title="Produit expiré!" 
                    type="error"
                    description="Ce produit est déjà expiré. Veuillez prendre des mesures immédiates."
                    show-icon
                    :closable="false"
                    class="mb-4"
                />
                <el-alert 
                    v-else-if="isExpiringStockDetail.status === 'critical'"
                    title="Expiration imminente!" 
                    type="error"
                    :description="`Ce produit expire dans ${isExpiringStockDetail.daysLeft} jours. Action requise.`"
                    show-icon
                    :closable="false"
                    class="mb-4"
                />
                <el-alert 
                    v-else-if="isExpiringStockDetail.status === 'warning'"
                    title="Expiration proche" 
                    type="warning"
                    :description="`Ce produit expire dans ${isExpiringStockDetail.daysLeft} jours.`"
                    show-icon
                    :closable="false"
                    class="mb-4"
                />

                <el-form label-position="top">
                    <el-form-item label="Nom">
                        <el-input v-model="stockDetails.name" />
                    </el-form-item>
                    
                    <el-form-item label="Stock">
                        <el-input type="number" v-model="stockDetails.stock" />
                    </el-form-item>
                    
                    <el-form-item label="Date d'expiration">
                        <el-date-picker v-model="stockDetails.expiration_date" type="date" style="min-width: 100%;" />
                    </el-form-item>
                </el-form>
                <template #footer>  
                    <el-button type="primary" @click="async () => { await UpdateStockItem(); }">
                        Sauvegarder
                    </el-button>
                </template>
            </el-dialog>
        </div>
    </main-layout>
</template>

<style>
/* Add custom CSS to style the table rows based on expiration status */
.el-table .el-table__row {
    position: relative;
}

/* Add custom style to the ui-table */
:deep(.el-table__row) {
    cursor: pointer;
}

:deep(.el-table__row[data-status="expired"]) {
    background-color: rgba(245, 108, 108, 0.2) !important;
}

:deep(.el-table__row[data-status="critical"]) {
    background-color: rgba(245, 108, 108, 0.1) !important;
}

:deep(.el-table__row[data-status="warning"]) {
    background-color: rgba(230, 162, 60, 0.1) !important;
}

/* Styles for the days left column */
:deep(.el-table__row[data-status="expired"] .days-left-cell) {
    background-color: #f56c6c;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

:deep(.el-table__row[data-status="critical"] .days-left-cell) {
    background-color: #f56c6c;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

:deep(.el-table__row[data-status="warning"] .days-left-cell) {
    background-color: #e6a23c;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

/* Filter indicator */
.filter-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background-color: #fef0f0;
    border-radius: 4px;
}

/* Alert styling */
.expiry-alert {
    border-left: 4px solid #f56c6c;
}
</style>