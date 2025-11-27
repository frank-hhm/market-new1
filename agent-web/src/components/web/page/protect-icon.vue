<template>
    <template v-if="protect && (
        protect.device_visitor_status == 1
        || 
        protect.ios_version !== 0
        || 
        protect.android_version !== 0
        || (protect.country && protect.country.length > 0)
        || (protect.bot && protect.bot.length > 0)
        || (protect.language && protect.language.length > 0)
        || (protect.network && protect.network.length > 0)
        || (protect.system && protect.system.length > 0)
        || (protect.proxy && protect.proxy.length > 0)
    )">
        <a-popover title="防护">
            <div class="icon icon-wancheng-4" :class="`text-green`"></div>
            <template #content>
                <!-- <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.network && $utils.inArray('client_ip', protect.network) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止动态IP访问<div class="fz12 text-grey">(浏览器获取与服务器获取的客户端IP不一致)</div>
                    </div>
                </div> -->
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.network && $utils.inArray('black_ip', protect.network) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止高风险网络访问<div class="fz12 text-grey">(近期有滥用行为,黑名单)
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.network && $utils.inArray('cloud_ip', protect.network) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止可能来自云服务器的访问<div class="fz12 text-grey">(如：腾讯云、阿里云等)
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.bot && $utils.inArray('bot', protect.bot) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止机器人访问<div class="fz12 text-grey">(包含机器人、爬虫、工具类请求)
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.warning_device && $utils.inArray('program', protect.warning_device) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止打开编程辅助工具浏览器访问
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.warning_device && $utils.inArray('timezone', protect.warning_device) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止时区异常的设备访问
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.warning_device && $utils.inArray('visitor_id', protect.warning_device) ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">禁止浏览器指纹异常访问
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.device_visitor_status ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">单一设备访问次数限制<div class="fz12 text-grey">(规则为10分钟内访问次数：> 2)
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.country && protect.country.length > 0 ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">访问的国家
                        <div class="fz12 text-grey" v-if="protect.country_data && protect.country_data.length > 0">
                            (
                            {{ protect.country_type === 1 ? '允许' : '不允许' }}:
                            <template v-for="(item, index) in protect.country_data" :key="index">
                                {{ item.name }}{{ index < protect.country.length - 1 ? ',' : '' }} </template>
                                    )
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.language && protect.language.length > 0 ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">访问的系统语言
                        <div class="fz12 text-grey" v-if="protect.language_data && protect.language_data.length > 0">
                            (
                            {{ protect.language_type === 1 ? '允许' : '不允许' }}:
                            <template v-for="(item, index) in protect.language_data" :key="index">
                                {{ item.name }}{{ index < protect.language.length - 1 ? ',' : '' }} </template>
                                    )
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.system && protect.system.length > 0 ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">访问的操作系统
                        <div class="fz12 text-grey" v-if="protect.system_data && protect.system_data.length > 0">
                            (<template v-for="(item, index) in protect.system_data" :key="index">
                                {{ item.name }}{{ index < protect.system.length - 1 ? ',' : '' }} </template>
                                    )
                        </div>
                        <div v-else class="fz12 text-grey">(全部)</div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.ios_version > 0 ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">访问的IOS版本
                        <div class="fz12 text-grey" v-if="protect.ios_version">
                            允许大于版本 {{ protect.ios_version }} 的系统访问
                        </div>
                        <div class="fz12 text-grey" v-else>
                            允许大于所有版本 的系统访问
                        </div>
                    </div>
                </div>
                <div class="protect-item">
                    <div class="icon icon-wancheng-4 fz12" :class="[
        protect.android_version > 0 ? `text-green` : `text-grey`
    ]"></div>
                    <div class="ml10">访问的android版本
                        <div class="fz12 text-grey" v-if="protect.android_version">
                            允许大于版本 {{ protect.android_version }} 的系统访问
                        </div>
                        <div class="fz12 text-grey" v-else>
                            允许大于所有版本 的系统访问
                        </div>
                    </div>
                </div>
            </template>
        </a-popover>
    </template>
    <template v-else>
        <a-tooltip content="未开启防护状态">
            <div class="icon icon-wancheng-4" :class="`text-grey`"></div>
        </a-tooltip>
    </template>
</template>

<script lang="ts">
export default {
    name: "pageProtectIcon",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";


const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const props = withDefaults(
    defineProps<{
        protect?: any;
    }>(),
    {
        protect: {}
    }
);
</script>

<style scoped>
.protect-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}
</style>