@extends('app.layouts.default')
@section('content')
    <div class="weui-cells">
    @foreach ($matches as $match)
        <div class="matches-weui-cell">
            {{--<img src="{{ Storage::url($match->leftTeam->team_logo_url) }}" style="width: 50px;display: block"/>
            <span>{{ $match->leftTeam->team_short_name }}</span>--}}
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
                    {{--<p>联系人名称</p>--}}
                @endif
                {{--<p class="weui-tournament__label">{{ $match->tournament->tournament_name }}</p>--}}
                <p class="weui-tournament__label">{{ $match->tournament->tournament_name }}</p>
            </div>
            <div class="weui-team">
                <div class="weui-team__icon">
                    <img src="{{ Storage::url($match->rightTeam->team_logo_url) }}">
                </div>
                <p class="weui-team__label">{{ $match->rightTeam->team_short_name }}</p>
            </div>

        </div>
    @endforeach
    </div>
@endsection
