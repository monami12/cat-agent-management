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
                <img src="{{ $url }}">
            </li>
            <li >
                <div style="font-size:xx-large;">
                    {{ __('Profile') }}
                </div>
                <div>
                    <p class="my-font-x-large">
                        {{ __('Name') }}
                    </p>
                    <p class="my-font-large">
                        {{ $user['name']['first'] }}
                    </p>
                </div>
                <div>
                    <p class="my-font-x-large">
                        {{ __('Gender') }}
                    </p>
                    <p class="my-font-large">
                        {{ $user['gender'] }}
                    </p>
                </div>
                <div>
                    <p class="my-font-x-large">
                        {{ __('Age') }}
                    </p>
                    <p class="my-font-large">
                        {{ $age }}
                    </p>
                </div>
                <div>
                    <p class="my-font-x-large">
                        {{ __('Country') }}
                    </p>
                    <p class="my-font-large">
                        {{ $breed['origin'] }}
                    </p>
                </div>
                <div>
                    <p class="my-font-x-large">
                        {{ __('Breeds') }}
                    </p>
                    <p class="my-font-large">
                        {{ $breed['name'] }}
                    </p>
                </div>
                <div class="pt-6">
                    <x-secondary-button  onclick="window.location.reload ();" >{{ __('NEXT') }}</x-secondary-button>
                    <form style="display: inline;" method="POST" action="{{ route('cats.store') }}">
                        @csrf
                        <input type="hidden" name="name"
                            value="{{ $user['name']['first'] }}">
                        <input type="hidden" name="gender"
                            value="{{ $user['gender'] }}">
                        <input type="hidden" name="age"
                            value="{{ $age }}">
                        <input type="hidden" name="country"
                            value="{{ $breed['origin'] }}">
                        <input type="hidden" name="breeds"
                            value="{{ $breed['name'] }}">
                        <input type="hidden" name="url"
                            value="{{ $url }}">
                        <x-primary-button id="cathire">{{ __('雇う') }}</x-primary-button>
                    </form>
                </div>
            </li>
            <li>
                <canvas id="myChart" style="display: inline-block;"></canvas>
            </li>
        </ul>
    </div>

    <x-slot name="footer" style="float: right;">

    </x-slot>
</x-app-layout>

{{-- <script>
    function catnext(event){
        alert("hello");
    }

    let catnext = document.getElementById('catnext');
    catnext.addEventListener('click', catnext);
</script> --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['適応力', 'かわいさ', '活発さ', '健康度', 'かしこさ'],
            datasets: [{
                data: [
                    {{ $breed["adaptability"]}},
                    {{ $breed["affection_level"]}},
                    {{ $breed["energy_level"]}},
                    5 - {{ $breed["health_issues"]}},
                    {{ $breed["intelligence"]}},
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                display: false
                }
            },
            maintainAspectRatio: false,
            scale: {
                min: 0,
                max: 5,
                beginAtZero: true,
            },
        },
    });
</script>
