<template>

    <div class="main-5">
        <div class="main-5-bg" :style="[
            `background-image:url(${$utils.staticImgPath('/index-bg-5.png')})`,
        ]"></div>
        <div class="wrap-row">
            <div class="main-title">我们的优势 真实存在</div>
            <div class="main-desc">无惧多空,小资金大利润,为你节约更多的成本</div>
            <div class="main-box">
                <div class="main-box-item">
                    <div class="main-box-item-title">成本优势</div>
                    <div class="main-box-item-desc">
                        开户免费,最低保证金只需要 100 美元,超低手续费,0 点差
                    </div>
                </div>
                <div class="main-box-item">
                    <div class="main-box-item-title">资金安全</div>
                    <div class="main-box-item-desc">
                        保证金独立存管,入金支持支付宝,银联,数字货币等多种支付,实时到账,安全快捷
                    </div>
                </div>
                <div class="main-box-item">
                    <div class="main-box-item-title">软件优势</div>
                    <div class="main-box-item-desc">
                        自主 APP 自由选择,毫秒级订单执行速度,可设定止盈止损有效控制风险
                    </div>
                </div>
                <div class="main-box-item">
                    <div class="main-box-item-title">交易优势</div>
                    <div class="main-box-item-desc">
                        全天 24 小时双向交易杠杆最高可达 1000 倍小资撬动财富
                    </div>
                </div>
                <div class="main-box-item">
                    <div class="main-box-item-title">服务优势</div>
                    <div class="main-box-item-desc">
                        24 小时客服免费咨询服务,提供专业客观的投资策略分析全球热点财经报道
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script lang="ts">
export default {
    name: "commonMain5",
};
</script>


<script setup lang="ts">
import { ref, reactive, getCurrentInstance, nextTick, onMounted, computed } from "vue";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;
</script>


<style scoped>
.main-5 {
    position: relative;
    width: 100%;
    z-index: 1;
    padding: 100px 0;
    /* background: #bfd6ff; */
}

.main-5-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    background-position: center;
    z-index: -1;
}

.main-5 .main-title {
    font-size: var(--base-title-size);
    font-weight: bold;
    text-align: center;
    color: #fff;
}

.main-5 .main-desc {
    margin-top: 20px;
    text-align: center;
    font-size: 16px;
    color: #fff;
}

.main-5 .main-box {
    width: 100%;
    margin-top: 60px;
    display: flex;
    flex-wrap: wrap;
}

.main-5 .main-box .main-box-item {
    padding: 20px;
    color: #fff;
    width: calc(33.33% - 48px - 24px);
    margin: 12px;
    border: 4px solid #535353;
}

.main-5 .main-box .main-box-item .main-box-item-title {
    font-size: 20px;
}

.main-5 .main-box .main-box-item .main-box-item-desc {
    margin-top: 20px;
    line-height: 20px;
    color: #f6f6f6;
}



@media screen and (max-width:699px) {
    .main-box {
        display: block;
    }

    .main-5 .main-box .main-box-item {
        width: 100%
    }
}
</style>