import { defineStore } from 'pinia';

interface State {
  socket: any
}

export const useSocketStore = defineStore("socket", {
    state: () : State => {
      return {
        // socket : new WebSocket("ws://165.227.152.200:3090") ,
         socket : new WebSocket("wss://clickdoc.webredirect.org/websocket/"),

      };
    },
    getters:{
      getSocket(state)
      {
        return state.socket
      }
    }
});