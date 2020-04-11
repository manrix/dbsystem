import { LOADING } from '../mutation-types'

// initial state
const state = {
    isLoading: false
};

// mutations
const mutations = {
    [LOADING](state, value) {
        state.isLoading = value;
    }
};

export default {
    state,
    mutations
}