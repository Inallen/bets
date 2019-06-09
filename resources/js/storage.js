


export default{
    state: {
        matches: []
    },
    mutations: {
        refreshMatches(state, lists) {
            state.matches = lists;
        }
    },
    actions: {
        refreshMatches({commit}) {
            api.getUser().then(function(res) {
                commit('refreshMatches', res.data);
            });
        }
    }
}
