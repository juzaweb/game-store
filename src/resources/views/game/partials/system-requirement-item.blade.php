<li>
    <div class="row system-requirement-item mb-3 border p-3 rounded">
        <input type="hidden" class="requirement-id" name="system_requirements[{{ $marker }}][id]" value="{{ $requirement->id ?? '' }}">

        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4">
                    {{ Field::select(__('game-store::translation.type'), "system_requirements[{$marker}][type]", [
                        'options' => [
                            'minimum' => __('game-store::translation.minimum'),
                            'recommended' => __('game-store::translation.recommended')
                        ],
                        'value' => $requirement->type ?? 'minimum',
                        'required' => true
                    ]) }}
                </div>
                <div class="col-md-4">
                    {{ Field::select(__('game-store::translation.platform'), "system_requirements[{$marker}][platform]", [
                        'options' => [
                            'pc' => __('game-store::translation.pc_windows'),
                            'mac' => __('game-store::translation.mac'),
                            'linux' => __('game-store::translation.linux')
                        ],
                        'value' => $requirement->platform ?? 'pc',
                        'required' => true
                    ]) }}
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm remove-system-requirement form-control">
                        <i class="fas fa-times"></i> {{ __('game-store::translation.remove') }}
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{ Field::textarea(__('game-store::translation.requirements'), "system_requirements[{$marker}][requirements]", [
                        'value' => $requirement->requirements ?? '',
                        'rows' => 10,
                        'placeholder' => __('Enter system requirements in JSON format, e.g.:
{
  "OS": "Windows 7/8/10",
  "Processor": "Intel Core i5-2400 @ 2.5 GHz",
  "Memory": "6 GB RAM",
  "Graphics": "NVIDIA GeForce GTX 660",
  "Disk Space": "42 GB",
  "Architecture": "64-bit processor and OS",
  "API": "DirectX 11"
}')
                    ]) }}
                </div>
            </div>
        </div>
    </div>
</li>
