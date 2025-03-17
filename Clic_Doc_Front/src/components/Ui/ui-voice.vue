<template>
  <div>
    <!-- Button to open the Voice Recorder Modal -->
    <el-button type="primary" @click="openModal" style="margin-top: 10px;">Open Voice Recorder</el-button>
  
    <!-- Voice Recording Modal -->
    <el-dialog v-model="isModalVisible" title="Voice Recorder" @close="closeModal">
      <!-- Recording Section -->
      <div class="form-group">
        <p>{{ recording ? 'Recording...' : loading ? 'Transcribing...' : 'Press Start to record' }}</p>

        <!-- Start Recording Button with Overlay -->
        <div v-if="!recording && !loading" id="btn_start">
          <iframe src="https://lottie.host/embed/9511afd8-a7b5-4feb-9d20-22e269d3fa2b/xGQ6GOvE4g.json"></iframe>
          <a class="overlay-button" @click="startRecording">Start</a>
        </div>

        <!-- Loading Animation (while waiting for transcription) -->
        <div v-if="loading" id="loading_animation">
          <iframe src="https://lottie.host/embed/83d82769-a41c-4f2c-9b12-374dd762f712/JkC9AwsyIO.json"></iframe>
        </div>

        <!-- Voice Recorder Container (Visible during recording) -->
        <div v-if="recording" id="voice_rec_container">
          <img src="../../assets/icon/micro.svg" class="microphone-icon">
          <iframe src="https://lottie.host/embed/99ade226-0b43-4e56-9482-0ea8db4364c7/xnstuY22pA.json"></iframe>
        </div>

        <!-- Stop Recording Button -->
        <el-button 
          type="danger" 
          @click="stopRecording" 
          v-if="recording"
        >
          Stop Recording
        </el-button>
      </div>

      <!-- Transcription Result -->
      <div v-if="transcription">
        <p>Transcription:</p>
        <p>{{ transcription }}</p>
      </div>

      <!-- Modal Footer Buttons -->
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="closeModal">Close</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { VoiceService } from '../../../core/Clients/Voice2Text';

export default defineComponent({
  name: 'UiVocal',
  emits: ['transcription'],
  data() {
    return {
      isModalVisible: false,
      recording: false,
      loading: false, // New loading state for API response
      mediaRecorder: null as MediaRecorder | null,
      audioChunks: [] as Blob[], // Stores audio data in chunks
      transcription: null as string | null,
      voiceService: new VoiceService(), // Instantiate the service
    };
  },
  methods: {
    openModal() {
      this.isModalVisible = true;
      this.transcription = null;
    },
    closeModal() {
      this.isModalVisible = false;
      this.stopRecording();
      this.audioChunks = [];
      this.transcription = null;
      this.loading = false; // Reset loading state on close
    },
    async startRecording() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        this.mediaRecorder = new MediaRecorder(stream);

        // Capture audio data in chunks
        this.mediaRecorder.ondataavailable = (event) => {
          this.audioChunks.push(event.data);
        };

        // When recording stops, create an audio Blob and send it to the API
        this.mediaRecorder.onstop = async () => {
          const audioBlob = new Blob(this.audioChunks, { type: 'audio/wav' });
          this.loading = true; // Set loading to true when we start transcribing
          await this.transcribeAudio(audioBlob);
        };

        this.mediaRecorder.start();
        this.recording = true;
        this.audioChunks = [];
      } catch (error) {
        console.error("Error accessing microphone: ", error);
        alert("Microphone access denied or not supported.");
      }
    },
    stopRecording() {
      if (this.mediaRecorder && this.recording) {
        this.mediaRecorder.stop();
        this.recording = false;
      }
    },
    async transcribeAudio(audioBlob: Blob) {
      try {
        const response = await this.voiceService.transcribeAudio(audioBlob);
        this.transcription = response.data.transcription;
        this.$emit('transcription', this.transcription); // Emit the transcription to the parent
      } catch (error) {
        console.error("Error transcribing audio:", error);
      } finally {
        this.loading = false; // Hide loading animation when transcription is complete
      }
    },
  },
});
</script>

<style scoped>
.dialog-footer {
  text-align: right;
}
.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

#btn_start {
  position: relative;
  width: 100%;
  display: flex;
  justify-content: center;
}

#btn_start iframe {
  width: 300px;
  height: 300px;
}

#loading_animation iframe {
  width: 300px;
  height: 300px;
}

/* Overlay button */
.overlay-button {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  background: transparent;
  border: none;
}

#voice_rec_container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.microphone-icon {
  width: 150px;
}

.dialog-footer {
  display: flex;
  justify-content: end;
  padding-top: 35px;
}
</style>
