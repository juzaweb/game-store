@extends('core::layouts.admin')

@section('content')
    <form action="{{ $action }}" method="post" class="form-ajax" enctype="multipart/form-data">
        @if ($model->exists)
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ $backUrl }}" class="btn btn-warning">
                    <i class="fas fa-arrow-left"></i> {{ __('game-store::translation.back') }}
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('game-store::translation.save') }}
                </button>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-9">
                <x-card>
                    {{ Field::text(__('game-store::translation.title'), 'title', ['required' => true, 'value' => $model->title]) }}

                    {{ Field::editor(__('game-store::translation.content'), 'content', ['rows' => 5, 'value' => $model->content]) }}
                </x-card>

                {{ Field::images(__('game-store::translation.screenshots'), 'screenshots', ['value' => $model?->getMedia('screenshots')?->pluck('path')]) }}

                <x-card title="{{ __('game-store::translation.download_links') }}">
                    <ul id="download-links" class="list-unstyled">
                        @foreach ($model->downloadLinks ?? [] as $index => $link)
                            @component('game-store::game.partials.download-link-item', [
                                'marker' => $link->id ?? $index,
                                'link' => $link,
                            ])
                            @endcomponent
                        @endforeach
                    </ul>

                    <button type="button" class="btn btn-success add-download-link">
                        <i class="fas fa-plus"></i> {{ __('game-store::translation.add_download_link') }}
                    </button>
                </x-card>

                @if ($model->exists)
                    @include('game-store::game.partials.system-requirement')
                @endif
            </div>

            <div class="col-md-3">
                <x-card>
                    {{ Field::select(__('game-store::translation.status'), 'status', [
                        'value' => $model->status?->value,
                        'required' => true,
                    ])
                    ->dropDownList(\Juzaweb\Modules\GameStore\Enums\GameStatus::all()) }}

                    {{ Field::checkbox(trans('game-store::translation.is_free'), 'is_free', ['value' => $model->is_free ?? false]) }}

                    {{ Field::currency(trans('game-store::translation.price'), 'price', ['value' => $model->price, 'class' => 'is-number', 'disabled' => $model->is_free ?? false]) }}

                    {{ Field::currency(trans('game-store::translation.compare_price'), 'compare_price', ['value' => $model->compare_price, 'class' => 'is-number', 'disabled' => $model->is_free ?? false]) }}
                </x-card>

                <x-card title="{{ __('game-store::translation.categories') }}">
                    <div class="scrollable-list" style="max-height: 350px; overflow-y: auto;">
                        @component('core::components.categories-checkbox', [
                            'categories' => $categories,
                            'selectedCategories' => $model->categories->pluck('id')->toArray(),
                            'level' => 0,
                            'storeUrl' => admin_url('game-categories'),
                        ])
                        @endcomponent
                    </div>
                </x-card>

                <x-card title="{{ __('game-store::translation.vendors') }}">
                    {{ Field::tags(__('game-store::translation.vendors'), 'vendors[]', ['value' => $model->vendors->pluck('id')->toArray()])
                        ->dataUrl(load_data_url(\Juzaweb\Modules\GameStore\Models\GameVendor::class))
                        ->placeholder(__('game-store::translation.select_or_add_new_vendor'))
                        ->dropDownList($model->vendors->mapWithKeys(fn($item) => [$item->id => $item->name])->toArray()) }}
                </x-card>

                <x-card title="{{ __('game-store::translation.platforms') }}">
                    {{ Field::tags(__('game-store::translation.platforms'), 'platforms[]', ['value' => $model->platforms->pluck('id')->toArray()])->dataUrl(load_data_url(\Juzaweb\Modules\GameStore\Models\GamePlatform::class))->placeholder(__('game-store::translation.select_or_add_new_platform'))->dropDownList($model->platforms->mapWithKeys(fn($item) => [$item->id => $item->name])->toArray()) }}
                </x-card>

                <x-card title="{{ __('game-store::translation.languages') }}">
                    {{ Field::tags(__('game-store::translation.languages'), 'languages[]', ['value' => $model->languages->pluck('id')->toArray()])
                    ->dataUrl(load_data_url(\Juzaweb\Modules\GameStore\Models\GameLanguage::class))
                    ->placeholder(__('game-store::translation.select_or_add_new_language'))
                    ->dropDownList($model->languages->mapWithKeys(fn($item) => [$item->id => $item->name])->toArray()) }}
                </x-card>

                <x-card title="{{ __('game-store::translation.thumbnail') }}">
                    {{ Field::image(__('game-store::translation.thumbnail'), 'thumbnail', ['value' => $model->thumbnail]) }}
                </x-card>

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script type="text/html" id="download-link-template" nonce="{{ csp_script_nonce() }}">
        @component('game-store::game.partials.download-link-item', [
            'marker' => '{marker}',
            'link' => null,
        ])
        @endcomponent
    </script>

    <script type="text/javascript" nonce="{{ csp_script_nonce() }}">
        $(function() {
            $("#download-links").sortable();
            $("#download-links").disableSelection();

            // Toggle price fields based on is_free checkbox
            function togglePriceFields() {
                const isFree = $('#is_free').is(':checked');
                const priceField = $('input[name="price"]');
                const comparePriceField = $('input[name="compare_price"]');

                if (isFree) {
                    priceField.prop('disabled', true);
                    comparePriceField.prop('disabled', true);
                } else {
                    priceField.prop('disabled', false);
                    comparePriceField.prop('disabled', false);
                }
            }

            // Initialize on page load
            togglePriceFields();

            // Listen to checkbox change
            $('#is_free').on('change', function() {
                togglePriceFields();
            });

            $(document).on('click', '.add-download-link', function() {
                let temp = document.getElementById('download-link-template').innerHTML;
                let length = ($("#download-links li").length + 1);
                let newLink = replace_template(temp, {
                    'marker': length,
                });

                $("#download-links").append(newLink);
            });

            $("#download-links").on('click', '.remove-download-link', function(e) {
                e.preventDefault();
                let item = $(this);

                if (!item.closest('li').find('.link-id').val()) {
                    item.closest('li').remove();
                    return;
                }

                Swal.fire({
                    title: '',
                    text: '{{ trans('core::translation.are_you_sure') }}',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ trans('core::translation.yes') }}',
                    cancelButtonText: '{{ trans('core::translation.cancel') }}',
                }).then((result) => {
                    if (result.value) {
                        item.closest('li').remove();
                    }
                });
            });
        });
    </script>
@endsection
