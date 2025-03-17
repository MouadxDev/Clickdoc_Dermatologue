<script lang="ts" setup>
import { useUiStore } from '../../core/Data/stores/ui';
import { useAuthStore } from '../../core/Data/stores/auth';
import { Auth } from '../../core/Clients/Auth';
import { IResponse } from "../../core/Types/IResponse";
import { useRouter } from "vue-router";
import { ref, reactive } from "vue";
import { ElMessage } from 'element-plus';
import { Users } from '../../core/Clients/Users';
import UiUpload from '../pages/ui-upload.vue';
import { getImageURL } from '../../core/Clients/UploadService';


// Stores and services
const auth = new Auth();
const store = useAuthStore();
const ui = useUiStore();
const router = useRouter();

// States
const isDropdownOpen = ref(false);
const isModalVisible = ref(false);
const defaultAvatar = 'https://placehold.co/150'; 
const fileInput = ref<HTMLInputElement | null>(null);


// Temporary data storage for modal form, including password fields
const tempUserData = reactive({
    name: '',
    email: '',
    avatar: '',
    oldPassword: '',
    currentPassword: '',
    newPassword: ''
});

// Functions
async function logout() {
    console.log("logout");
    
    const response: IResponse = await auth.logout();
    
    if (response.status === 200) {
        router.push("/login");
    }
}

function toggleDropdown() {
    isDropdownOpen.value = !isDropdownOpen.value;
}

function closeDropdown() {
    isDropdownOpen.value = false;
}

function showAccountModal() {
    // Load existing user data into tempUserData
    tempUserData.name = store.user.name;
    tempUserData.email = store.user.email;
    tempUserData.avatar = store.user.avatar || defaultAvatar;
    isModalVisible.value = true;
}

function closeAccountModal() {
    isModalVisible.value = false;
}

function handleFileUpload(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            tempUserData.avatar = reader.result as string;
        };
        reader.readAsDataURL(file);
    }
}

async function updateAccount() {
    const usersService = new Users(); 
  

    // let avatarUrl =  getImageURL(); 
    



    // Create the request payload
    const updatePayload = {
        id: store.user.id, 
        name: tempUserData.name,
        email: tempUserData.email,
        avatar: tempUserData.avatar, // Updated avatar URL
        oldPassword: tempUserData.oldPassword,
        password: tempUserData.newPassword ? tempUserData.newPassword : undefined,
    };

    try {
        // Call the update method from Users class to send the request to the backend
        const response = await usersService.update(updatePayload);

        if (response.status === 200) {
            store.user.name = tempUserData.name;
            store.user.email = tempUserData.email;
            store.user.avatar = avatarUrl;
            ElMessage.success("Information updated successfully");
            closeAccountModal();
        }

    } catch (error) {
        ElMessage.error("Error updating your information");
    }
}


</script>


<template>
    <a-layout class="h-screen overflow-y-auto background-clickdoc">
        <a-layout-sider v-if="!ui.fold" :trigger="null" style="background: white;">
            <ui-menu :has-logo="true" :is-superadmin="store.user.role === 'superAdmin'" logo="/logo.png" />
        </a-layout-sider>
        <a-layout style="background: none;">
            <a-layout-header style="background: none;">
                <div class="navbar navbar-sm rounded-xl h-10">
                    <div class="navbar-start">
                        <button class="btn btn-ghost btn-circle" @click="ui.setFold(!ui.fold)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                        </button>
                    </div>
                    <div class="navbar-center">
                        <div class="logo-solo-text"></div>
                    </div>
                    <div class="navbar-end">
                        <button class="btn btn-ghost btn-circle">
                            <div class="indicator">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="badge badge-xs badge-primary indicator-item"></span>
                            </div>
                        </button>
                        <div class="dropdown">
                            <label tabindex="0" class="btn btn-ghost rounded-full" @click="toggleDropdown">
                                <el-avatar :size="32" class="mr-3" :src="store.user.avatar" />
                                <span class="text-large font-600 mr-3">{{ store.user.name }}</span>
                            </label>
                            <ul v-if="isDropdownOpen" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                                <li><a href="#" @click="() => { closeDropdown(); showAccountModal(); }">Mon profil</a></li>
                                <li><a href="#" @click="() => { closeDropdown(); logout(); }">De déconnecter</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </a-layout-header>
            <a-layout-content class="p-2 overflow-y-auto">
                <add-patient></add-patient>
                <edit-patient></edit-patient>
                <add-rdv></add-rdv>
                <slot />
            </a-layout-content>
        </a-layout>
    </a-layout>

    <!-- Modal for "Mon compte" -->
    <el-dialog v-model="isModalVisible" title="Mon Compte" @close="closeAccountModal">
        <form @submit.prevent="updateAccount">
            <!-- Profile Picture Upload Section -->
            <div class="form-group">
                <div class="profile-upload" @click="$refs.fileInput.click()">
                    <img :src="tempUserData.avatar || defaultAvatar" alt="Profile Picture" class="profile-avatar" />
                    <input type="file" ref="fileInput" @change="handleFileUpload" class="file-input" accept="image/*" style="display: none;" />
                </div>
                <label class="label-pr">Profile Picture</label>
                <!-- <ui-upload /> -->
                
            </div>

            <!-- Name Input -->
            <div class="form-group">
                <label for="nameInput">Nom</label>
                <input type="text" id="nameInput" v-model="tempUserData.name" class="form-control" placeholder="Enter your name">
            </div>

            <!-- Email Input -->
            <div class="form-group">
                <label for="emailInput">Email</label>
                <input type="email" id="emailInput" v-model="tempUserData.email" class="form-control" placeholder="Enter your email">
            </div>


            <!-- Current Password Input -->
            <div class="form-group">
                <label for="passwordInput">Mot de passe actuel</label>
                <input type="password" id="passwordInput" v-model="tempUserData.oldPassword" class="form-control" placeholder="Current password">
            </div>

            <!-- New Password Input -->
           <div class="form-group">
                <label for="passwordInput">Nouveau mot de passe</label>
                <input type="password" id="passwordInput" v-model="tempUserData.newPassword" class="form-control" placeholder="Current password">
            </div>


            <!-- Modal Footer Buttons -->
            <span slot="footer" class="dialog-footer">
                <el-button type="button" class="Btn_close" @click="closeAccountModal">Fermer</el-button>
                <el-button type="submit" @click="updateAccount">Enregistrer</el-button>
            </span>

        </form>
    </el-dialog>
</template>

<style scoped>
    .Btn_close:hover{
        background-color: #ff4c4c; /* Darker red background on hover */
        color: #fff; /* White text */
        border: 1px solid #ff4c4c;  
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .profile-upload {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .profile-avatar {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
        transition: border-color 0.3s;
    }

    .profile-avatar:hover {
        border-color: #007bff;
    }

    .dialog-footer {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    .el-button {
        padding: 10px 20px;
    }
    .label-pr{
        text-align: center;
    }
</style>
