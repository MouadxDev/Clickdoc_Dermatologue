<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import { DoughnutChart, BarChart, PieChart, LineChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import { Reporting } from '../../core/Clients/Reporting';

// Register Chart.js components
Chart.register(...registerables);

// Initialize client
const client = new Reporting();

// Define interfaces for chart data
interface ChartData {
  labels: string[];
  datasets: Array<{
    data: number[] | any[];
    backgroundColor: string[] | string;
    label?: string;
    borderColor?: string;
    borderWidth?: number;
    tension?: number;
  }>;
}

interface Charts {
  demographic: ChartData | null;
  dates: ChartData | null;
  hours: ChartData | null;
  billing: ChartData | null;
  patients: ChartData | null;
  consultations: ChartData | null;
}

// Chart refs with proper types
const charts = ref<Charts>({
  demographic: null,
  dates: null,
  hours: null,
  billing: null,
  patients: null,
  consultations: null,
});

// Time period filter
const selectedPeriod = ref('month');
const periods = ['week', 'month', 'year'];

// Chart configurations
const chartConfigs = {
  demographic: {
    plugins: {
      legend: {
        position: 'bottom' as const,
      },
      tooltip: {
        enabled: true,
        callbacks: {
          label: function(tooltipItem: any) {
            let value = tooltipItem.raw; // Get the count value
            return `${value} patient${value > 1 ? 's' : ''}`;
          }
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false
  },
  dates: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(tooltipItem: any) {
            let value = tooltipItem.raw;
            return `${value} patient${value > 1 ? 's' : ''}`;
          }
        }
      }
    }
  },
  billing: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      tooltip: {
        callbacks: {
          label: function(tooltipItem: any) {
            let value = tooltipItem.raw;
            return `${value} MAD`;
          }
        }
      }
    }
  }
};
const chartConfigsRdv = {
  demographic: {
    plugins: {
      legend: {
        position: 'bottom' as const,
      },
      tooltip: {
        enabled: true,
        callbacks: {
          label: function(tooltipItem: any) {
            let value = tooltipItem.raw; // Get the count value
            return `${value} rendez-vous${value > 1 ? '' : ''}`;
          }
        }
      }
    },
    responsive: true,
    maintainAspectRatio: false
  }
};
// Color schemes
const colorSchemes = {
  demographic: ['#6495ED', '#FF5733'],
  patients: ['#5DADE2', '#48C9B0'],
  hours: ['#3498DB', '#1ABC9C', '#F1C40F', '#E67E22', '#9B59B6', '#16A085'],
  consultations: ['#FF6384', '#36A2EB', '#FFCE56']
};

// Interfaces for reports
interface DateReport {
  date: string;
  count: number;
}

interface HourReport {
  heure: string;
  count: number;
}

// Data transformation functions
const transformDemographicData = (data: any): ChartData => ({
  labels: ['Hommes', 'Femmes'],
  datasets: [{
    data: [data.demo.male, data.demo.females],
    backgroundColor: colorSchemes.demographic,
  }]
});

const transformDateData = (data: DateReport[]): ChartData => ({
  labels: data.map(d => d.date),
  datasets: [{
    label: 'Total des rendez-vous par date',
    data: data.map(d => d.count),
    backgroundColor: '#76C7C0',
    borderColor: '#76C7C0',
    borderWidth: 1,
    tension: 0.4
  }]
});

const transformHourData = (data: HourReport[]): ChartData => ({
  labels: data.map(h => h.heure),
  datasets: [{
    label: 'Consultations par heure',
    data: data.map(h => h.count),
    backgroundColor: colorSchemes.hours,
  }]
});

const monthNames = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

