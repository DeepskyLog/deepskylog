<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __("Profile Information") }}
    </x-slot>

    <x-slot name="description">
        {!! __('Update your account\'s profile information and email address.') !!}
    </x-slot>

    @if (auth()->user()->country == "")
        @push("scripts")
            <script>
                window.onload = function () {
                    const title = '{{ __("Missing information!") }}';
                    const description =
                        '{!! __("Your country of residence is not set. Please set it on this page!") !!}';
                    window.$wireui.notify({
                        title: title,
                        description: description,
                        icon: 'warning',
                    });
                };
            </script>
        @endpush
    @elseif (auth()->user()->about == "")
        @push("scripts")
            <script>
                window.onload = function () {
                    const title = '{{ __("Missing information!") }}';
                    const description =
                        '{{ __("Please provide some information about your astronomical interests!") }}';
                    window.$wireui.notify({
                        title: title,
                        description: description,
                        icon: 'warning',
                    });
                };
            </script>
        @endpush
    @endif

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div
                x-data="{ photoName: null, photoPreview: null }"
                class="col-span-6 sm:col-span-4"
            >
                <!-- Profile Photo File Input -->
                <input
                    type="file"
                    class="hidden"
                    wire:model.live="photo"
                    x-ref="photo"
                    x-on:change="
                        photoName = $refs.photo.files[0].name
                        const reader = new FileReader()
                        reader.onload = (e) => {
                            photoPreview = e.target.result
                        }
                        reader.readAsDataURL($refs.photo.files[0])
                    "
                />

                <x-label for="photo" value="{{ __('Photo') }}"/>

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img
                        src="{{ $this->user->profile_photo_url }}"
                        alt="{{ $this->user->name }}"
                        class="h-20 w-20 rounded-full object-cover"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div
                    class="mt-2"
                    x-show="photoPreview"
                    style="display: none"
                >
                    <span
                        class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                    ></span>
                </div>

                <br/>
                <x-button
                    type="submit"
                    label="{{ __('Select A New Photo') }}"
                    x-on:click.prevent="$refs.photo.click()"
                />

                @if ($this->user->profile_photo_path)
                    <x-button
                        type="submit"
                        label="{{ __('Remove Photo') }}"
                        wire:click="deleteProfilePhoto"
                    />
                @endif

                <x-input-error for="photo" class="mt-2"/>
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-5">
            <x-input
                id="name"
                label="{{ __('Name') }}"
                type="text"
                class="mt-1 block w-full"
                wire:model.live="state.name"
                autocomplete="name"
            />
            <x-input-error for="name" class="mt-2"/>
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-5">
            <x-input
                id="email"
                label="{{ __('Email') }}"
                type="email"
                class="mt-1 block w-full"
                wire:model.live="state.email"
            />
            <x-input-error for="email" class="mt-2"/>

            @if (

                Laravel\Fortify\Features::enabled(
                    Laravel\Fortify\Features::emailVerification()
                ) && ! $this->user->hasVerifiedEmail()            )
                <p class="mt-2 text-sm">
                    {!! __("Your email address is unverified.") !!}

                    <button
                        type="button"
                        class="text-sm text-gray-400 underline hover:text-gray-500"
                        wire:click.prevent="sendEmailVerification"
                    >
                        {!! __("Click here to re-send the verification email.") !!}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p
                        v-show="verificationLinkSent"
                        class="mt-2 text-sm font-medium text-green-600"
                    >
                        {{ __("A new verification link has been sent to your email address.") }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Username -->
        <div class="col-span-6 sm:col-span-5">
            <x-input
                id="username"
                label="{!! __('Username') !!}"
                disabled
                type="text"
                class="mt-1 block w-full"
                value="{{ $this->user->username }}"
            />
        </div>

        {{-- Country of residence --}}
        <div class="col-span-6 sm:col-span-5">
            <x-select
                label="{{ __('Country of residence') }}"
                wire:model.live="state.country"
                :async-data="route('countries.index', ['lang' => app()->getLocale()])"
                option-label="name"
                option-value="id"
            />
        </div>

        {{-- About --}}
        <div class="col-span-6 text-sm text-gray-400 sm:col-span-5">
            {{ __("Tell something about your astronomical interests") }}
        </div>
        <div class="col-span-6 sm:col-span-5" wire:ignore>
            <textarea
                wire:model.live="state.about"
                class="h-48 min-h-fit"
                name="message"
                id="message"
            ></textarea>
        </div>

        <!-- fstOffset -->
        <div class="col-span-6 sm:col-span-5">
            <x-number
                step=".01"
                min="-5.0"
                max="5.0"
                id="fstOffset"
                label="{!! __('Offset between measured SQM value and the faintest visible star.') !!}"
                type="number"
                class="mt-1 block w-full"
                wire:model.live="state.fstOffset"
                autocomplete="fstOffset"
            />
            <x-input-error for="fstOffset" class="mt-2"/>
        </div>

        {{-- License --}}
        <div class="col-span-6 sm:col-span-5">
            {{-- <div x-data='' wire:ignore> --}}
            <x-select
                label="{{ __('License for drawings') }}"
                wire:model.live="state.copyrightSelection"
                :async-data="route('licenses.index')"
                option-label="name"
                option-value="name"
            />
        </div>

        {{-- Copyright notice --}}
        @if ($state["copyrightSelection"] === "Enter your own copyright text")
            <div class="col-span-6 sm:col-span-5">
                <x-input
                    id="copyright"
                    label="{{ __('Copyright notice') }}"
                    type="text"
                    class="mt-1 block w-full"
                    wire:model.live="state.copyright"
                    value="{{ auth()->user()->copyright }}"
                />
            </div>
        @endif
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="mr-3" on="saved">
            {{ __("Saved.") }}
        </x-action-message>

        <x-button
            type="submit"
            secondary
            label="{{ __('Save') }}"
            wire:loading.attr="disabled"
            wire:target="photo"
        />
    </x-slot>
</x-form-section>

@push("scripts")
    <script
        src="{{ asset("js/tinymce/tinymce.min.js") }}"
        referrerpolicy="origin"
    ></script>
    <script>
        tinymce.init({
            selector: "#message", // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: "lists emoticons quickbars wordcount",
            toolbar: "undo redo | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | emoticons | wordcount",
            menubar: false,
            license_key: 'gpl',
            quickbars_insert_toolbar: false,
            quickbars_image_toolbar: false,
            quickbars_selection_toolbar: "bold italic",
            skin: "oxide-dark",
            content_css: "dark",
            setup: function (editor) {
                editor.on("init change", function () {
                    editor.save();
                });
                editor.on("change", function () {
                    @this.
                    set("state.about", editor.getContent());
                });
            }
        });
    </script>
@endpush
