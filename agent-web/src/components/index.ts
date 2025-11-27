import { App, Plugin } from 'vue';
import copyright from '@/components/plugins/copyright/index.vue'
import uploadBtn from '@/components/plugins/upload-btn/index.vue'
import selectIconModal from '@/components/select-modal/icon/index.vue'
import shortcutsTime from '@/components/plugins/shortcuts-time/index.vue'
import editor from '@/components/plugins/editor/index.vue'
import videoPlay from '@/components/plugins/video-play/index.vue'
import searchMap from '@/components/plugins/search-map/index.vue'
import page from '@/components/plugins/page/index.vue'
import audioPlay from '@/components/plugins/audio-play/index.vue'


import agentColumnDetail from '@/components/agent/column-detail.vue'
import agentSearchSelect from '@/components/agent/search-select.vue'
import memberColumnDetail from '@/components/member/column-detail.vue'

export const ComponentsPlugin: Plugin = {
    install(app: App) {
        app.component('copyright', copyright);
        app.component('upload-btn', uploadBtn);
        app.component('select-icon-modal', selectIconModal);
        app.component('shortcuts-time', shortcutsTime);
        app.component('editor', editor);
        app.component('video-play', videoPlay);
        app.component('search-map', searchMap);
        app.component('page', page);
        app.component('audio-play', audioPlay);

        app.component('agent-column-detail', agentColumnDetail);
        app.component('agent-search-select', agentSearchSelect);
        app.component('member-column-detail', memberColumnDetail);
    },
};
export { copyright, uploadBtn, selectIconModal, shortcutsTime, editor, videoPlay, searchMap, page, audioPlay,

    agentColumnDetail,
    agentSearchSelect,
    memberColumnDetail
 };