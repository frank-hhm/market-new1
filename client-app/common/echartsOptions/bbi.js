import calculateMA from "./ma.js"

const calc_BBI = (data,xiaoshu = 2) => {
	//BBI=(3日均价+6日均价+12日均价+24日均价)/4
	let ma3 = calculateMA(3, data)
	let ma6 = calculateMA(6, data)
	let ma12 = calculateMA(12, data)
	let ma24 = calculateMA(24, data)
	let bbi = ma24.map((item, index) => {
		if (ma24[index] == "-" || ma12[index] == "-" || ma6[index] == "-" || ma3[index] == "-") {
			return "-"
		} else {
			return parseFloat((parseFloat(ma3[index]) + parseFloat(ma6[index]) + parseFloat(ma12[index]) +
				parseFloat(ma24[index])) / 4).toFixed(xiaoshu)
		}
	})
	return bbi
}

export default calc_BBI
