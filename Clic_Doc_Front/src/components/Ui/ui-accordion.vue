<script lang="ts" setup>
    import {Accordion} from '../../../core/Types/Components/Accordion';

    const props = defineProps<{
        items:Array<Accordion>;
        type:string;
        name:string;
    }>()
</script>
<template >
    <div v-for="(item,index) in props.items " :class="'mb-2 collapse collapse-'+props.type+' background-clickdoc'">
        <input type="radio" :name="props.name" :checked="index==0?true:false" />  
        <div class="collapse-title text-xl font-medium">
            {{ item.label }}
        </div>
        <div class="collapse-content"> 
            <component :is="item.component" >
            </component>
        </div>
    </div>
</template>

<style>
    .collapse-content{
        overflow: auto;
    }
</style>