@extends('core::layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @can('games.create')
                <a href="{{ $createUrl }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('game-store::translation.add_game') }}
                </a>

                <a href="javascript:void(0);" class="btn btn-success" data-toggle="modal" data-target="#igdb-import-modal">
                    <i class="fas fa-download"></i> {{ __('game-store::translation.import_from_igdb') }}
                </a>
            @endcan
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <x-core::datatables.filters>
                <div class="col-md-3 jw-datatable_filters">

                </div>
            </x-core::datatables.filters>
        </div>

        <div class="col-md-12 mt-2">
            <x-card title="{{ __('game-store::translation.games') }}">
                {{ $dataTable->table() }}
            </x-card>
        </div>
    </div>

    @include('game-store::game.partials.igdb-import-modal')
@endsection

@section('scripts')
    {{ $dataTable->scripts(null, ['nonce' => csp_script_nonce()]) }}

    <script type="text/javascript" nonce="{{ csp_script_nonce() }}">
        $(function() {
            // Search on button click
            $('#igdb-search-btn').on('click', function() {
                performIGDBSearch();
            });

            // Search on Enter key
            $('#igdb-search-input').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    performIGDBSearch();
                }
            });

            function performIGDBSearch() {
                let query = $('#igdb-search-input').val().trim();

                if (query.length < 2) {
                    showError('{{ __('game-store::translation.please_enter_at_least_2_characters') }}');
                    return;
                }

                // Show loading, hide results and errors
                $('#igdb-loading').show();
                $('#igdb-search-results').hide();
                $('#igdb-no-results').hide();
                $('#igdb-error').hide();
                $('#igdb-results-list').empty();

                $.ajax({
                    url: '{{ route('admin.games.igdb.search') }}',
                    method: 'POST',
                    data: {
                        query: query,
                    },
                    success: function(response) {
                        $('#igdb-loading').hide();

                        if (response.success && response.data && response.data.length > 0) {
                            displayResults(response.data);
                        } else {
                            $('#igdb-no-results').show();
                        }
                    },
                    error: function(xhr) {
                        $('#igdb-loading').hide();
                        let message =
                            '{{ __('game-store::translation.igdb_search_failed', ['error' => '']) }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showError(message);
                    }
                });
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }

            function displayResults(games) {
                let html = '';

                games.forEach(function(game) {
                    let escapedName = escapeHtml(game.name);
                    let escapedSummary = '';
                    if (game.summary) {
                        escapedSummary = game.summary.length > 150 ?
                            escapeHtml(game.summary.substring(0, 150) + '...') :
                            escapeHtml(game.summary);
                    }

                    // For URLs, we don't need HTML escaping as browsers handle URL encoding
                    let coverUrl = game.cover_url ? game.cover_url.replace('t_thumb', 't_cover_small') : '';
                    let coverImg = coverUrl ?
                        '<img src="https:' + coverUrl + '" alt="' + escapedName +
                        '" style="width: 50px; height: auto; margin-right: 10px;">' :
                        '<div style="width: 50px; height: 70px; background: #ddd; margin-right: 10px; display: inline-block;"></div>';

                    html +=
                        '<a href="javascript:void(0)" class="list-group-item list-group-item-action igdb-game-item" data-igdb-id="' +
                        game.id + '">';
                    html += '<div class="d-flex align-items-start">';
                    html += coverImg;
                    html += '<div class="flex-grow-1">';
                    html += '<h6 class="mb-1">' + escapedName + '</h6>';
                    html += '<p class="mb-1 small text-muted">' + escapedSummary + '</p>';
                    html += '</div>';
                    html += '</div>';
                    html += '</a>';
                });

                $('#igdb-results-list').html(html);
                $('#igdb-search-results').show();
            }

            function showError(message) {
                $('#igdb-error').text(message).show();
            }

            // Handle game import
            $(document).on('click', '.igdb-game-item', function(e) {
                e.preventDefault();

                let igdbId = $(this).data('igdb-id');
                let gameName = $(this).find('h6').text();

                Swal.fire({
                    title: '{{ __('game-store::translation.import') }}',
                    text: '{{ __('core::translation.are_you_sure') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('game-store::translation.import') }}',
                    cancelButtonText: '{{ __('game-store::translation.cancel') }}',
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return $.ajax({
                            url: '{{ route('admin.games.igdb.import') }}',
                            method: 'POST',
                            data: {
                                igdb_id: igdbId,
                                name: gameName,
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        let response = result.value;

                        if (response.success) {
                            Swal.fire({
                                title: '{{ __('core::translation.success') }}',
                                text: response.message,
                                icon: 'success',
                                timer: 2000
                            }).then(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                }
                            });
                        } else {
                            Swal.fire({
                                title: '{{ __('core::translation.error') }}',
                                text: response.message ||
                                    '{{ __('game-store::translation.igdb_import_failed', ['error' => '']) }}',
                                icon: 'error'
                            });
                        }
                    }
                }).catch((error) => {
                    console.log(error);
                    Swal.fire({
                        title: '{{ __('core::translation.error') }}',
                        text: error.responseJSON?.message ||
                            '{{ __('game-store::translation.igdb_import_failed', ['error' => '']) }}',
                        icon: 'error'
                    });
                });
            });

            // Reset modal on close
            $('#igdb-import-modal').on('hidden.bs.modal', function() {
                $('#igdb-search-input').val('');
                $('#igdb-search-results').hide();
                $('#igdb-no-results').hide();
                $('#igdb-error').hide();
                $('#igdb-loading').hide();
                $('#igdb-results-list').empty();
            });
        });
    </script>
@endsection
