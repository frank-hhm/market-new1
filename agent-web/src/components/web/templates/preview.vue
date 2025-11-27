<template>
    <div>
        <a-modal v-model:visible="visible" title="预览" width="400px" top="50px">
            <div class="templates-iframe" v-if="visible">
                <iframe :src="iframeSrc" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
            <template #footer>
                <a-button @click="close">关闭</a-button>
            </template>
        </a-modal>
    </div>
</template>

<script lang="ts">
export default {
    name: "templatesPreview",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";


const visible = ref<boolean>(false)

const iframeSrc = ref<string>('')


const open = (src: string = '') => {
    iframeSrc.value = src
    visible.value = true;
};

const close = () => {
    visible.value = false;
};

defineExpose({ open });

</script>

<style scoped>
.templates-iframe {
    height: 600px;
}
</style>