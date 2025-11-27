<template>
  <div class="">
    <div class="wrap-row">
      <div class="main-box">
        <div class="main-box-title">免责声明</div>
        <div class="main-box-desc">
            <div class="desc-item">（一）投资有风险，交易需谨慎</div>
          <div class="desc-text">
            任何一支证券或金融产品的历史数据都不能保证其未来的表现及回报，虽然投资多样化可以帮助分散风险，但是并不能在市场低迷时确保您获益或防止您损失；投资证券或金融产品总会存在潜在的亏损可能，投资者在进行投资前需考虑自身的投资目标及风险承受能力。
          </div>

          <div class="desc-text">
            即使按照建议书进行交易，期货有限公司的产品和服务也可能导致损失和利润，特别是杠杆产品的交易，例如但不限于外汇，衍生品和商品，可能非常具有投机性，损失和利润可能会剧烈而迅速地波动。
          </div>

            <div class="desc-item">（二）市场风险和网上交易</div>
          <div class="desc-text">
            期货交易涉及相当大的风险，并非适合每个人。请参照客户协议书了解风险的详细介绍。不论网上交易多么方便或有效率，这并不降低期货交易的风险。
            </div>

            <div class="desc-item">（三）账户安全，密码保护</div>
          <div class="desc-text">
            客户明白并确认客户的交易密码须绝对保密，客户是交易密码的唯一授权用户。同时，客户同意就任何利用其交易密码而向盛世景发出的交易指令承担全部责任(不论是否由客户授权并经盛世景批准)。交易商不须对客户因执行交易指令而引致的损失负责。若客户将交易密码透漏给第三者，不论密码透漏是蓄意的、非故意的或错误的，客户必须对所有交易、损失、费用及支出负责，而盛世景无须为任何经核实密码后执行的交易所引致的损失、费用及支出负责。
          </div>

            <div class="desc-item">（四）本平台无带单服务</div>
          <div class="desc-text">
            本公司不提供任何带单群或微信带单服务，市面上涌现的任何组织形式的带单，本公司概不承认，若用户因此造成经济损失本公司不接受、不承担任何赔偿责任。
          </div>

            <div class="desc-item">（五）市场信息，仅供参考</div>
          <div class="desc-text">
            本公司以及其控股公司或任何附属公司将竭力确保所提供信息的可靠性和准确性，但不能保证其绝对可靠和准确，本公司的行情价格、图、评论和购买或者其他信息应该谨慎作为仅参考使用。
          </div>
          <div class="desc-text">
            本公司有权于任意时间修改或变更本声明内容，请您随时注意。您于任何修改或变更后继续使用本产品时，都将视为您已阅读、了解并接受该等修改或变更。


          </div>
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
  line-height: 32px;
}
.desc-item .desc-title{
    margin-bottom: 30px;
}
.main-box-desc .desc-text{
  color: var(--base-text-grey);
    margin-bottom: 10px;
}
</style>