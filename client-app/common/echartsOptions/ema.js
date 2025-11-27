//若Y=EMA(X，N)，则Y=［2*X+(N-1)*Y’］/(N+1)，其中Y’表示上一周期的Y值。
const calculateEMA=(data,dayCount,xiaoshu = 2)=>{
	let EMAList=[]
	for(let i=0;i<data.length;i++){
		if(i<dayCount-1){
			EMAList.push("-")
		}
		else{
			let newData=data.slice(i-dayCount+1,i+1)
			let EMA=exec_calcEMA(newData,dayCount)
			EMAList.push(parseFloat(EMA).toFixed(xiaoshu))
		}
	}
	return EMAList
}
const exec_calcEMA=(data,dayCount)=>{
	if(dayCount==1){
		return (2*parseFloat(data[dayCount-1][1]))/(dayCount+1)
	}
	return (2*parseFloat(data[dayCount-1][1])+(dayCount-1)*exec_calcEMA(data,dayCount-1))/(dayCount+1)
}
export default calculateEMA