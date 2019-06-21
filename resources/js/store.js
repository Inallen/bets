import Vuex from 'vuex';
import connector from './connector';
window.Vue = require('vue');

Vue.use(Vuex);
const modelMatches = {
    state: {
        matches: []
    },
    mutations: {
        REFRESH_MATCHES(state, matches) {
            state.matches = matches.data;
        }
    },
    actions: {
        refreshMatches({commit}, params) {
            connector.refreshMatches(params).then(function(res) {
                commit('REFRESH_MATCHES', res.data);
            });
        }
    }
}
export default new Vuex.Store({
    modules: {
        modelMatches
    }
});
