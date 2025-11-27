<template>
    <div class="body-footer">
        <div class="wrap-row">
            <div class="footer-nav">
                <div class="footer-nav-item" @click="toPath('/about')">关于我们</div>
                <div class="footer-nav-item" @click="toPath('/article')">
                    实时资讯
                </div>
                <div class="footer-nav-item" @click="toPath('/warning')">
                    安全保障
                </div>
                <div class="footer-nav-item" @click="toPath('/private')">
                    隐私条款
                </div>
                <div class="footer-nav-item" @click="toPath('/disclaimer')">
                    免责声明
                </div>
            </div>
            <div class="footer-desc">
                本网站由{{
                    config?.system_name
                }}负责管理，为客户提供全球金融衍生品交易服务。我们持有美国全国期货协会颁发的NFA，美国财政部FinCEN颁发的MSB等金融合法牌照，并在香港设有分公司以满足亚太地区客户快速增长的金融服务需求。
            </div>

            <div class="footer-bottom">
                <div>期货有风险，投资需谨慎！</div>
                <div>
                    Copyright© 2017-2021 {{ config?.system_name }}. All Rights
                    Reserved.
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "common-footer",
};
</script>

<script setup lang="ts">
import router from "@/router";
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";
import { ref, reactive, getCurrentInstance, nextTick, onMounted, computed } from "vue";

const { config } = storeToRefs(useAppStore());
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const toPath = (path: string) => {
    router.push({
        path,
    });
};

</script>

<style scoped>

.body-footer {
  background: #242933;
  color: #b2b2b2;
  padding:60px 20px 0;
}
.body-footer .footer-nav {
  display: flex;
  margin-bottom: 40px;
}
.body-footer .footer-nav .footer-nav-item {
  margin-right: 20px;
  cursor: pointer;
  color: #fff;
  user-select: none;
}
.body-footer .footer-desc {
  margin-bottom: 40px;
}
.body-footer .footer-nav .footer-nav-item:hover {
  color: var(--base-default-bg);
}
.body-footer .footer-bottom {
  height: 60px;
  line-height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

@media screen and (max-width: 799px) {
  .body-footer .footer-nav {
    flex-wrap: wrap;
  }
  .body-footer .footer-nav .footer-nav-item {
    margin-bottom: 10px;
  }
  .body-footer .footer-bottom {
    padding: 10px 0;
    display: block;
    line-height: 24px;
    text-align: center;
  }
  .body-kefu {
    right: 10px;
    bottom: 20px;
    height: 40px;
    width: 40px;
    border-radius: 40px;
    font-size: 12px;
  }
}
</style>