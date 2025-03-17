import { ElMessage } from 'element-plus';
import ENV from '../env';
import { IResponse } from '../Types/IResponse';
import { httpClient } from './HttpClients';

export class Stock {

    public baseUrl = ENV.VITE_BACKEND_API + ENV.VITE_BACKEND_URL_V1 + ENV.VITE_BACKEND_URL_Stock;
    public client = new httpClient(this.baseUrl);

    // Fetch stock by ID
    public async getByID(id: number): Promise<any> {
        try {
            const response = await this.client.get('/' + id);
            return response.data;
        } catch (error: any) {
            ElMessage.error(error.response?.data || 'Error fetching stock details');
            return {
                status: error.response?.status || 500,
                data: error.response?.data || { message: 'An unexpected error occurred' }
            };
        }
    }

    // Add new stock
    public async add(request: any): Promise<IResponse> {
        try {
            const response = await this.client.post('', request);
            ElMessage.success('Stock added successfully');
            return response.data;
        } catch (error: any) {
            ElMessage.error('Error adding stock');
            return {
                status: error.response?.status || 500,
                data: error.response?.data || { message: 'An unexpected error occurred' }
            };
        }
    }

    // Fetch all stock
    public async getAll(request: any = {}): Promise<any> {
        try {
            const page = request.page || 1; // Default to page 1 if undefined
            const toGet = request.toGet || 25; // Default to 10 items per page if undefined
            const response = await this.client.get(`?page=${page}&toGet=${toGet}`);
            return response.data;
        } catch (error: any) {
            console.log(error);
            return {
                status: error.response?.status || 500,
                data: error.response?.data || { message: 'An unexpected error occurred' }
            };
        }
    }

    // Update stock
    public async update(request: any): Promise<IResponse> {
        try {
            const response = await this.client.put('/' + request.id, request);
            ElMessage.success('Stock mis à jour avec succès');
            return response.data;
            
        } catch (error: any) {
            ElMessage.error('Error updating stock');
            return {
                status: error.response?.status || 500,
                data: error.response?.data || { message: 'An unexpected error occurred' }
            };
        }
    }

    // Delete stock
    public async delete(id: string): Promise<IResponse> {
        try {
            const response = await this.client.delete(`/${id}`);
            ElMessage.success('Stock deleted successfully');
            return response.data;
        } catch (error: any) {
            ElMessage.error('Error deleting stock');
            return {
                status: error.response?.status || 500,
                data: error.response?.data || { message: 'An unexpected error occurred' }
            };
        }
    }
}
