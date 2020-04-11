import { UPDATE_USER } from '../mutation-types'

// initial state
const state = {
    user: {}
};

// mutations
const mutations = {
    [UPDATE_USER](state, value) {
        state.user = value;
    }
};

export default {
    state,
    mutations
}