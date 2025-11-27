// 行情
import Vue from 'vue';
export default {
	namespaced: true,
	state: {
		markets: [],
		marketPrices: {},
		marketId: 'all',
		sector: [],
		marketStatus: false,
	},
	getters: {
		getMarketStatus(state) {
			return state.marketStatus || false
		},
		getMarkets(state) {
			return state.markets || []
		},
		getSector(state) {
			return state.sector || []
		},
		getMarketById: (state) => (id) => {
			return state.markets.find(item => item.id == id);
		},
		getMarketId(state) {
			return state.marketId || "all";
		},
		getMarketPrice: (state,getters) => (id) => {
			return Number(state.marketPrices[id]) || getters.getMarketDefaultPrice(id);
		},
		getMarketDefaultPrice: (state) => (id) => {
			const marketItem = state.markets.find(item => item.id == id);
			if(!marketItem){
				return 0;
			}
			return marketItem.price || 0
		},
		getPriceChange: (state) => (id) => {
			const market = state.markets.find(item => item.id === id);
			if (!market || !market.price || !market.open_price) return {
				changeValue: 0,
				changeNum: 0
			};

			const price = parseFloat(market.price);
			const openPrice = parseFloat(market.open_price);

			if (isNaN(price) || isNaN(openPrice)) return {
				changeValue: 0,
				changeNum: 0
			};

			let liuChu = openPrice || price;
			if (liuChu === 0) return {
				changeValue: 0,
				changeNum: 0
			};

			let _price = price - openPrice;
			let changeZhi = _price / liuChu;

			if (changeZhi < 0) {
				let changeJuedui = Math.ceil(Math.abs(changeZhi) * 10000) / 10000;
				changeZhi = parseFloat(changeJuedui * -1);
			}

			return {
				changeValue: parseFloat((changeZhi * 100).toFixed(2)),
				changeNum: _price.toFixed(2)
			};
		}
	},
	mutations: {
		setMarketStatus(state, data) {
			state.marketStatus = data
		},
		setMarkets(state, data) {
			state.markets = data
		},
		setSector(state, data) {
			state.sector = data
		},
		setMarketId(state, data) {
			state.marketId = data
		},
		setMarketItem(state, market) {
			// 更新价格
			Vue.set(state.marketPrices, market.id, market.p);
			const index = state.markets.findIndex(item => item.id === market.id);
			if (index !== -1) {
				const item = state.markets[index];
				// 判断涨跌状态
				const up_status = parseFloat(market.p) > parseFloat(item.price);
				Vue.set(item, 'up_status', up_status);
				// 保存旧价
				Vue.set(item, 'old_price', item.price);
				// 设置新价
				Vue.set(item, 'price', market.p);
			}
		}
	}
}