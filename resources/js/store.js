import Vuex from 'vuex';
import connector from './connector';
window.Vue = require('vue');

Vue.use(Vuex);
const modelMatches = {
    namespaced: true,

    state: {
        matches: [],
        next_page_url: null,
    },
    getters: {
        nextPageUrl: state => {
            return state.next_page_url;
        },
    },
    mutations: {
        REFRESH_MATCHES(state, matches) {
            state.matches = matches.data;
            state.next_page_url = matches.next_page_url;
        },
        LOAD_MORE(state, matches) {
            state.matches = state.matches.concat(matches.data);
            state.next_page_url = matches.next_page_url;
        },
    },
    actions: {
        refreshMatches({commit}, params) {
            return new Promise((resolve, reject) => {
                connector.refreshMatches(params).then(function(res) {
                    commit('REFRESH_MATCHES', res.data);
                    resolve();
                });
            });
        },
        loadMore({commit}, url, params) {
            return new Promise((resolve, reject) => {
                connector.loadMore(url, params).then(function(res) {
                    commit('LOAD_MORE', res.data);
                    resolve();
                });
            });
        }
    }
}
export default new Vuex.Store({
    modules: {
        modelMatches
    }
});
