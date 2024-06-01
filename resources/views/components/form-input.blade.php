<div class="form-input {{ $css }} @error($id) is-invalid @enderror {{ $type }}">
    <label for="form-link-{{ $id }}" class="">{{ $title }}</label>


    @if ($type == 'file')
    <div class="max-w-32 max-h-32">
        <img src="" alt="" id="form-link-{{ $id }}-file-preview" class="h-full">

    </div>

    
@endif

    
    <input placeholder="{{$placeholder}}" @if ($deny==true ) readonly @endif step="any" @if($type=="checkbox" && $defaultValue) checked @endif type="{{ $type }}" @if ($required=='true' ) required @endif id="form-link-{{ $id }}" name="{{ $id }}" value="{{ old($id) ?: $defaultValue }}"  class="input">
    @error($id)
    <small class="text-red-600">{{ $message }}</small>
    @enderror


    {{ $slot }}

    @if ($type == 'file')


    <script>
        const input = document.getElementById('form-link-{{ $id }}');
        const img = document.getElementById('form-link-{{ $id }}-file-preview');
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    img.src = reader.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
    
@endif
   
</div>