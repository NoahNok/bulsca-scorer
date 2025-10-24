@props(['label' => null, 'type' => 'text', 'name', 'default' => null, 'section' => '', 'description' => null])
<div class="{{ $type == 'checkbox' ? 'se-checkbox' : 'se-form-input' }} imb-0" x-data="{
    self: {
        error: false,
        data: '{{ $default ?? '' }}',
        section: '{{ $section }}',
        element: null,
    },

    name: '{{ $name }}',

    checkError(el) {
        this.self.error = el.checkValidity() ? false : el.validationMessage
    },

    init() {

        this.self.element = $refs.input
        if (form == null) {
            console.log(`'${this.name}' is not within a form and thus will be standalone`)
            return
        }

        form.data[this.name] = this.self


    }
}">


    @if ($type == 'checkbox')
        <div>
            <input type="checkbox" :name="name" @if ($default) checked @endif
                id="can_be_live" x-model="self.data" x-ref="input" {{ $attributes->merge() }} @input="checkError($el)">
            <input type="hidden" :name="name" value="0">
            <label for="can_be_live">{{ $label }}</label>
        </div>
        <p>If this competition can be viewed live</p>
    @elseif ($type == 'select')
        @if ($label)
            <label for="">{{ $label }}</label>
        @endif

        @if ($description)
            {!! $description !!}
        @endif


        <select :name="name" x-model="self.data" x-ref="input" {{ $attributes->merge() }}
            @input="checkError($el)">
            {{ $slot }}
        </select>
    @else
        @if ($label)
            <label for="">{{ $label }}</label>
        @endif

        @if ($description)
            {!! $description !!}
        @endif

        <input type="{{ $type }}" :name="name" x-model="self.data" x-ref="input"
            value="{{ $default ?? '' }}" {{ $attributes->merge() }} @input="checkError($el)">
    @endif


    <small x-text="self.error" x-show="self.error"></small>
</div>
