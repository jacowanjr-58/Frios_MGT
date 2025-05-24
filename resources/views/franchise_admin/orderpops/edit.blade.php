

<!-- Shipping Fields (hidden defaults) -->
<input type="hidden" name="ship_to_country" value="US">
<input type="hidden" name="ship_method" value="Standard">

<!-- Visible fields (optional) -->
<div class="mt-4">
    <label>Recipient Name</label>
    <input type="text" name="ship_to_name" class="form-input" value="{{ old('ship_to_name') }}">
    @error('ship_to_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Address Line 1</label>
    <input type="text" name="ship_to_address1" class="form-input" value="{{ old('ship_to_address1') }}">
    @error('ship_to_address1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Address Line 2</label>
    <input type="text" name="ship_to_address2" class="form-input" value="{{ old('ship_to_address2') }}">
</div>
<div class="grid grid-cols-2 gap-4 mt-2">
    <div>
        <label>City</label>
        <input type="text" name="ship_to_city" class="form-input" value="{{ old('ship_to_city') }}">
        @error('ship_to_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
    <div>
        <label>State</label>
        <input type="text" name="ship_to_state" class="form-input" value="{{ old('ship_to_state') }}">
        @error('ship_to_state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>
</div>
<div class="mt-2">
    <label>ZIP Code</label>
    <input type="text" name="ship_to_zip" class="form-input" value="{{ old('ship_to_zip') }}">
    @error('ship_to_zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
<div class="mt-2">
    <label>Phone</label>
    <input type="text" name="ship_to_phone" class="form-input" value="{{ old('ship_to_phone') }}">
</div>
