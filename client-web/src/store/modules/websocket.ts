import { ref } from "vue"
import store from "@/store"
import { defineStore } from "pinia"
import { baseWsUrl, getToken, dispatchEvents } from "@/utils";
import router from "@/router";
import { websocketCallbackFunction } from "@/utils/ws-callback-function";

let isWebSocketOpen = false

export const useWebsocketStore = defineStore("websocket", () => {
    const ws = ref<WebSocket | null>(null);

    const wsStatus = ref<boolean>(false)

    const initWebSocket = async () => {
        if (!ws.value) {
            isWebSocketOpen = true;
            let wsUrl = baseWsUrl();
            wsUrl = baseWsUrl();
            ws.value = new WebSocket(wsUrl);
            setupEventListeners(ws.value);
        }
    }

    let heartbeatIntervalId: any | unknown = null;

    const setupEventListeners = (websocket: WebSocket) => {
        heartbeatIntervalId = setInterval(() => {
            if (websocket?.readyState === WebSocket.OPEN) {
                let obj: any = { type: 'ping' };

                if (getToken()) {
                    obj.token = getToken()
                }
                sendMessage(obj);
            } else if (websocket?.readyState === WebSocket.CLOSED || websocket?.readyState === WebSocket.CLOSING) {
                clearInterval(heartbeatIntervalId!);
            }
        }, 20000);
        const handleClose = (event: CloseEvent) => {
            isWebSocketOpen = false;
            if (heartbeatIntervalId) {
                clearInterval(heartbeatIntervalId);
            }
            ws.value = null;
            setTimeout(() => {
                if (!isWebSocketOpen) {
                    initWebSocket();
                }
            }, 2000);
        }
        const handleMessage = (event: MessageEvent) => {
            const res = JSON.parse(event.data);
            dispatchEvents('websocketMessage', res);
        }
        const handleOpen = (event: Event) => {
            wsStatus.value = true;
            let obj: any = { type: 'ping' };
            if (getToken()) {
                obj.token = getToken()
            }
            sendMessage(obj);
        }
        const handleError = (error: Event | ErrorEvent) => {
            wsStatus.value = false;
            isWebSocketOpen = false;
            if (heartbeatIntervalId) {
                clearInterval(heartbeatIntervalId);
            }
            ws.value = null;
            setTimeout(() => {
                if (!isWebSocketOpen) {
                    initWebSocket();
                }
            }, 3000);
        }
        websocket.addEventListener('open', handleOpen);
        websocket.addEventListener('message', handleMessage);
        websocket.addEventListener('error', handleError);
        websocket.addEventListener('close', handleClose)
    }

    const sendMessage = (data: any) => {
        if (ws.value?.readyState === WebSocket.OPEN) {
            ws.value.send(JSON.stringify(data));
        }
    }
    return { ws, wsStatus, initWebSocket, sendMessage }
})
/** 在 setup 外使用 */
export function useWebsocketStoreHook() {
    return useWebsocketStore(store)
}
