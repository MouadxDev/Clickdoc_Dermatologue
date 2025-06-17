<script setup lang='ts'>
    import { Ref , onBeforeMount , onMounted, ref , watch } from 'vue';
    import { useUtilStore } from '../../../core/Data/stores/utilitaire';
    import {RendezVous} from '../../../core/Clients/RendezVous.ts'
    import { useRdvStore } from '../../../core/Data/stores/rendez-vous';
    import moment from 'moment';
    import { ElMessage, ElMessageBox } from 'element-plus';
    import router from '../../router/index.ts';


    const client = new RendezVous();
    const date : Ref<Date> =ref(new Date())
    const eventss :Ref<any> = ref([])
	const loaded : Ref<boolean> = ref(false)
    const loading :Ref<boolean> = ref(true); 
    const util = useUtilStore();
    const store = useRdvStore();
    
    const showDay = ref(false)

    const innerList : Ref<Array<any>> = ref([])

    const mainList : Ref<Array<any>> = ref([])
    
    async function confirmDelete(id: number) {
  // Open a confirmation dialog
        const confirm = await ElMessageBox.confirm(
            'Êtes-vous sûr de vouloir supprimer ce rendez-vous ?',
            'Confirmation',
            {
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler',
            type: 'warning',
            }
        ).catch(() => false); // If the user cancels, handle it gracefully

        if (confirm) {
            try {
            // Perform delete action
            await client.delete(id); // Assuming `client.delete` exists and handles API deletion
            // Update the events list
            await changeToEvents();
            ElMessage.success('Le rendez-vous a été supprimé avec succès.');
            } catch (error) {
            ElMessage.error('Une erreur s’est produite lors de la suppression.');
            }
        }
        }

        const changeToEvents = async () => {
    loaded.value = false;
    mainList.value = await client.getByMonth(moment(date.value).format("MM"));

    let generated = [];
    for (const [key, value] of Object.entries(mainList.value)) {
        generated.push({
            description:
                value.type +
                " (" +
                (value.statut == "postponed"
                    ? "reporté"
                    : value.statut == "salle attente"
                    ? "salle d'attente"
                    : value.statut == "planified"
                    ? "Planifié"
                    : value.statut == "canceled"
                    ? "Annulé"
                    : "Honoré") +
                " )",
            title: value.name + " " + value.surname,
            time: {
                start:
                    moment(value.date, "DD/MM/YYYY").format("YYYY-MM-DD") +
                    " " +
                    moment(value.heure, "HH:mm").format("HH:mm"),
                end:
                    moment(value.date, "DD/MM/YYYY").format("YYYY-MM-DD") +
                    " " +
                    moment(value.heure, "HH:mm")
                        .add(30, "minutes")
                        .format("HH:mm"),
            },
            isEditable: true,
            id: value.id, // Event ID
            uid: value.uid, // Include UID here
            colorScheme: value.color,
        });
    }

    eventss.value = generated;
    loaded.value = true;
};


    async function saveRDV()
    {
        
        const rdvId = rdv.value.id;
        
        const matchedRdv = mainList.value.find(item => item.id === rdvId);
        

        if (matchedRdv) {
            // If found, update the rdv.value with the date from matchedRdv

            rdv.value.date = matchedRdv.date; 
            rdv.value.heure = matchedRdv.heure; 

            await client.update(rdv.value); 
            store.setTrigger(true);
            editRDV.value = false;
            
        } else {
            console.error("No matching appointment found for ID:", rdvId);
        }

        // await client.update(rdv.value)
        // store.setTrigger(true)
        // editRDV.value=false
    }


    watch(date, async (newValue,oldValue) => {
      
        if(moment(oldValue).format("MM")==moment(newValue).format("MM"))
        {
            innerList.value = await client.getByDate((moment(newValue).format()).split("+")[0])
            showDay.value=true
        }
        else
        {
            mainList.value = await client.getByMonth(moment(newValue).format("MM"))
        }
    })

    watch(store, async (newState) => {
        if(newState.trigger == true){
			
            await changeToEvents()
			loaded.value=true
			console.log("Loaded changed")
            store.setTrigger(false)
        }
    }, { deep: true})
    
    watch(util, async (newState) => {
        if(newState.RDV == false){
            util.setRDV(false,"")
        }
    }, { deep: true})
	

    const config = {
      dayBoundaries: {
        start: 6,
        end: 20,
      },
	  style: {
        colorSchemes: {
          rose: {
            color: '#000',
            backgroundColor: '#FFC0CB',
          },
          rouge: {
            color: '#fff',
            backgroundColor: '#ff0000',
          },
			bleu:{
				color:"#fff",
				backgroundColor:'#0000ff'
			},
			vert:{
				color:"#fff",
				backgroundColor:"#00ff00"
			},
			jaune:{
				color:"#000",
				backgroundColor:"#FFFF00"
			},
			violet:{
				color:"#fff",
				backgroundColor:"#800080"
			},
			orange:{
				color:"#000",
				backgroundColor:"#FFA500"
			}
        }
      }
    }

    const editRDV :Ref<boolean> = ref(false)
        const heures = ["06:00","06:30","07:00","07:30","08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30","20:00"]
        const rdv : Ref<any> = ref({
            id:"",
            statut:"",
            date:"",
            heure:""
        })

    function addRDV(date:string)
    {
        var date = moment(date,'YYYY-MM-DD HH:ss').format('DD/MM/YYYY');
        util.setRDV(true,date)
    }
    function show(id:number)
    {
        rdv.value.id=id;
        editRDV.value=true
    }

