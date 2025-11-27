// 1.初始价（TYP）=（当日最高价+当日最低价+当日收盘价）/3
// 2.HH=N日内最高价的最高值
// LL=N日内最低价的最低值
// 初级压力线（WEKR）=TYP+(TYP-LL)
// 中级压力线（MIDR）=TYP+(HH-LL)
// 强力压力线（STOR）=2*HH-LL

// 初级支撑线（WEKS）=TYP-(HH-TYP)
// 中级支撑线（MIDS）=TYP-(HH-LL)
// 强力支撑线（STOS）=2*LL-HH
const calc_MIKE=(value,dayCount,xiaoshu = 2)=>{
	let HHList=[]
	let LLList=[]
	
	let WEKRList=[]
	let MIDRList=[]
	let STORList=[]
	
	let WEKSList=[]
	let MIDSList=[]
	let STOSList=[]
	for(let i=0;i<value.length;i++){
		if(i<dayCount-1||!value[i][0]||!value[i][1]||!value[i][2]||!value[i][3]){
			WEKRList.push("-")
			MIDRList.push("-")
			STORList.push("-")
			
			WEKSList.push("-")
			MIDSList.push("-")
			STOSList.push("-")
		}
		else{
			let list=value.slice(i-dayCount+1,i+1)
			let HH=calc_highest(list)
			let LL=calc_lowest(list)
			
			//[开盘值, 收盘值, 最低值, 最高值]
			const TYP=(parseFloat(value[i][3])+parseFloat(value[i][2])+parseFloat(value[i][1]))/3
			let WEKR=TYP+(TYP-LL)
			let MIDR=TYP+(HH-LL)
			let STOR=2*HH-LL
			
			let WEKS=TYP-(HH-TYP)
			let MIDS=TYP-(HH-LL)
			let STOS=2*LL-HH
			
			WEKRList.push(parseFloat(WEKR).toFixed(xiaoshu))
			MIDRList.push(parseFloat(MIDR).toFixed(xiaoshu))
			STORList.push(parseFloat(STOR).toFixed(xiaoshu))
			
			WEKSList.push(parseFloat(WEKS).toFixed(xiaoshu))
			MIDSList.push(parseFloat(MIDS).toFixed(xiaoshu))
			STOSList.push(parseFloat(STOS).toFixed(xiaoshu))
		}	
	}
	return [WEKRList,MIDRList,STORList,WEKSList,MIDSList,STOSList]
}
const calc_highest=(value)=>{
	let HH=0
	for(let i=0;i<value.length;i++){
		if(HH==0){
			HH=parseFloat(value[i])
		}
		else if(parseFloat(value[i])>HH){
			HH=parseFloat(value[i])
		}
	}
	return HH
}
const calc_lowest=(value)=>{
	let LL=0
	for(let i=0;i<value.length;i++){
		if(LL==0){
			LL=parseFloat(value[i])
		}
		else if(parseFloat(value[i])<LL){
			LL=parseFloat(value[i])
		}
	}
	return LL
}
export default calc_MIKE