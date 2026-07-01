@php
    $selected = $selected ?? '';
    $codes = [
        '+1' => 'US/CA (+1)', '+44' => 'UK (+44)', '+91' => 'India (+91)', '+61' => 'Australia (+61)',
        '+49' => 'Germany (+49)', '+33' => 'France (+33)', '+39' => 'Italy (+39)', '+34' => 'Spain (+34)',
        '+31' => 'Netherlands (+31)', '+46' => 'Sweden (+46)', '+41' => 'Switzerland (+41)', '+351' => 'Portugal (+351)',
        '+971' => 'UAE (+971)', '+966' => 'Saudi Arabia (+966)', '+974' => 'Qatar (+974)', '+965' => 'Kuwait (+965)',
        '+92' => 'Pakistan (+92)', '+880' => 'Bangladesh (+880)', '+94' => 'Sri Lanka (+94)', '+977' => 'Nepal (+977)',
        '+65' => 'Singapore (+65)', '+60' => 'Malaysia (+60)', '+62' => 'Indonesia (+62)', '+63' => 'Philippines (+63)',
        '+66' => 'Thailand (+66)', '+84' => 'Vietnam (+84)', '+81' => 'Japan (+81)', '+82' => 'South Korea (+82)',
        '+86' => 'China (+86)', '+852' => 'Hong Kong (+852)', '+7' => 'Russia (+7)', '+90' => 'Turkey (+90)',
        '+20' => 'Egypt (+20)', '+27' => 'South Africa (+27)', '+234' => 'Nigeria (+234)', '+254' => 'Kenya (+254)',
        '+55' => 'Brazil (+55)', '+52' => 'Mexico (+52)', '+54' => 'Argentina (+54)', '+64' => 'New Zealand (+64)',
        '+353' => 'Ireland (+353)', '+48' => 'Poland (+48)', '+380' => 'Ukraine (+380)', '+972' => 'Israel (+972)',
    ];
@endphp
<option value="">Code</option>
@foreach ($codes as $code => $label)
    <option value="{{ $code }}" {{ $selected === $code ? 'selected' : '' }}>{{ $label }}</option>
@endforeach
