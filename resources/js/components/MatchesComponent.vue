<template>

        <pull-to-refresh
            :top-load-method="onPullUp"
            :bottom-load-method="onPullDown">
            <div class="weui-cells">
            <div class="matches-weui-cell" v-for="match in matches" :key="match.id">
                <div class="weui-team">
                    <div class="weui-team__icon">
                        <img :src="_config.filePath(match.left_team.team_logo_url)">
                    </div>
                    <p class="weui-team__label">{{ match.left_team.team_short_name }}</p>
                </div>
                <div class="matches-weui-cell__bd" v-if="match.result == 'unknown'">
                    <p><span class="weui-match_real_label">竞猜中</span></p>
                    <p class="weui-match_real_time__label">{{ _config.timestamp2String(match.start_time) }}</p>
                    <p class="weui-tournament__label">{{ match.tournament.tournament_name }}</p>
                </div>
                <div class="matches-weui-cell__bd" v-else>
                    <p class="weui-match_result__label">{{ match.left_team_score }} : {{ match.right_team_score }}</p>
                    <p class="weui-match_time__label">{{ _config.timestamp2String(match.start_time) }}</p>
                    <p class="weui-tournament__label">{{ match.tournament.tournament_name }}</p>
                </div>
                <div class="weui-team">
                    <div class="weui-team__icon">
                        <img :src="_config.filePath(match.right_team.team_logo_url)">
                    </div>
                    <p class="weui-team__label">{{ match.right_team.team_short_name }}</p>
                </div>
            </div>
            </div>
        </pull-to-refresh>

</template>

<script>
    import { mapState, mapActions } from 'vuex';
    import PullToRefresh from './pull-to-refresh';
    export default({
        computed: mapState({
            matches: state => state.modelMatches.matches
        }),
        components: {
            PullToRefresh
        },
        created() {
            this.refreshMatches();
        },
        methods: {
            ...mapActions([
                'refreshMatches'
            ]),
            getAvatar(avatar){
                return "/gate/profile/photo"+avatar;
            },
            onPullUp(loaded) {
                setTimeout(()=>{
                    this.refreshMatches();
                    loaded('done');//finish the refreshing state
                },3000);
            },
            onPullDown(loaded) {
                console.log('finishCallback');
                setTimeout(()=>{
                    this.refreshMatches();
                    loaded('done');//finish the refreshing state
                },3000);
            },
        }
    });
</script>
