<template>
    <a-popover title="防护">
        <div class="protect-item" :class="[
            check_status === 1 ? `text-green` : `text-red`
        ]">
            <span class="protect-icon icon icon-wancheng-4"></span>
            <span class="fz12 ml5">{{ check_status? '通过' : '拦截' }}</span>
        </div>
        <template #content>
            <div class="protect-item" v-if="check_data.device_error === 1">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            `text-red`
        ]"></div>
                <div class="ml10">异常设备(自动屏蔽)</div>
            </div>
            <div class="protect-item" v-if="check_data.source_param === 1">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[`text-red`
        ]"></div>
                <div class="ml10">渠道参数异常(自动屏蔽)
                </div>
            </div>
            <!-- <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.client_ip === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止动态IP访问<div class="fz12 text-grey">(浏览器获取与服务器获取的客户端IP不一致)</div>
                </div>
            </div> -->
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.black_ip === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止高风险网络访问<div class="fz12 text-grey">(近期有滥用行为,黑名单)
                    </div>
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.cloud_ip === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止可能来自云服务器的访问<div class="fz12 text-grey">(如：腾讯云、阿里云等)
                    </div>
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.is_bot === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止机器人访问<div class="fz12 text-grey">(包含机器人、爬虫、工具类请求)
                    </div>
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.program === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止打开编程辅助工具浏览器访问
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.timezone === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止时区异常的设备访问
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.visitor_id === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">禁止浏览器指纹异常访问
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.single_limit === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">单一设备访问次数限制<div class="fz12 text-grey">(规则为10分钟内访问次数：> 2)
                    </div>
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.country === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">访问的国家</div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.language === 1 || check_data?.language_agent === 1 ? `text-red` : `text-green`
        ]"></div>
                <div class="ml10">访问的系统语言
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.system === 0 ? `text-green` : `text-red`
        ]"></div>
                <div class="ml10">访问的操作系统
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.ios_version === 1 || check_data?.ios_version === 1 ? `text-red` : `text-green`
        ]"></div>
                <div class="ml10">访问的IOS版本
                </div>
            </div>
            <div class="protect-item">
                <div class="protect-icon icon icon-wancheng-4 fz12" :class="[
            check_data.android_version === 1 || check_data?.android_version === 1 ? `text-red` : `text-green`
        ]"></div>
                <div class="ml10">访问的android版本
                </div>
            </div>
        </template>
    </a-popover>
</template>

<script lang="ts">
export default {
    name: "pageProtectError",
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
        check_data?: any;
        check_status?: boolean | number | any;
    }>(),
    {
        check_data: {},
        check_status: false
    }
);
</script>

<style scoped>
.protect-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}
.protect-icon{
    font-size: 12px;
}
</style>