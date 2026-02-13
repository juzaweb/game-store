@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="{{ $action }}" method="post" class="form-ajax" enctype="multipart/form-data">
                @csrf
                @if ($model->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <x-card>
                            {{ Field::text(__('game-store::translation.name'), 'name', ['required' => true, 'value' => $model->name]) }}
                        </x-card>
                    </div>

                    <div class="col-md-4">
                        <x-card>
                            {{ Field::select(__('game-store::translation.parent_category'), 'parent_id', [
                                'options' => $parentCategories,
                                'value' => $model->parent_id,
                                'placeholder' => __('game-store::translation.select_parent_category'),
                            ]) }}
                        </x-card>

                        <x-card>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> {{ __('game-store::translation.save') }}
                                </button>

                                <a href="{{ action([Juzaweb\Modules\GameStore\Http\Controllers\GameCategoryController::class, 'index']) }}"
                                    class="btn btn-secondary">
                                    <i class="fa fa-times"></i> {{ __('game-store::translation.cancel') }}
                                </a>
                            </div>
                        </x-card>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
