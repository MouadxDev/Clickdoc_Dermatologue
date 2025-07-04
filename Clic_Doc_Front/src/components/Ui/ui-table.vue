<script setup lang="ts">
import {
  defineProps,
  onBeforeMount,
  ref,
  watch,
  computed,
} from 'vue';
import { eventBus } from '../../utils/eventBus'; 

import { useRouter } from 'vue-router';
import moment from "moment";
import { StoreDefinition } from 'pinia';

const props = defineProps<{
  title: string;
  fields: Array<any>;
  client: any;
  add?: boolean;
  export?:boolean; 
  print?: boolean;
  patient_id?: number;
  onAdd: Function | any;
  onFilter: Function | any;
  onExport: Function | any;
  search?: boolean;
  hasAvatar?: boolean;
  hasLogo?: boolean;
  hasButton?: boolean;
  refresh?: boolean;
  actions?: any;
  hasFilters?: boolean;
  noHeader?: boolean;
  isMain?: boolean | true;
  isSmall?: boolean | false;
  filters?: any;
  others?: any | [];
  hasBack?: boolean | false;
  toRoute?: string | "";
  triggerStore?: StoreDefinition | any;
  filter?: boolean | true;
  filterKey?: string; // New prop for dynamic filtering
  serverSideFilter?: boolean | false; // New prop to enable server-side filtering
  searchMethod?: string; // New prop to specify search method name
  useSearchMethod?: boolean | false; // New prop to use search method instead of getAll
}>();

const current_page = ref(1);
const total = ref(1);
const page_size = ref(25);
const patient_id: any = props.patient_id;
const loading = ref(true);
const client = props.client;
const fields = props.fields;
const router = useRouter();
const data: any = ref({});
const store = props.triggerStore != undefined ? props.triggerStore() : undefined;

const filterText = ref(''); // Filter input
const debounceTimer = ref<NodeJS.Timeout | null>(null); // For debouncing API calls

// Computed property to filter data based on filterText
const filteredData = computed(() => {
  // If server-side filtering or search method is enabled, don't filter on frontend
  if (props.serverSideFilter || props.useSearchMethod) {
    return data.value.data || [];
  }
  
  // Client-side filtering (existing logic)
  return data.value.data?.filter((item: any) =>
    JSON.stringify(item).toLowerCase().includes(filterText.value.toLowerCase())
  ) || [];
});

// Watch for filter text changes and trigger API call if server-side filtering is enabled
watch(filterText, (newValue) => {
  if (props.serverSideFilter || props.useSearchMethod) {
    // Clear existing timer
    if (debounceTimer.value) {
      clearTimeout(debounceTimer.value);
    }
    
    // Set new timer for debounced API call
    debounceTimer.value = setTimeout(async () => {
      current_page.value = 1; // Reset to first page when filtering
      await getData();
    }, 500); // 500ms delay
  }
});

if (props.triggerStore != undefined) {
  watch(store, async (newState) => {
    if (newState.trigger == true) {
      await getData();
      store.setTrigger(false);
    }
  }, { deep: true });
}

onBeforeMount(async () => {
  await getData();
});

async function getData() {
  loading.value = true;
  
  try {
    // Check if we should use search method and filter text is provided
    if (props.useSearchMethod && filterText.value.trim() !== '') {
      const searchMethodName = props.searchMethod || 'search';
      
      // Call the search method
      if (typeof client[searchMethodName] === 'function') {
        const searchResult = await client[searchMethodName](filterText.value);
        
        // Handle different response formats
        if (searchResult && typeof searchResult === 'object') {
          // If search returns paginated data
          if (searchResult.data && Array.isArray(searchResult.data)) {
            data.value = searchResult;
            current_page.value = searchResult.current_page || 1;
            total.value = searchResult.total || searchResult.data.length;
            page_size.value = searchResult.per_page || searchResult.data.length;
          } 
          // If search returns direct array or single object
          else if (Array.isArray(searchResult)) {
            data.value = {
              data: searchResult,
              current_page: 1,
              total: searchResult.length,
              per_page: searchResult.length
            };
            current_page.value = 1;
            total.value = searchResult.length;
            page_size.value = searchResult.length;
          }
          // If search returns single object
          else {
            const resultArray = [searchResult];
            data.value = {
              data: resultArray,
              current_page: 1,
              total: 1,
              per_page: 1
            };
            current_page.value = 1;
            total.value = 1;
            page_size.value = 1;
          }
        }
      } else {
        console.error(`Search method '${searchMethodName}' not found in client`);
        // Fallback to getAll
        await getDataWithGetAll();
      }
    } else {
      // Use normal getAll method when search is empty or useSearchMethod is false
      await getDataWithGetAll();
    }
  } catch (error) {
    console.error('Error fetching data:', error);
    // Fallback to empty data
    data.value = {
      data: [],
      current_page: 1,
      total: 0,
      per_page: page_size.value
    };
  }
  
  loading.value = false;
}

async function getDataWithGetAll() {
  const toSend = { 
    page: current_page.value, 
    toGet: page_size.value, 
    patient_id: patient_id 
  };
  
  if (props.hasFilters == true) {
    Object.assign(toSend, props.filters);
  }

  // Add filter parameter if server-side filtering is enabled
  if (props.serverSideFilter && filterText.value.trim() !== '') {
    if (props.filterKey) {
      toSend[props.filterKey] = filterText.value;
    } else {
      toSend['filter'] = filterText.value; // Default filter key
    }
  }

  console.log(toSend);
  data.value = await client.getAll(toSend);
  current_page.value = data.value.current_page;
  total.value = data.value.total;
  page_size.value = data.value.per_page;
}

