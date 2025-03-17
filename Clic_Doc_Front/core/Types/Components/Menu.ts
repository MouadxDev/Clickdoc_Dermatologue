import {Item} from "./Item"
import {Group} from "./Group"
export interface Menu {
    items?: Array<Item>;
    collapse?:boolean;
    hasLogo?:boolean;
    logo?:string;
    hasGroups?:boolean;
    isSuperadmin?:boolean;
    groups?:Array<Group>;
}