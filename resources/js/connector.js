window.axios = require('axios');
import config from './config';
export default {
    refreshMatches: function (params) {
        return window.axios.get(config.baseUrl + 'api/matches', {
            params: params
        })
    },
}
