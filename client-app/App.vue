<script>
	import {
		websocketCallbackFunction
	} from '@/websocket/callback-function.js'
	import {
		createWebsocket
	} from '@/websocket/index.js'
	import {
		initDetail
	} from "@/utils/initData.js"

	import {
		updateUrl
	} from "@/common/config.js"
	import {
		mapGetters,
		mapState
	} from "vuex"

	export default {
		onLaunch: function() {
			let t = this;
			t.$u.api.initConfigApi().then((res) => {
				t.$store.commit('app/setConfig', res.data)
			})

			if (uni.getStorageSync("token")) {
				initDetail()
			}

			t.$u.api.getProductConfigApi().then((res) => {
				t.$store.commit('market/setMarkets', res.data.list || [])
				t.$store.commit('market/setSector', res.data.sector || [])
			})
			createWebsocket()
			uni.$on("websocketMessage", websocketCallbackFunction)
			console.log('App Launch')
			// //模拟app数据
			// let plus = {
			// 	runtime: {
			// 		version: 1.5,
			// 		appid: '123'
			// 	},
			// 	os: {
			// 		name: "test"
			// 	}
			// }
			//#ifdef APP-PLUS  
			//更新
			// const updated = uni.getStorageSync('updated'); // 尝试读取storage
			// if (uni.getStorageSync('updated_version')) {
			// 	var version = uni.getStorageSync('updated_version');
			// 	//更新后版本号存储未更新，那么重新存储
			// 	if (version < plus.runtime.version) {
			// 		version = plus.runtime.version;
			// 		uni.setStorageSync('updated_version', version);
			// 	}
			// } else {
			// 	//存储初始版本号
			// 	var version = plus.runtime.version;
			// 	uni.setStorageSync('updated_version', version);
			// }
			const updated = uni.getStorageSync('updated'); // 尝试读取storage
			if (uni.getStorageSync('updated_version')) {
				var version = uni.getStorageSync('updated_version');
				//更新后版本号存储未更新，那么重新存储
				if (version < uni.getAppBaseInfo().appWgtVersion) {
					version = uni.getAppBaseInfo().appWgtVersion;
					uni.setStorageSync('updated_version', version);
				}
			} else {
				//存储初始版本号
				var version = uni.getAppBaseInfo().appWgtVersion;
				uni.setStorageSync('updated_version', version);
			}
			
			console.log("version:",version,plus.runtime.versionCode)
			console.log("version:",uni.getAppBaseInfo().appWgtVersion)
			var server = updateUrl; //检查更新地址
			var req = { //升级检测数据  
				"appid": plus.runtime.appid,
				"version": version,
				"name": plus.os.name
			};
			// var req = { //升级检测数据
			// 	"appid": 123,
			// 	"version": 123,
			// 	"name": "Android"
			// };
			uni.request({
				url: server,
				data: req,
				method: "post",
				header: {
					'content-type': 'application/x-www-form-urlencoded' //自定义请求头信息
				},
				success: (res) => {
					// 整包更新 
					var rsp = res.data.data.rsp;
					console.log(JSON.stringify(rsp))
					if (rsp.status === 1) {
						uni.showModal({ //提醒用户更新  
							title: "更新提示",
							content: rsp.note,
							confirmText: "立即去官网下载",
							success: (res) => {
								if (res.confirm) {
									uni.setStorageSync('updated_version', '');
									plus.runtime.openURL(rsp.url);
								}
							}
						})
					} else if (rsp.status === 2) {
						uni.showModal({
							//提醒用户更新  
							title: "发现新版本",
							content: rsp.note,
							// showCancel: false,
							confirmText: "立即下载更新",
							cancelText:"去官网下载",
							success: (res) => {
								if (res.confirm) {
									var w = plus.nativeUI.showWaiting("开始下载...", {
										back: "none"
									});
									var downloadTask = uni.downloadFile({
										url: rsp.wgturl,
										success: (downloadResult) => {
											if (downloadResult.statusCode ===
												200) {
												plus.nativeUI.toast("下载完成")
												plus.runtime.install(downloadResult
													.tempFilePath, {
														force: false
													},
													function() {
														console.log(
															'install success...'
														);
														plus.nativeUI
															.closeWaiting();
														plus.nativeUI.alert(
															"更新完成！",
															function() {
																uni.setStorageSync(
																	'updated_version',
																	rsp
																	.to_version
																);
																plus.runtime
																	.restart();
															});
													},
													function(e) {
														console.error(
															'install fail...'
														);
														plus.nativeUI
															.closeWaiting();
														plus.nativeUI.alert(
															"更新失败[" + e
															.code + "]：" +
															e.message);
													});


											}
										}
									});
									var pre_percent = 0;

									downloadTask.onProgressUpdate((res) => {
										let percent = parseInt(parseFloat(res
											.totalBytesWritten) / parseFloat(
											res
											.totalBytesExpectedToWrite) * 100)
										if (percent > pre_percent + 10) {
											w.setTitle("下载中..." + percent + "%"+"请稍等");
											pre_percent = percent
											// console.log('下载进度' + pre_percent);
										}

										// 满足测试条件，取消下载任务。
										if (percent === 100) {
											plus.nativeUI.closeWaiting();
											// downloadTask.abort();
										}
									});
								}else{
									uni.setStorageSync('updated_version', '');
									plus.runtime.openURL(rsp.url);
								}
							}
						})
					}
				},
			})
			//#endif 
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style lang="scss">
	@import "uview-ui/index.scss";
	@import "./static/style/icon.css";

	//全局样式
	@import "./static/style/global.css";

	page {
		font-size: $baseFontSizeDefault;
	}

	.submit-btn {
		height: 96rpx;
		background: $baseColor;
		border-radius: $baseRadius;
		font-size: $baseFontSize;
		line-height: 96rpx;
		text-align: center;
		color: $baseBgColor;
	}

	.submit-btn.disabled {
		background: #E3E3E3;
		color: #000;
	}

	.text-green {
		color: $baseGreenColor;
	}

	.text-red {
		color: $baseRedColor;
	}
</style>