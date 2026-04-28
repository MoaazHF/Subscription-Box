@php($title = 'Customize Box')

@extends('layouts.app')

@section('content')

    <section class="space-y-8" data-swap-root>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('boxes.show', $box->id) }}" class="air-button-secondary">Back to box</a>
            <span class="{{ $isLocked ? 'inline-flex items-center rounded-full border border-danger/15 bg-danger/5 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-danger' : 'air-chip-dark' }}">
                {{ $isLocked ? 'Customization locked' : 'Customization open' }}
            </span>
        </div>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <div class="air-panel overflow-hidden">
                    <div class="air-photo flex min-h-[280px] flex-col justify-between bg-[radial-gradient(circle_at_top_right,_rgba(255,56,92,0.18),_transparent_34%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)] p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="air-chip">{{ ucfirst($box->shipping_tier) }} shipping tier</span>
                            <span class="air-chip">{{ $box->items->count() }} current item{{ $box->items->count() === 1 ? '' : 's' }}</span>
                        </div>

                        <div>
                            <p class="air-kicker">Team 2 customization</p>
                            <h1 class="mt-3 text-4xl font-semibold tracking-[-0.03em] text-ink">
                                {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }} box
                            </h1>
                            <p class="mt-3 max-w-2xl text-sm leading-7 text-ash">Subscribers can swap or remove items until the lock date. The page shows the remaining window, weight effect, and current contents in one place.</p>
                        </div>
                    </div>
                </div>

                <div class="air-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="air-kicker">Current items</p>
                            <h2 class="air-title">Swap or remove before lock.</h2>
                        </div>
                        @if (! $isLocked && $hoursUntilLock < 48)
                            <span class="inline-flex items-center rounded-full border border-danger/15 bg-danger/5 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-danger">
                                Less than 48 hours left
                            </span>
                        @endif
                    </div>

                    <div class="mt-8 grid gap-5 md:grid-cols-2">
                        @forelse ($box->items as $item)
                            <article class="air-grid-card overflow-hidden">
                                <div class="air-photo flex h-40 items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(255,56,92,0.10),_transparent_30%),linear-gradient(180deg,_#ffffff_0%,_#f7f7f7_100%)]">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full border border-hairline bg-canvas text-2xl text-ink">◫</div>
                                </div>

                                <div class="mt-5 flex h-[calc(100%-10rem)] flex-col">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <h3 class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ $item->name }}</h3>
                                        <span class="air-chip">{{ $item->weight_g }}g</span>
                                    </div>

                                    <p class="mt-3 flex-1 text-sm leading-7 text-ash">{{ $item->description ?? 'No description available.' }}</p>

                                    <div class="mt-5 flex flex-wrap gap-3 border-t border-hairline pt-5">
                                        <button
                                            type="button"
                                            data-open-swap="{{ $item->pivot->id }}"
                                            class="air-button-secondary disabled:cursor-not-allowed disabled:border-hairline disabled:bg-cloud disabled:text-mute"
                                            {{ $isLocked ? 'disabled' : '' }}>
                                            Swap
                                        </button>

                                        <form action="{{ route('boxes.remove', ['box' => $box->id, 'boxItem' => $item->pivot->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="air-button-danger disabled:cursor-not-allowed disabled:border-hairline disabled:bg-cloud disabled:text-mute" {{ $isLocked ? 'disabled' : '' }}>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="air-panel-soft md:col-span-2">
                                <p class="text-sm text-ash">This box currently has no items to customize.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="space-y-6 xl:sticky xl:top-32 xl:self-start">
                <div class="air-panel">
                    <p class="air-kicker">Lock and weight</p>
                    <h2 class="air-title">Keep changes inside the active window.</h2>

                    <div class="mt-6 grid gap-3">
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Lock date</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $box->lock_date?->format('M d, Y g:i A') ?? 'N/A' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Remaining time</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $isLocked ? 'Closed' : $box->lock_date?->diffForHumans() }}</p>
                        </div>
                        <div class="air-stat">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Weight</p>
                                    <p class="mt-2 text-sm font-semibold text-ink">{{ number_format($box->total_weight_g / 1000, 2) }} / 3.00 kg</p>
                                </div>
                                <span class="air-chip">{{ ucfirst($box->shipping_tier) }}</span>
                            </div>
                            <div class="mt-4 h-3 rounded-full bg-hairline/70">
                                <div class="{{ $weightBarClass }} h-3 rounded-full transition-all" style="width: {{ $weightPercent }}%"></div>
                            </div>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Replacement pool</p>
                            <p class="mt-2 text-sm font-semibold text-ink">{{ $availableItems->count() }} in-stock item{{ $availableItems->count() === 1 ? '' : 's' }}</p>
                        </div>
                        <div class="air-stat">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Add-ons</p>
                            <form action="{{ route('boxes.add', $box->id) }}" method="POST" class="mt-3">
                                @csrf
                                <select name="new_item_id" class="air-select mb-3 text-xs py-2 px-3" @disabled($isLocked)>
                                    @foreach ($availableItems as $availableItem)
                                        <option value="{{ $availableItem->id }}">{{ $availableItem->name }} (+${{ number_format($availableItem->unit_price, 2) }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="air-button-secondary w-full text-xs py-2" @disabled($isLocked)>Add as extra</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        @if (session('add_warning'))
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4" data-add-warning-modal>
            <div class="absolute inset-0 bg-ink/45" onclick="this.parentElement.remove()"></div>
            <div class="relative z-10 w-full max-w-2xl">
                <div class="air-panel">
                    <p class="air-kicker">Add Extra Item</p>
                    <h3 class="mt-3 text-2xl font-semibold tracking-[-0.02em] text-ink">Warning</h3>
                    <div class="mt-6 rounded-[24px] border border-plus/20 bg-plus/5 p-5 text-sm text-focus">
                        <p class="mt-2 leading-7">{{ session('add_warning.message') }}</p>
                    </div>
                    <form action="{{ route('boxes.add', $box->id) }}" method="POST" class="mt-6 flex flex-wrap gap-3">
                        @csrf
                        <input type="hidden" name="new_item_id" value="{{ session('add_warning.new_item_id') }}">
                        <input type="hidden" name="confirm_allergen" value="1">
                        <button type="submit" class="air-button-primary">Confirm add anyway</button>
                        <button type="button" class="air-button-secondary" onclick="this.closest('[data-add-warning-modal]').remove()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="fixed inset-0 z-50 hidden items-center justify-center px-4" data-swap-modal data-start-open="{{ session('swap_warning') ? 'true' : 'false' }}">
            <div class="absolute inset-0 bg-ink/45" data-modal-overlay></div>

            <div class="relative z-10 w-full max-w-2xl">
                <div class="air-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="air-kicker">Swap item</p>
                            <h3 class="mt-3 text-2xl font-semibold tracking-[-0.02em] text-ink">Choose a replacement item.</h3>
                        </div>
                        <button type="button" data-close-swap class="air-button-secondary">Close</button>
                    </div>

                    @if (session('swap_warning'))
                        <div class="mt-6 rounded-[24px] border border-plus/20 bg-plus/5 p-5 text-sm text-focus">
                            <p class="font-semibold text-ink">Warning</p>
                            <p class="mt-2 leading-7">{{ session('swap_warning.message') }}</p>
                        </div>

                        <form action="{{ route('boxes.swap', $box->id) }}" method="POST" class="mt-6 flex flex-wrap gap-3">
                            @csrf
                            <input type="hidden" name="remove_box_item_id" value="{{ session('swap_warning.remove_box_item_id') }}">
                            <input type="hidden" name="new_item_id" value="{{ session('swap_warning.new_item_id') }}">

                            @if (session('swap_warning.type') === 'rotation')
                                <input type="hidden" name="confirm_rotation" value="1">
                            @else
                                <input type="hidden" name="confirm_allergen" value="1">
                            @endif

                            <button type="submit" class="air-button-primary">Confirm swap anyway</button>
                            <button type="button" data-close-swap class="air-button-secondary">Cancel</button>
                        </form>
                    @else
                        <form action="{{ route('boxes.swap', $box->id) }}" method="POST" class="mt-6 space-y-5">
                            @csrf
                            <input type="hidden" name="remove_box_item_id" value="" data-remove-box-item-input>

                            @if ($availableItems->isEmpty())
                                <div class="rounded-[24px] border border-danger/15 bg-danger/5 p-5 text-sm text-danger">
                                    No replacement items are in stock right now.
                                </div>
                            @endif

                            <div class="space-y-2">
                                <label for="new_item_id" class="text-sm font-semibold text-ink">Replacement item</label>
                                <select id="new_item_id" name="new_item_id" class="air-select" @disabled($availableItems->isEmpty())>
                                    @foreach ($availableItems as $availableItem)
                                        <option value="{{ $availableItem->id }}">{{ $availableItem->name }} · {{ $availableItem->weight_g }}g</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="air-button-primary disabled:cursor-not-allowed disabled:bg-mute" @disabled($availableItems->isEmpty())>Swap item</button>
                                <button type="button" data-close-swap class="air-button-secondary">Cancel</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[data-swap-root]');

            if (! root) {
                return;
            }

            const modal = root.querySelector('[data-swap-modal]');
            const overlay = root.querySelector('[data-modal-overlay]');
            const removeBoxItemInput = root.querySelector('[data-remove-box-item-input]');
            const openerButtons = root.querySelectorAll('[data-open-swap]');
            const closeButtons = root.querySelectorAll('[data-close-swap]');

            if (! modal) {
                return;
            }

            const openModal = (boxItemId = '') => {
                if (removeBoxItemInput) {
                    removeBoxItemInput.value = boxItemId;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            openerButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    openModal(button.dataset.openSwap || '');
                });
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            if (overlay) {
                overlay.addEventListener('click', closeModal);
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && ! modal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            if (modal.dataset.startOpen === 'true') {
                openModal(removeBoxItemInput ? removeBoxItemInput.value : '');
            }
        });
    </script>
@endpush
