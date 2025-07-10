<script setup lang="ts">
    import { useUtilStore } from '../../core/Data/stores/utilitaire';
    import { WaitingList } from '../../core/Clients/WaitingList';
    import { RendezVous } from '../../core/Clients/RendezVous';
    import { Facture } from '../../core/Clients/Facture';
    import { useRdvStore } from '../../core/Data/stores/rendez-vous';
    import { Ref, onBeforeMount, ref , watch, reactive, computed } from 'vue';
    import { Payment } from '../../core/Clients/Payment';
    import { useRouter } from 'vue-router';
	import axios from "axios"

    const store = useUtilStore()
    const rdvStore = useRdvStore()
    const client = new WaitingList()
    const factureClient = new Facture()
    const paymentClient = new Payment()
    const rdvClient = new RendezVous()
    const patients : Ref<any> = ref([]);
    const factures : Ref<any> = ref([]);
    const patient : Ref<number> = ref(0);
    const router = useRouter()
    const fields =[
        {prop:'surname',label:'Prénom'},
        {prop:'name',label:'Nom'},
        {prop:'type',label:'Type '},
        {prop:'heure',label:'Heure '}
    ]
    const showEditModal = ref(false);
    
    const rdv = reactive({
        patient_id:"",
        id:"",
        heure:"",
        statut:""
    })

	const long_banner = ref<any>(null)
	const square_banner = ref<any>(null)
    const heures = ["06:00","06:30","07:00","07:30","08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30","20:00"]
    const RdvS : Ref<Array<any>> = ref([])
	const report : Ref<any> = ref({
        mca:0,
        dca:0
    })
	const addPayment :Ref<boolean> = ref(false)
	const facture_to_send :Ref<number> = ref(0)
	const index_facture :Ref<number> = ref(0)
    const paiement :Ref<any> = ref({
        patient_id:0,
        amount:0,
        type:"Espèce"
    })

    async function getData () {
        RdvS.value = await rdvClient.isToday();        
    }
	
	async function getReports(){
		const data = await paymentClient.getReport("")
		report.value.mca = 0
		data?.mca.forEach((element:any) => {
			report.value.mca += element.amount
		});
		report.value.dca = 0
		data?.dca.forEach((element:any) => {
			report.value.dca += element.amount
		});
		report.value.wL = data.wL
		report.value.patients = data.patients

	}

    const totalPaid = computed(() => {
        return factures.value.reduce((total:number, facture:any) => {
            return total + facture.total_paid;  // Use the total_paid directly
        }, 0);
    });

    const totalToPay = computed(() => {
        return factures.value.reduce((total:number, facture:any) => {
            // Only include the remaining amount for factures that are not fully paid
            if (facture.statut !== 'Payé en totalité') {
                return total + facture.remaining_amount+facture.total_paid;
            }
            return total;
        }, 0);
    });

    const remainingAmount = computed(() => {
        return factures.value.reduce((total:number, facture:any) => {
            // Sum the remaining amounts for factures that are not fully paid
            if (facture.statut !== 'Payé en totalité') {
                return total + facture.remaining_amount;
            }
            return total;
        }, 0);
    });


    async function loadSitFinanciere(){
        factures.value = await factureClient.getThings(patient.value);
    }

	async function getBanners ()
	{
		try {
		const responseLong = await axios.get("https://directus.clickdoc.ma/publicity/items/long_banners?fields=*.*,image.*&sort=-id")
		const responseSquare = await axios.get("https://directus.clickdoc.ma/publicity/items/long_banners?fields=*.*,image.*&sort=-id")
		long_banner.value = responseLong.data.data[0]
		square_banner.value = responseSquare.data.data[0]
		}catch(error){
			console.log("no banner")
		}
	}
    async function saveFacture(){
        await paymentClient.add(paiement.value)
        paiement.value={
            patient_id:0,
            amount:0,
            type:"Espèce",
			facture_id:facture_to_send.value
        }
        await factureClient.update({id:facture_to_send.value,statut:factures.value[index_facture.value].statut})
        addPayment.value=false
        await loadSitFinanciere()
    }
    onBeforeMount(async ()=>{
        await getData()
		await getBanners()
		await getReports()
        patients.value = await factureClient.getAll();
    })
	
    function changeState(index: number) {
        index_facture.value = index;
        paiement.value.patient_id = factures.value[index].patient_id;
        facture_to_send.value = factures.value[index].id;
        paiement.value.facture_id = factures.value[index].id;
        
        if (factures.value[index].statut === "Payé partiellement") {
            // For partially paid invoices, show the modal to add payment
            addPayment.value = true;
        } else if (factures.value[index].statut === "Payé en totalité") {
            // For fully paid invoices, also show the modal (if desired)
            addPayment.value = true;
            // Pre-fill the payment amount with the remaining amount (which equals the invoice amount)
            paiement.value.amount = factures.value[index].amount;
        }
    }


    const actions = [
        {
            label: 'Edit',
            icon: 'edit',
            action: openEditModal
        },
        {
            icon: "View",
            text:"voir",
            action: (n:any)=>{show(n.uid)}
        },
    ]

    async function saveEdit() {
        await rdvClient.update(rdv);        
        rdvStore.setTrigger(true);
    }

    function show(n:any)
    {
        router.push("/dossiers/"+n)
    }

    function openEditModal(rowData) {
        rdv.heure = rowData.heure
        rdv.statut = rowData.statut
        rdv.id = rowData.rdv_id
        rdv.date = rowData.date
        rdv.patient_id = rowData.patient_id
        rdv.page = 1
        rdv.toGet = 25
        showEditModal.value = true;
    }

    watch(rdvStore, async (newState) => {
        if(newState.trigger == true){
            await getData()
            rdvStore.setTrigger(false)
        }
    }, { deep: true})

