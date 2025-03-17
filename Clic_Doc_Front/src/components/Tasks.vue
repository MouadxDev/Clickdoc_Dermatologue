1<script setup lang="ts">
    import { Users } from '../../core/Clients/Users';
    import { Tasks } from '../../core/Clients/Tasks';
    import { Ref,onMounted,ref } from 'vue';
    import { useAuthStore } from '../../core/Data/stores/auth';
    

    const client = new Users();
    const taskClient = new Tasks();
    const assistants :Ref<any> = ref();
    const tasks : Ref<Array<any>> = ref([])
    
    // Add Data to the archive
    const archivedTasks: Ref<Array<any>> = ref([]);
    const statuts = ['en attente','fini','annulé'];
    const task :Ref<any> = ref({})
    
    const store = useAuthStore()
    

    const getTasks = async () => {
        const allTasks = await taskClient.getAll();
        tasks.value = allTasks.filter(task => task.status !== "archive"); // Visible tasks
        archivedTasks.value = allTasks.filter(task => task.status === "archive"); // Archived tasks
    };

    // switsh view to archive
    const showArchived = ref(false);
    const toggleArchivedTasks = () => {
        showArchived.value = !showArchived.value; // Toggle the value
    };
    




    const addTask = async () =>{
        await taskClient.add(task.value)
        await getTasks()
        task.value = {}
    }
	
	const updateTask = async (value) =>{
		await taskClient.update(value)
        // await getTasks()
        task.value = {}
	}
    
    const deleteTask = async (taskToDelete) => {
        try {
            // Optionally, confirm with the user
            if (confirm('Are you sure you want to delete this task?')) {
                // Send delete request to the server
                await taskClient.delete(taskToDelete.id);

                // Remove task from the local state
                tasks.value = tasks.value.filter(task => task.id !== taskToDelete.id);
            }
        } catch (error) {
            console.error('Failed to delete task:', error);
        }
    };

    const archiveTask = async (taskToArchive) => {
    
        const updatedTask = {
            ...taskToArchive, // Spread the existing properties of the task
            status: "archive", // Change the status to "archive"
        };
        
        await updateTask(updatedTask);
        tasks.value = tasks.value.filter(task => task.id !== taskToArchive.id);

      
    };
    

    onMounted(async ()=>{
        assistants.value = await client.getAll()
        await getTasks()
    })
</script>
<template>
    <div>
        <div class="my-4">
            <el-form label-position="top">
                <el-row :gutter="10">
                    <el-col :span="6">
                        <el-form-item label="Libellé"> 
                            <el-input v-model="task.libelle" />
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Date d'echeance"> 
                            <el-date-picker
                                v-model="task.expiration_date"
                                type="date"
                                placeholder="Cliquez pour selectionner"
                                format="DD/MM/YYYY"
                                value-format="DD/MM/YYYY"
                                class="w-full"
                                style="width:100vw"
                            />
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Asistant"> 
                            <el-select v-model="task.user_id" class="w-full">
                                <el-option v-for="a in assistants" :label="a.name" :value="a.id" />
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="&nbsp"> 
                            <button class="btn background-clickdoc btn-sm btn-block" type="button" @click="addTask()" > Enregistrer </button>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
        </div>
        
        <div class="Show-Arr" @click="toggleArchivedTasks" v-if="store.user.role !== 'assistant'">
            <p>{{ showArchived ? 'Afficher Tâches' : 'Afficher Archive' }}</p> 
            <el-icon><img src="https://clickdoc.webredirect.org/public/Svg/visible.svg" alt="Toggle View" class="fill-gray" /></el-icon>
        </div>

        <el-table :data="showArchived ? archivedTasks : tasks">
            <el-table-column prop="libelle" label="Libellé" />
            <el-table-column prop="assistant" label="Assistant" />
            <el-table-column prop="expiration_date" label="Date d'expiration" />
            <el-table-column >
                <template #default="scope">
                    <el-select class="w-full" v-model="scope.row.status" @change(scope.row)>
                        <el-option v-for="status in statuts" :label="status" :value="status" />
                    </el-select>
                </template>
            </el-table-column>
            
            <!-- New section that will be enabled only by doctors -->

            <el-table-column label="Actions" class-name="action_head" v-if="store.user.role !== 'assistant'" >
            <template #default="scope">
                <div class="actions-container">
                <!-- Delete Action -->
                <button  @click="deleteTask(scope.row)">
                    <el-icon><img src="https://clickdoc.webredirect.org/public/Svg/trush.svg" alt="Delete" /></el-icon>
                </button>
                <!-- Archive Action -->
                <button  @click="archiveTask(scope.row)">
                    <el-icon><img src="https://clickdoc.webredirect.org/public/Svg/archive.svg" alt="Archive" /></el-icon>
                </button>
                </div>
            </template>
            </el-table-column>

        </el-table>
    </div>
</template>

<style>
 .actions-container {
    justify-content: center;
    display: flex;
    gap: 5px;
}
.action_head{
    text-align: center !important;
}
.Show-Arr{
    display: flex;
    gap: 10px;
    justify-content: end;
    cursor: pointer;
}
.fill-gray{
    filter: brightness(0) saturate(100%) invert(79%) sepia(10%) saturate(297%) hue-rotate(180deg) brightness(86%) contrast(93%);
    margin-top: 5px;
}
</style>