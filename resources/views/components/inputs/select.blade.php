<div class="form-group row">
    <label for="{{ $name }}" class="col-md-4 col-form-label text-md-right">{{ $label }}</label>

    <div class="col-md-6">
        <select id="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
                name="{{ $name }}"
                @isset($required)
                required
            @endisset
        >
            @foreach($options as $option)
                <option value="{{ $option->id }}" {{ old($name, $option->id ?? '') == $selected ? ' selected ' : '' }}>{{ $option->name }}</option>
            @endforeach
        </select>
        @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
