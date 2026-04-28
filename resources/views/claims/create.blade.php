<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>File a Claim</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6">

        <a href="{{ route('deliveries.show', $delivery) }}"
           class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-6 transition">
            &larr; Back to Delivery
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

            <div class="mb-6">
                <h1 class="text-xl font-bold text-gray-900">File a Claim</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Report an issue with delivery
                    <span class="font-mono text-gray-700">{{ $delivery->tracking_number ?? substr($delivery->id, 0, 8) }}</span>
                </p>
            </div>

            <form method="POST"
                  action="{{ route('claims.store', $delivery) }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                {{-- Claim Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Claim Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition
                            {{ old('type') === 'damaged' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                            <input type="radio" name="type" value="damaged"
                                   id="type-damaged"
                                   class="sr-only"
                                   {{ old('type', 'damaged') === 'damaged' ? 'checked' : '' }} />
                            <div>
                                <span class="block text-sm font-semibold text-gray-900">📦 Damaged Item</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Items arrived broken or damaged</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition
                            {{ old('type') === 'missing' ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                            <input type="radio" name="type" value="missing"
                                   id="type-missing"
                                   class="sr-only"
                                   {{ old('type') === 'missing' ? 'checked' : '' }} />
                            <div>
                                <span class="block text-sm font-semibold text-gray-900">🔍 Missing Box</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Box was never delivered</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Please describe the issue in detail (min. 10 characters)..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none
                            @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photo Upload --}}
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                        Photo <span class="text-gray-400 font-normal">(optional — JPG/PNG/WebP, max 5MB)</span>
                    </label>
                    <div class="mt-1 flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-8
                        hover:border-indigo-400 transition cursor-pointer"
                         onclick="document.getElementById('photo').click()">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500" id="photo-label">Click to upload a photo</p>
                        </div>
                    </div>
                    <input id="photo" name="photo" type="file"
                           accept="image/jpeg,image/png,image/webp"
                           class="hidden"
                           onchange="document.getElementById('photo-label').textContent = this.files[0]?.name ?? 'Click to upload a photo'" />
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('deliveries.show', $delivery) }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                        Submit Claim
                    </button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Highlight selected claim type card
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', () => {
                document.querySelectorAll('label:has(input[name="type"])').forEach(label => {
                    label.classList.remove('border-red-500', 'bg-red-50');
                    label.classList.add('border-gray-200', 'bg-white');
                });
                const activeLabel = radio.closest('label');
                activeLabel.classList.remove('border-gray-200', 'bg-white');
                activeLabel.classList.add('border-red-500', 'bg-red-50');
            });
        });
    </script>
</body>
</html>
