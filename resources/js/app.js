import StravaIntegrationAddon from './Integrations/StravaIntegrationAddon';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import StravaFixSetup from './Integrations/StravaFixSetup';
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
import VueEasyLightbox from 'vue-easy-lightbox'
import 'leaflet/dist/leaflet.css';
import Units from './Plugins/units';
import moment from 'moment';

require('./bootstrap');

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(VueEasyLightbox)
            .use(Units)
            .component('task-strava-upload', StravaFixSetup)
            .component('strava-integration-addon', StravaIntegrationAddon)
            .mixin({
                methods: { route },
                computed: {
                    next15Mins() {
                        let now = moment();
                        return now
                            .add(15 - (now.minute() % 15), "minutes")
                            .seconds(0)
                            .format("HH:mm:ss");
                    },
                    nextDay() {
                        return moment()
                            .add(1, 'days')
                            .seconds(0)
                            .minutes(0)
                            .hours(0)
                            .format('DD/MM/YYYY');
                    }
                },
            })
            .mount(el);
    },
});

InertiaProgress.init({ color: '#4B5563' });
