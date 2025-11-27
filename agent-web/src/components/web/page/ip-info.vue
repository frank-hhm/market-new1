<template>
    <template v-if="ip !== ''">
        <div>
            <div class="nowrap te1">{{ ip || '' }}</div>
            <div class="nowrap te1 text-grey">
                <template v-if="info?.country"><span>{{ info?.country }}</span></template>
                <template v-if="info?.province"><span>{{ info?.province }}</span></template>
                <template v-if="info?.isp"><span>[{{ info?.isp }}]</span></template>
            </div>
        </div>
        <div v-if="client_ip != '' && client_ip != ip">
            <div class="nowrap te1 text-red">{{ client_ip || '' }}</div>
            <div class="nowrap te1 text-grey">
                <template v-if="client_info?.country"><span>{{ client_info?.country }}</span></template>
                <template v-if="client_info?.province"><span>{{ client_info?.province }}</span></template>
                <template v-if="client_info?.isp"><span>[{{ client_info?.isp }}]</span></template>
            </div>
        </div>
    </template>
    <template v-else>
        <span class="text-grey">无</span>
    </template>
</template>

<script lang="ts">
export default {
    name: "pageIpInfo",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";


const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const props = withDefaults(
    defineProps<{
        ip?: string;
        info?: any;
        client_ip?: string;
        client_info?: any;
    }>(),
    {
        ip: "",
        info: {},
        client_ip: "",
        client_info: {}
    }
);
</script>

<style scoped></style>