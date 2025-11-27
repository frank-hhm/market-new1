<template>
    <div>
        <a-carousel class="main-1" :style="{
            width: '100%',
            height: calculatedHeight + 'px',
        }" :default-current="1">
            <a-carousel-item v-for="(image, index) in bannerImages" :key="index">
                <div class="banner-item" :style="[`background-image:url(${$utils.staticImgPath(image)})`]"></div>
            </a-carousel-item>
        </a-carousel>
        <mainMarket></mainMarket>


        <main3></main3>


        <main4></main4>

        <main5></main5>

        <div class="main-6">
            <div class="wrap-row">
                <div class="main-title">合作伙伴</div>
                <div class="main-box">
                    <img class="main-6-icon" :src="$utils.staticImgPath('/index-icon-6.png')" />
                </div>
            </div>
        </div>

    </div>
    <commonFooter></commonFooter>
</template>
<script setup lang="ts">
import { getCurrentInstance, ref, onMounted, watch } from "vue";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";
import mainMarket from "@/views/common/market.vue"
import main3 from "@/views/common/main-3.vue"
import main4 from "@/views/common/main-4.vue"
import main5 from "@/views/common/main-5.vue"
import commonFooter from "@/views/common/common-footer.vue"

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const { clientWidth } = storeToRefs(useAppStore());

const bannerImages = ref<string[]>(["/banner/01.png", "/banner/02.png"]);
const calculateHeightBy16_9 = () => {
    const height = (clientWidth.value * 5) / 12;
    return height;
};

const calculatedHeight = ref<number>(calculateHeightBy16_9());

onMounted(() => { });

watch(clientWidth, () => {
    calculatedHeight.value = calculateHeightBy16_9();
});
</script>

<style scoped>
.banner-item {
    width: 100%;
    height: 100%;
    background-size: 100%;
    background-repeat: no-repeat;
    background-position: center;
}

.main-1 {
    display: block;
}


.main-6 {
    position: relative;
    width: 100%;
    z-index: 1;
    padding: 100px 0;
    background: linear-gradient(45deg, #027FFF, #44BBFF);
}

.main-6 .main-title {
    font-size: var(--base-title-size);
    font-weight: bold;
    text-align: center;
    color: #fff;
}

.main-6 .main-box {
    width: 100%;
    margin-top: 60px;
}

.main-6 .main-box .main-6-icon {
    width: 100%;
    display: block;
}
</style>