<template>

    <a-modal :title="type ? '登录' : '开户'" @BeforeCancel="close" :width="'600px'" :top="useSetting().ModalTop"
        class="modal" v-model:visible="visible" :align-center="false" title-align="start" :mask-closable="false"
        render-to-body :footer="false">

        <template #title>
            <a-radio-group type="button" v-model="type">
                <a-radio value="login" :disabled="loginLoad">登录</a-radio>
                <a-radio value="register" :disabled="loginLoad">开户</a-radio>
            </a-radio-group>
        </template>
        <div class="login-body" v-loading="loginLoad">
            <img class="login-banner" v-if="config?.login_banner" :src="config?.login_banner" />
            <div class="mt30">
                <a-form class="login-body-form" v-if="type === 'login'" :model="loginForm" layout="vertical"
                    ref="loginRef" :rules="loginRules">
                    <a-form-item field="account" label="账号或手机" class="login-main-form-item">
                        <a-input v-model="loginForm.account" placeholder="请输入账号或手机" size="large">
                            <template #prefix>
                                <icon-user />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item field="password" label="密码" class="login-main-form-item">
                        <a-input-password v-model="loginForm.password" placeholder="请输入密码" allow-clear size="large">
                            <template #prefix>
                                <icon-lock />
                            </template>
                        </a-input-password>
                    </a-form-item>
                    <a-form-item field="captcha_code" label="图片验证码" class="login-main-form-item default-append">
                        <a-input v-model="loginForm.captcha_code" placeholder="请输入验证码" allow-clear maxlength="4"
                            size="large">
                            <template #prefix>
                                <icon-dice />
                            </template>
                            <template #append>
                                <a-spin :loading="captchaLoading">
                                    <div @click="toLoginCaptcha" class="login-captcha-img">
                                        <img v-if="captchaImage" :src="captchaImage" />
                                    </div>
                                </a-spin>
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item class="login-main-form-item">
                        <div class="login-main-form-item-fill">
                            <div>
                                <a-button type="primary" :loading="loginLoad" long @click="toLogin"
                                    size="large">登录</a-button>
                            </div>
                            <div class="flex between items-center">
                                <a-button class="mt12" type="text" size="mini" @click="onRegister">去开户</a-button>
                                <div></div>
                                <!-- <a-button class="mt12" type="text" size="mini" @click="onRegister">找回密码</a-button> -->
                            </div>
                            <a-button class="mt12" long @click="close()" size="large">暂不登录</a-button>
                        </div>
                    </a-form-item>
                </a-form>
                <a-form class="login-body-form" v-if="type === 'register'" :model="registerForm" layout="vertical"
                    ref="registerRef" :rules="registerRules">
                    <a-form-item field="mobile" label="手机号码" class="login-main-form-item">
                        <a-input v-model="registerForm.mobile" placeholder="请输入手机号码" size="large">
                            <template #prefix>
                                <icon-phone />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item field="code" label="图片验证码" class="login-main-form-item default-append">
                        <a-input v-model="registerForm.code" placeholder="请输入验证码" allow-clear maxlength="4"
                            size="large">
                            <template #prefix>
                                <icon-dice />
                            </template>
                            <template #append>
                                <a-spin :loading="captchaLoading">
                                    <div @click="toLoginCaptcha" class="login-captcha-img">
                                        <img v-if="captchaImage" :src="captchaImage" />
                                    </div>
                                </a-spin>
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item label="手机验证码" field="mobile_code" class="login-main-form-item">
                        <a-input placeholder="请输入手机验证码" v-model="registerForm.mobile_code">
                            <template #append>
                                <a-button type="text" size="mini" :disabled="isCodeStatus || codeLoading"
                                    @click="getPhoneCode" :loading="codeLoading">{{ codeText }}</a-button>
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item field="username" label="用户名" class="login-main-form-item">
                        <a-input v-model="registerForm.username" placeholder="请输入用户名" size="large">
                            <template #prefix>
                                <icon-user />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item field="pwd" label="密码" class="login-main-form-item">
                        <a-input-password v-model="registerForm.pwd" placeholder="请输入密码" allow-clear size="large">
                            <template #prefix>
                                <icon-lock />
                            </template>
                        </a-input-password>
                    </a-form-item>
                    <a-form-item field="conf_pwd" label="确定密码" class="login-main-form-item">
                        <a-input-password v-model="registerForm.conf_pwd" placeholder="请再次输入账号密码" allow-clear
                            size="large">
                            <template #prefix>
                                <icon-lock />
                            </template>
                        </a-input-password>
                    </a-form-item>

                    <a-form-item field="invite_code" label="邀请码" class="login-main-form-item">
                        <a-input v-model="registerForm.invite_code" placeholder="请输入邀请码" size="large">
                            <template #prefix>
                                <icon-user />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item class="login-main-form-item">
                        <div class="login-main-form-item-fill">
                            <a-button type="primary" :loading="loginLoad" long @click="toRegister"
                                size="large">提交开户申请</a-button>
                            <div>
                                <a-button class="mt12" type="text" size="mini" @click="onLogin">去登录</a-button>
                            </div>
                        </div>
                    </a-form-item>
                </a-form>
                <div class="register-bottom" v-if="type === 'register'">
                    <a-checkbox class="register-bottom-checkbox" v-model="registerChecked">我已阅读并同意</a-checkbox>
                    <div class="artical" @click="onArtical('agreement_customer', '《客户协议》')">
                        《客户协议》
                    </div>
                    <div class="artical" @click="onArtical('agreement_risk', '《风险披露》')">
                        《风险披露》
                    </div>
                    <div class="artical" @click="onArtical('agreement_disclaimers', '《免责声明》')">
                        《免责声明》
                    </div>
                    <loginArticalModal ref="loginArticalModalRef"></loginArticalModal>
                </div>
            </div>
        </div>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "login-modal",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { useAppStore, useChargeStore, useMemberStore } from "@/store";
