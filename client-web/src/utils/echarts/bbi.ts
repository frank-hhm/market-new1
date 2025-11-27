import calculateMA from "./ma"

const calc_BBI = (data:any,decimal = 2) => {
	//BBI=(3日均价+6日均价+12日均价+24日均价)/4
	let ma3 = calculateMA(data,3,decimal )
	let ma6 = calculateMA(data,6, decimal)
	let ma12 = calculateMA(data,12, decimal)
	let ma24 = calculateMA(data,24, decimal)
	let bbi = ma24.map((item:any, index:number) => {
		if (ma24[index] == "-" || ma12[index] == "-" || ma6[index] == "-" || ma3[index] == "-") {
			return "-"
		} else {
			return parseFloat(String((parseFloat(ma3[index]) + parseFloat(ma6[index]) + parseFloat(ma12[index]) + parseFloat(ma24[index])) / 4)).toFixed(decimal)
		}
	})
	return bbi
}
export default calc_BBI
