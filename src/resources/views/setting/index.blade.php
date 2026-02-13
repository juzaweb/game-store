@extends('core::layouts.admin')

@section('content')
    <form action="" class="form-ajax" method="post">
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('game-store::translation.save') }}
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <x-card title="{{ __('game-store::translation.igdb_settings') }}">
                    {{ Field::security(__('game-store::translation.client_id'), 'igdb_client_id', [
                        'value' => setting('igdb_client_id'),
                    ]) }}

                    {{ Field::security(__('game-store::translation.client_secret'), 'igdb_client_secret', [
                        'value' => setting('igdb_client_secret'),
                    ]) }}

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        {{ __('game-store::translation.get_your_api_key_from_igdb_you_need_an_account_to_generate_an_api_key') }}
                        <a rel="noopener noreferrer" href="https://api-docs.igdb.com/#getting-started" target="_blank">
                            {{ __('game-store::translation.get_api_key') }}
                        </a>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('game-store::translation.save') }}
                </button>
            </div>
        </div>
    </form>
@endsection
