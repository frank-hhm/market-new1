const install = (Vue, vm) => {
	// ---------------------------------publics
	const initConfigApi = (data = {}) => vm.$u.get("/api/common.publics/configData")
	const getVcodeApi = (data = {}) => vm.$u.post("/api/common.publics/sendSms", data)
	const getNewVcodeApi = (data = {}) => vm.$u.post("/api/common.publics/sendCode", data)
	const getPaymentSelect = (data = {}) => vm.$u.get("/api/common.publics/getPaymentSelect")
	const getPaymentConfig = (data = {}) => vm.$u.get("/api/common.publics/getPaymentConfig", data)
	const getAgreementApi = (data = {}) => vm.$u.get("/api/common.publics/agreement", data)
	const getProHome = (data = {}) => vm.$u.get("/api/common.publics/getProHome")
	const getHomeApi = (data = {}) => vm.$u.get("/api/common.publics/getHome")
	const getDownloadApp = (data = {}) => vm.$u.get("/api/common.publics/getDownloadApp")

	// ---------------------------------login
	const loginApi = (data = {}) => vm.$u.post("/api/login/index", data)
	const registerApi = (data = {}) => vm.$u.post("/api/login/register", data)

	// ---------------------------------Product
	const getProductSelectApi = (data = {}) => vm.$u.get("/api/product/getProductSelect")
	const getProductDetailApi = (data = {}) => vm.$u.get("/api/product/getProductDetail", data)
	const getProductParamsDetail = (data = {}) => vm.$u.get("/api/product/getProductParamsDetail", data)
	const getProductConfigApi = (data = {}) => vm.$u.get("/api/product/getProductConfig")
	const getKDataApi = (data = {}) => vm.$u.get("/api/marketKLine/kLine", data)

	// ---------------------------------member
	const getMemberDetailApi = (data = {}) => vm.$u.get("/api/member.member/detail", data)
	const bindMemberRealApi = (data = {}) => vm.$u.post("/api/member.member/bindReal", data)
	const updateMemberApi = (data = {}) => vm.$u.put("/api/member.member/update", data)
	const bindBankCardApi = (data = {}) => vm.$u.put("/api/member.member/bindBankCard", data)
	const bindUsdtApi = (data = {}) => vm.$u.put("/api/member.member/bindUsdtApi", data)
	const bindAlipayApi = (data = {}) => vm.$u.put("/api/member.member/bindAlipayApi", data)
	
	const updateMemberPasswordApi = (data = {}) => vm.$u.put("/api/member.member/updatePass", data)
	const resetPasswordApi = (data = {}) => vm.$u.put("/api/member.member/resetPass", data)
	const loginqieApi = (data = {}) => vm.$u.get("/api/member.member/loginqie")
	const rechargeTransferApi = (data = {}) => vm.$u.post("/api/member.member/recharge", data)
	const usdtpayTransferApi = (data = {}) => vm.$u.post("/api/member.member/usdtpayTransferApi", data)
	const turnTableMemberApi = (data = {}) => vm.$u.post("/api/member.member/turnTable", data)
	const getMemberTurnTableCountApi = (data = {}) => vm.$u.get("/api/member.member/getMemberTurnTableCount")
	const getTurnTableRecordListApi = (data = {}) => vm.$u.get("/api/activity.turntable_record/getList", data)
	const withdrawApi = (data = {}) => vm.$u.post("/api/finance.memberWithdrawal/create", data)
	const getWaterApi = (data = {}) => vm.$u.get("/api/finance.FinanceLog/list", data)
	const getWater2Api = (data = {}) => vm.$u.get("/api/finance.FinanceLog/getWaterList", data)
	const getCommissionWaterList = (data = {}) => vm.$u.get("/api/finance.FinanceLog/getCommissionWaterList", data)
	
	const getMemberCoinSort = (data = {}) => vm.$u.get("/api/member.coin/getMemberCoinSort", data)
	const getCommissionDetail = (data = {}) => vm.$u.get("/api/member.member/getCommissionDetail", data)
	const addOptionalProductApi = (data = {}) => vm.$u.post("/api/product/addOptional", data)
	const delOptionalProductApi = (data = {}) => vm.$u.put("/api/product/delOptional", data)
	const getTeamListApi = (data = {}) => vm.$u.get("/api/member.member/getTeamList", data)

	// ---------------------------------order
	const createOrderApi = (data = {}) => vm.$u.post("/api/order/create", data)
	const holdModifyApi = (data = {}) => vm.$u.post("/api/order/upYs", data)
	const getOrderChiCanSelectApi = (data = {}) => vm.$u.get("/api/order/getOrderChiCanSelect", data)
	const sellApi = (data = {}) => vm.$u.post("/api/order/sell", data)
	const getTodayComplateOrderSelectApi = (data = {}) => vm.$u.get("/api/order/getTodayComplateOrderSelect", data)
	const historyRecordApi = (data = {}) => vm.$u.post("/api/order/chengList", data)
	const getOrderDetailApi = (data = {}) => vm.$u.get("/api/order/getOrderDetail", data)
	const getOrderRobotDetailApi = (data = {}) => vm.$u.get("/api/order.robot/getOrderDetail", data)
	const cancelOrderRobotApi = (data = {}) => vm.$u.post("/api/order.robot/cancel", data)
	const penddingOrderApi = (data = {}) => vm.$u.post("/api/order.robot/createEntrust", data)
	const peddingModifyApi = (data = {}) => vm.$u.post("/api/order.robot/upYsWei", data)

	const getArticleListApi = (data = {}) => vm.$u.get("/api/article/list", data)
	const getArticleDetailApi = (data = {}) => vm.$u.get("/api/article/detail", data)


	const getNoticeListApi = (data = {}) => vm.$u.get("/api/notice/list", data)
	const getNoticeDetailApi = (data = {}) => vm.$u.get("/api/notice/detail", data)

	const getFollowPersonListApi = (data = {}) => vm.$u.get("/api/follow.person/getList", data)
	const getFollowPersonOrderListApi = (data = {}) => vm.$u.get("/api/follow.order/getList", data)
	const getFollowPersonDetailApi = (data = {}) => vm.$u.get("/api/follow.person/getDetail", data)
	
	const getFollowPersonTradingListApi = (data = {}) => vm.$u.get("/api/follow.person/getTradingList", data)
	const createFollowPersonOrderApi = (data = {}) => vm.$u.post("/api/follow.person/createOrder", data)
	const getFollowWaterList = (data = {}) => vm.$u.get("/api/finance.FinanceLog/getFollowWaterList", data)
	const closeFollowPersonOrderApi = (data = {}) => vm.$u.post("/api/follow.person/endStatus", data)
	const createSubscribeApi = (data = {}) => vm.$u.post("/api/member.member/createSubscribe", data)
	
	Vue.prototype.$u.api = {
		initConfigApi,
		getVcodeApi,
		getNewVcodeApi,
		getPaymentSelect,
		getPaymentConfig,
		getAgreementApi,
		getProHome,
		getHomeApi,
		getDownloadApp,


		loginApi,
		registerApi,

		getProductConfigApi,
		getProductSelectApi,
		getProductDetailApi,
		getKDataApi,
		getProductParamsDetail,

		getMemberDetailApi,
		bindMemberRealApi,
		updateMemberApi,
		bindBankCardApi,
		bindUsdtApi,
		bindAlipayApi,
		updateMemberPasswordApi,
		resetPasswordApi,
		loginqieApi,
		rechargeTransferApi,
		usdtpayTransferApi,
		turnTableMemberApi,
		getMemberTurnTableCountApi,
		getTurnTableRecordListApi,
		withdrawApi,
		getWaterApi,
		getWater2Api,
		getCommissionWaterList,
		getMemberCoinSort,
		getCommissionDetail,
		addOptionalProductApi,
		delOptionalProductApi,
		getTeamListApi,

		createOrderApi,
		peddingModifyApi,
		penddingOrderApi,
		holdModifyApi,
		sellApi,
		getTodayComplateOrderSelectApi,
		historyRecordApi,
		getOrderDetailApi,
		getArticleListApi,
		getArticleDetailApi,
		getOrderRobotDetailApi,
		cancelOrderRobotApi,

		getNoticeDetailApi,
		getNoticeListApi,

		getFollowPersonListApi,
		getFollowPersonDetailApi,
		getFollowPersonTradingListApi,
		createFollowPersonOrderApi,
		getFollowPersonOrderListApi,
		getFollowWaterList,
		closeFollowPersonOrderApi,
		createSubscribeApi
	}
}

export default {
	install
}