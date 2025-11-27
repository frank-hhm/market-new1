<template>
  <div class="main-3" :style="[
      `background-image:url(${$utils.staticImgPath('/_icon-bg.png')})`]">
    <div class="wrap-row">
      <div class="main-title">产品丰富 一站全配置</div>
      <div class="main-desc">
        涵盖贵金属,原油,股指,数字货币等 30+全球热门交易产品
      </div>
      <div class="main-box" >
        <div class="main-box-items">
          <div class="main-box-item">
            <img class="main-box-item-icon" :src="$utils.staticImgPath('/_icon-1.png')" />
            <div class="main-box-item-title">贵金属</div>
            <div class="main-box-item-desc">
              涵盖最受欢迎的,黄金白银，可抵御通胀，也可以投资增值
            </div>
          </div>

          <div class="main-box-item">
            <img class="main-box-item-icon" :src="$utils.staticImgPath('/_icon-2.png')" />
            <div class="main-box-item-title">股指</div>
            <div class="main-box-item-desc">
              涵盖深受投资者青睐的全球热门指数，恒指纳指,德指等
            </div>
          </div>
        </div>
            <img class="main-box-item-logo" :src="$utils.staticImgPath('/_icon-logo.png')"/>
      
        <div class="main-box-items">
          <div class="main-box-item">
            <img class="main-box-item-icon" :src="$utils.staticImgPath('/_icon-3.png')" />
            <div class="main-box-item-title">数字货币</div>
            <div class="main-box-item-desc">
              涵盖国际最大交易量之比特币，以太坊，等20+热门交易货币
            </div>
          </div>

          <div class="main-box-item">
            <img class="main-box-item-icon" :src="$utils.staticImgPath('/_icon-4.png')" />
            <div class="main-box-item-title">原油</div>
            <div class="main-box-item-desc">
              涵盖目前投资市场上最大原油产品分别是美国石油,及英国石油
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: "commonMain3",
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

.main-3 {
  position: relative;
  width: 100%;
  z-index: 1;
  padding: 100px 0 60px;
  /* background: #bfd6ff; */
  background-color: #D0E0FB;
      background-size: 100% 100%;
      background-position: center;
      background-repeat: no-repeat  ;
}
.main-3 .main-title {
  font-size: var(--base-title-size);
  font-weight: bold;
  text-align: center;
}
.main-3 .main-desc {
  margin-top: 20px;
  text-align: center;
  font-size: 16px;
  color: var(--base-text-grey);
}
.main-3 .main-box {
  margin-top:120px;
  width: 100%;
}

.main-3 .main-box-items {
  margin-bottom: 120px;
  width: 100%;
  display: flex;
  justify-content:space-around;
}
.main-box-item-logo{
    position: absolute;
    top: calc(50% - 20px);
    left: calc(50% - 60px);
    width: 120px;
}
.main-3 .main-box-item {
  width:200px;
  padding:60px 30px 30px;
  background: linear-gradient(to right, #017efe, #43b9ff);
  color: #fff;
  border-radius: 10px;
  position: relative;
}
.main-3 .main-box-item .main-box-item-title {
  font-size: 24px;
  text-align: center;
  font-weight: bold;
}
.main-3 .main-box-item .main-box-item-icon{
    width: 60px;
    height: 60px;
    position: absolute;
    left: calc(50% - 30px);
    top: -30px;
    border:2px solid #fff;
    border-radius: 60px;
}
.main-3 .main-box-item .main-box-item-desc {
  margin-top: 10px;
  line-height:20px;
  color: #f7fafd;
}


@media screen and (max-width:859px) {
   .main-box-item-logo{
        display: none;
    }
}
@media screen and (max-width:599px) {
   .main-box-item-logo{
        display: none;
    }
    .main-3 .main-box{
      margin-top: 60px;
    }
    .main-3 .main-box-items{
      display: block;
    }
    .main-3 .main-box-items{
      
      margin-bottom: 60px;
    }
    .main-3 .main-box-item{
      width: calc(100% - 60px);
      margin-bottom: 60px;
    }
}
</style>