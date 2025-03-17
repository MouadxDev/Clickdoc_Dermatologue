import {useRdvStore} from "../stores/rendez-vous"
import { RendezVous } from "../../Clients/RendezVous";
import { useUtilStore } from "../stores/utilitaire";

export class RdvService {
    private store = useRdvStore();
    private util = useUtilStore();
    private client = new RendezVous();

    public async add_rendez_vous(request:any) {
        const data :Array<any> = await  this.client.add(request);
        this.store.setRDVList(data)
        this.store.setTrigger(true)
        this.util.setRDV(false)
    }

}