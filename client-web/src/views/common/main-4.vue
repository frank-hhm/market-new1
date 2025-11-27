<template>
  <div class="main-4">
    <div
      class="main-4-bg"
      :style="[
        `background-image:url(${$utils.staticImgPath('/index-bg-2.png')})`,
      ]"
    ></div>
    <div class="wrap-row">
      <div class="main-box">
        <div class="main-box-left">
          <img
            class="main-box-left-img"
            :src="$utils.staticImgPath('/market-icon.png')"
          />
        </div>
        <div class="main-box-right">
          <div class="main-box-right-title">关于盛世景</div>
          <div class="main-box-right-desc">
            <p>
              盛世景创立于香港，拥有虚拟货币交易，国际期货交易，股指交易，是一家全方位服务国际综合性的交易平台，在金融行业一直处于领先地位。
            </p>
            <p>
              根据公司的战略发展规划,在美国、英国、新加坡、澳大利亚均设有海外事业部，公司计划在未来几年内将营业部扩增，从而形成辐射全球的期货市场行销网路模式。
            </p>
            <p>
              我们致力于为投资者获取各方面的股资，行情走势等第一手信息，节约其时间和金钱，提高认知效率，以辅助其各类商业行为，包括区块链数字资产投资，资讯情服百科知识，细分行业资讯,国外公司产品信息资料服务。
            </p>
            <p>
              未来，盛世最将以"清点时间财富，让货币安全快捷”为企业愿望,恪守"无缺陷产品,零距离服务"的质时理念,秉承"中国人面前我代表盛世景,外国人面前我代表中国"的企业精神不懈努力!
            </p>
          </div>
          <div class="main-box-right-btn">
            <img
              @click="toDownload('ios')"
              class="main-box-right-btn-icon"
              :src="$utils.staticImgPath('/ios-download-icon.png')"
            />
            <img
              @click="toDownload('android')"
              class="main-box-right-btn-icon"
              :src="$utils.staticImgPath('/android-download-icon.png')"
            />

            <a-tooltip position="bottom" background-color="#fff">
              <img
                class="main-box-right-btn-icon"
                :src="$utils.staticImgPath('/qrcode-download-icon.png')"
              />
              <template #content>
                <a-image
                  class="main-box-qrcode"
                  :src="config?.qrcode_h5"
                  :width="160"
                  :height="160"
                  show-loader
                  @click="
                    () => {
                      qrcodeH5Visible = true;
                    }
                  "
                />
              </template>
            </a-tooltip>
          </div>
        </div>
      </div>
    </div>
    <a-image-preview
      :src="config?.qrcode_h5"
      v-model:visible="qrcodeH5Visible"
    />
  </div>
</template>
<script lang="ts">
export default {
  name: "commonMain4",
};
</script>
<script lang="ts" setup>
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import { getCurrentInstance, ref } from "vue";
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";
const { config } = storeToRefs(useAppStore());
const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const qrcodeH5Visible = ref<boolean>(false);

const toDownload = (type: string) => {
  if (type === "ios") {
    $utils.downloadFile(config.value?.download_ios);
  } else if (type === "android") {
    $utils.downloadFile(config.value?.download_android);
  } else {
    $utils.errorMsg("未知错误");
  }
};
</script>
<style scoped>
.main-4 {
  position: relative;
  width: 100%;
  z-index: 1;
  padding: 120px 0;
}
.main-4-bg {
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
.main-4 .main-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}
.main-4 .main-box .main-box-left {
  width: 45%;
}
.main-4 .main-box .main-box-right {
  width: 45%;
}
.main-4 .main-box .main-box-left .main-box-left-img {
  width: calc(100% - 40px);
}
.main-4 .main-box .main-box-right .main-box-right-title {
  font-size: var(--base-title-size);
  font-weight: bold;
}
.main-4 .main-box .main-box-right .main-box-right-desc {
  margin-top: 40px;
  font-size: 16px;
  color: var(--base-text-grey);
}
.main-4 .main-box .main-box-right .main-box-right-desc p {
  margin-top: 20px;
  line-height: 24px;
}

.main-4 .main-box .main-box-right .main-box-right-btn {
  margin-top: 40px;
  display: flex;
}
.main-4 .main-box .main-box-right .main-box-right-btn-icon {
  height: 48px;
  margin-right: 20px;
  cursor: pointer;
}
.main-box-qrcode {
  height: 160px;
  width: 160px;
}

@media screen and (max-width:999px) {
   .main-4 .main-box .main-box-right{
        width: 50%;
    }
   .main-4 .main-box .main-box-left{
        width: calc(50% - 20px);
    }
}
@media screen and (max-width:859px) {
   .main-4 .main-box{
        display: block;
    }
   .main-4 .main-box .main-box-right{
    margin-top: 40px;
        width: 100%;
    }
   .main-4 .main-box .main-box-left{
        width: 100%;
    }
    .main-4 .main-box .main-box-right .main-box-right-title{
      text-align: center;
    }
    .main-4 .main-box .main-box-right .main-box-right-btn{
      justify-content: center;
    }
}
@media screen and (max-width:559px) {
    .main-4 .main-box .main-box-right .main-box-right-btn{
      flex-wrap: wrap;
      justify-content: center;
    }
    .main-4 .main-box .main-box-right .main-box-right-btn-icon{
      margin-bottom: 20px;
    }
}
</style>