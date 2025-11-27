// Hn=N天中的最高价；
// Ln=N天中的最低价；
// C=今天的收盘价；
// Q=HY-C；
// R=HY-LY；
// 其中：公式中N日为选设参数，短期的威廉指标可设为6日或9日，中期为14日或20日，长期为70日。
// 故威廉指标WR=Q/R*100
const calc_WR=(value,dayCount)=>{
	let wrList=[]
	for(let i=0;i<value.length;i++){
		if(i<dayCount-1||!value[i][0]||!value[i][1]||!value[i][2]||!value[i][3]){
			wrList.push("-")
		}
		else{
			let list=value.slice(i-dayCount+1,i)
			//[开盘值, 收盘值, 最低值, 最高值]
			let maxValue=Math.max(...list.map(item=>parseFloat(item[3])))
			let minValue=Math.min(...list.map(item=>parseFloat(item[2])))
			let wr=(maxValue-parseFloat(value[i][1]))/(maxValue-minValue)
			wrList.push(wr)
		}
	}
	return wrList
}
export default calc_WR