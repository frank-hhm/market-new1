// 　1、当前交易日的最高价与最低价间的波幅
//     2、前一交易日收盘价与当个交易日最高价间的波幅
// 　3、前一交易日收盘价与当个交易日最低价间的波幅
const calc_atr=(value,dayCount)=>{
	let atrList=[]
	for(let i=0;i<value.length;i++){
		if(i<dayCount||!value[i][0]||!value[i][1]||!value[i][2]||!value[i][3]){
			atrList.push("-")
		}
		else{
			let sum=0
			//[开盘值, 收盘值, 最低值, 最高值]
			for(let j=i-dayCount+1;j<i;j++){
				let tr=Math.max(Math.abs(parseFloat(value[j][3])-parseFloat(value[j][2])),
				Math.abs(parseFloat(value[j-1][1])-parseFloat(value[j][3])),
				Math.abs(parseFloat(value[j-1][1])-parseFloat(value[j][2])))
				sum+=tr
			}
			let atr=sum/dayCount
			atrList.push(atr)
		}
	}
	return atrList 
}
export default calc_atr