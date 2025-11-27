<template>
  <div class="layout-nav" :class="isMobile ? 'mobile' : ''">
    <div class="layout-nav-left">
      <div class="layout-nav-logo">
        <img class="logo-image" id="logoImage" v-if="config?.system_logo" :src="config?.system_logo" />
        <div class="layout-nav-name-wrap">
          <div class="name">{{ config?.system_name || '盛世景' }}</div>
        </div>
      </div>
      <div class="layout-nav-menus">
        <div class="layout-nav-menus-item" :class="cur.path == '/index' || cur.path == '/' ? 'active' : ''"
          @click="toPath('/index')">首页</div>
        <div class="layout-nav-menus-item" :class="cur.path == '/about' ? 'active' : ''" @click="toPath('/about')">关于我们
        </div>
        <!-- <div class="layout-nav-menus-item">活动专区</div> -->
        <div class="layout-nav-menus-item" :class="cur.path == '/article' ? 'active' : ''" @click="toPath('/article')">
          实时资讯
        </div>
        <div class="layout-nav-menus-item" :class="cur.path == '/download' ? 'active' : ''"
          @click="toPath('/download')">
          移动端下载</div>
        <div class="layout-nav-menus-item" :class="cur.path == '/trading' ? 'active' : ''" @click="toPath('/trading')">
          交易中心
        </div>
      </div>

      <!-- <div>
          <div>交易中心</div>
          <div>全球资讯</div>
      </div> -->
    </div>
    <div class="layout-nav-right" ref="rightRef">

      <a-button class="mr20" type="text" @click="toKeFu" size="mini">
        联系客服
      </a-button>
      <a-button class="mr20"
        v-if="!isMoni && member?.id && (member?.bind_status?.value == 0 || member?.bind_status?.value == 3)" type="text"
        status="danger" size="mini" @click="onBindCard">
        完成实名认证，领取更多豪礼
      </a-button>
      <a-button class="mr20" shape="circle" size="mini" @click="onFull">
        <icon-expand v-if="!isFull" />
        <icon-shrink v-else />
      </a-button>
      <a-popover v-if="member?.id" position="br">
        <div class="layout-nav-user">
          <a-avatar :size="24" :imageUrl="member?.avatar"></a-avatar>
          <div class="ml10" >
            <div class=""> {{ member?.username }}</div>
          </div>
        </div>
        <template #content>
          <div v-loading="allLoading">
            <div class="layout-nav-right-member">
              <div class="flex items-center between">
                <div class="flex">
                  <a-avatar :size="40" :imageUrl="member?.avatar"></a-avatar>
                  <div class="ml10" >
                    <div>{{ member?.username }}</div>
                    <div>{{ member?.mobile }}</div>
                  </div>
                </div>
                <div>
                  <a-button size="small" @click="toggleMoni()">{{ member.moni == 1 ? '切换真实' : '切换模拟' }}</a-button>
                </div>
              </div>

              <memberNavCoinComponent></memberNavCoinComponent>
              <a-alert class="mb20" type="warning" v-if="member?.id && member?.bind_status?.value == 2">认证状态{{
                member?.bind_status?.name }}</a-alert>
              <a-alert class="mb20" type="error" v-if="member?.id && member?.bind_status?.value == 3">认证状态{{
                member?.bind_status?.name }}</a-alert>
            </div>
            <div class="mt40 mb10 flex end">
              <a-space>
                <!-- <a-button type="text" size="mini">个人信息</a-button> -->
                <a-button type="text" size="mini" @click="onUpdatePassword">修改密码</a-button>
                <!-- <a-button type="text" size="mini">出金申请</a-button> -->
                <a-button type="text" size="mini"
                  v-if="member?.id && (member?.bind_status?.value == 0 || member?.bind_status?.value == 3)"
                  @click="onBindCard">{{ member?.bind_status?.value == 3 ? '重新认证' : '去认证' }}</a-button>
                <a-button size="mini" @click="onLoginout">退出登录</a-button>
              </a-space>
            </div>
          </div>
        </template>
      </a-popover>

      <a-space v-else>
        <a-button type="primary" @click="onLogin">开户</a-button>
        <a-button type="primary" @click="onLogin">登录</a-button>
      </a-space>
      <loginModalComponent></loginModalComponent>
      <memberBindCardComponent></memberBindCardComponent>
      <memberUpdatePasswordComponent ref="memberUpdatePasswordComponentRef"></memberUpdatePasswordComponent>
    </div>
  </div>
