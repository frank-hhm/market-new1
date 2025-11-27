<template>
    <div class="">
        <img class="body-content-top-icon" :src="$utils.staticImgPath('/warning-top.jpg')" />
        <div class="wrap-row">
            <div class="main-box">
                <div class="main-box-title">
                    安全保障
                </div>
                <div class="main-box-desc">
                    保证金独立存管,入金支持支付宝,银联,数字货币等多种支付,实时到账,安全快捷
                </div>
            </div>
        </div>
    </div>
    <commonFooter></commonFooter>
</template>

<script setup lang="ts">
import { getCurrentInstance, ref, onMounted, watch } from "vue";
import commonFooter from "@/views/common/common-footer.vue"

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;
</script>
<style scoped>
.body-content-top-icon {
    width: 100%;
    display: block;
}

.main-box {
    margin-top: 20px;
    margin-bottom: 20px;
    padding: 60px 20px;
    background: #fff;
    min-height: 300px;
}

.main-box-title {

    font-size: var(--base-title-size);
    font-weight: bold;
    text-align: center;
}

.main-box-desc {
    margin-top: 40px;
    text-align: center;
    font-size: 16px;
    color: var(--base-text-grey);
}
</style>