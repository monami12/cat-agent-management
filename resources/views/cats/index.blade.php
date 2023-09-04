<x-app-layout>
    @if(count($cats) > 0)
        <x-slot name="header">
            <form style="display: inline;" method="GET" action="{{route("cats.download")}}">
                <x-primary-button >{{ __('CAT EXPORT') }}</x-primary-button>
            </form>
        </x-slot>

        <div class="shadow-sm  divide-y" >
            @foreach ($cats as $cat)
                <div class="p-2 flex " >
                    @if ($cat->user->is(auth()->user()))
                        <a href="{{route('cats.edit', $cat)}}"> <img class="object-cover" src="{{ asset('storage/_thumbs/'.$cat->thumb) }}"  ></a>
                    @else
                        <img class="object-cover" src="{{ asset('storage/_thumbs/'.$cat->thumb) }}" >
                    @endif ($cat->user->is(auth()->user()))
                    <div class="flex-1 ml-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-gray-800">{{ $cat->name }}</span>
                                <small class="ml-2 text-sm text-gray-600">{{ $cat->created_at->format('j M Y, g:i a') }}</small>
                                @unless ($cat->created_at->eq($cat->updated_at))
                                    <small class="text-sm text-gray-600"> &middot; {{ __('edited') }}</small>
                                @endunless
                            </div>
                            @if ($cat->user->is(auth()->user()))
                                <x-dropdown>
                                    <x-slot name="trigger">
                                        <button>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('cats.edit', $cat)">
                                            {{ __('Edit') }}
                                        </x-dropdown-link>
                                        <form method="POST" action="{{ route('cats.destroy', $cat) }}">
                                            @csrf
                                            @method('delete')
                                            <x-dropdown-link :href="route('cats.destroy', $cat)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                {{ __('Delete') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                        </div>
                        {{-- <p class="mt-4 text-lg text-gray-900">{{ $cat->message }}</p> --}}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div>
            <div>
                最初のCATエージェントを雇いましょう
            </div>            
            <form method="GET" action="{{route("cats.create")}}">
                <x-primary-button >{{ __('エージェントを雇う') }}</x-primary-button>
            </form>
        </div>
    @endif


</x-app-layout>
