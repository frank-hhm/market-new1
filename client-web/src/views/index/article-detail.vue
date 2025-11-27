<template>
  <div class="wrap-row">
        <div class="article-detail">
            <div class="article-top">
                <img class="article-cover" :src="articleDetail.cover" alt="" />
                <div class="article-info">
                     <div class="article-title">{{articleDetail.title}}</div>
                    <div class="article-time">{{articleDetail.create_time}}</div>
                </div>
            </div>
            <div class="article-content" v-html="articleDetail.content"></div>
        </div>
    </div>
    <commonFooter></commonFooter>
</template>
<script lang="ts" setup>
import { getArticleDetailApi } from "@/api/common";
import { useSetting } from "@/hooks/useSetting";
import router from "@/router";
import { PageLimitType, Result, ResultError } from "@/types";
import { getCurrentInstance, onMounted, ref } from "vue";
import commonFooter from "@/views/common/common-footer.vue"

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const initLoading = ref<boolean>(false)

const operationId = ref<number>(0);

const articleDetail = ref<any>({});
const toInit = () => {
    console.log(operationId.value)
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getArticleDetailApi({
        id: operationId.value
    }).then((res: any) => {
        articleDetail.value = res.data
        initLoading.value = false;
    })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
}

onMounted(() => {
    operationId.value = Number(router.currentRoute.value.query.id);
    toInit();
})


</script>


<style scoped>
.article-top{
    display: flex;
    align-items: center;
}
.article-info{
    margin-left: 20px;
    width: calc(100% - 220px);
}
.article-detail{
    margin-top: 30px;
}
.article-title{
    font-size: 32px;
    font-weight: bold;
}
.article-time{
    margin-top: 10px;
}
.article-cover{
    width: 200px;
}
.article-content{
    margin-top: 50px;
    margin-bottom: 20px;
    font-size: 16px;
    line-height: 24px;
    min-height: 460px;
}
.article-detail{
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    margin-bottom: 20px;
}
@media screen and (max-width:699px) {
    .article-top{
        display: block;
    }
    .article-cover{
        width: 100%;
    }
    .article-info{
        margin-left: 0;
        width: 100%;
        margin-top: 40px;
    }
    .article-title{
        font-size: 24px;
    }

}
</style>