// Fetch and transform data
async function fetchData() {
  try {
    const data = await client.getAll();
    
    // Transform and set chart data
    charts.value = {
      demographic: transformDemographicData(data),
      dates: transformDateData(data.date),
      hours: transformHourData(data.horaires),
      billing: {
        // labels: data.billing.monthly_trends.map((t: any) => `Mois ${t.month}`),
        labels: data.billing.monthly_trends.map((t: any) => monthNames[t.month-1]),
        datasets: [{
          label: 'Revenus Mensuels',
          data: data.billing.monthly_trends.map((t: any) => t.total),
          backgroundColor: '#4BC0C0',
          borderColor: '#36A2EB',
          borderWidth: 2,
          tension: 0.4
        }]
      },
      patients: {
        labels: ['Nouveaux', 'Réguliers'],
        datasets: [{
          data: [data.patients.new, data.patients.regular],
          backgroundColor: colorSchemes.patients,
        }]
      },
      consultations: {
        labels: data.consultations.map((c: any) => c.libelle),
        datasets: [{
          data: data.consultations.map((c: any) => c.count),
          backgroundColor: colorSchemes.consultations,
        }]
      }
    };
  } catch (error) {
    console.error('Error fetching data:', error);
  }
}

// Lifecycle hooks
onMounted(async () => {
  await fetchData();
});

// Period change handler
const handlePeriodChange = async (period: string) => {
  selectedPeriod.value = period;
  await fetchData(); // Refetch data with new period
};
</script>

<template>
  <main-layout>
    <div class="dashboard container mx-auto p-6">
      <!-- Header with filters -->
      <div class="dashboard-header mb-8">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
          <div class="period-filter">
            <el-radio-group v-model="selectedPeriod" @change="handlePeriodChange">
              <el-radio-button 
                v-for="period in periods" 
                :key="period" 
                :label="period"
                class="capitalize"
              >
                {{ period }}
              </el-radio-button>
            </el-radio-group>
          </div>
        </div>
      </div>

      <!-- Dashboard Grid -->
      <el-row :gutter="20" class="dashboard-grid">
        <!-- Demographic and Patient Charts Row -->
        <el-col :xs="24" :sm="12" :md="6" class="mb-6" v-for="(chart, index) in ['demographic', 'patients']" :key="index">
          <el-card class="h-full">
            <template #header>
              <div class="font-bold">
                {{ chart === 'demographic' ? 'Répartition par Sexe' : 'Nouveaux vs Réguliers' }}
              </div>
            </template>
            <div class="chart-container h-64">
              <DoughnutChart 
                v-if="charts[chart]"
                :chartData="charts[chart]"
                :options="chartConfigs.demographic"
              />
            </div>
          </el-card>
        </el-col>

        <!-- Hours and Consultations Charts Row -->
        <el-col :xs="24" :sm="12" :md="6" class="mb-6" v-for="(chart, index) in ['hours', 'consultations']" :key="index">
          <el-card class="h-full">
            <template #header>
              <div class="font-bold">
                {{ chart === 'hours' ? 'Rendez-vous par Heure' : 'Types de Consultations' }}
              </div>
            </template>
            <div class="chart-container h-64">
              <PieChart 
                v-if="charts[chart]"
                :chartData="charts[chart]"
                :options="chartConfigsRdv.demographic"
              />
            </div>
          </el-card>
        </el-col>

        <!-- Full Width Charts -->
        <el-col :span="24" class="mb-6">
          <el-card>
            <template #header>
              <div class="font-bold">Rendez-vous par Date</div>
            </template>
            <div class="chart-container ">
              <BarChart 
                v-if="charts.dates"
                :chartData="charts.dates"
                :options="chartConfigs.dates"
              />
            </div>
          </el-card>
        </el-col>

        <el-col :span="24" class="mb-6">
          <el-card>
            <template #header>
              <div class="font-bold">Revenus Mensuels</div>
            </template>
            <div class="chart-container ">
              <LineChart 
                v-if="charts.billing"
                :chartData="charts.billing"
                :options="chartConfigs.billing"
              />
            </div>
          </el-card>
        </el-col>
      </el-row>
    </div>
  </main-layout>
</template>

<style scoped>
.dashboard {
  max-width: 1400px;
}

.chart-container {
  position: relative;
  width: 100%;
}

.el-card {
  transition: all 0.3s ease;
}

.el-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .dashboard-grid {
    margin-top: 1rem;
  }
  
  .chart-container {
    height: 300px !important;
  }
}
</style>