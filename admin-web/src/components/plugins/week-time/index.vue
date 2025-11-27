<template>
    <div class="week-time-body">
        <div class="week-time-select">
            <a-dropdown @popup-visible-change="onChange" position="bl" class="week-time-container">
                <a-button type="text" size="small">配置</a-button>
                <template #content>
                    <div class="week-time-main">
                        <div class="week-time-main-item" v-for="(item, index) in TimeArrData" :key="index">
                            <div class="item-header">
                                <div>{{ weekNameArr[index] }}</div>
                                <div class="">
                                    <a-button size="small" @click="onCreateTime(index)">
                                        添加时间段
                                    </a-button>
                                </div>
                            </div>
                            <div class="item-main">
                                <div class="main-items" v-for="(items, idx) in item" :key="idx">
                                    <div class="items-time">
                                        <a-time-picker type="time-range" format="HH:mm"
                                            v-model="TimeArrData[index][idx]" @change="onChangeTime" />
                                    </div>
                                    <a-button @click="onDeleteTime(index, idx)"><icon-delete /></a-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </a-dropdown>
            <div class="week-time-select-empty" v-if="TimeArrLength === 0">
                无
            </div>
            <a-tooltip v-else position="bottom">
                <div class="week-time-select-text">
                    <template v-for="(item, index) in timeData" :key="index">
                        <template v-if="item">{{ weekNameArr[index] }}：{{ item }}、</template>
                    </template>
                </div>
                <template #content>
                    <template v-for="(item, index) in timeData" :key="index">
                        <template v-if="item">{{ weekNameArr[index] }}：{{ item }}<br /></template>
                        <template v-else>{{ weekNameArr[index] }}：无<br /></template>
                    </template>
                </template>
            </a-tooltip>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "week-time",
};
</script>
<script setup lang="ts">
import {
    ref,
    computed,
    watch,
    getCurrentInstance,
    nextTick,
    onMounted
} from "vue";


const props = withDefaults(
    defineProps<{
        modelValue: string[]
    }>(),
    {
        modelValue: () => {
            return [];
        }
    }
);

const emit = defineEmits(['update:modelValue'])

const weekNameArr = ref<string[]>([
    "星期天",
    "星期一",
    "星期二",
    "星期三",
    "星期四",
    "星期五",
    "星期六"
]);

const TimeArrLength = computed(() => {
    let _lengthArr: boolean[] = [];
    TimeArrData.value.forEach((item, index) => {
        if (item && item.length > 0) {
            item.forEach((items) => {
                if (items && items != '') {
                    _lengthArr[index] = true;
                }
            });
        }
    });
    return _lengthArr.length;
});

const timeData = ref<any[]>(props.modelValue || [])

const TimeArrData = ref<any[]>([
    [],
    [],
    [],
    [],
    [],
    [],
    []
]);

const onChange = (res: boolean) => {
    if (res === true) { }
}

const onCreateTime = (index: number) => {
    TimeArrData.value[index].push("")
}

const onDeleteTime = (index: number, idx: number) => {
    TimeArrData.value[index].splice(idx, 1)
    onChangeTime();
}

const onChangeTime = () => {
    TimeArrData.value.forEach((item, index) => {
        let _itemArr: any[] = [];
        item.forEach((items: any, idx: number) => {
            if (items && items.length > 0) {
                let _items = JSON.parse(JSON.stringify(items))
                _itemArr.push(_items.join("~"))
            }
        })
        timeData.value[index] = _itemArr.join("|")
    })
    emit("update:modelValue", timeData.value)
}

const onInit = () => {
    // console.log("=====1",timeData.value, props.modelValue)
    props.modelValue.forEach((item, index) => {
        let _item:string[] = []
        if (item != "") {
            _item = item.split("|")
        }
        // console.log("=====2",_item)
        if (_item && _item.length > 0) {
            let _itemArr: any[] = [];
            _item.forEach((items: any, idx: number) => {
                if (items && items.length > 0) {
                    let _items = JSON.parse(JSON.stringify(items))
                    _itemArr.push(_items.split("~"))
                }
            })
            TimeArrData.value[index] = _itemArr
        }
    })
    // console.log("=====2",timeData.value,TimeArrData.value)
}

onMounted(() => {
    nextTick(() => {
        onInit();
    })
})

watch(() => {
    return props.modelValue
}, (newVal, oldVal) => {
    nextTick(() => {
        timeData.value = JSON.parse(JSON.stringify(newVal))
        onInit();
    })
})

</script>

<style>
.week-time-container .arco-dropdown-list-wrapper {
    max-height: 400px;
}
</style>

<style scoped>
.week-time-body {
    width: 100%;
}

.week-time-select {
    width: 100%;
    display: flex;
    align-items: center;
}

.week-time-main {
    padding: 0 10px 10px 10px;
    width: 360px;
}

.week-time-select-text {
    margin-left: var(--base-margin);
    color: var(--color-text-1);
    width: calc(100% - 60px);
    white-space: nowrap;
    /* 强制文本不换行 */
    text-overflow: ellipsis;
    /* 溢出时显示省略号 */
    overflow: hidden;
}

.week-time-main-item .item-header {
    color: var(--color-text-1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
}

.week-time-main-item .item-main {
    margin-top: 10px;
}

.week-time-main-item {
    border-bottom: 1px solid var(--color-border-1);
}

.week-time-main-item .main-items {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.week-time-main-item .items-time {
    width: calc(100% - 60px);
}
.week-time-select-empty{
    font-size: var(--base-size-small);
    margin-left: var(--base-margin);
}
</style>