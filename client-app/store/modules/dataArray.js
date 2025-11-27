export default {
	namespaced: true,
	state: {},
	getters: {
		inArray: (state, getters, rootState, rootGetters) => (search,array) => {
			for (var i in array) {
				if (array[i] == search) {
					return true;
				}
			}
			return false;
		}
	},
	mutations: {}
}