import { Result, ResultError } from "@/types";
import { storeToRefs } from "pinia";
import { getCaptchaApi, getMobileCodeCaptchaApi } from "@/api/common";
import { ValidatedError, Notification } from "@arco-design/web-vue";
import { loginApi, registerApi } from "@/api/login";
import loginArticalModal from "./login-artical.vue";
import { setToken } from "@/utils";

const { config } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const visible = ref<boolean>(false);

const type = ref<"login" | "register">("login");

const loginForm = reactive({
    account: "",
    password: "",
    captcha_uniqid: "",
    captcha_code: "",
});
const loginRules = {
    account: [{ required: true, message: "请输入账号", trigger: "blur" }],
    password: [{ required: true, message: "请输入密码", trigger: "blur" }],
    captcha_code: [{ required: true, message: "请输入验证码", trigger: "blur" }],
};

const registerForm = reactive({
    mobile: "",
    username: "",
    pwd: "",
    conf_pwd: "",
    code: "",
    mobile_code: "",
    invite_code: "",
    captcha_uniqid: ""
});
const registerRules = {
    mobile: [{ required: true, message: "请输入手机号码", trigger: "blur" }],
    username: [{ required: true, message: "请输入用户名", trigger: "blur" }],
    pwd: [{ required: true, message: "请输入账号密码", trigger: "blur" }],
    conf_pwd: [{ required: true, message: "请再次输入账号密码", trigger: "blur" }],

    code: [{ required: true, message: "请图形验证码", trigger: "blur" }],
    mobile_code: [{ required: true, message: "请手机验证码", trigger: "blur" }],
    invite_code: [{ required: true, message: "请输入邀请码", trigger: "blur" }],
};


const captchaImage = ref<string>("");

const captchaLoading = ref<boolean>(false);

const toLoginCaptcha = () => {
    captchaLoading.value = true;
    getCaptchaApi()
        .then((res: Result) => {
            captchaImage.value = res.data.data;
            if (type.value == 'login') {
                loginForm.captcha_uniqid = res.data.uniqid;
            } else {
                registerForm.captcha_uniqid = res.data.uniqid;
            }
            captchaLoading.value = false;
        })
        .catch((err: ResultError) => {
            captchaLoading.value = false;
            $utils.errorMsg(err);
        });
};

const loginLoad = ref<boolean>(false);

const toLogin = () => {
    if (!proxy?.$refs['loginRef']) return;
    if (loginLoad.value == true) {
        return false;
    }
    proxy?.$refs['loginRef']?.validate((valid: boolean) => {
        if (!valid) {
            loginLoad.value = true;
            let obj = {
                username: loginForm.account,
                password: loginForm.password,
                captcha_uniqid: loginForm.captcha_uniqid,
                captcha_code: loginForm.captcha_code,
                type: "web"
            }
            loginApi(obj).then((res: Result) => {
                Notification.success({
                    title: "登录成功",
                    content: "",
                    duration: 1500,
                    onClose: () => {
                        useMemberStore().setMember(res.data.member);
                        setToken(res.data.token, res.data.expires_time);
                        useMemberStore().setLogin(true);
                        useMemberStore().setIsMoni(res.data.member?.moni == 0 ? false : true);
                        useMemberStore().setMemberCoin(res.data.member_coin);
                        useChargeStore().setOrderHold(res.data.order_hold || []);
                        useChargeStore().setOrderRobot(res.data.order_robot || []);
                        useMemberStore().setLoginModal(false)
                    },
                });
            }).catch((err: ResultError) => {
                $utils.errorMsg(err);
                setTimeout(() => {
                    loginLoad.value = false;
                }, 1000);
            });
        }
    })
}

