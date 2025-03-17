import { reactive } from 'vue';

export const eventBus = reactive({
    filterText: '',
    setFilter(value: string) {
        this.filterText = value;
    }
});
