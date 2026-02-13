<li>
    <div class="row download-link-item mb-3 border p-3 rounded">
        <input type="hidden" class="link-id" name="download_links[{{ $marker }}][id]" value="{{ $link->id ?? '' }}">

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    {{ Field::text(__('game-store::translation.title'), "download_links[{$marker}][title]", ['value' => $link->title ?? '', 'placeholder' => __('game-store::translation.eg_windows_version')]) }}
                </div>
                <div class="col-md-6">
                    {{ Field::text(__('game-store::translation.url'), "download_links[{$marker}][url]", ['value' => $link->url ?? '', 'placeholder' => __('game-store::translation.https')]) }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    {{ Field::text(__('game-store::translation.file_size'), "download_links[{$marker}][size]", ['value' => $link->size ?? '', 'placeholder' => __('game-store::translation.eg_500_mb')]) }}
                </div>
                <div class="col-md-6">
                    {{ Field::text(__('game-store::translation.platform'), "download_links[{$marker}][platform]", ['value' => $link->platform ?? '', 'placeholder' => __('game-store::translation.eg_windows_mac_linux')]) }}
                </div>
            </div>
            <button type="button" class="btn btn-danger btn-sm remove-download-link">
                <i class="fas fa-times"></i> {{ __('game-store::translation.remove') }}
            </button>
        </div>
    </div>
</li>