const registerChecked = ref<boolean>(false);

const loginArticalModalRef = ref<HTMLElement>();

const onArtical = (type: string, title: string) => {
    proxy?.$refs['loginArticalModalRef']?.open(type, title);

}
const toRegister = () => {
    if (!proxy?.$refs['registerRef']) return;
    if (loginLoad.value == true) {
        return false;
    }
    proxy?.$refs['registerRef']?.validate((valid: boolean) => {
        if (!valid) {
            if (registerChecked.value == false) {
                $utils.errorMsg("请阅读并同意协议");
                return false;
            }
            loginLoad.value = true;
            let obj = {
                mobile: registerForm.mobile,
                username: registerForm.username,
                pwd: registerForm.pwd,
                conf_pwd: registerForm.conf_pwd,
                code: registerForm.conf_pwd,
                invite_code: registerForm.invite_code,
                mobile_code: registerForm.mobile_code,

            }
            registerApi(obj).then((res: Result) => {
                $utils.successMsg("开户成功！");
                loginLoad.value = false;
                type.value = 'login';
            }).catch((err: ResultError) => {
                $utils.errorMsg(err);
                setTimeout(() => {
                    loginLoad.value = false;
                }, 1000);
            });
        }
    })
}

const onRegister = () => {
    type.value = 'register';
}

const onLogin = () => {
    type.value = 'login';
}


const codeText = ref<string>("获取验证码");

const codeLoading = ref<boolean>(false);

const codeTimer = ref<unknown | any>(null);

const codeCount = ref<number>(60);

const isCodeStatus = ref<boolean>(false);

const getPhoneCode = () => {
    // return false;
    proxy?.$refs['registerRef']?.validateField(['mobile', 'code'], (error: undefined | Record<string, ValidatedError>) => {
        if (error === undefined) {
            if (codeLoading.value === true) {
                return
            }
            codeLoading.value = true;
            getMobileCodeCaptchaApi({
                phone: registerForm.mobile,
                captcha_code: registerForm.code,
                captcha_uniqid: registerForm.captcha_uniqid,
            }).then((res: Result) => {
                $utils.successMsg(res.message);
                setCodeTimer();
                codeLoading.value = false;
            }).catch((err: ResultError) => {
                if (err.data?.code == 702) {
                    registerForm.code = "";
                    toLoginCaptcha();
                }
                $utils.errorMsg(err);
                codeLoading.value = false;
            });
        }
    })
};
const setCodeTimer = () => {
    isCodeStatus.value = true;
    codeCount.value = 60
    codeText.value = codeCount.value + "s"
    codeTimer.value = setInterval(() => {
        if (codeCount.value <= 0) {
            codeText.value = "获取验证码"
            if (codeTimer.value) {
                clearInterval(Number(codeTimer.value));
            }
            codeTimer.value = null
            isCodeStatus.value = false;
            return
        }
        codeCount.value--
        codeText.value = codeCount.value + "s"
    }, 1000)
}


onMounted(() => {
    toLoginCaptcha();
});

const open = (isSet: boolean = true) => {
    visible.value = true;
    if (isSet) {
        useMemberStore().setLoginModal(true);
    }
};

const close = (isSet: boolean = true) => {
    visible.value = false;
    type.value = 'login';
    proxy?.$refs['loginRef']?.resetFields();
    proxy?.$refs['registerRef']?.resetFields();
    loginLoad.value = false;
    if (isSet) {
        useMemberStore().setLoginModal(false);
    }
    return true;
};

watch(
    () => useMemberStore().loginModal,
    (val) => {
        if (val === true) {
            open(false);
        } else {
            close(false);
        }
    },
    {
        deep: true,
        immediate: true
    }
);

defineExpose({ open });
</script>

<style scoped>
.login-body {
    margin: 0 0 30px;
}

.login-body-form {
    width: 320px;
    margin: 0 auto;
}

.login-banner {
    width: 100%;
}



.login-captcha-img {
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    text-align: center;
}

.login-captcha-img img {
    width: 80px;
    height: 34px;
    cursor: pointer;
    user-select: none;
    display: block;
}

.login-main-form-item-fill {
    width: 100%;
}

.register-bottom {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    font-size: var(--base-size-small);
    user-select: none;
}

.register-bottom .artical {
    cursor: pointer;
    color: rgb(var(--primary-6));
}
</style>