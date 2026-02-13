<div class="modal fade" id="igdb-import-modal" tabindex="-1" role="dialog" aria-labelledby="igdb-import-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="igdb-import-modal-label">
                    {{ __('game-store::translation.import_from_igdb') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('game-store::translation.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="igdb-search-input">{{ __('game-store::translation.search_games_on_igdb') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="igdb-search-input" 
                               placeholder="{{ __('game-store::translation.search_placeholder') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="igdb-search-btn">
                                <i class="fas fa-search"></i> {{ __('game-store::translation.search') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div id="igdb-search-results" class="mt-3" style="display: none;">
                    <h6>{{ __('game-store::translation.search') }} {{ __('game-store::translation.results') }}:</h6>
                    <div id="igdb-results-list" class="list-group">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <div id="igdb-no-results" class="alert alert-info mt-3" style="display: none;">
                    {{ __('game-store::translation.no_results_found') }}
                </div>

                <div id="igdb-loading" class="text-center mt-3" style="display: none;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>{{ __('game-store::translation.searching') }}</p>
                </div>

                <div id="igdb-error" class="alert alert-danger mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> {{ __('game-store::translation.close') }}
                </button>
            </div>
        </div>
    </div>
</div>
