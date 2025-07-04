                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 mt-3 rounded-2xl p-3 sm:p-6 shadow-2xl border border-blue-200 relative overflow-hidden">


                            <!-- Header -->
                            <div class="relative z-10  items-start sm:items-center justify-between mb-4 sm:mb-6">
                                <div class="flex items-center space-x-2 sm:space-x-3 mb-2 sm:mb-0">
                                <div class="p-2 sm:p-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl shadow-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-2xl font-bold text-gray-800">Données Vitales</h3>
                                    <p class="text-gray-600 text-xs sm:text-sm">Suivi des constantes physiologiques</p>
                                </div>
                                </div>
                                <div class="flex items-center space-x-2 self-end sm:self-auto">
                                <div class="w-2 h-2 sm:w-3 sm:h-3 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-xs sm:text-sm text-gray-600">En temps réel</span>
                                </div>
                            </div>

                            <div class="relative z-10">
                        
                                <!-- Patient Constants -->
                                <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-lg border border-gray-100 mb-4 sm:mb-6">
                                    <div class="flex items-center mb-3 sm:mb-4">
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-red-100 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <svg class="w-3 h-3 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm sm:text-lg font-semibold text-gray-800">Constantes Médicales</h4>
                                    </div>
                                    
                                    <div class="flex flex-col space-y-3">
                                    <div class="flex items-center p-2 sm:p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg sm:rounded-xl border border-purple-200">
                                        <div class="w-6 h-6 sm:w-10 sm:h-10 bg-purple-100 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                        <svg class="w-3 h-3 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                        <p class="text-xs sm:text-sm text-gray-600 mb-1">Diabète</p>
                                        <p class="font-bold text-sm sm:text-base text-gray-800 truncate">
                                            <span :class="{'text-red-600': patient.diabetes > 0, 'text-green-600': patient.diabetes == 0}">
                                            {{ patient.diabetes==1?"Type 1":patient.diabetes==2?"Type 2":patient.diabetes==3?"Prédiabètes":"Non" }}
                                            </span>
                                        </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-2 sm:p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg sm:rounded-xl border border-blue-200">
                                        <div class="w-6 h-6 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                        <svg class="w-3 h-3 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                                        </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                        <p class="text-xs sm:text-sm text-gray-600 mb-1">Groupe Sanguin</p>
                                        <p class="font-bold text-sm sm:text-base text-gray-800 truncate">{{ patient.blood_type }}</p>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <!-- Vital Signs -->
                                <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-5 shadow-lg border border-gray-100">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 sm:mb-4">
                                    <div class="flex items-center mb-2 sm:mb-0">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-green-100 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <svg class="w-3 h-3 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        </div>
                                        <h4 class="text-sm sm:text-lg font-semibold text-gray-800">Mesures Vitales</h4>
                                    </div>
                                    <div v-if="mesure" class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ moment(mesure.created_at).format("DD/MM/YYYY") }}
                                    </div>
                                    </div>

                                    <div v-if="mesure">
                                    <div class="flex flex-col space-y-3">
                                        <!-- Height -->
                                        <div class="vital-card bg-gradient-to-br from-emerald-50 to-teal-50 border-emerald-200">
                                        <div class="vital-icon bg-emerald-100 text-emerald-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Taille</p>
                                            <p class="vital-value">{{ mesure.taille }} <span class="vital-unit">cm</span></p>
                                        </div>
                                        </div>

                                        <!-- Weight -->
                                        <div class="vital-card bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200">
                                        <div class="vital-icon bg-blue-100 text-blue-600">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Poids</p>
                                            <p class="vital-value">{{ mesure.poids }} <span class="vital-unit">kg</span></p>
                                        </div>
                                        </div>

                                        <!-- Blood Pressure -->
                                        <div class="vital-card bg-gradient-to-br from-red-50 to-pink-50 border-red-200">
                                        <div class="vital-icon bg-red-100 text-red-600">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Tension</p>
                                            <p class="vital-value">{{ mesure.tension }} <span class="vital-unit">mmHg</span></p>
                                        </div>
                                        </div>

                                        <!-- Heart Rate -->
                                        <div class="vital-card bg-gradient-to-br from-purple-50 to-violet-50 border-purple-200">
                                        <div class="vital-icon bg-purple-100 text-purple-600">
                                            <div class="w-5 h-5 heartbeat-icon">💓</div>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Fréquence Cardiaque</p>
                                            <p class="vital-value">{{ mesure.fr_cardiaque }} <span class="vital-unit">bpm</span></p>
                                        </div>
                                        </div>

                                        <!-- Saturation -->
                                        <div class="vital-card bg-gradient-to-br from-cyan-50 to-blue-50 border-cyan-200">
                                        <div class="vital-icon bg-cyan-100 text-cyan-600">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Saturation</p>
                                            <p class="vital-value">{{ mesure.saturation }} <span class="vital-unit">%</span></p>
                                        </div>
                                        </div>

                                        <!-- Blood Sugar -->
                                        <div class="vital-card bg-gradient-to-br from-orange-50 to-amber-50 border-orange-200">
                                        <div class="vital-icon bg-orange-100 text-orange-600">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Glycémie</p>
                                            <p class="vital-value">{{ mesure.gly }} <span class="vital-unit">mmol/L</span></p>
                                        </div>
                                        </div>

                                        <!-- Temperature -->
                                        <div class="vital-card bg-gradient-to-br from-yellow-50 to-orange-50 border-yellow-200">
                                        <div class="vital-icon bg-yellow-100 text-yellow-600">
                                            <svg class="w-3 h-3 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                        <div class="vital-content">
                                            <p class="vital-label">Température</p>
                                            <p class="vital-value">{{ mesure.temp }} <span class="vital-unit">°C</span></p>
                                        </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="mt-4 sm:mt-6 flex justify-center">
                                        <button 
                                        @click="renseign=true" 
                                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center space-x-2 text-sm sm:text-base"
                                        >
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span>Mettre à jour</span>
                                        </button>
                                    </div>
                                    </div>

                                    <!-- No Data State -->
                                    <div v-else class="text-center py-8 sm:py-12">
                                    <div class="w-12 h-12 sm:w-20 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                        <svg class="w-6 h-6 sm:w-10 sm:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-600 mb-2">Aucune donnée disponible</h3>
                                    <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">Commencez par renseigner les mesures vitales du patient</p>
                                    <button 
                                        @click="renseign=true" 
                                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold py-2 px-4 sm:py-3 sm:px-8 rounded-lg sm:rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 text-sm sm:text-base"
                                    >
                                        Commencer la saisie
                                    </button>
                                    </div>
                                </div>
                        

                                <!-- Patient Silhouette -->
                                <div  class="mt-4 lg:mt-0">
                                    <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-lg border border-gray-100 text-center">
                                        <div class="relative">
                                        <div class="absolute inset-0 bg-gradient-to-t from-blue-100 to-transparent rounded-lg sm:rounded-xl"></div>
                                        <img src="https://clickdoc.webredirect.org/public/icons/silhouette.png" class="w-full max-w-xs mx-auto relative z-10 filter drop-shadow-lg">
                                        </div>
                                        <div class="mt-3 sm:mt-4 p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg sm:rounded-xl">
                                        <h4 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 sm:mb-2">Vue d'ensemble</h4>
                                        <p class="text-xs sm:text-sm text-gray-600">Visualisation anatomique du patient</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dialog for data entry -->
                            <el-dialog title="Saisie des Mesures Vitales" v-model="renseign" width="90%" class="custom-dialog max-w-2xl">
                                <div class="space-y-4">
                                <el-form label-position="top" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <el-form-item label="Taille" class="mb-4">
                                    <el-input v-model="mesure_rens.taille" size="large" class="rounded-lg">
                                        <template #append>cm</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Poids" class="mb-4">
                                    <el-input v-model="mesure_rens.poids" size="large" class="rounded-lg">
                                        <template #append>kg</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Tension Artérielle" class="mb-4">
                                    <el-input v-model="mesure_rens.tension" size="large" class="rounded-lg">
                                        <template #append>mmHg</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Fréquence Cardiaque" class="mb-4">
                                    <el-input v-model="mesure_rens.fr_cardiaque" size="large" class="rounded-lg">
                                        <template #append>bpm</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Saturation en Oxygène" class="mb-4">
                                    <el-input v-model="mesure_rens.saturation" size="large" class="rounded-lg">
                                        <template #append>%</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Glycémie" class="mb-4">
                                    <el-input v-model="mesure_rens.gly" size="large" class="rounded-lg">
                                        <template #append>mmol/L</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item label="Température Corporelle" class="mb-4 sm:col-span-2">
                                    <el-input v-model="mesure_rens.temp" size="large" class="rounded-lg">
                                        <template #append>°C</template>
                                    </el-input>
                                    </el-form-item>
                                    
                                    <el-form-item class="sm:col-span-2">
                                    <button 
                                        @click="async()=>await renseigner()" 
                                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
                                    >
                                        Enregistrer les mesures
                                    </button>
                                    </el-form-item>
                                </el-form>
                                </div>
                            </el-dialog>
                    </div>

                    <style>
.vital-card {
  padding: 0.75rem;
  border-radius: 0.75rem;
  border: 2px solid #e5e7eb;
  transition: all 0.3s;
  display: flex;
  align-items: center;
}

.vital-card:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
              0 4px 6px -2px rgba(0, 0, 0, 0.05);
  transform: translateY(-0.25rem);
}

.vital-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.vital-content {
  flex: 1;
  min-width: 0;
}

.vital-label {
  font-size: 0.875rem;
  color: #4b5563;
  margin-bottom: 0.25rem;
  font-weight: 500;
}

.vital-value {
  font-size: 1.125rem;
  font-weight: 700;
  color: #1f2937;
}

.vital-unit {
  font-size: 0.875rem;
  font-weight: 400;
  color: #6b7280;
}

.heartbeat-icon {
  animation: heartbeat 1.5s ease-in-out infinite;
}

@keyframes heartbeat {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

/* Dialog customizations */


/* Responsive adjustments for small screens */
@media (max-width: 640px) {
  .vital-card {
    min-height: 80px;
  }

  .vital-value {
    font-size: 0.875rem;
    line-height: 1.25rem;
  }

  .vital-label {
    font-size: 0.75rem;
  }
}
</style>
