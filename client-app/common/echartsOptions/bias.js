
// 　　1.BIAS=(收盘价-收盘价的N日简单平均)/收盘价的N日简单平均*100

// 　　2.BIAS指标有三条指标线，N的参数一般设置为6日、12日、24日。

const calc_BIAS=(value,dayCount)=>{
		let biasList=[]
		//[开盘值, 收盘值, 最低值, 最高值]
		for(let i=0;i<value.length;i++){
			if(i<dayCount-1||!value[i][0]||!value[i][1]||!value[i][2]||!value[i][3]){
				biasList.push("-")
			}
			else{
				let sum=0
				for(let j=i-dayCount+1;j<i;j++){
					sum+=parseFloat(value[j][1])
				}
				let bias=(parseFloat(value[i][1])-sum/dayCount)/(sum/dayCount)*100
				biasList.push(bias)
			}
		}
		return biasList
}
export default calc_BIAS