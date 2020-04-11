import Vue from 'vue'
import Main from './layout/App.vue'
import store from './store'
import router from './router'
import axios from "axios"
import "./plugins/axios"
import VueAxios from "vue-axios"
import Buefy from "buefy"
import "../sass/app.scss"
import NoResults from "./components/NoResults.vue"
import VueBreadcrumbs from 'vue-breadcrumbs'
import { UPDATE_USER } from './store/mutation-types'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
    formatToLocalDate, formatToLocalDateTime, formatToLocalTime
} from "./helpers"
import BaseLayout from "./layout/BaseLayout"

Vue.config.productionTip = false;

// register mixins
Vue.mixin({
    methods: {
        formatToLocalTime(datetime) {
            return formatToLocalTime(datetime);
        },

        formatToLocalDate(datetime) {
            return formatToLocalDate(datetime);
        },

        formatToLocalDateTime(datetime) {
            return formatToLocalDateTime(datetime);
        },
    }
});

// register breadcrumb navigation component
Vue.use(VueBreadcrumbs, {
    template: `
        <nav class="breadcrumb has-bullet-separator is-small is-uppercase" aria-label="breadcrumbs" v-if="$breadcrumbs.length">
            <ul>
               <li><router-link to="/">DBSystem</router-link></li>
                <li v-for="(crumb, key) in $breadcrumbs"><router-link class="breadcrumb-item" :to="linkProp(crumb)" :key="key">{{ crumb | crumbText }}</router-link></li>
            </ul>
        </nav>`
});

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component('base-layout', BaseLayout);
Vue.component('no-results', NoResults);

Vue.use(VueAxios, axios);

// buefy components
Vue.use(Buefy, {
    defaultIconPack: 'fas',
});

// retrieve the initial user data passed by the server
store.commit(UPDATE_USER, window.APP.user);

// register filters
Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1)
});

// start the app
window.VueApp = new Vue({
    el: '#app',
    store,
    router,
    render: h => h(Main)
});
