import { ElMessage } from 'element-plus';
import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class VoiceService {
    private baseUrl = ENV.AI_Voice2Text; // Use the environment variable for the API URL
    private client = new httpClient(this.baseUrl);

    public async transcribeAudio(audioBlob: Blob): Promise<IResponse> {
        const formData = new FormData();
        formData.append('audio', audioBlob, 'recording.wav'); // Send audio as 'recording.wav'

        try {
            const response = await this.client.post('', formData);
            return {
                status: response.status,
                data: response.data,
            };
        } catch (error: any) {
            ElMessage.error('Failed to transcribe audio');
            return {
                status: error.response?.status || 500,
                data: error.response?.data || 'Unknown error',
            };
        }
    }
}