defineExpose({
  getData
});
</script>

<template>
  <div class="container">
    <div class="rounded-2xl p-4 bg-white mt-3" v-if="props.noHeader == false">
      <div class="card-header">
        <div v-if="props.hasBack == true">
          <el-button size="large" link @click="router.back()">
            <el-icon>
              <Back />
            </el-icon>
          </el-button>
        </div>
        <!-- Header content -->
        <span class="text-xl font-semibold">{{ props.title }}</span>
        <div>
          <el-button
            size="small"
            link
            v-if="props.others && props.others.length > 0"
            v-for="o in props.others"
            :key="o.action"
            @click="() => { o.action() }"
            :type="o.type"
            class="btn-doc"
            aria-label="Submit"
          >
            <el-icon class="font-bold">
              <component :is="o.icon" />
            </el-icon>
            &nbsp;&nbsp; <span class="font-bold">{{ o.text }}</span>
          </el-button>

          <el-button
            size="small"
            link
            v-if="props.export == true"
            @click="() => { props.onExport() }"
            class="btn-doc"
            aria-label="Submit"
          >
            <el-icon>
              <UploadOutlined />
            </el-icon>
            &nbsp;&nbsp; Exporter
          </el-button>

          <el-button
            size="small"
            link
            v-if="props.add == true"
            @click="() => { props.onAdd() }"
            class="btn-doc"
            aria-label="Submit"
          >
            <el-icon>
              <Plus />
            </el-icon>
            &nbsp;&nbsp; Ajouter
          </el-button>

          <el-button
            size="small"
            link
            v-if="props.print == true"
            @click="() => { print = true }"
            class="btn-doc"
            aria-label="Submit"
          >
            <el-icon>
              <Print />
            </el-icon>
            &nbsp;&nbsp; Imprimer
          </el-button>
          <el-button
            size="small"
            link
            v-if="props.hasFilters == true"
            @click="() => { props.onFilter() }"
            class="btn-doc"
            aria-label="Submit"
          >
            <el-icon>
              <Filter />
            </el-icon>
            &nbsp;&nbsp; Filtrer
          </el-button>
          <el-button
            size="small"
            link
            class="btn-doc"
            @click="async () => { await getData() }"
            aria-label="Submit"
          >
            <el-icon>
              <Refresh />
            </el-icon>
            &nbsp;&nbsp; Actualiser
          </el-button>
        </div>
      </div>
    </div>

    <div class="rounded-2xl p-4 bg-white mt-3">
      <slot></slot>
      <!-- Filter input for data table -->
      <el-input
        v-model="filterText"
        id="filter_input"
        :placeholder="(props.serverSideFilter || props.useSearchMethod) ? 'Rechercher...' : 'Tapez pour filtrer'"
        class="pb-4"
        clearable
       
      />
      
      <!-- Data table -->
      <el-table
        :height="props.isMain == true ? 'calc(100vh - 250px)' : 'calc(100vh - 350px)'"
        :max-height="props.isMain == true ? 'calc(100vh - 250px)' : 'calc(100vh - 350px)'"
        v-loading="loading"
        style="width: 100%"
        :data="filteredData"
        :border="true"
        :size="$props.isSmall == true ? 'small' : 'medium'"
        @row-click="(row: any) => { toRoute != undefined ? router.push(props.toRoute + '/' + row.uid) : '' }"
      >
        <el-table-column v-if="props.hasAvatar == true" prop="avatar" width="60px">
          <template #default="scope">
            <div class="flex items-center space-x-3">
              <div class="avatar">
                <div class="mask mask-squircle w-10 h-10">
                  <img :src="scope.row.avatar" :alt="scope.row.name" />
                </div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column v-if="props.hasLogo == true" prop="avatar" width="60px">
          <template #default="scope">
            <div class="flex items-center space-x-3">
              <div class="avatar">
                <div class="mask mask-squircle w-10 h-10">
                  <img :src="scope.row.logo == null ? '/logo.png' : scope.row.logo" :alt="scope.row.name" />
                </div>
              </div>
            </div>
          </template>
        </el-table-column>

        <el-table-column v-for="f in fields" :key="f.prop" :prop="f.prop" :label="f.label" :width="f.width">
          <template #default="scope" v-if="f.isBoolean == true">
            <div :class="'rounded-lg text-center text-white ' + (scope.row[f.prop] == 1 ? 'bg-green-500' : 'bg-red-500')">
              {{ scope.row[f.prop] == 1 ? 'Oui' : 'Non' }}
            </div>
          </template>
          <template #default="scope" v-if="f.isDate == true">
            <div class="">
              {{ moment(scope.row[f.prop]).format("DD/MM/YYYY") }}
            </div>
          </template>
        </el-table-column>

        <el-table-column v-if="actions.length > 0" prop="avatar" width="80px">
          <template #default="scope">
            <div class="text-center">
              <el-button link v-for="a in actions" :key="a.icon" @click="a.action(scope.row)">
                <el-icon><component :is="a.icon" /></el-icon>
              </el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>

      <div class="w-full p-2">
        <el-pagination
          class="mx-auto shadow-0"
          v-model:current-page="current_page"
          v-model:page-size="page_size"
          :small="true"
          :disabled="false"
          :background="false"
          :page-sizes="[1, 15, 25, 30, 50, 100]"
          layout="total, sizes, prev, pager, next, jumper"
          :total="total"
          @size-change="async () => { await getData() }"
          @current-change="async () => { await getData() }"
        />
      </div>
    </div>
  </div>
</template>