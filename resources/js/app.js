import {createInertiaApp, Link} from '@inertiajs/inertia-vue';
import { InertiaProgress } from '@inertiajs/progress';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import { createApp, h } from 'vue';
import {createVuetify} from 'vuetify'


import Settings from '@tobytwigger/laravel-settings-vue/dist/index';
import JobStatus from '@tobytwigger/laravel-job-status-vue';

require('./bootstrap');
import { applyPolyfills, defineCustomElements } from '@bruit/component/loader';
Vue.config.ignoredElements = [/bruit-\w*/];
Vue.use(Settings, {
    axios: axios
});
Vue.use(JobStatus, {
    axios: axios,
    url: '/_api'
});


applyPolyfills().then(() => {
    defineCustomElements(window);
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./pages/${name}.vue`),
    setup({ el, app, props }) {
        createApp({ render: () => h(App, props) })
            .use(createVuetify({
                icons: {
                    iconfont: 'mdiSvg',
                },
            }))
            .mount(el)
    },
})

InertiaProgress.init({ color: '#4B5563' });
