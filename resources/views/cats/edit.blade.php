<style>
    .horizondisp-1 ul li {
        display: inline-block;
        vertical-align: top;
    }

    .horizondisp-1 ul li img {
        display: inline-block;
    }

    .my-font-large {
        display: inline;
        font-size:large;
    }
    .my-font-x-large {
        display: inline;
        font-size:x-large;
    }
</style>

<x-app-layout>
    <div class="horizondisp-1">
        <ul>
            <li style="width:50%; ">
                <img src="{{ $cat->url }}">
            </li>
            <li >
                <form style="display: inline;" method="POST" action="{{ route('cats.update',$cat) }}">
                    @csrf
                    @method('patch')

                    <div style="font-size:xx-large;">
                        {{ __('Profile') }}
                    </div>
                    <div>
                        <x-input-label value="{{ __('Name') }}">
                        </x-input-label>
                        <x-text-input name="name" value="{{ $cat->name }}">
                        </x-text-input>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="{{ __('Gender') }}">
                        </x-input-label>
                        <x-text-input name="gender" value="{{ $cat->gender }}">
                        </x-text-input>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="{{ __('Age') }}">
                        </x-input-label>
                        <x-text-input name="age" value="{{ $cat->age }}">
                        </x-text-input>
                        <x-input-error :messages="$errors->get('age')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="{{ __('Country') }}">
                        </x-input-label>
                        <x-text-input name="country" value="{{ $cat->country }}">
                        </x-text-input>
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label value="{{ __('Breeds') }}">
                        </x-input-label>
                        <x-text-input name="breeds" value="{{ $cat->breeds }}">
                        </x-text-input>
                        <x-input-error :messages="$errors->get('breeds')" class="mt-2" />
                    </div>
                    <div class="pt-6">
                        <x-primary-button >{{ __('EDIT') }}</x-primary-button>
                    </div>
                </form>
            </li>
        </ul>
    </div>
</x-app-layout>