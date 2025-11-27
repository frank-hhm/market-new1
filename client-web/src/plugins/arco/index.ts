import { type App } from "vue"
import ArcoVue from '@arco-design/web-vue';

import '@/styles/test1.less';
// import '@arco-design/web-vue/dist/arco.css';
// import '@arco-themes/vue-dev-frank/index.less';
import '@/styles/test.less';
export function loadArcoPlus(app: App) {
  app.use(ArcoVue)
}
