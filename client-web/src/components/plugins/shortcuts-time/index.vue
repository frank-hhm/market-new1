<template>
    <a-range-picker :show-time="isShowTime" v-model="_modelValue" :shortcuts="shortcuts" shortcuts-position="left"
        :format="format" @change="onChange" @select="onSelect" @ok="onOk" style="width: 100%;"
        :disabledDate="disabledDate" @popupVisibleChange="onPopupVisibleChange" :size="isMobile?'small':'medium'"/>
</template>
<script lang="ts">
export default {
    name: "shortcuts-time",
};
</script>
<script setup lang="ts">
import { useAppStore } from "@/store";
import { get } from "http";
import { storeToRefs } from "pinia";
import { ref, getCurrentInstance, watch } from "vue";
const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const props = withDefaults(
    defineProps<{
        modelValue?: string | number | string[] | number[] | any;
        shortcuts?: boolean;
        btnShortcuts?: boolean;
        format?: string;
        size?: string;
        isShowTime?: boolean;
        default?: number[] | string[];
        max?: number | string | boolean;
    }>(),
    {
        modelValue: "",
        shortcuts: true,
        btnShortcuts: true,
        format: "YYYY-MM-DD HH:mm:ss",
        size: "default",
        isShowTime: true,
        default: () => {
            return [-1, 1, 3, 7, 30, 365]
        },
        max: false
    }
);

const _modelValue = ref<string | number | string[] | number[] | any>(
    props.modelValue
);

const emit = defineEmits(["change", "update:modelValue"]);

interface shortcutsItem {
    label: string;
    value: () => any;
}

let now = new Date();
let nowTime = now.getTime(); //当前时间戳
let nowDay = now.toLocaleDateString(); //当天
let nowDayTime = new Date(nowDay).getTime(); //当天开始时间戳
let nowMonth = now.getMonth(); //当前月
let nowYear = now.getFullYear(); //当前年

const getShortcuts = () => {
    let arrData: any = []
    if ($utils.inArray(-1, props.default)) {
        arrData.push({
            label: '昨天',
            value: () => {
                const start = nowDayTime - 3600 * 1000 * 24;
                const end = nowDayTime - 1;
                return [start, end];
            },
        })
    }
    if ($utils.inArray(1, props.default)) {
        arrData.push({
            label: '今天',
            value: () => {
                const end = nowDayTime + 3600 * 1000 * 24 - 1;
                return [nowDayTime, end];
            },
        })
    }
    if ($utils.inArray(3, props.default)) {
        arrData.push({
            label: '最近3天',
            value: () => {
                const start = nowDayTime - 3600 * 1000 * 24 * 2;
                const end = nowDayTime + 3600 * 1000 * 24 - 1;
                return [start, end];
            },
        })
    }
    if ($utils.inArray(7, props.default)) {
        arrData.push({
            label: '最近7天',
            value: () => {
                const start = nowDayTime - 3600 * 1000 * 24 * 6;
                const end = nowDayTime + 3600 * 1000 * 24 - 1;
                return [start, end];
            },
        })
    }
    if ($utils.inArray(30, props.default)) {
        arrData.push({
            label: '本月',
            value: () => {
                let start = new Date(nowYear, nowMonth, 1).getTime(); //本月开始时间戳
                let end = new Date(nowYear, nowMonth + 1, 0).getTime() + 3600 * 1000 * 24 - 1; // 本月结束时间戳
                return [start, end];
            },
        })
    }
    if ($utils.inArray(365, props.default)) {
        arrData.push({
            label: '今年',
            value: () => {
                let start = new Date(nowYear, 0).getTime();
                let end = nowTime;
                return [start, end];
            },
        })
    }

    return arrData;
}

const shortcuts = ref<shortcutsItem[]>(getShortcuts());

const onPopupVisibleChange = (visible: boolean) => {
    if (visible) {
        _modelValue.value = [];
    }
}
const disabledDate = (current: any) => {
    const dates = _modelValue.value;
    if (dates && dates.length && props.max) {
        const tooLate = dates[0] && Math.abs((Number(new Date(current)) - dates[0]) / (24 * 60 * 60 * 1000)) > Number(props.max);
        const tooEarly = dates[1] && Math.abs((Number(new Date(current)) - dates[1]) / (24 * 60 * 60 * 1000)) > Number(props.max);
        return tooEarly || tooLate;
    }
    return false;
}

const onChange = (dateString: any, date: any) => {
    emit("change", dateString);
    emit("update:modelValue", dateString);
}

const onSelect = (dateString: any, date: any) => {
    _modelValue.value = date;
    // console.log('onSelect: ', dateString, date);
    // emit("change", dateString);
    // emit("update:modelValue", dateString);
}
const onOk = (dateString: any, date: any) => {
    emit("change", dateString);
    emit("update:modelValue", dateString);
}


const onClear = () => {
    _modelValue.value = props.modelValue
}


defineExpose({ onClear });
</script>