</template>
<script lang="ts">
export default {
  name: "LayoutNav",
};
</script>
<script setup lang="ts">
import {
  getCurrentInstance,
  ref,
  onMounted,
  watch,
  onBeforeUnmount,
} from "vue";
import { storeToRefs } from "pinia";
import { useAppStore, useChargeStore, useMemberStore } from "@/store";
import loginModalComponent from "@/components/member/login.vue";
import memberNavCoinComponent from "@/components/member/member-nav-coin.vue";
import memberBindCardComponent from "@/components/member/bind-card.vue";
import memberUpdatePasswordComponent from "@/components/member/update-password.vue";
import { Message } from "@arco-design/web-vue";
import { loginqieApi, logoutApi } from "@/api/member";
import { Result, ResultError } from "@/types";
import { setToken } from "@/utils";
import router from "@/router";

const emit = defineEmits(["change"]);

const { config, isMobile, allLoading } = storeToRefs(useAppStore());

const { member, isMoni } = storeToRefs(useMemberStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const logoImageEl = ref<HTMLElement | null>();

onMounted(() => {
  logoImageEl.value = document.getElementById("logoImage");
  logoImageEl.value?.addEventListener("error", () => {
    onImageError();
  });
});


const cur = ref<any>(router.currentRoute);

watch(
  () => router.currentRoute.value.path,
  (val) => {
    cur.value.path = val;
  },
  { deep: true }
);


const toKeFu = () => {
  $utils.toKeFu()
}

const onBindCard = () => {
  useMemberStore().setBindCardModal(true)
}

const memberUpdatePasswordComponentRef = ref<HTMLElement>()
const onUpdatePassword = () => {
  proxy?.$refs['memberUpdatePasswordComponentRef']?.open()
}

const onImageError = () => {
  if (logoImageEl.value) {
    logoImageEl.value.style.display = "none";
  }
};

const isFull = ref<boolean>(false);

const onFull = () => {
  if (document.fullscreenElement) {
    document.exitFullscreen();
  } else {
    document.body.requestFullscreen().catch((err) => {
      console.error("Error attempting to enable full screen", err);
    });
  }
};

const toPath = (path: string) => {
  router.push({
    path,
  });
};
const onLogin = () => {
  useMemberStore().setLoginModal(true);
}

const toggleMoni = () => {
  useAppStore().setAllLoading(true);
  useMemberStore().setInitMemberStatus(true);
  loginqieApi().then((res: Result) => {
    setToken(res.data.token, res.data.expires_time);
    useMemberStore().setMember(res.data.member);
    useMemberStore().setLogin(true);
    useMemberStore().setIsMoni(res.data.member?.moni == 0 ? false : true);
    useMemberStore().setMemberCoin(res.data.member_coin);
    useChargeStore().setOrderHold(res.data.order_hold || []);
    useChargeStore().setOrderRobot(res.data.order_robot || []);
    useMemberStore().setInitMemberStatus(false);
    useAppStore().setAllLoading(false);
  }).catch((err: ResultError) => {
    useMemberStore().setInitMemberStatus(false);
    useAppStore().setAllLoading(false);
  });
}

const onLoginout = () => {

  Message.loading({
    id: "outlogin",
    content: "正在退出...",
  });
  logoutApi().then(async (res: Result) => {
    useMemberStore().resetToken();
    $utils.setStorage("token", null);
    setTimeout(() => {
      Message.success({
        id: "outlogin",
        content: "退出成功!",
        duration: 1000,
        onClose: () => {
          onLogin();
        },
      });
    }, 500);
  });
}


const handleFullScreenChange = () => {
  isFull.value = !!document.fullscreenElement;
};

const screenWidth = ref();

const rightRef = ref<HTMLElement>();

onMounted(() => {
  screenWidth.value = document.body.clientWidth;
  window.onresize = () => {
    return (() => {
      screenWidth.value = document.body.clientWidth;
    })();
  };
  document.addEventListener("fullscreenchange", handleFullScreenChange);
  // websocketOpenCallback();
});

onBeforeUnmount(() => {
  document.removeEventListener("fullscreenchange", () => { });
});
</script>
<style scoped>
.layout-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 50px;
  background: var(--color-bg-1);
  border-bottom: 1px solid var(--color-border-1);
}

