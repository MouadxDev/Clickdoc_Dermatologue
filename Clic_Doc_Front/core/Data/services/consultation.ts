import {useConsultStore} from "../stores/consultation"
import { Consultation } from "../../Clients/Consult";
import { IResponse } from "../../Types/IResponse";
import { useRouter } from "vue-router";


export class ConsultationService {
    private consultationStore = useConsultStore();
    private client = new Consultation();
    private router = useRouter();

    public async add_consultation(request:any) {
        const data :IResponse = await  this.client.add(request);
        this.consultationStore.setConsult(data.data.consultation);
        this.consultationStore.setUID(data.data.uid);
        this.consultationStore.setPatientID(data.data.patient);
        this.consultationStore.setExamenID(data.data.examen);
        this.consultationStore.setDiagnosticID(data.data.diagnostic);
        this.consultationStore.setObservationID(data.data.observation);
        this.consultationStore.setEdit(true)
        this.router.push("/consultation");
    }

    public async get_consultation(consultation_id:number,edit:boolean) {
        const data :IResponse = await  this.client.getOne(consultation_id);
        this.consultationStore.setConsult(data.data.consultation);
        this.consultationStore.setUID(data.data.uid);
        this.consultationStore.setPatientID(data.data.patient);
        this.consultationStore.setExamenID(data.data.examen);
        this.consultationStore.setDiagnosticID(data.data.diagnostic);
        this.consultationStore.setObservationID(data.data.observation);
        this.consultationStore.setFactureID(data.data.diagnostic);
        this.consultationStore.setEdit(edit)
        this.router.push("/consultation");
    }

}