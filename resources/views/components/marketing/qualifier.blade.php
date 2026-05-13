<?php

declare(strict_types=1);

use App\DTOs\AnswerData;
use App\Enums\JourneyStatus;
use App\Models\Journey;
use App\Models\JourneyAnswer;
use App\Rules\TurnstileToken;
use App\Services\QualificationService;
use App\Support\PageCatalog;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

// @phpstan-ignore-next-line
new class extends Component
{
    public int $step = 1;

    public string $primaryGoal = '';

    public string $biggestGap = '';

    public string $teamShape = '';

    public string $timeline = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public bool $consent = false;

    public string $turnstileToken = '';

    public bool $turnstileEnabled = false;

    /** @var array<int, array{value: string, label: string, description: string}> */
    public array $goalOptions = [];

    /** @var array<int, array{value: string, label: string, description: string}> */
    public array $gapOptions = [];

    /** @var array<int, array{value: string, label: string, description: string}> */
    public array $teamOptions = [];

    /** @var array<int, array{value: string, label: string, description: string}> */
    public array $timelineOptions = [];

    /** @var array<string, string|null> */
    public array $tracking = [];

    public function mount(): void
    {
        $this->goalOptions = [
            [
                'value' => 'unify_operations',
                'label' => 'Run the business from one connected set of tools',
                'description' => 'Best when several teams need shared information, less manual work, and clearer reporting.',
            ],
            [
                'value' => 'measure_marketing',
                'label' => 'See what your marketing is really delivering',
                'description' => 'Best when campaigns are running but results and return are still hard to see clearly.',
            ],
            [
                'value' => 'modernise_collaboration',
                'label' => 'Make email, documents, and teamwork feel simpler',
                'description' => 'Best when communication, files, and daily collaboration feel harder than they should.',
            ],
        ];

        $this->gapOptions = [
            [
                'value' => 'manual_handoffs',
                'label' => 'Too much manual work between teams',
                'description' => 'Sales, finance, projects, and operations still rely on copying information between systems.',
            ],
            [
                'value' => 'campaign_visibility',
                'label' => 'Marketing is active, but results are hard to see',
                'description' => 'The team is busy, but it is still hard to tell which campaigns are paying off.',
            ],
            [
                'value' => 'email_docs_chat',
                'label' => 'Email, documents, and chat are all over the place',
                'description' => 'People are switching between too many tools just to get basic work done.',
            ],
        ];

        $this->teamOptions = [
            [
                'value' => 'multi_department_growth',
                'label' => 'A growing team across several departments',
                'description' => 'Leaders need a clearer view of sales, delivery, finance, and support.',
            ],
            [
                'value' => 'marketing_led',
                'label' => 'A marketing team that needs clearer results',
                'description' => 'The main need is smoother campaigns, better reporting, and a clearer path from interest to sale.',
            ],
            [
                'value' => 'distributed_team',
                'label' => 'A hybrid or remote team that needs smoother collaboration',
                'description' => 'Internal communication, documents, and day-to-day teamwork need to feel simpler.',
            ],
        ];

        $this->timelineOptions = [
            [
                'value' => 'now',
                'label' => 'Immediately',
                'description' => 'There is already budget or executive urgency behind the project.',
            ],
            [
                'value' => 'this_quarter',
                'label' => 'This quarter',
                'description' => 'The team wants a clear plan and a realistic timeline.',
            ],
            [
                'value' => 'this_year',
                'label' => 'This year',
                'description' => 'The business is planning ahead and wants clarity before committing.',
            ],
        ];

        $this->tracking = [
            'utm_source' => request()->string('utm_source')->toString() ?: null,
            'utm_medium' => request()->string('utm_medium')->toString() ?: null,
            'utm_campaign' => request()->string('utm_campaign')->toString() ?: null,
            'utm_term' => request()->string('utm_term')->toString() ?: null,
            'utm_content' => request()->string('utm_content')->toString() ?: null,
            'referrer' => request()->headers->get('referer'),
        ];

        $this->turnstileEnabled = (bool) config('turnstile.enabled')
            && filled(config('turnstile.site_key'))
            && filled(config('turnstile.secret_key'));
    }

    public function nextStep(): void
    {
        $this->resetErrorBag();

        match ($this->step) {
            1 => $this->validate([
                'primaryGoal' => ['required', 'string'],
            ], [
                'primaryGoal.required' => 'Tell us what you are trying to improve first.',
            ]),
            2 => $this->validate([
                'biggestGap' => ['required', 'string'],
            ], [
                'biggestGap.required' => 'Tell us which bottleneck is creating the most drag right now.',
            ]),
            3 => $this->validate([
                'teamShape' => ['required', 'string'],
            ], [
                'teamShape.required' => 'Tell us which team setup sounds most like yours.',
            ]),
            4 => $this->validate([
                'timeline' => ['required', 'string'],
            ], [
                'timeline.required' => 'Tell us when you want to have the right solution in motion.',
            ]),
            default => null,
        };

        $this->step++;
    }

    public function previousStep(): void
    {
        $this->resetErrorBag();
        $this->step = max(1, $this->step - 1);
    }

    public function qualify(QualificationService $qualificationService): void
    {
        $this->ensureNotRateLimited();

        $rules = [
            'primaryGoal' => ['required', 'string'],
            'biggestGap' => ['required', 'string'],
            'teamShape' => ['required', 'string'],
            'timeline' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'consent' => ['accepted'],
        ];

        if ($this->turnstileEnabled) {
            $rules['turnstileToken'] = ['required', 'string', new TurnstileToken()];
        }

        /** @var array{primaryGoal: string, biggestGap: string, teamShape: string, timeline: string, name: string, email: string, phone?: string|null, company?: string|null, consent: bool, turnstileToken?: string} $validated */
        $validated = $this->validate($rules, [
            'name.required' => 'Add the best person for us to contact.',
            'email.required' => 'Add the email address we should use.',
            'consent.accepted' => 'Please confirm that we can contact you about the right Zoho solution.',
            'turnstileToken.required' => 'Please complete the verification challenge.',
        ]);

        $answers = [
            new AnswerData(stepKey: 'diagnostic_1', fieldKey: 'primary_goal', value: $validated['primaryGoal']),
            new AnswerData(stepKey: 'diagnostic_2', fieldKey: 'biggest_gap', value: $validated['biggestGap']),
            new AnswerData(stepKey: 'diagnostic_3', fieldKey: 'team_shape', value: $validated['teamShape']),
            new AnswerData(stepKey: 'diagnostic_4', fieldKey: 'timeline', value: $validated['timeline']),
        ];

        $productKey = $qualificationService->route($answers);

        $journey = Journey::create([
            'source_page_key' => 'landing',
            'assigned_product_key' => $productKey,
            'status' => JourneyStatus::Routed,
            'utm_source' => $this->tracking['utm_source'],
            'utm_medium' => $this->tracking['utm_medium'],
            'utm_campaign' => $this->tracking['utm_campaign'],
            'utm_term' => $this->tracking['utm_term'],
            'utm_content' => $this->tracking['utm_content'],
            'referrer' => $this->tracking['referrer'],
            'ip_hash' => hash('sha256', request()->ip() ?? ''),
            'user_agent' => request()->userAgent(),
            'contact_json' => array_filter([
                'name' => trim($validated['name']),
                'email' => trim($validated['email']),
                'phone' => is_string($validated['phone'] ?? null) && trim($validated['phone']) !== '' ? trim($validated['phone']) : null,
                'company' => is_string($validated['company'] ?? null) && trim($validated['company']) !== '' ? trim($validated['company']) : null,
            ], static fn (mixed $value): bool => $value !== null && $value !== ''),
            'consent_at' => now(),
        ]);

        $now = now()->toDateTimeString();

        JourneyAnswer::insert(array_map(
            static fn (AnswerData $answer): array => [
                'journey_id' => $journey->id,
                'step_key' => $answer->stepKey,
                'field_key' => $answer->fieldKey,
                'value' => $answer->value,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $answers,
        ));

        RateLimiter::clear($this->rateLimiterKey());

        $this->redirectRoute('products.show', [
            'slug' => PageCatalog::productRouteSegment($productKey),
            't' => $journey->journey_token,
        ], navigate: true);
    }

    private function ensureNotRateLimited(): void
    {
        $key = $this->rateLimiterKey();

        if (RateLimiter::tooManyAttempts($key, 6)) {
            throw ValidationException::withMessages([
                'form' => 'Too many matching attempts have been sent from this browser. Please wait a few minutes and try again.',
            ]);
        }

        RateLimiter::hit($key, 15 * 60);
    }

    private function rateLimiterKey(): string
    {
        return 'landing-qualifier:'.hash('sha256', (request()->ip() ?? 'unknown').'|landing');
    }
};
?>

<div class="panel panel-strong overflow-hidden p-0 shadow-hero">
    <div class="border-b border-white/10 bg-slate-950 px-6 py-5 text-white sm:px-7 lg:px-6 lg:py-4">
        <p class="eyebrow !text-teal-200/90">Find Your Best Zoho Option</p>
        <h2 class="mt-2 font-display text-2xl font-semibold leading-tight text-balance lg:text-[2rem]">Answer a few clear questions and we will suggest the Zoho option that suits you best.</h2>
        <p class="mt-2 text-sm text-slate-300 lg:max-w-xl">Tell us what you need, what is slowing you down, and how soon you want to improve things so we can point you in the right direction.</p>
    </div>

    <div class="space-y-5 px-6 py-6 sm:px-7 lg:space-y-4 lg:px-6 lg:py-5">
        <div class="grid grid-cols-5 gap-2 lg:gap-1.5">
            @foreach([1 => 'What do you need to improve?', 2 => 'What is slowing you down?', 3 => 'What does your team look like?', 4 => 'When do you want this live?', 5 => 'Where should we send it?'] as $stepNumber => $stepLabel)
                <div class="space-y-2 text-center lg:space-y-1.5">
                    <div class="{{ $step >= $stepNumber ? 'bg-teal-500 text-white' : 'bg-slate-100 text-slate-500' }} mx-auto flex h-9 w-9 items-center justify-center rounded-full text-sm font-semibold transition-colors lg:h-8 lg:w-8 lg:text-xs">
                        {{ $stepNumber }}
                    </div>
                    <span class="hidden text-xs font-medium leading-4 text-slate-600 2xl:block lg:text-[11px]">{{ $stepLabel }}</span>
                </div>
            @endforeach
        </div>

        @if($step === 1)
            <div class="space-y-3">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">Question 1 of 4</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-950">What are you trying to improve first?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 lg:leading-5">Choose the business outcome that best matches why you are here today.</p>
                </div>
                <div class="grid gap-3 lg:grid-cols-3">
                    @foreach($goalOptions as $option)
                        <button
                            type="button"
                            wire:click="$set('primaryGoal', '{{ $option['value'] }}')"
                            class="{{ $primaryGoal === $option['value'] ? 'border-teal-400 bg-teal-50 shadow-soft' : 'border-slate-200 bg-white hover:border-teal-200 hover:bg-teal-50/50' }} w-full rounded-3xl border p-5 text-left transition-all lg:h-full lg:p-3.5"
                        >
                            <div class="space-y-1.5">
                                <div class="text-base font-semibold text-slate-950 lg:text-[0.95rem] lg:leading-5">{{ $option['label'] }}</div>
                                <div class="text-sm leading-6 text-slate-600 lg:hidden xl:block xl:leading-5">{{ $option['description'] }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @error('primaryGoal')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror
        @elseif($step === 2)
            <div class="space-y-3">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">Question 2 of 4</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-950">What is the biggest bottleneck in the current setup?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 lg:leading-5">Pick the issue that is creating the most friction for the team right now.</p>
                </div>
                <div class="grid gap-3 lg:grid-cols-3">
                    @foreach($gapOptions as $option)
                        <button
                            type="button"
                            wire:click="$set('biggestGap', '{{ $option['value'] }}')"
                            class="{{ $biggestGap === $option['value'] ? 'border-teal-400 bg-teal-50 shadow-soft' : 'border-slate-200 bg-white hover:border-teal-200 hover:bg-teal-50/50' }} w-full rounded-3xl border p-5 text-left transition-all lg:h-full lg:p-3.5"
                        >
                            <div class="space-y-1.5">
                                <div class="text-base font-semibold text-slate-950 lg:text-[0.95rem] lg:leading-5">{{ $option['label'] }}</div>
                                <div class="text-sm leading-6 text-slate-600 lg:hidden xl:block xl:leading-5">{{ $option['description'] }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @error('biggestGap')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror
        @elseif($step === 3)
            <div class="space-y-3">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">Question 3 of 4</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-950">Which option sounds most like your team?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 lg:leading-5">This helps us understand whether you need business software, marketing tools, or a better way for your team to work together.</p>
                </div>
                <div class="grid gap-3 lg:grid-cols-3">
                    @foreach($teamOptions as $option)
                        <button
                            type="button"
                            wire:click="$set('teamShape', '{{ $option['value'] }}')"
                            class="{{ $teamShape === $option['value'] ? 'border-teal-400 bg-teal-50 shadow-soft' : 'border-slate-200 bg-white hover:border-teal-200 hover:bg-teal-50/50' }} w-full rounded-3xl border p-5 text-left transition-all lg:h-full lg:p-3.5"
                        >
                            <div class="space-y-1.5">
                                <div class="text-base font-semibold text-slate-950 lg:text-[0.95rem] lg:leading-5">{{ $option['label'] }}</div>
                                <div class="text-sm leading-6 text-slate-600 lg:hidden xl:block xl:leading-5">{{ $option['description'] }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @error('teamShape')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror
        @elseif($step === 4)
            <div class="space-y-3">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">Question 4 of 4</p>
                    <h3 class="mt-2 text-lg font-semibold text-slate-950">When do you want the right solution in motion?</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600 lg:leading-5">Your timing helps us suggest the most helpful place to start.</p>
                </div>
                <div class="grid gap-3 lg:grid-cols-3">
                    @foreach($timelineOptions as $option)
                        <button
                            type="button"
                            wire:click="$set('timeline', '{{ $option['value'] }}')"
                            class="{{ $timeline === $option['value'] ? 'border-teal-400 bg-teal-50 shadow-soft' : 'border-slate-200 bg-white hover:border-teal-200 hover:bg-teal-50/50' }} w-full rounded-3xl border p-5 text-left transition-all lg:h-full lg:p-3.5"
                        >
                            <div class="space-y-1.5">
                                <div class="text-base font-semibold text-slate-950 lg:text-[0.95rem] lg:leading-5">{{ $option['label'] }}</div>
                                <div class="text-sm leading-6 text-slate-600 lg:hidden xl:block xl:leading-5">{{ $option['description'] }}</div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            @error('timeline')
                <p class="text-sm text-rose-600">{{ $message }}</p>
            @enderror
        @else
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 lg:p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">Final step</p>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    We will show you the Zoho option that suits you best and send you to the page where you can learn more or speak to us.
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="qualifier-name" class="mb-2 block text-sm font-medium text-slate-700">Full Name</label>
                    <input wire:model.blur="name" id="qualifier-name" type="text" placeholder="Jane Smith" class="field-input">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="qualifier-email" class="mb-2 block text-sm font-medium text-slate-700">Work Email</label>
                    <input wire:model.blur="email" id="qualifier-email" type="email" placeholder="jane@company.co.za" class="field-input">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="qualifier-phone" class="mb-2 block text-sm font-medium text-slate-700">Phone Number</label>
                    <input wire:model.blur="phone" id="qualifier-phone" type="tel" placeholder="+27 82 123 4567" class="field-input">
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="qualifier-company" class="mb-2 block text-sm font-medium text-slate-700">Company</label>
                    <input wire:model.blur="company" id="qualifier-company" type="text" placeholder="Acme (Pty) Ltd" class="field-input">
                    @error('company')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <label class="flex items-start gap-3 rounded-3xl border border-slate-200 bg-white p-5">
                <input wire:model="consent" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-teal-500 focus:ring-teal-400">
                <span class="text-sm leading-6 text-slate-600">
                    I agree that INFX Solutions may contact me about the Zoho options and services that match my needs.
                </span>
            </label>
            @error('consent')
                <p class="text-sm text-rose-600">{{ $message }}</p>
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
        @endif

        @error('form')
            <p class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }}</p>
        @enderror

        <div class="flex flex-col gap-3 sm:flex-row">
            @if($step > 1)
                <button
                    type="button"
                    wire:click="previousStep"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                >
                    Back
                </button>
            @endif

            @if($step < 5)
                <button
                    type="button"
                    wire:click="nextStep"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl bg-linear-to-r from-teal-500 to-cyan-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5"
                >
                    Continue
                </button>
            @else
                <button
                    type="button"
                    wire:click="qualify"
                    wire:loading.attr="disabled"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl bg-linear-to-r from-teal-500 to-cyan-500 px-5 py-3.5 text-sm font-semibold text-white shadow-soft transition-transform hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <span wire:loading.remove wire:target="qualify">Show My Best Option</span>
                    <span wire:loading wire:target="qualify">Finding Your Best Option...</span>
                </button>
            @endif
        </div>

        <p class="text-center text-xs text-slate-500">Your details stay protected. Contact details are encrypted and handled securely. No spam. Clear next steps.</p>
    </div>
</div>
