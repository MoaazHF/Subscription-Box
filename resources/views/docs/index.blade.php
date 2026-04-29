@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-[1200px] space-y-8">
        <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
            <div class="bg-[linear-gradient(150deg,#0f172a_0%,#1e293b_42%,#334155_100%)] p-8 sm:p-10">
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-white/80">Documentation Center</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-bold tracking-[-0.04em] text-white sm:text-5xl">Enterprise documentation for resources, support, and company policies.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/85">Single-source documentation surface with tabbed navigation and topic-level anchors for quick access.</p>
            </div>
        </div>

        <div class="air-float rounded-[28px] border border-hairline bg-canvas p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-3" role="tablist" aria-label="Documentation sections">
                @foreach ($tabs as $tabKey => $tab)
                    <button
                        type="button"
                        id="tab-{{ $tabKey }}"
                        data-doc-tab-trigger="{{ $tabKey }}"
                        class="doc-tab-trigger inline-flex cursor-pointer items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ $tabKey === $activeTab ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}"
                        aria-controls="panel-{{ $tabKey }}"
                        aria-selected="{{ $tabKey === $activeTab ? 'true' : 'false' }}"
                        role="tab"
                    >
                        {{ $tab['title'] }}
                    </button>
                @endforeach
            </div>

            <div class="mt-8 space-y-8">
                @foreach ($tabs as $tabKey => $tab)
                    <div
                        id="panel-{{ $tabKey }}"
                        data-doc-tab-panel="{{ $tabKey }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $tabKey }}"
                        class="space-y-6 {{ $tabKey === $activeTab ? '' : 'hidden' }}"
                    >
                        <div>
                            <h2 class="text-3xl font-bold tracking-[-0.03em] text-ink">{{ $tab['title'] }}</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-7 text-ash">{{ $tab['description'] }}</p>
                        </div>

                        <div class="grid gap-5 lg:grid-cols-2">
                            @foreach ($tab['topics'] as $topic)
                                <article id="{{ $topic['id'] }}" class="rounded-[24px] border border-hairline bg-[linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)] p-6 scroll-mt-32">
                                    <div class="flex items-start justify-between gap-4">
                                        <h3 class="text-xl font-bold tracking-[-0.02em] text-ink">{{ $topic['title'] }}</h3>
                                        <span class="rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold text-ink">Live</span>
                                    </div>
                                    <p class="mt-3 text-sm leading-7 text-ash">{{ $topic['summary'] }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-rausch">{{ $topic['metadata'] }}</p>
                                    <ul class="mt-4 space-y-2 text-sm text-ash">
                                        @foreach ($topic['details'] as $detail)
                                            <li class="flex items-start gap-2">
                                                <span class="mt-1.5 inline-block h-1.5 w-1.5 rounded-full bg-rausch"></span>
                                                <span>{{ $detail }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const triggers = Array.from(document.querySelectorAll('[data-doc-tab-trigger]'));
            const panels = Array.from(document.querySelectorAll('[data-doc-tab-panel]'));

            if (!triggers.length || !panels.length) {
                return;
            }

            const activateTab = function (tabKey, syncUrl = true) {
                triggers.forEach(function (trigger) {
                    const isActive = trigger.dataset.docTabTrigger === tabKey;
                    trigger.classList.toggle('border-ink', isActive);
                    trigger.classList.toggle('bg-ink', isActive);
                    trigger.classList.toggle('text-white', isActive);
                    trigger.classList.toggle('border-hairline', !isActive);
                    trigger.classList.toggle('bg-canvas', !isActive);
                    trigger.classList.toggle('text-ink', !isActive);
                    trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.dataset.docTabPanel !== tabKey);
                });

                if (syncUrl) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tabKey);
                    window.history.replaceState({}, '', url.toString());
                }
            };

            triggers.forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    activateTab(trigger.dataset.docTabTrigger);
                });
            });

            const initialTab = new URLSearchParams(window.location.search).get('tab') || '{{ $activeTab }}';
            const hasInitialTab = triggers.some(function (trigger) {
                return trigger.dataset.docTabTrigger === initialTab;
            });

            activateTab(hasInitialTab ? initialTab : 'resources', false);

            if (window.location.hash) {
                const hashTarget = document.querySelector(window.location.hash);
                if (hashTarget) {
                    hashTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    </script>
@endpush
