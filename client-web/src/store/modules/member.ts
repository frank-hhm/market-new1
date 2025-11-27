
import store, { useChargeStore, useChargeStoreHook } from "@/store"
import { Result, ResultError } from "@/types"
import { defineStore } from "pinia"
import { ref } from "vue"
import { getCacheMemberDetaulColor, removeToken, setCacheMemberDetaulColor } from "@/utils";
import { useAppStore } from "./app"
export const useMemberStore = defineStore("member", () => {

    const memberDefaultColor = ref<string>(getCacheMemberDetaulColor())

    const member = ref<any>({})

    const memberCoin = ref<any>({})

    const isMoni = ref<boolean>(false)

    const isLogin = ref<boolean>(false)

    const isInitMemberStatus = ref<boolean>(false)

    //登录modal状态
    const loginModal = ref<boolean>(false)
    //身份证认证状态
    const bindCardModal = ref<boolean>(false)

    //入金弹窗状态
    const rechargeModal = ref<boolean>(false)

    const setLoginModal = (_value: boolean) => {
        loginModal.value = _value
    }
    const setBindCardModal = (_value: boolean) => {
        if (member.value?.bind_status === 1) {
            return false;
        }
        bindCardModal.value = _value
    }
    const setRechargeModal = (_value: boolean) => {
        rechargeModal.value = _value
    }
    const setLogin = (_value: boolean) => {
        isLogin.value = _value
    }
    const setMember = (value: any) => {
        member.value = value
    }
    const setIsMoni = (value: boolean) => {
        isMoni.value = value
    }
    const setMemberCoin = (value: any) => {
        memberCoin.value = value
    }

    //获取余额
    const getBalance = () => {
        let balance = memberCoin.value?.balance || 0
        if (isNaN(balance)) {
            return 0.00;
        }
        return Number(balance).toFixed(2)
    }

    const getUpColor = () => {
        return memberDefaultColor.value == "red" ? useAppStore().redColor : useAppStore().greenColor
    }
    const getDownColor = () => {
        return memberDefaultColor.value == "red" ? useAppStore().greenColor : useAppStore().redColor
    }

    /** 重置 Token */
    const resetToken = () => {
        removeToken()
        isLogin.value = false
        setIsMoni(false)
        setMember(null)
        setMemberCoin([])
        useChargeStoreHook().setOrderHold([])
        useChargeStoreHook().setOrderRobot([])
    }

    const setInitMemberStatus = (_value: boolean) => {
        isInitMemberStatus.value = _value
    }

    return {
        getUpColor,
        getDownColor,
        setMember,
        member,
        isMoni,
        memberCoin,
        isLogin,
        setLogin,
        setIsMoni,
        setMemberCoin,
        memberDefaultColor,
        setLoginModal,
        loginModal,
        setBindCardModal,
        setRechargeModal,
        rechargeModal,
        bindCardModal,
        getBalance,
        resetToken,
        isInitMemberStatus,
        setInitMemberStatus
    }
})
/** 在 setup 外使用 */
export function useMemberStoreHook() {
    return useMemberStore(store)
}


export default {
    useMemberStore,
    useMemberStoreHook
}