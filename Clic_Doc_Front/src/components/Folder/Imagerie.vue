<script setup lang="ts">
import { Ref, onMounted, ref } from 'vue';

import { Upload } from '../../../core/Clients/Upload';
import { Imagerie } from '../../../core/Clients/Imagerie';
import moment from 'moment';

const props = defineProps<{
    id:number
}>()

const uploadClient = new Upload()
const client = new Imagerie()
const imagerie : Ref<any> = ref({
    image:"",
    comment:"",
    patient_id: props.id
})

const gallery :Ref<any> = ref({})
const loading = ref(false);

async function upload() {
    var formData = new FormData();
    var file = document.querySelector('input[type=file]')?.files[0];
    formData.append("file",file);
    const resp = await uploadClient.add(formData)
    imagerie.value.image=resp.data.full_path
}

async function deleteItem(id:any) {
    if(confirm("Êtes vous sur de vouloir supprimer cette image") ==true)
    {
        await client.delete(id)
        await fetchData()
    }
    
}

const fetchData = async () => {
    loading.value=true
    gallery.value= await client.getAll({patient_id:props.id})
    loading.value=false
    
}

const ajouter =  async () =>{
    await client.add(imagerie.value)
    imagerie.value = {
        image:"",
        comment:"",
        patient_id:props.id
    }
    await fetchData()
}

onMounted(async ()=>{
    await fetchData()
})

</script>

<template>
    <div class="container mx-auto">
        <el-form>
            <el-row :gutter="10">
                <el-col :span="6">
                    <input type="file" id="file" hidden class="file-input file-input-bordered file-input-xs w-full max-w-xs" @change="async ()=>{await upload()}"/>
                    <label for="file" class="btn btn-sm btn-block background-clickdoc" > Selectionner Image</label>
                </el-col>
                <el-col :span="12">
                    <el-input v-model="imagerie.comment" :disabled="imagerie.image.length==0" placeholder="commentaire" />
                </el-col>
                <el-col :span="6">
                    <button class="btn btn-sm btn-block background-clickdoc" type="button" :disabled="imagerie.image.length==0" @click="async ()=>await ajouter()"  > Enregistrer </button>
                </el-col>
            </el-row>
        </el-form>
        <el-row :gutter="10" class="mt-4" v-if="loading==false">
            <el-col :span="4" v-for="image in gallery" class="text-center">
                <el-card :body-style="{ padding: '0px' }" class="h-46">
                    <div class="p-2 h-40">
                        <img
                            :src="image.image"
                            class="image"
                        />
                    </div>
                    <div style="padding: 14px;background-color: white;z-index: 20;" >
                        <div class="bg-white-500">
                            <span class="text-sm w-full bg-white">{{ image.commentaire }}</span> <br>
                            <span class="text-sm w-full bg-white"> {{ moment(image.created_at).format("DD/MM/YYYY") }} </span> <br>
                            <button class="btn text-red-500 btn-link" style="text-decoration: none;" type="button" @click="async ()=>await deleteItem(image.id)"> supprimer </button>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <div class="text-center" v-else>
            <a class="btn btn-link btn-lg loading text-clickdoc" />
        </div>

    </div>
</template>