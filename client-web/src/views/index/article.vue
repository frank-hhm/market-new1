<template>
  <div class="wrap-row">
    <div class="article-list" v-loading="initLoading">
        <div class="article-item" v-for="(item,index) in lists" :key="index" @click="toDetail(item.id)">
            <div class="article-item-left">
                <img class="article-item-left-img" :src="item.cover" />
            </div>
            <div class="article-item-right">
                <div class="article-item-right-title">{{ item.title }}</div>
                    <div class="article-item-right-time">{{ item.create_time }}</div>
                  <div class="article-item-right-bottom">
                      <div class="article-item-right-btn">查看详细 </div>
                  </div>
            </div> 
        </div>
    </div>
    <div class="mt20 mb40 flex end">
      <page :listPage="listPage" @change="pageChange"></page>
    </div>
  </div>
  <commonFooter></commonFooter>
</template>
<script lang="ts" setup>
import { getArticleListApi } from "@/api/common";
import { useSetting } from "@/hooks/useSetting";
import router from "@/router";
import { PageLimitType, Result, ResultError } from "@/types";
import { getCurrentInstance, onMounted, ref } from "vue";
import commonFooter from "@/views/common/common-footer.vue"

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const toInit = () => {
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  initLoading.value = true;
  getArticleListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      setTimeout(() => {
        initLoading.value = false;
      }, 300);
    })
    .catch((error: ResultError) => {
      $utils.errorMsg(error);
    });
};

const listPage = ref<any>({
  total: 0,
  limit: useSetting().PageLimit.value,
  page: 1,
});

const pageChange = (res: PageLimitType) => {
  listPage.value = res;
  toInit();
};
onMounted(() => {
  toInit();
});

const toDetail = (id: number) => {
  router.push({
    path: "/article/detail",
    query: {
      id: id,
    },
  });
};
</script>

<style scoped>
.article-list{
  margin-top: 20px;
}
.article-item{
  display: flex;
  background: #fff;
  padding: 10px;
  margin-bottom: 20px;
    border-radius: 5px;
    cursor: pointer;
    user-select: none;
}
.article-item-left-img{
    width: 160px;
    height: 90px;
    border-radius: 5px;
}

.article-item-right{
  width: calc(100% - 200px);
  margin-left: 20px;
}
.article-item-right-title{
  line-height: 24px;
  height: 48px;
  font-size: 16px;
  font-weight: bold;
}
.article-item-right-time{
  color: var(--base-text-grey);
}
.article-item-right-bottom{
  display: flex;
  justify-content: flex-end;
}
.article-item-right-btn{
  cursor: pointer;
  background: var(--base-default-bg);
  color: #fff;
  border-radius: 4px;
  padding: 5px 10px;
  user-select: none;
}


@media screen and (max-width:599px) {
  .article-item-left-img{
    height: auto;
    width: 100%;
  }
    .article-item{
      display: block;
      padding-bottom: 40px;
    }
    .article-item-right{
      margin-top: 30px;
      margin-left: 0;
      width: 100%;
    }
    .article-item-right-bottom{
      justify-content: center;
      margin-top: 20px;
    }
	
}
</style>