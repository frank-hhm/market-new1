// // //当前环境测试dev
// export const BASE_URL='http://dev-pm3-new.irugf.com/'
// export const WS_URL='ws://dev-pm3-new.irugf.com:9501'
// export const updateUrl='http://dev-pm3-new.irugf.com/api/common.publics/appUpgrade'

//当前环境测试uat
// export const BASE_URL='http://www.clwft.cn/'
// export const WS_URL='ws://ws.clwft.cn:9501'
// export const updateUrl='http://api.clwft.cn/api/common.publics/appUpgrade'

//当前环境测试uat
export const getHost= () => {
	if(process.env.NODE_ENV === 'development'){
		return  'www.butchery.cc';
	}
	return window.location.hostname;
}
export const BASE_URL= () => {
	let t = window.location.protocol?window.location.protocol:"http:";
	if(process.env.NODE_ENV === 'development'){
		t = "https:"
	}
	return t + "//" +getHost();
}
export const WS_URL=() => {
	return (window.location.protocol == "https:"?("wss:" + "//"+ getHost() +'/ws'):("ws:" + "//"+ getHost() +'/ws'));
}
export const updateUrl='https://api.butchery.cc/api/common.publics/appUpgrade'

// //当前环境pro
// export const BASE_URL='http://www.hclnx.com/'
// export const WS_URL='ws://ws.hclnx.com'
// export const updateUrl='http://app.irugf.com/api/common.publics/appUpgrade'
export const bankList=[
    '工商银行', '农业银行', '中国银行', '建设银行', '交通银行', '中信银行', '光大银行', '华夏银行', '民生银行', '广发银行', '招商银行', '兴业银行', '浦发银行', '恒丰银行', '浙商银行', '徽商银行', '上海农商银行', '上海银行', '邮政储蓄银行', '平安银行', '北京银行', '云南省农村信用社海南省农村信用社广西农村信用社东莞农村商业银行', '福建省农村信用社安徽省农村信用社联合社浙江省农村信用社江苏省农村信用社联合社吉林农村信用社北京农村商业银行', '天津银行', '包商银行', '重庆银行', '广州银行', '昆仑银行', '广州农村商业银行', '浙江民泰商业银行', '厦门银行', '杭州银行', '汉口银行', '宁波银行', '东亚银行', '长沙银行', '华融湘江银行', '江西银行', '温州银行', '昆仑银行', '深圳农村商业银行'
]