// 账户
// 内部封装：计算单个订单的保证金
function _calcOrderMargin(order) {
	return parseFloat(order.fee) || 0
	// const price = parseFloat(order.buyprice);
	// const onumber = parseFloat(order.onumber); // 手数
	// const money = parseFloat(order.product_money); // 定价
	// const number = parseFloat(order.product_number); // 倍数
	// const leverage = 1; // 杠杆（可选）

	// // 保证金 = (买入价 * 手数 * 定价 * 倍数) / 杠杆
	// return ((price * onumber * money * number) / leverage);
}
export default {
	namespaced: true,
	state: {},
	getters: {
		getYingKui: (state, getters, rootState, rootGetters) => (order) => {
			const marketPrice = rootGetters['market/getMarketPrice'](order.pid);
			if (!marketPrice || isNaN(marketPrice)) return {
				amount: '0.00',
				sign: '',
				className: ''
			};
		
			const buyPrice = parseFloat(order.buyprice);
			const currentPrice = parseFloat(marketPrice);
			const onumber = parseFloat(order.onumber); // 手数
			const money = parseFloat(order.product_money); // 定价
			const number = parseFloat(order.product_number); // 倍数
			const ostyle = parseInt(order.ostyle.value); // 1=买跌 2=买涨
		
			// console.log(currentPrice - buyPrice, "buyPrice:" + buyPrice, "currentPrice:" + currentPrice,
			// 	"onumber:" + onumber, "money:" + money, "number:" + number, "ostyle:" + ostyle)
			let profit = (currentPrice - buyPrice) / number * money * onumber;
		
			if (ostyle === 1) {
				profit = (buyPrice - currentPrice) / number * money * onumber;
			}
			let className = '';
			let sign = '';
		
			if (profit > 0) {
				className = rootGetters['member/getUpColor'];
				sign = '+';
			} else if (profit < 0) {
				className = rootGetters['member/getDownColor'];
				sign = '';
			} else {
				className = '';
				sign = '';
			}
		
			return {
				amount:  profit.toFixed(2), // 只保留绝对值
				sign,
				className
			};
		},
		//可用
		getAvailableBalance: (state, getters, rootState, rootGetters) => {
			const balance = parseFloat(rootGetters['member/getBalance']) || 0;
			const marginUsed = parseFloat(rootGetters['wallet/getTotalMarginUsed']) || 0;
			const totalProfit = parseFloat(rootGetters['wallet/getTotalProfit']) || 0;
			return (balance - marginUsed + totalProfit).toFixed(2);
		},
		//净值
		getNetValue: (state, getters, rootState, rootGetters) => {
			const balance = parseFloat(rootGetters['member/getBalance']) || 0;
			const totalProfit = parseFloat(rootGetters['wallet/getTotalProfit']) || 0;
			return  (balance + totalProfit).toFixed(2);
		},
		//保证比例
		getMarginRatio: (state, getters, rootState, rootGetters) => {
			//保证比例 = 净值 / 占用的保证金 * 100
			const marginUsed = parseFloat(rootGetters['wallet/getTotalMarginUsed']) || 0;
			const netValue = parseFloat(rootGetters['wallet/getNetValue']) || 0;
			
			if (netValue === 0 || marginUsed === 0) return '0.00';
			return  ((netValue / marginUsed * 100) ).toFixed(2);
		},
		
		//占用的保证金
		getTotalMarginUsed: (state, getters, rootState, rootGetters) => {
			let order_hold = rootGetters['charge/getOrderHold']  || []
			return order_hold.reduce((sum, order) => {
				const margin = _calcOrderMargin(order); // 封装为内部函数
				return sum + margin;
			}, 0).toFixed(2);
		},
		getTotalProfit: (state, getters, rootState, rootGetters) => {
			let order_hold = rootGetters['charge/getOrderHold'] || []
			const totalProfit = order_hold.reduce((sum, order) => {
				const profit = parseFloat(getters.getYingKui(order).amount) || 0;
				return ( parseFloat(sum) + profit).toFixed(2);
			}, 0);

			// const totalFee = order_hold.reduce((sum, order) => {
			// 	const fee = parseFloat(order.sx_fee) || 0;
			// 	return (parseFloat(sum) + fee).toFixed(2);
			// }, 0);

			// return (parseFloat(totalProfit) - parseFloat(totalFee)).toFixed(2);

			return parseFloat(totalProfit).toFixed(2);
		}
	},
	mutations: {}
}