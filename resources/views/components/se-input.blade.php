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
            <input type="checkbox" x-model="self.data" :name="name" {{ $attributes->merge() }}
                @if ($default) checked @endif id="checkbox-{{ $name }}"
                @input="checkError($el)">
            <input type="hidden" {{ $attributes->merge() }} :name="name" value="0">
            <label for="checkbox-{{ $name }}">{{ $label }}</label>
        </div>
        <p>{{ $description }}</p>
    @elseif ($type == 'select')
        @if ($label)
            <label for="">{{ $label }}</label>
        @endif

        @if ($description)
            {!! $description !!}
        @endif


        <select :name="name" x-model="self.data" x-ref="input" {{ $attributes->merge() }}
            :class="{ 'border-red-500! border-dashed': self.error }" @input="checkError($el)">
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
            :class="{ 'border-red-500! border-dashed': self.error }" value="{{ $default ?? '' }}"
            {{ $attributes->merge() }} @input="checkError($el)">
    @endif


    <small x-text="self.error" x-show="self.error"></small>
</div>
