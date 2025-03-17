export interface Patient {
    id?:number;
    uid?:string;
    name: string;
    surname: string;
    sex:string;
    date_of_birth: string;
    phone:string;
    CIN?:string;
    coverage:boolean;
    coverage_type:string;
    avatar?: string;
}