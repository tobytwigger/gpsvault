import StravaIntegrationAddon from './Integrations/StravaIntegrationAddon';
import {createInertiaApp, Link} from '@inertiajs/inertia-vue';
import { InertiaProgress } from '@inertiajs/progress';
import StravaFixSetup from './Integrations/StravaFixSetup';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import Units from './plugins/units';
import vuetify from './plugins/vuetify';
import UiKit from 'ui/install';
import 'leaflet/dist/leaflet.css';
import VueEasyLightbox from 'vue-easy-lightbox'
// import VueSidebarMenu from 'vue-sidebar-menu'
// import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import Vue from 'vue';

require('./bootstrap');

Vue.use(Units);
Vue.use(UiKit);
Vue.use(VueEasyLightbox);
Vue.component('task-strava-upload', StravaFixSetup);
Vue.component('strava-integration-addon', StravaIntegrationAddon);
Vue.component('Link', Link);
Vue.mixin({methods: { route }});

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
