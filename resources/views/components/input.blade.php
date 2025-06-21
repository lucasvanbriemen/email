@props([
    'value' => '',
    'type' => 'text',
    'name' => null,
    'id' => null,
    'class' => null,

    'label' => null,
])


<div class="input-wrapper">
    <input {{ $attributes->merge(['type' => $type, 'name' => $name, 'id' => $id, 'value' => $value, 'class' => ($class ?? '')]) }} placeholder=" ">
    <label for="{{ $id }}" class="input-label">
        {{ $label ?? ucfirst($name) }}
    </label>
</div>

