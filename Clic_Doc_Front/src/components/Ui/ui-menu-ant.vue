<script setup lang="ts">
    import {Menu} from "../../../core/Types/Components/Menu"
    import { useRouter } from 'vue-router';

    const props = defineProps<Menu>()
    const router = useRouter()
</script>
<template>
    <div class="rounded-2xl p-4  h-inside shadow-lg overflow-y-auto">
        <div class="text-center rounded-2xl mb-3 " v-if="props.hasLogo==true">
            <img :src="props.logo" >
        </div>
        <div class="">
            <ul class="menu menu-sm rounded-box">
                <li v-for="item in props.items" :key="item.link" @click="router.push(item.link)">
                    <a>
                        <el-icon> <component :is="item.icon"/> </el-icon> {{ item.text }}
                    </a>
                </li>
            </ul>
            <ul class="menu menu-sm rounded-box" v-for="group in props.groups" >
                <span v-if="group.superAdmin==true && props.isSuperadmin==true || group.superAdmin==false && props.isSuperadmin==false "  >
                    <li class="menu-title text-uppercase"> {{ group.name }} </li>
                    <li v-for="item in group.items" :key="item.link"  @click="router.push(item.link)">
                        <a>
                            <el-icon> <component :is="item.icon"/> </el-icon> {{ item.text }}
                        </a>
                    </li>
                </span>
            </ul>
        </div>
    </div>
</template>