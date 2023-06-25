import {createInertiaApp, Link} from '@inertiajs/vue3';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import { createApp, h } from 'vue';
import {createVuetify} from 'vuetify'


import Settings from '@tobytwigger/laravel-settings-vue/dist/index';
import JobStatus from '@tobytwigger/laravel-job-status-vue';

require('./bootstrap');
import { applyPolyfills, defineCustomElements } from '@bruit/component/loader';
Vue.config.ignoredElements = [/bruit-\w*/];


applyPolyfills().then(() => {
    defineCustomElements(window);
});

createInertiaApp({
    progress: { color: '#4B5563' },
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./pages/${name}.vue`),
    setup({ el, App, props }) {
        createApp({ render: () => h(App, props) })
            .use(createVuetify({
                icons: {
                    iconfont: 'mdiSvg',
                },
            }))
            .use(JobStatus, {
                axios: axios,
                url: '/_api'
            })
            .use(Settings, {
                axios: axios
            })
            .mount(el)
    },
})

