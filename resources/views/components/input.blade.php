@props([
    'value' => '',
    'type' => 'text',
    'name' => null,
    'id' => null,
    'class' => null,

    'label' => null,
])

@php
    $text_type = [
        'text' => 'text',
        'email' => 'email',
        'password' => 'password',
        'number' => 'number',
        'tel' => 'tel',
        'url' => 'url',
    ];

    $checkbox_type = [
        'checkbox' => 'checkbox'
    ];

    $button_type = [
        'submit' => 'submit',
        'button' => 'button',
        'reset' => 'reset',
    ];
@endphp

@if (in_array($type, array_keys($text_type)))
    <div class="input-wrapper text-input">
        <input {{ $attributes->merge(['type' => $type, 'name' => $name, 'id' => $id, 'value' => $value, 'class' => ($class ?? '')]) }} placeholder=" ">
        <label for="{{ $id }}" class="input-label">
            {{ $label ?? ucfirst($name) }}
        </label>
    </div>
@endif

@if (in_array($type, array_keys($checkbox_type)))
    <div class="input-wrapper checkbox-input">
        <label class="container">
            <input type="checkbox" {{ $attributes->merge(['name' => $name, 'id' => $id, 'checked' => $value, 'class' => ($class ?? '')]) }}>
            <span class="checkmark"></span>{{ $label ?? ucfirst($name) }}
        </label>
    </div>
@endif

@if (in_array($type, array_keys($button_type)))
    <div class="input-wrapper button-input">
        <button {{ $attributes->merge(['type' => $type, 'name' => $name, 'id' => $id, 'class' => ($class ?? '')]) }}>
            {{ $label ?? ucfirst($name) }}
        </button>
    </div>
@endif

