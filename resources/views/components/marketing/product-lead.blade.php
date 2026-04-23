<?php

declare(strict_types=1);

use App\DTOs\SubmissionData;
use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Services\PrefillService;
use App\Services\SubmissionRecorder;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Component;

new class extends Component
{
    public string $pageKey = '';

    public string $productName = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public ?string $journeyToken = null;

    public ?string $journeyMessage = null;

    /** @var array<string, string> */
    public array $journeyFields = [];

    /** @var array<string, string|null> */
    public array $tracking = [];

    public function mount(string $pageKey, string $productName): void
    {
        $this->pageKey = $pageKey;
        $this->productName = $productName;
        $this->tracking = [
            'utm_source' => request()->string('utm_source')->toString() ?: null,
            'utm_medium' => request()->string('utm_medium')->toString() ?: null,
            'utm_campaign' => request()->string('utm_campaign')->toString() ?: null,
            'utm_term' => request()->string('utm_term')->toString() ?: null,
            'utm_content' => request()->string('utm_content')->toString() ?: null,
            'referrer' => request()->headers->get('referer'),
        ];

        $token = request()->string('t')->toString();

        if ($token === '') {
            return;
        }

        /** @var Journey|null $journey */
        $journey = Journey::query()
            ->with('answers')
            ->where('journey_token', $token)
            ->notExpired()
            ->first();

        if ($journey === null || $journey->assigned_product_key !== $this->pageKey) {
            $this->journeyMessage = 'We could not restore a matching journey token for this page, so this request will start fresh.';

            return;
        }

        $this->journeyToken = $journey->journey_token;
        $this->journeyFields = app(PrefillService::class)->extractSafeFields($journey)->fields;
        $this->journeyMessage = 'Your product match has been carried through. This consultation request will stay linked to the earlier qualification journey.';
    }

    public function submit(SubmissionRecorder $submissionRecorder): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Add the best person for us to contact.',
            'email.required' => 'Add the email address we should use.',
        ]);

        if ($this->journeyToken !== null) {
            /** @var Journey|null $journey */
            $journey = Journey::query()
                ->where('journey_token', $this->journeyToken)
                ->notExpired()
                ->first();

            if ($journey === null) {
                $this->addError('form', 'Your saved journey has expired. Please submit the form again and we will start a fresh request.');

                return;
            }

            if ($journey->status === JourneyStatus::Submitted) {
                $this->addError('form', 'This journey has already been submitted.');

                return;
            }

            if ($journey->assigned_product_key !== $this->pageKey) {
                $this->addError('form', 'The saved journey token does not match this product page.');

                return;
            }
        }

        $serverMeta = array_filter([
            'ip_hash' => hash('sha256', request()->ip() ?? ''),
            'user_agent' => request()->userAgent(),
            'referrer' => $this->tracking['referrer'],
            'utm_source' => $this->tracking['utm_source'],
            'utm_medium' => $this->tracking['utm_medium'],
            'utm_campaign' => $this->tracking['utm_campaign'],
            'utm_term' => $this->tracking['utm_term'],
            'utm_content' => $this->tracking['utm_content'],
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $data = new SubmissionData(
            productKey: $this->pageKey,
            name: trim($validated['name']),
            email: trim($validated['email']),
            phone: trim((string) $validated['phone']) !== '' ? trim((string) $validated['phone']) : null,
            company: trim((string) $validated['company']) !== '' ? trim((string) $validated['company']) : null,
            metaJson: $serverMeta,
            journeyToken: $this->journeyToken,
        );

        try {
            $submissionRecorder->record($data);
        } catch (UniqueConstraintViolationException) {
            $this->addError('form', 'A consultation request for this journey already exists.');

            return;
        }

        $this->redirectRoute('thanks', navigate: true);
    }
};
?>

<div class="panel panel-strong overflow-hidden p-0 shadow-hero">
    <div class="border-b border-white/10 bg-slate-950 px-6 py-5 text-white sm:px-7">
        <p class="eyebrow !text-teal-200/90">Free Strategy Consultation</p>
        <h2 class="mt-2 font-display text-2xl font-semibold leading-tight">Talk to INFX about {{ $productName }}.</h2>
        <p class="mt-2 text-sm text-slate-300">A specialist will review your request, the buying context, and the most practical next step for rollout.</p>
    </div>

    <div class="space-y-5 px-6 py-6 sm:px-7">
        @if($journeyMessage)
            <div class="rounded-3xl border border-teal-200 bg-teal-50 px-4 py-4 text-sm leading-6 text-teal-800">
                {{ $journeyMessage }}
            </div>
        @endif

        @if($journeyFields !== [])
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Captured Context</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($journeyFields as $fieldKey => $value)
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-700 shadow-xs">
                            {{ str($fieldKey)->replace('_', ' ')->headline() }}: {{ $value }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <form wire:submit="submit" class="space-y-4">
            <div>
                <label for="lead-name" class="mb-2 block text-sm font-medium text-slate-700">Full Name</label>
                <input wire:model.blur="name" id="lead-name" type="text" placeholder="Jane Smith" class="field-input">
                @error('name')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="lead-email" class="mb-2 block text-sm font-medium text-slate-700">Work Email</label>
                <input wire:model.blur="email" id="lead-email" type="email" placeholder="jane@company.co.za" class="field-input">
                @error('email')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="lead-phone" class="mb-2 block text-sm font-medium text-slate-700">Phone Number</label>
                    <input wire:model.blur="phone" id="lead-phone" type="tel" placeholder="+27 82 123 4567" class="field-input">
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lead-company" class="mb-2 block text-sm font-medium text-slate-700">Company</label>
                    <input wire:model.blur="company" id="lead-company" type="text" placeholder="Acme (Pty) Ltd" class="field-input">
                    @error('company')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @error('form')
                <p class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-linear-to-r from-teal-500 to-cyan-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span wire:loading.remove wire:target="submit">Request My Consultation</span>
                <span wire:loading wire:target="submit">Sending Your Request...</span>
            </button>
        </form>

        <p class="text-center text-xs text-slate-400">POPIA-conscious contact capture. No pressure. Clear next steps within one business day.</p>
    </div>
</div>
