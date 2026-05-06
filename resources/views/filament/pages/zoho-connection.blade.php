<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Connection settings"
            description="Save the Zoho OAuth settings here so CRM sync can run without env-backed secrets."
        >
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex flex-wrap gap-3">
                    <x-filament::button type="submit">
                        Save connection
                    </x-filament::button>
                    <x-filament::button type="button" color="gray" wire:click="refreshToken">
                        Refresh token
                    </x-filament::button>
                    <x-filament::button color="gray" tag="a" :href="route('admin.zoho.authorize')">
                        Authorize Zoho
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section
            heading="Authorization checklist"
            description="Use the same sequence whenever you reconnect Zoho or rotate the OAuth credentials."
        >
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">1. Save the app details</p>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Confirm the client, token, scope, and redirect details before requesting authorization.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">2. Authorize Zoho once</p>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Complete the consent flow to store the refresh token that keeps CRM sync working.
                    </p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white/80 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">3. Refresh only if needed</p>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Use the manual refresh action when the access token needs operator help, not as part of daily setup.
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
