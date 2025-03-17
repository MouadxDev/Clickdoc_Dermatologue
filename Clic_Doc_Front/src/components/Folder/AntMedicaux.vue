<script setup lang="ts">
import {
  onMounted,
  ref,Ref
} from 'vue';

import { AntMedicaux } from '../../../core/Clients/AntMedicaux';

const props = defineProps<{id:number}>()

const client = new AntMedicaux();
const model: Ref<any> = ref({
    id:null,
    chronic:false,
    cardiac:false,
    lung:false,
    kidney:false,
    cancer:false,
    nerves:false
})

async function getData()
{
    model.value = await client.getByID(props.id)
}

onMounted(async()=>{
    await getData()
})

async function setData()
{
    await client.update(model.value);
    await getData()
}
</script>

<template>
    <div class="p-4">
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.chronic"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                ></el-switch>
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/chronic.png">
        </el-col>
        <el-col :span="model.chronic==true?5:12" class="text-lg">
            Maladies chroniques
        </el-col>
        <el-col v-if="model.chronic==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.chronic_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.cardiac"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/heart.png"> 
        </el-col>
        <el-col :span="model.cardiac==true?5:20" class="text-lg">
            Maladies cardiaques
        </el-col>
        <el-col v-if="model.cardiac==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.cardiac_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.lung"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/lungs.png"> 
        </el-col>
        <el-col :span="model.lung==true?5:20" class="text-lg">
            Maladies pulmonaires
        </el-col>
        <el-col v-if="model.lung==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.lung_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.kidney"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/kidney.png"> 
        </el-col>
        <el-col :span="model.kidney==true?5:20" class="text-lg">
            Maladies rénales/Hépatiques
        </el-col>
        <el-col v-if="model.kidney==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.kidney_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.gastric"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/stomach.png"> 
        </el-col>
        <el-col :span="model.gastric==tue?5:20" class="text-lg">
            Maladies gastro/intestinales
        </el-col>
        <el-col v-if="model.gastric==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.gastric_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.cancer"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/cancer.png">
        </el-col>
        <el-col :span="model.cancer==true?5:20" class="text-lg">
            Antécédents de cancer
        </el-col>
        <el-col v-if="model.cancer==true" :span="15" class="flex text-lg">
            <el-input
                v-model="model.cancer_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
    <hr>
    <el-row class="my-2">
        <el-col :span="2">
                <el-switch
                v-model="model.nerves"
                style="--el-switch-on-color: #13ce66; --el-switch-off-color: #ff4949"
                :active-value="1"
                :inactive-value="0"
                @change="async()=>{await setData()}"
                />
        </el-col>
        <el-col :span="2" class="flex text-lg">
            <img class="h-12 w-12" src="/nerve.png">
        </el-col>
        <el-col :span="model.nerves==true?5:20" class="text-lg">
            Maladies neurologiques
        </el-col>
        <el-col v-if="model.nerves" :span="15" class="flex text-lg">
            <el-input
                v-model="model.nerves_comment"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Plus de details"
                @change="async()=>{await setData()}"
            />
        </el-col>
    </el-row>
</div>
</template>