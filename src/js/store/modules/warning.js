import { WARN_CHANGE } from '../mutation-types'

// initial state
const state = {
    warnBeforeChange: false
};

// mutations
const mutations = {
    [WARN_CHANGE](state, value) {
        state.warnBeforeChange = value;
    }
};

export default {
    state,
    mutations
}