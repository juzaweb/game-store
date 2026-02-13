<x-card title="{{ __('game-store::translation.system_requirements') }}">
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                @foreach ($model->platforms as $platform)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="platform-{{ $platform->id }}-tab" data-toggle="pill"
                            href="#platform-{{ $platform->id }}" role="tab"
                            aria-controls="platform-{{ $platform->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $platform->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">

            <div class="tab-content" id="custom-tabs-one-tabContent">
                @foreach ($model->platforms as $platform)
                    @php
                        $minimumReq = $model->systemRequirements
                            ->where('type', 'minimum')
                            ->where('game_platform_id', $platform->id)
                            ->first();
                        $recommendedReq = $model->systemRequirements
                            ->where('type', 'recommended')
                            ->where('game_platform_id', $platform->id)
                            ->first();
                    @endphp
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="platform-{{ $platform->id }}" role="tabpanel"
                        aria-labelledby="platform-{{ $platform->id }}-tab">
                        {{ Field::textarea(__('game-store::translation.minimum_requirements'), "requirements[minimum][{$platform->id}]", [
                            'value' => $minimumReq->requirements ?? '',
                            'rows' => 8,
                        ]) }}

                        {{ Field::textarea(__('game-store::translation.recommended_requirements'), "requirements[recommended][{$platform->id}]", [
                            'value' => $recommendedReq->requirements ?? '',
                            'rows' => 8,
                        ]) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-card>
