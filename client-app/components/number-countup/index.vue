<template>
	<text class="number-countup">{{numberResult}}</text>
</template>

<script>
	import {
		CountUp
	} from '@/utils/countUp';

	export default {
		props: {
			// 数字值
			value: {
				type: Number,
				default: 0
			},
			// 切换过渡时间
			duration: {
				type: Number,
				default: 0.2
			},
			// 保留的小数点
			decimal: {
				type: Number,
				default: 2
			},
			//  是否要千分位表示法
			isMillennials: {
				type: Boolean,
				default: false
			},
		},
		data() {
			return {
				numberResult: ''
			}
		},
		watch: {
			value(newValue, oldValue) {
				this.countUpItem.update(newValue);
			}
		},

		mounted() {
			const That = this;
			this.countUpItem = new CountUp({}, this.value, {
				duration: That.duration,
				decimalPlaces: That.decimal,
				useIndianSeparators: false,
				useGrouping:false,
				plugin: {
					render(ele, result) {
						That.numberResult = result;
					}
				}
			});
			this.countUpItem.start();
		}
	}
</script>

<style lang="scss" scoped>
</style>