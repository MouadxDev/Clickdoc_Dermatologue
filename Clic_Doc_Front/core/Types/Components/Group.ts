import { Item } from "./Item";

export interface Group {
    name: string;
    superAdmin:boolean;
    items:Array<Item>;
}