onBeforeMount(async () => {
    await changeToEvents()
})
    let clickTimer: ReturnType<typeof setTimeout> | null = null;
    let clickCount = 0;

    function handleEventClick(event: any) {
    clickCount++;
    console.log(event)
    if (clickCount === 1) {
        // Start a timer to detect double-click
        clickTimer = setTimeout(() => {
            clickCount = 0; // Reset click count after timeout
            console.log("Single-click detected, ignoring...");
        }, 300); // 300ms delay to detect double-click
        } else if (clickCount === 2) {
        // Double-click detected
        if (clickTimer) clearTimeout(clickTimer); // Clear the single-click timer
        clickCount = 0;

        console.log("event.clickedEvent.uid");
        console.log(event.clickedEvent.uid);

        

        if (event.clickedEvent.uid) {
            // Navigate to the new URL
            const uid = event.clickedEvent.uid;
            router.push(`/dossiers/${uid}`);  

            // Optionally, show a success message
            ElMessage.info(`Redirection vers le dossier avec l'UID : ${uid}`);
        } else {
            console.error("UID est manquant dans l'objet de l'événement !");
        }
    }
}




</script>

<template>
    <div>
        <Qalendar
            :config="config"
            @edit-event="(id:number) => { show(id) }"
            @delete-event="(id:number) => { confirmDelete(id) }"
            @datetime-was-clicked="(datetime:any) => { addRDV(datetime) }"
            @event-was-clicked="(id:number) => { handleEventClick(id) }" 
            :events="eventss"
            v-if="loaded"
        />
        
        <div class="text-center text-2xl" v-else>
            <span class="loading"></span> Chargement de l'agenda
        </div>
        <el-dialog title="Editer rendez-vous" v-model="editRDV"  >
            <el-form v-model="rdv" label-position="top">
                <el-form-item label="Statut">
                    <el-select class="w-full" v-model="rdv.statut"> 
                        <el-option value="salle attente" label="Salle d'attente" />
                        <el-option value="canceled" label="Rendez vous annulé" />
                        <el-option value="honored" label="Rendez vous honoré" />
                        <el-option value="planified" label="Rendez vous planifié" />
                        <el-option value="postponed" label="Rendez vous reporté" />
                    </el-select>
                </el-form-item>
                <el-form-item label="Date" v-if="rdv.statut=='postponed'">
                        <el-date-picker
                            v-model="rdv.date"
                            :disabled="util.dateRDV!=''"
                            type="date"
                            placeholder="Cliquez pour selectionner"
                            format="DD/MM/YYYY"
                            value-format="DD/MM/YYYY"
                            class="w-full"
                            style="width:100vw"
                        />
                    </el-form-item>
                <el-form-item label="Heure" v-if="rdv.statut=='postponed'">
                    <el-select class="w-full" v-model="rdv.heure">
                        <el-option v-for="h in heures" :key="h" :value="h" :label="h"  />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <button class="btn btn-sm btn-block background-clickdoc" type="button" @click="async()=> await saveRDV()"> Enregistrer </button>
                </el-form-item>
            </el-form>
        </el-dialog>
        
    </div>
</template>

<style>
    @import "qalendar/dist/style.css";
</style>