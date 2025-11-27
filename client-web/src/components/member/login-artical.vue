<template>
    <a-modal :title="title" @BeforeCancel="close" :width="'800px'" :top="useSetting().ModalTop" class="modal"
        v-model:visible="visible" :align-center="true" title-align="start" :mask-closable="false" render-to-body>
        <div class="login-artical-body" v-loading="initLoaing">
            <div v-html="articalDetail"></div>
        </div>

        <template #footer>
            <a-space>
                <a-button v-btn @click="close">关闭</a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "login-artical",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { Result, ResultError } from "@/types";
import { getAgreementApi } from "@/api/common";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const visible = ref<boolean>(false);

const title = ref<string>("");

const type = ref<string>("");

const initLoaing = ref<boolean>(false);

const articalDetail = ref<string>("");
const toInit = () => {
    initLoaing.value = true;
    getAgreementApi(type.value).then((res: Result) => {
        articalDetail.value = res.data.content;
        initLoaing.value = false;
    }).catch((err: ResultError) => {
        $utils.errorMsg(err);
        close();
    })
};

const open = (_type: string, _title: string) => {
    type.value = _type;
    title.value = _title;
    visible.value = true;
    nextTick(() => {
        toInit();
    });
};

const close = () => {
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>
<style scoped>
.login-artical-body {
    height: 80vh;
}
</style>