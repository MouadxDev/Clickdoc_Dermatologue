import axios, { AxiosInstance } from "axios";
import { useAuthStore } from "../Data/stores/auth";

export class httpClient {
  private client: AxiosInstance;

  constructor(baseUrl: string) {
    this.client = axios.create({
      baseURL: baseUrl,
    });

    // Request interceptor to attach token dynamically
    this.client.interceptors.request.use((config) => {
      const authStore = useAuthStore();
      const token = authStore.token;  // direct reactive state access
      if (token) {
        config.headers = config.headers || {};
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    }, (error) => Promise.reject(error));
  }

  public get(url: string) {
    return this.client.get(url);
  }

  public post(url: string, data: any) {
    return this.client.post(url, data);
  }

  public put(url: string, data: any) {
    return this.client.put(url, data);
  }

  public delete(url: string) {
    return this.client.delete(url);
  }
}
