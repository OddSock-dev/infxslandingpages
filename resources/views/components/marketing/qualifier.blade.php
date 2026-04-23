<?php

declare(strict_types=1);

use App\DTOs\AnswerData;
use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Services\QualificationService;
use App\Support\PageCatalog;
use Livewire\Component;

new class extends Component
{
    public int $step = 1;

    public string $selectedProduct = '';

    public bool $consent = false;

    /** @var array<int, array{value: string, label: string, description: string}> */
    public array $productOptions = [];

    /** @var array<string, string|null> */
    public array $tracking = [];

    public function mount(): void
    {
        $this->productOptions = collect(PageCatalog::productNavigation())
            ->map(fn (array $product): array => [
                'value' => $product['page_key'],
                'label' => $product['label'],
                'description' => match ($product['page_key']) {
                    'zoho_one' => 'Best for teams replacing software sprawl with one connected operating system.',
                    'zoho_marketing_plus' => 'Best for growth teams that need unified campaigns and cleaner attribution.',
                    'zoho_workplace' => 'Best for teams rethinking email, collaboration, and internal communication.',
                    default => 'Find the product that best matches the outcome you want.',
                },
            ])->values()->all();

        $this->tracking = [
            'utm_source' => request()->string('utm_source')->toString() ?: null,
            'utm_medium' => request()->string('utm_medium')->toString() ?: null,
            'utm_campaign' => request()->string('utm_campaign')->toString() ?: null,
            'utm_term' => request()->string('utm_term')->toString() ?: null,
            'utm_content' => request()->string('utm_content')->toString() ?: null,
            'referrer' => request()->headers->get('referer'),
        ];
    }

    public function nextStep(): void
    {
        $this->validate([
            'selectedProduct' => ['required', 'string'],
        ], [
            'selectedProduct.required' => 'Choose the Zoho journey you want help with first.',
        ]);

        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->resetErrorBag();
        $this->step = 1;
    }

    public function qualify(QualificationService $qualificationService): void
    {
        $validated = $this->validate([
            'selectedProduct' => ['required', 'string'],
            'consent' => ['accepted'],
        ], [
            'selectedProduct.required' => 'Choose the Zoho journey you want help with first.',
            'consent.accepted' => 'Please confirm that we can contact you about the right Zoho solution.',
        ]);

        $journey = Journey::create([
            'source_page_key' => 'landing',
            'status' => JourneyStatus::Qualifying,
            'utm_source' => $this->tracking['utm_source'],
            'utm_medium' => $this->tracking['utm_medium'],
            'utm_campaign' => $this->tracking['utm_campaign'],
            'utm_term' => $this->tracking['utm_term'],
            'utm_content' => $this->tracking['utm_content'],
            'referrer' => $this->tracking['referrer'],
            'ip_hash' => hash('sha256', request()->ip() ?? ''),
            'user_agent' => request()->userAgent(),
            'consent_at' => now(),
        ]);

        $now = now()->toDateTimeString();

        JourneyAnswer::insert([
            'journey_id' => $journey->id,
            'step_key' => 'step_1',
            'field_key' => 'product_interest',
            'value' => $validated['selectedProduct'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $productKey = $qualificationService->route([
            new AnswerData(
                stepKey: 'step_1',
                fieldKey: 'product_interest',
                value: $validated['selectedProduct'],
            ),
        ]);

        $journey->update([
            'assigned_product_key' => $productKey,
            'status' => JourneyStatus::Routed,
        ]);

        $this->redirectRoute('products.show', [
            'slug' => PageCatalog::productRouteSegment($productKey),
            't' => $journey->journey_token,
        ], navigate: true);
    }
};
?>

<div class="panel panel-strong overflow-hidden p-0 shadow-hero">
    <div class="border-b border-white/10 bg-slate-950 px-6 py-5 text-white sm:px-7">
        <p class="eyebrow !text-orange-200/90">Free Match Flow</p>
        <h2 class="mt-2 font-display text-2xl font-semibold leading-tight">Get routed to the right Zoho journey in under two minutes.</h2>
        <p class="mt-2 text-sm text-slate-300">Choose the best-fit path, confirm your interest, and we carry the context through to the consultation request.</p>
    </div>

    <div class="space-y-6 px-6 py-6 sm:px-7">
        <div class="flex items-center gap-3">
            @foreach([1 => 'Pick a journey', 2 => 'Confirm contact'] as $stepNumber => $stepLabel)
                <div class="flex items-center gap-3">
                    <div class="{{ $step >= $stepNumber ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-400' }} flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold transition-colors">
                        {{ $stepNumber }}
                    </div>
                    <span class="hidden text-sm font-medium text-slate-600 sm:inline">{{ $stepLabel }}</span>
                </div>
                @if($stepNumber === 1)
                    <div class="h-px flex-1 bg-slate-200"></div>
                @endif
            @endforeach
        </div>

        @if($step === 1)
            <div class="space-y-3">
                @foreach($productOptions as $option)
                    <button
                        type="button"
                        wire:click="$set('selectedProduct', '{{ $option['value'] }}')"
                        class="{{ $selectedProduct === $option['value'] ? 'border-orange-400 bg-orange-50 shadow-soft' : 'border-slate-200 bg-white hover:border-orange-200 hover:bg-orange-50/40' }} w-full rounded-3xl border p-5 text-left transition-all"
                    >
                        <div class="flex items-start gap-4">
                            <div class="{{ $selectedProduct === $option['value'] ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-500' }} mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl text-sm font-semibold transition-colors">
                                {{ strtoupper(substr($option['label'], 0, 1)) }}
                            </div>
                            <div class="space-y-1">
                                <div class="text-base font-semibold text-slate-950">{{ $option['label'] }}</div>
                                <div class="text-sm leading-6 text-slate-600">{{ $option['description'] }}</div>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
            @error('selectedProduct')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <button
                type="button"
                wire:click="nextStep"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-linear-to-r from-orange-500 to-amber-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5"
            >
                Continue To Confirmation
            </button>
        @else
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Selected Journey</p>
                <p class="mt-2 text-lg font-semibold text-slate-950">
                    {{ data_get(collect($productOptions)->firstWhere('value', $selectedProduct), 'label', 'Zoho Journey') }}
                </p>
            </div>

            <label class="flex items-start gap-3 rounded-3xl border border-slate-200 bg-white p-5">
                <input wire:model="consent" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm leading-6 text-slate-600">
                    I agree that INFX Solutions may contact me about the Zoho product or service path that best matches my needs.
                </span>
            </label>
            @error('consent')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-col gap-3 sm:flex-row">
                <button
                    type="button"
                    wire:click="previousStep"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    Back
                </button>
                <button
                    type="button"
                    wire:click="qualify"
                    wire:loading.attr="disabled"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl bg-linear-to-r from-orange-500 to-amber-500 px-5 py-3 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <span wire:loading.remove wire:target="qualify">Show My Best-Fit Path</span>
                    <span wire:loading wire:target="qualify">Matching Your Journey...</span>
                </button>
            </div>
        @endif

        <p class="text-center text-xs text-slate-400">POPIA-conscious lead capture. No spam. Clear next steps.</p>
    </div>
</div>
