<x-app-layout>
    <div>
        <div class="mx-auto max-w-screen bg-gray-900 px-2 py-10 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-4 grid-cols-1">
                <div class="col-span-1">
                    <img class="w-64 mx-auto object-cover" src="{{ $image }}"
                         alt="{{ $instrument->fullName() }}">
                </div>

                <div class="col-span-2">
                    <h4 class="font-bold text-xl">{{ $instrument->fullName() }}
                        @if (!$instrument->instrumentactive)
                            <div class="text-sm">{{ __("(Not active anymore)") }}</div>
                        @endif
                    </h4>
                    <br/>
                    <table class="table-auto w-full">
                        <tr>
                            <td>{{ __("Instrument Type") }}</td>
                            <td>{{ __($instrument->instrument_type->name) }}
                                @if ($instrument->instrument_type->name != "Naked Eye" && $instrument->instrument_type->name != "Binoculars" && $instrument->instrument_type->name != "Finderscope")
                                    {{ __("on") }} {{ __($instrument->mount_type->name) }} {{ __("mount") }}
                                @endif
                            </td>

                        </tr>

                        <tr>
                            <td>{{ __("Diameter") }}</td>
                            @auth
                                <td>{{ Auth::user()->showInches ? (number_format($instrument->diameter / 25.4, 2, '.', ',')) . ' ' . __('inch') : $instrument->diameter . ' ' . __('mm')}}
                                </td>
                            @endauth
                            @guest
                                <td>{{ $instrument->diameter . ' ' . __('mm')}}</td>
                            @endguest
                        </tr>

                        @if ($instrument->fd  > 0 && $instrument->instrument_type->name != "Naked Eye")
                            <tr>
                                <td>{{ __("Focal Length") }}</td>
                                @auth
                                    <td>{{ Auth::user()->showInches ? (number_format($instrument->fd * $instrument->diameter / 25.4, 2, '.' ,',')) . ' ' . __('inch') : $instrument->fd * $instrument->diameter . ' ' . __('mm')}}
                                        (F/{{ $instrument->fd }})
                                    </td>
                                @endauth
                                @guest
                                    <td>{{ $instrument->fd * $instrument->diameter . ' ' . __('mm')}}</td>
                                @endguest
                            </tr>
                        @endif

                        @if ($instrument->fixedMagnification)
                            <tr>
                                <td>{{ __("Fixed Magnification") }}</td>
                                <td>{{ $instrument->fixedMagnification }}</td>
                            </tr>
                        @endif

                        @if ($instrument->obstruction_perc)
                            <tr>
                                <td>{{ __("Central obstruction") }}</td>
                                <td>{{ $instrument->obstruction_perc }}%</td>
                            </tr>
                        @endif

                        <tr>
                            <td>{{ __("Owner") }}</td>
                            <td>
                                <a href="{{ route('observer.show', $instrument->user->slug) }}">{{  $instrument->user->name }}</a>
                            </td>
                        </tr>

                        @auth
                            @if ($instrument->user_id == Auth::user()->id)
                                {{--                                @if($instrument->sets()->count())--}}
                                {{--                                    <tr>--}}
                                {{--                                        <td>{{ __('In equipment sets') }}</td>--}}
                                {{--                                        <td>--}}
                                {{--                                            <div class="trix-content">--}}
                                {{--                                                <ul>--}}
                                {{--                                                    @foreach($instrument->sets()->get() as $set)--}}
                                {{--                                                        <li><a href="/set/{{ $set->id }}">{{ $set->name }}</a></li>--}}
                                {{--                                                    @endforeach--}}
                                {{--                                                </ul>--}}
                                {{--                                            </div>--}}
                                {{--                                        </td>--}}
                                {{--                                    </tr>--}}
                                {{--                                @endif--}}
                            @endif
                        @endauth

                        <tr>
                            <td>{{ __("Number of observations") }}</td>
                            <td>
                                {{--                                    <a href="/observation/instrument/{{ $instrument->id }}">--}}
                                {{  $instrument->observations }}
                                {{--                                    </a>--}}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __("First light") }}</td>
                            <td>
                                @if (! is_null($instrument->first_observation_date()[0]))
                                    @if ($instrument->first_observation_date()[1] > 0)
                                        <a
                                            href="{{ config("app.old_url") }}/index.php?indexAction=detail_observation&observation={{ $instrument->first_observation_date()[1] }}"
                                        >
                                            {{ $instrument->first_observation_date()[0] }}
                                        </a>
                                    @else
                                        <a
                                            href="{{ config("app.old_url") }}/index.php?indexAction=comets_detail_observation&observation={{ -$instrument->first_observation_date()[1] }}"
                                        >
                                            {{ $instrument->first_observation_date()[0] }}
                                        </a>
                                    @endif
                                @else
                                    {{ __("No observations added!") }}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td>{{ __("Last used on") }}</td>
                            <td>
                                @if (! is_null($instrument->last_observation_date()[0]))
                                    @if ($instrument->last_observation_date()[1] > 0)
                                        <a
                                            href="{{ config("app.old_url") }}/index.php?indexAction=detail_observation&observation={{ $instrument->last_observation_date()[1] }}"
                                        >
                                            {{ $instrument->last_observation_date()[0] }}
                                        </a>
                                    @else
                                        <a
                                            href="{{ config("app.old_url") }}/index.php?indexAction=comets_detail_observation&observation={{ -$instrument->last_observation_date()[1] }}"
                                        >
                                            {{ $instrument->last_observation_date()[0] }}
                                        </a>
                                    @endif
                                @else
                                    {{ __("No observations added!") }}
                                @endif
                            </td>
                        </tr>
                        @auth
                            @if ($instrument->user_id == Auth::user()->id)
                                <tr>
                                    <td>{{ __("Used eyepieces") }}</td>
                                    <td>{!! $instrument->get_used_eyepieces_as_string() !!}</td>
                                </tr>

                                <tr>
                                    <td>{{ __("Used filters") }}</td>
                                    <td>{!! $instrument->get_used_filters_as_string() !!}</td>
                                </tr>

                                <tr>
                                    <td>{{ __("Used lenses") }}</td>
                                    <td>{!! $instrument->get_used_lenses_as_string() !!}</td>
                                </tr>

                                <tr>
                                    <td>{{ __("Observed in the following locations") }}</td>
                                    <td>{!! $instrument->get_used_locations_as_string() !!}</td>
                                    {{--                                    <td>ADD GOOGLE MAPS PAGE</td>--}}
                                </tr>

                            @endif
                        @endauth
                        <tr>
                            <td>{{ __("Image orientation") }}</td>
                            <td>
                                @if ($instrument->flip_image == 0 && $instrument->flop_image == 0)
                                    <img class="w-64 mx-left object-cover" src="/images/unmirrored.png"
                                         alt="{{ __("Unmirrored image") }}">
                                @elseif ($instrument->flip_image == 1 && $instrument->flop_image == 0)
                                    <img class="w-64 mx-left object-cover" src="/images/flip.png"
                                         alt="{{ __("Flipped image") }}">
                                @elseif ($instrument->flip_image == 0 && $instrument->flop_image == 1)
                                    <img class="w-64 mx-left object-cover" src="/images/flop.png"
                                         alt="{{ __("Flopped image") }}">
                                @elseif ($instrument->flip_image == 1 && $instrument->flop_image == 1)
                                    <img class="w-64 mx-left object-cover" src="/images/flipflop.png"
                                         alt="{{ __("Flipped and flopped image") }}">
                                @endif
                            </td>
                        </tr>
                    </table>

                    @auth
                        @if (Auth::user()->id == $instrument->user_id || Auth::user()->isAdministrator())
                            <a href="/instrument/{{$instrument->user->slug}}/{{$instrument->slug }}/edit">
                                <x-button type="submit" secondary label="{{ __('Edit') }} {!! $instrument->name !!}"/>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
