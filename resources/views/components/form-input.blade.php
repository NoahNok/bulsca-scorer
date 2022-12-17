<div class="form-input {{ $css }} @error($id) is-invalid @enderror {{ $type }}">
    <label for="form-link-{{ $id }}" class="">{{ $title }}</label>
    <input placeholder="{{$placeholder}}" @if ($deny==true ) readonly @endif step="any" @if($type=="checkbox" && $defaultValue) checked @endif type="{{ $type }}" @if ($required=='true' ) required @endif id="form-link-{{ $id }}" name="{{ $id }}" value="{{ old($id) ?: $defaultValue }}" class="input">
    @error($id)
    <small class="text-red-600">{{ $message }}</small>
    @enderror
</div>