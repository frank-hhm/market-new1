<template>
  <div class="">
    <div class="wrap-row">
      <div class="main-box">
        <div class="main-box-title">隐私条款</div>
        <div class="main-box-desc">
          <p>
            香港盛世景有限公司以下简称“盛世景”私隐政策是根据个人资料(私隐)条例保护客户权益，便利于客户开设和维持交易户口，提供融资和金融顾问服务。
          </p>

          <p>
            盛世景忠诚地为客户所提供的个人资料，作出一个保密的监控。除了得到法例批淮，盛世景绝对不会把任何非公开性的资料予任何人士。
          </p>

          <p>
            当客户在盛世景开立或维持一个交易账户时，所提供的个人资料，只会作公司的内部商业用途，例如评估客户在财务上的需要，处理客户的交易以及其他要求，提供有关产品与服务，提供一般交易上的服务及按监管程序需要确认客户身份。
          </p>

          <p>
            盛世景需要客户提供以便运作的资料包括∶有关盛世景的账户申请表格以及其他表格上所提供的个人资料，例如姓名、地址、出生日期、身份证号码、银行资料等有关客户和盛世景以及其附属公司的交易资料有关客户调查报告公司的资料，有关核实客户身份的资料，例如政府档,
            护照或驾驶执照。
          </p>

          <p>
            盛世景只会把客户所提供的资料，有限地让和客户接触雇员查阅，以便提供相关的客户服务和产品介绍。盛世景也只授权与新账户申请时和信誉检察人员，才可以透过电子系统查阅相关资讯。
          </p>

          <p>
            这些程序上的要求，都是为了保障客户的非公开性资料受到公开，保护客户的私隐。盛世景也不会把客户的姓名和个人资料，销售或租借与任何人士。
          </p>
        </div>
      </div>
    </div>
  </div>
    <commonFooter></commonFooter>
</template>

<script setup lang="ts">
import { getCurrentInstance, ref, onMounted, watch } from "vue";
import commonFooter from "@/views/common/common-footer.vue"

</script>

<style scoped>
.main-box {
  margin-top: 20px;
  margin-bottom: 20px;
  padding: 60px 20px;
  background: #fff;
  min-height: 300px;
}
.main-box-title {
  font-size: var(--base-title-size);
  font-weight: bold;
  text-align: center;
}
.main-box-desc {
  margin-top: 40px;
  text-align: left;
  font-size: 16px;
  color: var(--base-text-grey);
  line-height: 32px;
}
.main-box-desc p{
    margin-bottom: 10px;
}
</style>