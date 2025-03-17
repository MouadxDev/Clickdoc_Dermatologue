<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{id:number}>()

const tab = ref("medicaux")

</script>
<template>
    <div class="container mx-auto">
        <el-tabs
            v-model="tab"
            type="card"
            v-if="props.id"
        >
            <el-tab-pane name="medicaux" label="Antécédants médicaux">
                <ant-medicaux :id="props.id"></ant-medicaux>
            </el-tab-pane>
            <el-tab-pane name="familiaux" label="Antécédants familiaux">
                <ant-familiaux :id="props.id"></ant-familiaux>
            </el-tab-pane>
            <el-tab-pane name="allergies" label="Allergies">
                <ant-allergies :id="props.id"></ant-allergies>
            </el-tab-pane>
            <el-tab-pane name="medications" label="Médicaments">
                <ant-drugs :id="props.id"></ant-drugs>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>