import { ElMessage } from 'element-plus';

import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class ConstFiles {
    public baseUrl = ENV.VITE_BACKEND_API + ENV.VITE_BACKEND_URL_V1 + ENV.VITE_BACKEND_URL_CONSTFILES;
    public client = new httpClient(this.baseUrl);

    /**
     * Get a paginated list of const files with optional search and category
     * @param q keyword for searching (optional)
     * @param category filter by category_id (optional)
     */
    public async getAll(q: string = '', category: string = ''): Promise<IResponse> {
        try {
            let query = '';
            if (q || category) {
                const params = new URLSearchParams();
                if (q) params.append('q', q);
                if (category) params.append('category', category);
                query = '?' + params.toString();
            }

            const response = await this.client.get(query);
            return response.data;
        } catch (error: any) {
            ElMessage.error("Erreur lors de l'acquisition des données");
            return {
                status: error.status,
                data: error.response?.data || null
            };
        }
    }

    /**
     * Add a new const file entry
     * @param request { category_id: string, label: string }
     */
    public async add(request: { category_id: string, label: string }): Promise<IResponse> {
        try {
            const response = await this.client.post('', request);
            return response.data;
        } catch (error: any) {
            ElMessage.error("Erreur lors de l'enregistrement des données");
            return {
                status: error.status,
                data: error.response?.data || null
            };
        }
    }
}
