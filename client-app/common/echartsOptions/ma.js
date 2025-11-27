//均线计算
 	const calculateMA = (dayCount, data,xiaoshu = 2) => {
 		var result = [];
 		for (var i = 0, len = data.length; i < len; i++) {
 			if (i < dayCount||!data[i][0]||!data[i][1]||!data[i][2]||!data[i][3]) {
 				result.push("-");
 				continue;
 			}
 			var sum = 0;
 			for (var j = 0; j < dayCount; j++) {
 				sum += parseFloat(data[i - j][1]);
 			}
 			result.push(parseFloat(sum / dayCount).toFixed(xiaoshu));
 		}
 		return result;
 	}
 	export default calculateMA
