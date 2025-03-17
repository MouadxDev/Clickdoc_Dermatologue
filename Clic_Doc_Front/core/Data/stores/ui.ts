import { defineStore } from 'pinia';

export const useUiStore = defineStore("ui", {
    state: () => {
      return {
        fold:false,
        size:{ label: 'Normal', value: 'normal', size:'10' },
      };
    },
    actions: {
      setFold(fold:boolean)
      {
        this.fold=fold
      }
    },
    persist: {
      storage: sessionStorage, // data in sessionStorage is cleared when the page session ends.
    },
  });