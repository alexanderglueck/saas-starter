<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <input id="{{ $name }}" type="{{ $type }}" class="form-control @error($name) is-invalid @enderror"
               name="{{ $name }}"
               @isset($required)
               required
               @endisset
               @isset($autocomplete)
               autocomplete="{{ $autocomplete }}"
               @endisset
               value="{{ old($name, $value ?? '') }}"
        >
        @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