</script>

<template>
    <main-layout>
        <div class="container mx-auto h-dashboard p-4 rounded-xl  ">
            <div class="stats mt-2 shadow w-full">
                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <img src="https://clickdoc.webredirect.org/public/icons/argent.png" class="h-8 w-8">
                    </div>
                    <div class="stat-title">Chiffre d'affaire mensuel</div>
                    <div class="stat-value"> {{ report.mca }} </div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <img src="https://clickdoc.webredirect.org/public/icons/argent.png" class="h-8 w-8">
                    </div>
                    <div class="stat-title">Revenu d'aujourd'hui</div>
                    <div class="stat-value">{{  report.dca }}</div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <img src="https://clickdoc.webredirect.org/public/icons/salle-dattente.png" class="h-8 w-8">
                    </div>
                    <div class="stat-title">Patients en attente</div>
                    <div class="stat-value">{{ report.wL }}</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-clickdoc">
                        <img src="https://clickdoc.webredirect.org/public/icons/dossiers.png" class="h-8 w-8">
                    </div>
                    <div class="stat-title">Patients traités </div>
                    <div class="stat-value">{{ report.patients }}</div>
                </div>
            </div>
			<div class="h-48 my-2 rounded-xl" v-if="long_banner" :style="'background-image:url('+long_banner.image.data.full_url+');background-size:cover;background-position:center'">
				<a class="w-full h-32" :href="long_banner.link"  target="_blank" ></a>
			</div>  
            <el-row :gutter="10" class="mt-4">
                <el-col :span="8">
                    <div class="h-thing rounded-xl bg-white shadow p-4" style="overflow: hidden;">
                        <el-row>
                            <el-col :span="24">
                                <div class="font-bold text-xl"> Salle d'attente </div>
                            </el-col>
                        </el-row>
                        <div class="h-there mt-2 overflow-y-auto">
                            <WaitingRoomVertical 
                                :client="client" 
                                :actions="actions"
                                :triggerStore="useRdvStore"
                                ref="waitingRoomRef"
                            />
                        </div>
                    </div>
                </el-col>
                <el-col :span="16">
                    <div class="rounded-xl bg-white shadow p-4">
                        <button class="btn btn-sm background-clickdoc" @click="store.setRDV(true,'')"> Nouveau RDV </button>
                    </div>
                    <div class="h-there-1 overflow-y-auto mt-6 rounded-xl bg-white shadow p-4 ">
                        <ui-agenda />
                    </div>
                </el-col>
                <el-col :span="12" class="">
                    <div class="h-there-2 rounded-xl bg-white shadow p-6 mt-2">
                        <div class="font-bold text-xl pb-3"> Situations financières </div>
                        <div class="h-there mt-2 overflow-y-auto">
                            <el-form>
                                <el-form-item label="Patient" class="pb-3">
                                    <el-select class="w-full" v-model="patient" @change="loadSitFinanciere()">
                                        <el-option :value="0" label="Aucun Patient selectionné" />
                                        <el-option v-for="p in patients" :value="p.id" :label="p.surname + ' ' + p.name + '(' + p.CIN + ')'" />
                                    </el-select>
                                </el-form-item>
                            </el-form>

                            <!-- Display Total Paid and Remaining -->
                            <div class="grid grid-cols-3 gap-4 mt-2">

                                <div class="bg-green-100 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Total à paye</div>
                                    <div class="text-xl text-green-600">{{ totalToPay || 0 }} MAD</div>
                                </div>
                                <div class="bg-yellow-100 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Total payé </div>
                                    <div class="text-xl text-yellow-600">{{ totalPaid || 0 }} MAD</div>
                                </div>
                                
                                <div class="bg-red-100 p-4 rounded-lg">
                                    <div class="text-sm text-gray-500">Restant à payer</div>
                                    <div class="text-xl text-red-600">{{ remainingAmount || 0 }} MAD</div>
                                </div>
                            </div>

                            <!-- Facture Table -->
                            <el-table :data="factures" :border="true" style="margin-top: 20px;">
                                <el-table-column label="Montant" prop="amount" />
                                <el-table-column label="Statut">
                                    <template #default="scope">
                      
                                        <el-select 
                                            v-model="scope.row.statut" 
                                            @change="changeState(scope.$index)" 
                                            :disabled="factures[scope.$index].statut === 'Payé en totalité'" 
                                            >
                                            <el-option value="Non Payé" label="Non Payé" />
                                            <el-option value="Payé en totalité" label="Payé en totalité" />
                                            <el-option value="Payé partiellement" label="Payé partiellement" />
                                            </el-select>
                                    </template>
                                </el-table-column>
                            </el-table>

                            <!-- Payment Modal -->
                            <el-dialog v-model="addPayment" title="Ajouter nouveau paiement">
                                <el-form v-model="paiement">
                                    <el-form-item label="Montant">
                                        <el-input v-model="paiement.amount" :disabled="factures[index_facture].statut=='Payé en totalité'" />
                                    </el-form-item>
                                    <el-form-item label="Modalité de paiement">
                                        <el-select class="w-full" v-model="paiement.type">
                                            <el-option value="Espèce" label="Espèce" />
                                            <el-option value="Chèque" label="Chèque" />
                                            <el-option value="Carte bancaire" label="Carte bancaire" />
                                        </el-select>
                                    </el-form-item>
                                    <el-form-item>
                                        <button class="btn btn-sm btn-block background-clickdoc" type="button" @click="async()=> await saveFacture()"> Enregistrer </button>
                                    </el-form-item>
                                </el-form>
                            </el-dialog>
                        </div>
                    </div>
                </el-col>

				<el-col :span="12" class="">
                    <div class="h-there-2 rounded-xl bg-white shadow p-6 mt-2 ">
                        <div class="font-bold text-xl"> Tâches </div>
                        <tasks />
                    </div>
                </el-col>
            </el-row>
            
            <el-dialog v-model="showEditModal" title="Edit Patient">
                <el-form>
                    <el-row :gutter="12">
                        <el-col :span="12">
                            <el-form-item label="Heure">
                                <el-select class="w-full" v-model="rdv.heure">
                                    <el-option v-for="h in heures" :key="h" :value="h" :label="h" v-if="async()=>await isEmpty(h)" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        
                        <el-col :span="12">
                            <el-form-item label="Statut">
                                <el-select class="w-full" v-model="rdv.statut"> 
                                    <el-option value="salle attente" label="Salle d'attente" />
                                    <el-option value="canceled" label="Rendez vous annulé" />
                                    <el-option value="honored" label="Rendez vous honoré" />
                                    <el-option value="planified" label="Rendez vous planifié" />
                                    <el-option value="postponed" label="Rendez vous reporté" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    
                    <el-form-item>
                        <button class="btn btn-sm btn-block background-clickdoc" type="button" @click="async () => {await saveEdit()}">Save</button>
                    </el-form-item>
                </el-form>
            </el-dialog>
        </div>
    </main-layout>
</template>