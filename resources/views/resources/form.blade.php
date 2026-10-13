@extends('layouts.app')

@section('title', $heading)
@section('breadcrumb', $heading)

@section('content')
    <section class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> CATALOG MANAGEMENT</div>
            <h1>{{ $heading }}</h1>
            <p class="page-subtitle">Fill in the details below. Fields marked with * are required.</p>
        </div>
    </section>

    <div class="form-layout">
        <section class="panel-card form-panel">
            @if ($errors->any())
                <div class="validation-summary">
                    <strong>Please check the following:</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <form method="POST" action="{{ $action }}">
                @csrf
                @if ($method !== 'POST') @method($method) @endif
                <div class="form-grid">
                    @foreach ($fields as $field)
                        @php
                            $fieldType = $field['type'] ?? 'text';
                            $value = old($field['name'], isset($item) ? $item->{$field['name']} : '');
                            if ($fieldType === 'datetime-local' && $value) {
                                $value = \Illuminate\Support\Carbon::parse($value)->format('Y-m-d\TH:i');
                            }
                            if ($field['name'] === 'completed' && $value !== '') {
                                $value = $value ? '1' : '0';
                            }
                            $wide = in_array($fieldType, ['textarea'], true);
                        @endphp
                        <div class="field-group {{ $wide ? 'field-wide' : '' }}">
                            <label class="form-label" for="{{ $field['name'] }}">{{ $field['label'] }} @if ($field['required'])<span>*</span>@endif</label>
                            @if ($fieldType === 'textarea')
                                <textarea class="form-control app-input @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}" rows="4" placeholder="{{ $field['placeholder'] ?? '' }}" @required($field['required'])>{{ $value }}</textarea>
                            @elseif ($fieldType === 'select')
                                <select class="form-select app-input @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}" @required($field['required'] ?? false)>
                                    <option value="">Choose {{ strtolower($field['label']) }}</option>
                                    @if (isset($field['choices']))
                                        @foreach ($field['choices'] as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                        @endforeach
                                    @elseif (isset($field['options']))
                                        @foreach ($options[$field['options']] as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @else
                                <input class="form-control app-input @error($field['name']) is-invalid @enderror" id="{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $fieldType }}" value="{{ $value }}" placeholder="{{ $field['placeholder'] ?? '' }}" @isset($field['min']) min="{{ $field['min'] }}" @endisset @isset($field['max']) max="{{ $field['max'] }}" @endisset @isset($field['step']) step="{{ $field['step'] }}" @endisset @required($field['required'] ?? false)>
                            @endif
                            @error($field['name'])<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="form-actions">
                    <a href="{{ isset($item) ? route($resource.'.show', $item) : route($resource.'.index') }}" class="btn btn-light app-secondary-btn">Cancel</a>
                    <button type="submit" class="btn btn-primary app-primary-btn">{{ isset($item) ? 'Save changes' : 'Create record' }} <span>→</span></button>
                </div>
            </form>
        </section>
        <aside class="form-aside">
            <div class="aside-icon">✦</div>
            <strong>Keep your catalog organized</strong>
            <p>Accurate details help your team find, manage, and publish content with confidence.</p>
            <div class="aside-rule"></div>
            <span>All information can be updated later.</span>
        </aside>
    </div>
@endsection
