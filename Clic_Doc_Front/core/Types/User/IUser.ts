export interface User {
    id:number;
    email: string;
    password:string;
    name: string;
    role?: number;
    category_id?: number;
    avatar?: string;
}