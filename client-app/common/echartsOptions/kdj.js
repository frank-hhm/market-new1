const calc_KDJ=(value)=>{
	//[开盘值, 收盘值, 最低值, 最高值]
	let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
	let kdjList=exec_KDJ(data)
	return kdjList
}


//　RSV=（今日收盘价一最近9天的最低价）÷（最近9天的最高价一最近9天的最低价）×100
const exec_KDJ=(value)=>{
	let rsvList=[]
	let kList=[]
	let dList=[]
	let jList=[]
	for(let i=0;i<value.length;i++){
		if(i<8){
			rsvList.push("-")
			kList.push("-")
			dList.push("-")
			jList.push("-")
			continue;
		}
		else{
			let lowestPrice_9=calc_lowest(value,i)
			let highestPrice_9=calc_highest(value,i)
			if(highestPrice_9-lowestPrice_9==0||!value[i][1]){
				rsvList.push("-")
				kList.push("-")
				dList.push("-")
				jList.push("-")
				continue;
			}
			let rsv=(parseFloat(value[i][1])-lowestPrice_9)/(highestPrice_9-lowestPrice_9)*100
			rsvList.push(rsv)
			
			// 当日K值=（2/3×前一日K值）+（1/3×当日RSV值）
			// 当日D值=（2/3×前一日D值）+（1/3×当日K值）
			// 若无前一日K值与D值,则可分别用50来代替。
			// J指标的计算公式为：J=（3×当日K值）一（2×当日D值）
			let lastK=kList[i-1]=="-"?50:kList[i-1]
			let lastD=dList[i-1]=="-"?50:dList[i-1]
			let k=(2/3*lastK)+(1/3*rsv)
			let d=(2/3*lastD)+(1/3*k)
			
			let j=(3*k)-(2*d)
			kList.push(k)
			dList.push(d)
			jList.push(j)
		}
	}
	return [kList,dList,jList]
	//最近9天的最低价
	function calc_lowest(value,i){
		let lowestPrice=0
		for(let j=i-8;j<=i;j++){
			if(lowestPrice==0){
				lowestPrice=parseFloat(value[j][2]) 
			}
			else if(parseFloat(value[j][2])<lowestPrice){
				lowestPrice=value[j][2]
			}
		}
		return lowestPrice
	}
	//最近9天的最高价
	function calc_highest(value,i){
		let highestPrice=0
		for(let j=i-8;j<=i;j++){
			if(highestPrice==0){
				highestPrice=parseFloat(value[j][3])
			}
			else if(parseFloat(value[j][3])>highestPrice){
				highestPrice=value[j][3]
			}
		}
		return highestPrice
	}
}
export default calc_KDJ