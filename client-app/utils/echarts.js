// const echarts = await import('@/static/echarts.js');
// export default {
// 	mounted() {
// 		// 此时 echarts 可以直接使用
// 		this.initEcharts();
// 	},
// 	methods: {
// 		initEcharts() {
// 			// 在这里使用 echarts 对象
// 		},
// 	},
// };
// export const initEchartsScript = (script,callback) => {
// 	// 动态引入较大类库避免影响页面展示
// 	// view 层的页面运行在 www 根目录，其相对路径相对于 www 计算
// 	const script = document.createElement('script')
// 	script.src = 'static/echarts.js'
// 	script.onload = this.initEcharts.bind(this)
//     if(callback && typeof callback == 'function'){
//         script.onload = callback.bind(this)
//     }
// 	document.head.appendChild(script)
//     if(callback && typeof callback == 'function'){
//         callback()
//     }
// }