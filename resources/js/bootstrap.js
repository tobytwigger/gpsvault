import Vue from 'vue';
import VueGridLayout from 'vue-grid-layout';

/**
 * Lodash
 */
window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.defaults.withCredentials = true;

/**
 * We'll set up any global configuration needed for Vue
 */
import VueEasyLightbox from 'vue-easy-lightbox'
import UiKit from 'ui/install';
import {Link} from '@inertiajs/inertia-vue';

Vue.use(UiKit);
Vue.use(VueEasyLightbox);
Vue.component('Link', Link);
Vue.component('grid-layout', VueGridLayout.GridLayout);
Vue.component('grid-item', VueGridLayout.GridItem);
Vue.mixin({methods: { route }});

import VueShepherdPlugin from 'vue-shepherd';
import 'shepherd.js/dist/css/shepherd.css';
Vue.use(VueShepherdPlugin);

Vue.prototype.$showTour = {
    onShow: null,
    show() {
        if(this.onShow) {
            this.onShow();
        }
    }
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';
// window.Pusher = require('pusher-js');
//
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'gps-vault',
//     wsHost: window.location.hostname,
//     wsPort: 6001,
//     wssPort: 6001,
//     disableStats: false,
//     enabledTransports: ['ws', 'wss']
// });
