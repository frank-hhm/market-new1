/** 统一处理 localStorage */

import CacheKey from "@/constants/cache-key"
import {  EnumListsType, PageLimitType } from "@/types"
import { getStorage, setStorage } from "@/utils"

export const getCacheLayout = () => {
  return getStorage(CacheKey.LAYOUT)
}
export const setCacheLayout = (layout: string) => {
  setStorage(CacheKey.LAYOUT, layout)
}

export const getCacheConfig = () => {
  return getStorage(CacheKey.CONFIG)
}
export const setCacheConfig = (data: any) => {
  setStorage(CacheKey.CONFIG, data)
}
export const getCacheTemplateDark = () => {
  return getStorage(CacheKey.TEMPLATE_DARK)
}
export const setCacheTemplateDark = (data: boolean) => {
  setStorage(CacheKey.TEMPLATE_DARK, data)
}

export const getCachePageLimit = () => {
  return getStorage(CacheKey.PAGE_LIMIT)
}
export const setCachePageLimit = (data: number) => {
  setStorage(CacheKey.PAGE_LIMIT, data)
}


export const getCacheMemberDetaulColor = () => {
  return getStorage(CacheKey.MEMBER_DEFAULT_COLOR)
}
export const setCacheMemberDetaulColor = (data: string[] | number) => {
  setStorage(CacheKey.MEMBER_DEFAULT_COLOR, data)
}

export const getCacheSelectMarketId = () => {
  return getStorage(CacheKey.SELECT_MARKET_ID)
}
export const setCacheSelectMarketId = (data: string | number) => {
  setStorage(CacheKey.SELECT_MARKET_ID, data)
}

export const getCacheMainIndicatorsIndex = () => {
  return getStorage(CacheKey.MAININDICATORSINDEX)
}
export const setCacheMainIndicatorsIndex = (data: string | number) => {
  setStorage(CacheKey.MAININDICATORSINDEX, data)
}

export const getCacheAssistantIndicatorsIndex = () => {
  return getStorage(CacheKey.ASSISTANTINDICATORSINDEX)
}
export const setCacheAssistantIndicatorsIndex = (data: string | number) => {
  setStorage(CacheKey.ASSISTANTINDICATORSINDEX, data)
}



export const getCacheLogin = () => {
  let obj = getStorage(CacheKey.LOGINDATA) || {};
  if (obj.account) {
    obj.account = window.atob(obj.account)
  }
  if (obj.password) {
    obj.password = window.atob(obj.password)
  }
  return obj
}

export const setCacheLogin = (data: {
  account: string;
  password: string;
} | unknown) => {
  setStorage(CacheKey.LOGINDATA, data)
}
export default {
  getCacheConfig,
  setCacheConfig,
  getCacheTemplateDark,
  setCacheTemplateDark,
  getCacheLogin,
  setCacheLogin,
  getCacheMemberDetaulColor,
  setCacheMemberDetaulColor,
  getCacheSelectMarketId,
  setCacheSelectMarketId,
  getCacheMainIndicatorsIndex,
  setCacheMainIndicatorsIndex,
  getCacheAssistantIndicatorsIndex,
  setCacheAssistantIndicatorsIndex
}