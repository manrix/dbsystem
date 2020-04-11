import Es6Promise from 'es6-promise'
Es6Promise.polyfill();

import Vue from 'vue'
import Vuex from 'vuex'
import databases from './modules/databases'
import destinations from './modules/destinations'
import loader from './modules/loader'
import warning from './modules/warning'
import user from './modules/user'

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
    modules: {
        databases,
        destinations,
        loader,
        warning,
        user
    },
    strict: debug
});