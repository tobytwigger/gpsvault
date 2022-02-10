import {createInertiaApp, Link} from '@inertiajs/inertia-vue';
import { InertiaProgress } from '@inertiajs/progress';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import vuetify from './plugins/vuetify';
import Vue from 'vue';

import Settings from '@elbowspaceuk/laravel-settings-vue/dist/index';

require('./bootstrap');
import { applyPolyfills, defineCustomElements } from '@bruit/component/loader';
Vue.config.ignoredElements = [/bruit-\w*/];
Vue.use(Settings, {
    axios: axios
});

applyPolyfills().then(() => {
    defineCustomElements(window);
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./pages/${name}.vue`),
    setup({ el, app, props }) {
        new Vue({
            vuetify,
            render: h => h(app, props),
        }).$mount(el)
    },
})

InertiaProgress.init({ color: '#4B5563' });
