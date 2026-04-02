@php($address = old("{$prefix}_address", []))

<div class="address-grid" data-address-prefix="{{ $prefix }}">
    <div class="field">
        <label for="{{ $prefix }}-region">{{ $title }} Region</label>
        <select
            id="{{ $prefix }}-region"
            name="{{ $prefix }}_address[region_code]"
            data-role="region"
            data-selected-code="{{ $address['region_code'] ?? '' }}"
            required
        >
            <option value="">Select a region</option>
        </select>
        <input type="hidden" name="{{ $prefix }}_address[region_name]" value="{{ $address['region_name'] ?? '' }}" data-role="region-name">
        @error("{$prefix}_address.region_code") <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="{{ $prefix }}-province">{{ $title }} Province</label>
        <select
            id="{{ $prefix }}-province"
            name="{{ $prefix }}_address[province_code]"
            data-role="province"
            data-selected-code="{{ $address['province_code'] ?? '' }}"
            disabled
        >
            <option value="">Select a province</option>
        </select>
        <input type="hidden" name="{{ $prefix }}_address[province_name]" value="{{ $address['province_name'] ?? '' }}" data-role="province-name">
        @error("{$prefix}_address.province_code") <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="{{ $prefix }}-city">{{ $title }} City / Municipality</label>
        <select
            id="{{ $prefix }}-city"
            name="{{ $prefix }}_address[city_code]"
            data-role="city"
            data-selected-code="{{ $address['city_code'] ?? '' }}"
            disabled
            required
        >
            <option value="">Select a city or municipality</option>
        </select>
        <input type="hidden" name="{{ $prefix }}_address[city_name]" value="{{ $address['city_name'] ?? '' }}" data-role="city-name">
        @error("{$prefix}_address.city_code") <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="{{ $prefix }}-barangay">{{ $title }} Barangay</label>
        <select
            id="{{ $prefix }}-barangay"
            name="{{ $prefix }}_address[barangay_code]"
            data-role="barangay"
            data-selected-code="{{ $address['barangay_code'] ?? '' }}"
            disabled
            required
        >
            <option value="">Select a barangay</option>
        </select>
        <input type="hidden" name="{{ $prefix }}_address[barangay_name]" value="{{ $address['barangay_name'] ?? '' }}" data-role="barangay-name">
        @error("{$prefix}_address.barangay_code") <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field full">
        <label for="{{ $prefix }}-street">{{ $title }} Street Address</label>
        <input id="{{ $prefix }}-street" type="text" name="{{ $prefix }}_address[street_address]" value="{{ $address['street_address'] ?? '' }}" placeholder="House number, street, subdivision, landmark" required>
        @error("{$prefix}_address.street_address") <span class="error-text">{{ $message }}</span> @enderror
    </div>

    <div class="field full">
        <label for="{{ $prefix }}-contact">{{ $title }} Contact Number</label>
        <input id="{{ $prefix }}-contact" type="text" name="{{ $prefix }}_address[contact_number]" value="{{ $address['contact_number'] ?? '' }}" placeholder="09XXXXXXXXX or landline" required>
        @error("{$prefix}_address.contact_number") <span class="error-text">{{ $message }}</span> @enderror
    </div>
</div>
