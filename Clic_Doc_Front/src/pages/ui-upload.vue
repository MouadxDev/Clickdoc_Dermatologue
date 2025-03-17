  <template>
    <div>
      <CldImage :public-id="imageRef" />
      <input type="file" @change="handleFileChange" />
      <button @click="uploadImage">Upload Image</button>
    </div>
  </template>
  
  <script lang="ts" setup>
        import { ref } from "vue";
        import axios from "axios";
        import { setImageURL } from "../../core/Clients/UploadService";

        const imageRef = ref('');
        const selectedFile = ref<File | null>(null);
            const cloudName = 'dr5srjvta'; // Replace with your Cloudinary cloud name
            const uploadPreset = 'Avatar_Up'; 

        function handleFileChange(event: Event) {
        selectedFile.value = (event.target as HTMLInputElement).files?.[0] || null;
        }

        async function uploadImage() {
        if (!selectedFile.value) {
            console.error("Please select a file first.");
            return;
        }

        const formData = new FormData();
        formData.append('file', selectedFile.value);
        formData.append('upload_preset', uploadPreset);

        try {
            const response = await axios.post(`https://api.cloudinary.com/v1_1/${cloudName}/image/upload`, formData);
            imageRef.value = response.data.public_id;
            setImageURL(response.data.secure_url);  // Set the image URL globally
        } catch (error) {
            console.error("Error uploading file:", error);
        }
        }
</script>
  