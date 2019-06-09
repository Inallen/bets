<template>
    <div class="weui-cells">

        <!--<div class="matches-weui-cell" v-for="match in matches">

            <div class="weui-team">
                <div class="weui-team__icon">
                    <img src="{{ Storage::url($match->leftTeam->team_logo_url) }}">
                </div>
                <p class="weui-team__label">{{ $match->leftTeam->team_short_name }}</p>
            </div>
            <div class="matches-weui-cell__bd">
                @if ($match->result != 'unknown' )
                <p class="weui-tournament_result__label">{{ $match->left_team_score }} : {{ $match->right_team_score }}</p>
                @else
                <p>联系人名称</p>
                @endif
                <p class="weui-tournament__label">{{ $match->tournament->tournament_name }}</p>
                <p class="weui-tournament__label">{{ $match->tournament->tournament_name }}</p>
            </div>
            <div class="weui-team">
                <div class="weui-team__icon">
                    <img src="{{ Storage::url($match->rightTeam->team_logo_url) }}">
                </div>
                <p class="weui-team__label">{{ $match->rightTeam->team_short_name }}</p>
            </div>

        </div>-->
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