.layout-nav .layout-nav-left {
  width: 40%;
  display: flex;
  align-items: center;
  margin-left: 20px;
}

.layout-nav .layout-nav-right {
  margin-right: 20px;
  display: flex;
  align-items: center;
  justify-content: end;
}

.layout-nav .layout-nav-logo {
  max-width: 406px;
  height: 40px;
  line-height: 40px;
  text-align: center;
  display: flex;
  align-items: center;
}

.layout-nav .layout-nav-logo .logo-image {
  height: 36px;
  max-width: 120px;
  margin-right: 6px;
}

.layout-nav .layout-nav-logo .layout-nav-name-wrap {
  display: flex;
  align-items: center;
  max-width: 280px;
}

.layout-nav .layout-nav-logo .name {
  font-size: 16px;
  color: var(--color-text-1);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.layout-nav .layout-nav-logo .version {
  margin-left: 5px;
  font-size: 12px;
  color: var(--color-text-4);
}

.layout-nav .layout-nav-menu {
  display: flex;
  overflow: hidden;
  flex: 1;
  align-items: center;
  -webkit-transition: all 0.1s ease-in-out;
  transition: all 0.1s ease-in-out;
}

.layout-nav-menu-btn {
  width: 40px;
  text-align: center;
  height: 40px;
  line-height: 40px;
  cursor: pointer;
}

.layout-nav-menu-btn:hover {
  color: rgba(var(--primary-1));
}

.layout-nav .layout-nav-menu-item {
  padding: 0 12px;
  font-size: var(--base-size-default);
  cursor: pointer;
  height: 30px;
  line-height: 30px;
  user-select: none;
  border-radius: 2px;
}

.layout-nav .layout-nav-menu-item:hover {
  color: rgba(var(--primary-1));
}

.layout-nav .layout-nav-menu-item.active {
  color: rgba(var(--primary-1));
}

.layout-nav-user {
  display: flex;
  align-items: center;
  cursor: pointer;
  user-select: none;
  color: var(--color-text-1);
}

.layout-nav.mobile .layout-nav-left {
  max-width: 140px;
  margin-left: 4px;
}

.layout-nav.mobile .layout-nav-logo {
  max-width: 140px;
}

.layout-nav.mobile .layout-nav-logo .layout-nav-name-wrap {
  display: block;
  max-width: 100px;
  padding: 10px 0;
}

.layout-nav.mobile .layout-nav-logo .layout-nav-name-wrap>div {
  line-height: 18px;
  text-align: left;
}

.layout-nav-right-member {
  min-width: 320px;
}



.layout-nav-menus {
  display: flex;
  align-items: center;
  margin: 0 5px 0 40px;
  height: 50px;
}

.layout-nav-menus .layout-nav-menus-item {
  margin: 0 20px;
  cursor: pointer;
  user-select: none;
  color: var(--color-text-1);
  text-wrap: nowrap;
}

.layout-nav-menus .layout-nav-menus-item:hover {
  color: rgb(var(--primary-6));
}

.layout-nav-menus .layout-nav-menus-item.active {
  color: rgb(var(--primary-6));
  font-weight: bold;
}
</style>