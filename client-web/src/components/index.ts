import { App, Plugin } from 'vue';
import LayoutBody from '@/components/layouts/LayoutBody.vue'
import LayoutBodyTabs from '@/components/layouts/LayoutBodyTabs.vue'
import LayoutBodyContent from '@/components/layouts/LayoutBodyContent.vue'
import copyright from '@/components/plugins/copyright/index.vue'
import page from '@/components/plugins/page/index.vue'
import shortcutsTime from '@/components/plugins/shortcuts-time/index.vue'

export const ComponentsPlugin: Plugin = {
    install(app: App) {
        app.component('layout-body', LayoutBody);
        app.component('layout-body-tabs', LayoutBodyTabs);
        app.component('layout-body-content', LayoutBodyContent);
        app.component('copyright', copyright);
        app.component('page', page);
        app.component('shortcuts-time', shortcutsTime);

    },
};
export {  LayoutBody, LayoutBodyTabs, LayoutBodyContent,copyright,page,shortcutsTime };