<?php

declare(strict_types=1);

use App\DTOs\ProductRecommendationData;
use App\DTOs\SubmissionData;
use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Rules\TurnstileToken;
use App\Services\PrefillService;
use App\Services\ProductRecommendationService;
use App\Services\SubmissionRecorder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

// @phpstan-ignore-next-line
new class extends Component
{
    public string $pageKey = '';

    public string $productName = '';

    public ?string $trialUrl = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $companySizeBand = '';

    public string $implementationTimeline = '';

    public string $currentEnvironment = '';

    public string $priorityOutcome = '';

    public ?string $journeyToken = null;

    public ?string $journeyMessage = null;

    public ?string $recommendationHeading = null;

    public string $turnstileToken = '';

    public bool $turnstileEnabled = false;

    public bool $qualificationComplete = false;

    public bool $showConsultationForm = false;

    /** @var array<string, string> */
    public array $journeyFields = [];

    /** @var array<string, string> */
    public array $qualificationSummary = [];

    /** @var list<string> */
    public array $recommendationReasons = [];

    /** @var array<int, array{value: string, label: string}> */
    public array $companySizeOptions = [];

    /** @var array<int, array{value: string, label: string}> */
    public array $timelineOptions = [];

    /** @var array<int, array{value: string, label: string}> */
    public array $environmentOptions = [];

    /** @var array<int, array{value: string, label: string}> */
    public array $outcomeOptions = [];

    /** @var array<string, string|null> */
    public array $tracking = [];

    public function mount(string $pageKey, string $productName, ?string $trialUrl = null): void
    {
        $this->pageKey = $pageKey;
        $this->productName = $productName;
        $this->trialUrl = $trialUrl;
        $this->companySizeOptions = [
            ['value' => '1_10', 'label' => '1-10 people'],
            ['value' => '11_50', 'label' => '11-50 people'],
            ['value' => '51_200', 'label' => '51-200 people'],
            ['value' => '200_plus', 'label' => '200+ people'],
        ];
        $this->timelineOptions = [
            ['value' => 'now', 'label' => 'Immediately'],
            ['value' => 'this_quarter', 'label' => 'This quarter'],
            ['value' => 'this_year', 'label' => 'This year'],
            ['value' => 'exploring', 'label' => 'Still exploring'],
        ];
        $this->turnstileEnabled = (bool) config('turnstile.enabled')
            && filled(config('turnstile.site_key'))
            && filled(config('turnstile.secret_key'));
        $this->tracking = [
            'utm_source' => request()->string('utm_source')->toString() ?: null,
            'utm_medium' => request()->string('utm_medium')->toString() ?: null,
            'utm_campaign' => request()->string('utm_campaign')->toString() ?: null,
            'utm_term' => request()->string('utm_term')->toString() ?: null,
            'utm_content' => request()->string('utm_content')->toString() ?: null,
            'referrer' => request()->headers->get('referer'),
        ];
        $this->environmentOptions = $this->environmentOptionsFor($pageKey);
        $this->outcomeOptions = $this->outcomeOptionsFor($pageKey);

        $token = request()->string('t')->toString();

        if ($token === '') {
            $this->journeyMessage = "Answer four quick questions below so we can suggest the best next step and unlock your free-trial option if {$this->productName} is a good fit.";

            return;
        }

        /** @var Journey|null $journey */
        $journey = Journey::query()
            ->with('answers')
            ->where('journey_token', $token)
            ->notExpired()
            ->first();

        if ($journey === null || $journey->assigned_product_key !== $this->pageKey) {
            $this->journeyMessage = 'We could not load your previous answers for this page, so we will start fresh. Answer four quick questions below and we will suggest the best next step.';

            return;
        }

        $this->journeyToken = $journey->journey_token;
        $this->journeyFields = app(PrefillService::class)->extractSafeFields($journey)->fields;

        /** @var array{name?: string, email?: string, phone?: string, company?: string}|null $contact */
        $contact = $journey->contact_json;

        if (is_array($contact)) {
            $this->name = (string) ($contact['name'] ?? '');
            $this->email = (string) ($contact['email'] ?? '');
            $this->phone = (string) ($contact['phone'] ?? '');
            $this->company = (string) ($contact['company'] ?? '');
        }

        if (isset($this->journeyFields['timeline'])) {
            $this->implementationTimeline = $this->journeyFields['timeline'];
        }

        /** @var ProductRecommendationData|null $recommendation */
        $recommendation = app(ProductRecommendationService::class)->build($this->productName, $this->journeyFields);

        $this->recommendationHeading = $recommendation?->headline;
        $this->recommendationReasons = $recommendation !== null ? $recommendation->reasons : [];
        $this->journeyMessage = 'We used your earlier answers to get your next step ready.';
        $this->qualificationComplete = true;
    }

    public function completeQualification(): void
    {
        $this->resetErrorBag();

        $this->validate($this->qualificationRules(), $this->qualificationMessages());

        $this->qualificationComplete = true;
        $this->showConsultationForm = false;
        $this->qualificationSummary = $this->selectedQualificationLabels();
        $this->journeyMessage = 'Thanks — your answers are in. Your free-trial and consultation options are ready below.';
    }

    public function requestConsultation(): void
    {
        $this->resetErrorBag('qualification');

        if (! $this->qualificationComplete) {
            $this->addError('qualification', 'Answer the four quick questions before choosing your next step.');

            return;
        }

        $this->showConsultationForm = true;
    }

    public function backToOptions(): void
    {
        $this->showConsultationForm = false;
    }

    public function submit(SubmissionRecorder $submissionRecorder): void
    {
        if (! $this->qualificationComplete) {
            $this->addError('qualification', 'Answer the four quick questions before choosing your next step.');

            return;
        }

        $this->ensureNotRateLimited();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'companySizeBand' => ['required', 'string'],
            'implementationTimeline' => ['required', 'string'],
            'currentEnvironment' => ['required', 'string'],
            'priorityOutcome' => ['required', 'string'],
        ];

        if ($this->turnstileEnabled) {
            $rules['turnstileToken'] = ['required', 'string', new TurnstileToken()];
        }

        $validated = $this->validate($rules, [
            'name.required' => 'Add the best person for us to contact.',
            'email.required' => 'Add the email address we should use.',
            'companySizeBand.required' => 'Tell us how many people will use this first.',
            'implementationTimeline.required' => 'Tell us when you want this live.',
            'currentEnvironment.required' => 'Tell us what the current setup looks like today.',
            'priorityOutcome.required' => 'Tell us the main result you want from this product.',
            'turnstileToken.required' => 'Please complete the verification challenge.',
        ]);
        /** @var array{name: string, email: string, phone: string|null, company: string|null, companySizeBand: string, implementationTimeline: string, currentEnvironment: string, priorityOutcome: string} $validated */

        $journey = null;

        if ($this->journeyToken !== null) {
            /** @var Journey|null $journey */
            $journey = Journey::query()
                ->where('journey_token', $this->journeyToken)
                ->notExpired()
                ->first();

            if ($journey === null) {
                $this->addError('form', 'Your saved link has expired. Please submit the form again and we will start a fresh request.');

                return;
            }

            if ($journey->status === JourneyStatus::Submitted) {
                $this->addError('form', 'This request has already been submitted.');

                return;
            }

            if ($journey->assigned_product_key !== $this->pageKey) {
                $this->addError('form', 'This saved link does not match this product page.');

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
            'company_size_band' => $validated['companySizeBand'],
            'implementation_timeline' => $validated['implementationTimeline'],
            'current_environment' => $validated['currentEnvironment'],
            'priority_outcome' => $validated['priorityOutcome'],
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $phone = $validated['phone'] !== null ? trim($validated['phone']) : null;
        $company = $validated['company'] !== null ? trim($validated['company']) : null;

        if ($journey !== null) {
            $journey->update([
                'contact_json' => array_filter([
                    'name' => trim($validated['name']),
                    'email' => trim($validated['email']),
                    'phone' => $phone !== '' ? $phone : null,
                    'company' => $company !== '' ? $company : null,
                ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            ]);
        }

        $data = new SubmissionData(
            productKey: $this->pageKey,
            name: trim($validated['name']),
            email: trim($validated['email']),
            phone: $phone !== '' ? $phone : null,
            company: $company !== '' ? $company : null,
            metaJson: $serverMeta,
            journeyToken: $this->journeyToken,
        );

        try {
            $submissionRecorder->record($data);
        } catch (UniqueConstraintViolationException) {
            $this->addError('form', 'A consultation request for this selection already exists.');

            return;
        }

        RateLimiter::clear($this->rateLimiterKey(trim($validated['email'])));

        $this->redirectRoute('thanks', navigate: true);
    }

    /**
     * @return array<string, list<string>>
     */
    private function qualificationRules(): array
    {
        return [
            'companySizeBand' => ['required', 'string'],
            'implementationTimeline' => ['required', 'string'],
            'currentEnvironment' => ['required', 'string'],
            'priorityOutcome' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function qualificationMessages(): array
    {
        return [
            'companySizeBand.required' => 'Tell us how many people will use this first.',
            'implementationTimeline.required' => 'Tell us when you want this live.',
            'currentEnvironment.required' => 'Tell us what the current setup looks like today.',
            'priorityOutcome.required' => 'Tell us the main result you want from this product.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function selectedQualificationLabels(): array
    {
        return array_filter([
            'Team Size' => $this->labelForValue($this->companySizeOptions, $this->companySizeBand),
            'Timeline' => $this->labelForValue($this->timelineOptions, $this->implementationTimeline),
            'Current Environment' => $this->labelForValue($this->environmentOptions, $this->currentEnvironment),
            'Priority Outcome' => $this->labelForValue($this->outcomeOptions, $this->priorityOutcome),
        ], static fn (?string $value): bool => $value !== null && $value !== '');
    }

    /**
     * @param  array<int, array{value: string, label: string}>  $options
     */
    private function labelForValue(array $options, string $value): ?string
    {
        foreach ($options as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return null;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function environmentOptionsFor(string $pageKey): array
    {
        return match ($pageKey) {
            'zoho_marketing_plus' => [
                ['value' => 'spreadsheet_campaigns', 'label' => 'Campaigns still live in spreadsheets and separate tools'],
                ['value' => 'crm_plus_tools', 'label' => 'You have a CRM, but marketing still feels disconnected'],
                ['value' => 'early_stage_marketing', 'label' => 'You are building a more organised marketing team'],
            ],
            'zoho_workplace' => [
                ['value' => 'google_microsoft_mix', 'label' => 'Google Workspace or Microsoft 365 is mixed with extra tools'],
                ['value' => 'legacy_mail_and_files', 'label' => 'Mail, files, and chat still feel scattered'],
                ['value' => 'privacy_first_shift', 'label' => 'You want a privacy-first alternative'],
            ],
            default => [
                ['value' => 'multiple_line_of_business_tools', 'label' => 'Too many separate business tools are in use'],
                ['value' => 'partially_on_zoho', 'label' => 'Some teams use Zoho, but most of the business does not'],
                ['value' => 'manual_ops_and_reporting', 'label' => 'Too much work and reporting is still manual'],
            ],
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function outcomeOptionsFor(string $pageKey): array
    {
        return match ($pageKey) {
            'zoho_marketing_plus' => [
                ['value' => 'attribution_clarity', 'label' => 'Clearer reporting on what is working'],
                ['value' => 'faster_campaign_delivery', 'label' => 'Launch campaigns faster with less tool switching'],
                ['value' => 'journey_automation', 'label' => 'Better customer communication and marketing automation'],
            ],
            'zoho_workplace' => [
                ['value' => 'cleaner_collaboration', 'label' => 'Smoother collaboration across mail, docs, chat, and meetings'],
                ['value' => 'migration_with_less_disruption', 'label' => 'Move away from the current suite with less disruption'],
                ['value' => 'privacy_and_control', 'label' => 'Stronger privacy and more control'],
            ],
            default => [
                ['value' => 'shared_operating_system', 'label' => 'One connected setup across the business'],
                ['value' => 'executive_visibility', 'label' => 'A clearer view across teams, delivery, and finance'],
                ['value' => 'automation_at_scale', 'label' => 'Less manual work and easier growth'],
            ],
        };
    }

    private function ensureNotRateLimited(): void
    {
        $key = $this->rateLimiterKey(trim($this->email));

        if (RateLimiter::tooManyAttempts($key, 4)) {
            throw ValidationException::withMessages([
                'form' => 'Too many consultation attempts were sent in a short period. Please wait a few minutes and try again.',
            ]);
        }

        RateLimiter::hit($key, 15 * 60);
    }

    private function rateLimiterKey(string $email): string
    {
        return 'product-lead:'.hash('sha256', implode('|', [
            request()->ip() ?? 'unknown',
            $this->pageKey,
            $email !== '' ? strtolower($email) : 'anonymous',
        ]));
    }
};
?>

<div class="panel panel-strong overflow-hidden p-0 shadow-hero">
    <div class="border-b border-white/10 bg-slate-950 px-6 py-5 text-white sm:px-7">
        <p class="eyebrow !text-teal-200/90">{{ $qualificationComplete ? 'Your Next Steps' : 'Quick Product Questions' }}</p>
        <h2 class="mt-2 font-display text-2xl font-semibold leading-tight text-balance">
            {{ $qualificationComplete ? "Choose how you would like to continue with {$productName}." : "Answer four quick questions so we can unlock the right next step for {$productName}." }}
        </h2>
        <p class="mt-2 text-sm text-slate-300">
            {{ $qualificationComplete ? 'Your best next step is ready. Start a free trial for hands-on access or ask INFX to help you plan the rollout first.' : 'These answers help us unlock the right next step, including a free trial when hands-on access makes sense for your team.' }}
        </p>
    </div>

    <div class="space-y-5 px-6 py-6 sm:px-7">
        @if($recommendationHeading)
            <div class="rounded-3xl border border-teal-200 bg-teal-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-teal-600">Suggested Option</p>
                <h3 class="mt-2 text-lg font-semibold text-slate-950">{{ $recommendationHeading }}</h3>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                    @foreach($recommendationReasons as $reason)
                        <li class="flex gap-3">
                            <span class="mt-1 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-500 text-xs font-semibold text-white">✓</span>
                            <span>{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($journeyMessage)
            <div class="rounded-3xl border border-teal-200 bg-teal-50 px-4 py-4 text-sm leading-6 text-teal-800">
                {{ $journeyMessage }}
            </div>
        @endif

        @if($journeyFields !== [])
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Your Earlier Answers</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($journeyFields as $fieldKey => $value)
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-700 shadow-xs">
                            {{ str($fieldKey)->replace('_', ' ')->headline() }}: {{ str($value)->replace('_', ' ')->headline() }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($qualificationSummary !== [])
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Your Answers</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($qualificationSummary as $label => $value)
                        <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-700 shadow-xs">
                            {{ $label }}: {{ $value }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @unless($qualificationComplete)
            <div class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="lead-company-size" class="mb-2 block text-sm font-medium text-slate-700">How many people will use this?</label>
                        <select wire:model.blur="companySizeBand" id="lead-company-size" class="field-input">
                            <option value="">Select one</option>
                            @foreach($companySizeOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        @error('companySizeBand')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lead-timeline" class="mb-2 block text-sm font-medium text-slate-700">When would you like to get started?</label>
                        <select wire:model.blur="implementationTimeline" id="lead-timeline" class="field-input">
                            <option value="">Select one</option>
                            @foreach($timelineOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                        @error('implementationTimeline')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="lead-environment" class="mb-2 block text-sm font-medium text-slate-700">What does the current setup look like?</label>
                    <select wire:model.blur="currentEnvironment" id="lead-environment" class="field-input">
                        <option value="">Select one</option>
                        @foreach($environmentOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('currentEnvironment')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lead-outcome" class="mb-2 block text-sm font-medium text-slate-700">What matters most to you?</label>
                    <select wire:model.blur="priorityOutcome" id="lead-outcome" class="field-input">
                        <option value="">Select one</option>
                        @foreach($outcomeOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    @error('priorityOutcome')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                @error('qualification')
                    <p class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</p>
                @enderror

                <button
                    type="button"
                    wire:click="completeQualification"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-linear-to-r from-teal-500 to-cyan-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5"
                >
                    Show My Options
                </button>
            </div>
        @else
            <div class="rounded-3xl border border-slate-200 bg-white p-5">
                <div class="space-y-3 sm:flex sm:items-center sm:justify-between sm:gap-4 sm:space-y-0">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Choose Your Next Step</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Start the free trial when you want hands-on access first. Choose a consultation when you want rollout advice, migration planning, or a second opinion before you begin.</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:w-auto sm:flex-row">
                        <button
                            type="button"
                            wire:click="requestConsultation"
                            class="{{ $showConsultationForm ? 'border-teal-400 bg-teal-50 text-teal-700' : 'border-slate-200 bg-slate-950 text-white hover:bg-slate-900' }} inline-flex items-center justify-center rounded-2xl border px-5 py-3 text-sm font-semibold transition"
                        >
                            Request Consultation
                        </button>

                        @if($trialUrl)
                            <a href="{{ $trialUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-2xl border border-teal-200 bg-teal-50 px-5 py-3 text-sm font-semibold text-teal-700 transition hover:border-teal-300 hover:bg-teal-100">
                                Start Free Trial
                            </a>
                        @endif
                    </div>
                </div>

                @if($trialUrl)
                    <p class="mt-4 text-sm leading-6 text-slate-600">The quick fit check unlocks your free trial with a clearer starting point, so you can explore the product around your priorities instead of starting cold.</p>
                @endif
            </div>

            @error('qualification')
                <p class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror

            @if($showConsultationForm)
                <form wire:submit="submit" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Consultation</p>
                        <h3 class="text-lg font-semibold text-slate-950">Tell us who should hear back from us.</h3>
                        <p class="text-sm leading-6 text-slate-600">We will use this information to tailor our reply and help you move forward with confidence.</p>
                    </div>

                    @if($companySizeBand === '' || $implementationTimeline === '' || $currentEnvironment === '' || $priorityOutcome === '')
                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4">
                            <p class="text-sm font-medium text-slate-700">Before we send this, add a little more detail about what you need.</p>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="consult-company-size" class="mb-2 block text-sm font-medium text-slate-700">How many people will use this?</label>
                                    <select wire:model.blur="companySizeBand" id="consult-company-size" class="field-input">
                                        <option value="">Select one</option>
                                        @foreach($companySizeOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('companySizeBand')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="consult-timeline" class="mb-2 block text-sm font-medium text-slate-700">When would you like to get started?</label>
                                    <select wire:model.blur="implementationTimeline" id="consult-timeline" class="field-input">
                                        <option value="">Select one</option>
                                        @foreach($timelineOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('implementationTimeline')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="consult-environment" class="mb-2 block text-sm font-medium text-slate-700">What does the current setup look like?</label>
                                <select wire:model.blur="currentEnvironment" id="consult-environment" class="field-input">
                                    <option value="">Select one</option>
                                    @foreach($environmentOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('currentEnvironment')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consult-outcome" class="mb-2 block text-sm font-medium text-slate-700">What matters most to you?</label>
                                <select wire:model.blur="priorityOutcome" id="consult-outcome" class="field-input">
                                    <option value="">Select one</option>
                                    @foreach($outcomeOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('priorityOutcome')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

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

                    @if($turnstileEnabled)
                        <div
                            x-data
                            x-init="window.infxTurnstileMount($refs.turnstile, '{{ config('turnstile.site_key') }}', (token) => $wire.set('turnstileToken', token), () => $wire.set('turnstileToken', ''))"
                            class="rounded-3xl border border-slate-200 bg-white p-4"
                        >
                            <div wire:ignore x-ref="turnstile"></div>
                        </div>
                        @error('turnstileToken')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    @endif

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button
                            type="button"
                            wire:click="backToOptions"
                            class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white"
                        >
                            Back to Options
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex flex-1 items-center justify-center rounded-2xl bg-linear-to-r from-teal-500 to-cyan-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            <span wire:loading.remove wire:target="submit">Send Consultation</span>
                            <span wire:loading wire:target="submit">Sending Your Request...</span>
                        </button>
                    </div>
                </form>
            @endif
        @endunless

        <p class="text-center text-xs text-slate-400">Your details stay protected. Verification is enabled. Clear next steps within one business day.</p>
    </div>
</div>
