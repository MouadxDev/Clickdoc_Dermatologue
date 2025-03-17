<script setup lang="ts">
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const props = defineProps<{
        title: string
        action?:any
        hasAction?:boolean
        noHeader?:boolean
        hasBack?:boolean
        isTop?:boolean
        isRounded?:boolean
        export?:boolean
        onExport: Function | any;
    }>()
</script>

<template>
    <div class="rounded-2xl p-4 bg-white mt-3 shadow-xl">
        <div class="card-header" v-if="props.isTop==true">
            <div v-if="props.hasBack==true">
                <el-button size="large" link  @click=" router.back()" > 
                    <el-icon>
                        <Back/>
                    </el-icon>
                </el-button>
            </div>
            <span class="text-xl font-semibold">{{ props.title }}</span>
            
            <!-- Add Export Button -->
             
            <el-button
                size="small"
                link
                v-if="props.export == true"
                @click="() => { props.onExport() }"
                class="btn-doc"
                aria-label="Submit" >
            <el-icon>
              <UploadOutlined />
            </el-icon>
            &nbsp;&nbsp; Exporter
          </el-button>

            <div v-if="props.hasAction==true" :class="{ hidden: props.hasAction }">
                <el-button size="large" link  @click="props.action.action()" > 
                    <el-icon>
                        <component :is="props.action.icon"/>
                    </el-icon> {{ props.action.text }} 
                </el-button>
            </div>

        </div>
        <el-card v-else> 
            <template #header v-if="props.noHeader!=true">
                <div class="card-header">
                    <div v-if="props.hasBack==true">
                        <el-button size="large" link  @click=" router.back()" > 
                            <el-icon>
                                <Back/>
                            </el-icon>
                        </el-button>
                    </div>
                    <span class="text-xl font-semibold">{{ props.title }}</span>
                    <div v-if="props.hasAction==true">
                        <el-button size="large" link  @click="props.action.action()" > 
                            <el-icon>
                                <component :is="props.action.icon"/>
                            </el-icon> {{ props.action.text }} 
                        </el-button>
                    </div>
                </div>
            </template>
            <div>
                <slot />
            </div>
        </el-card>
    </div>
</template>

<style>
.hidden {
    display: none;
}
</style>