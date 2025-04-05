<x-app-layout>
    <div class="w-full max-w-[1190px] px-6 sm:px-8 md:px-16 py-2 md:py-10 rounded-xl min-h-[300px] m-2">
        <div class="flex flex-col md:flex-row items-start md:items-center">
            <div class="md:mr-4">
                <h1 class="font-semibold text-2xl mb-2 tracking-wider drop-shadow-[3px_3px_5px_rgba(91,91,91,0.58)]">
                    Republic Acts Archive</h1>
            </div>
        </div>

        @if(session('success'))
            <div id="flash-message"
                class="bg-green-200 px-6 py-4 my-4 rounded-md text-lg flex items-center mx-auto max-w-lg absolute top-10 right-0">
                <svg viewBox="0 0 24 24" class="text-green-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                    <path fill="currentColor"
                        d="M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z">
                    </path>
                </svg>
                <span class="text-green-800 text-sm">{{ session('success') }}</span>
            </div>

            <script>
                setTimeout(function () {
                    document.getElementById('flash-message').style.display = 'none';
                }, 2000);
            </script>
        @endif

        <div class="flex flex-col md:flex-row gap-2">
            <div class="w-full md:w-1/2">
                <form action="{{ route('republic.index') }}" method="GET" class="mb-4" id="searchForm">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search }}"
                            class="w-full pr-4 py-2 rounded-lg shadow focus:outline-none focus:shadow-outline text-gray-600 font-medium"
                            placeholder="Search..." oninput="searchOnChange()">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <div class="absolute top-0 left-0 inline-flex items-center p-2">
                            <div id="loadingIndicator" class="hidden loader"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="w-1/2">
                <div class="relative md:w-full">
                    <form action="{{ route('republic.index') }}" method="GET" class="mb-4" id="filterForm">
                        <select id="date" name="date" autocomplete="date"
                            class="w-full rounded-lg border py-2 px-3 text-gray-700" onchange="this.form.submit()">
                            <option value="All" @if(!isset($selectedDate) || $selectedDate === 'All') selected @endif>All
                                Dates
                            </option>
                            @foreach ($dates as $date)
                                <option value="{{ $date }}" @if(isset($selectedDate) && $selectedDate === $date) selected
                                @endif>
                                    {{ $date }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="search" value="{{ $search }}">
                    </form>
                </div>
            </div>
        </div>

        <div class="w-full z-10">
            <div class="flex flex-col">
                @if(count($republics) === 0 && request()->has('search'))
                    <div class="text-gray-900 justify-center">
                        No Republic Acts found for your search query "{{ request('search') }}".
                    </div>
                @endif

                @if(count($republics) > 0)
                    @foreach ($republics as $act)
                        <div class='flex items-center mt-3'>
                            <div class="rounded-xl border-l-4 border-blue-700 p-5 shadow-md w-full bg-white">
                                <div class="flex w-full items-center justify-between border-b pb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-ml font-bold text-slate-700">Reference:
                                            <span class="font-light">{{ $act->reference ?? 'No Reference' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-8">
                                        <div class="text-xs text-neutral-500">
                                            @if($act->date)
                                                {{ \Carbon\Carbon::parse($act->date)->format('F j, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 mb-">
                                    <div class="mb-3 text-xl font-bold">
                                        <a href="{{ $act->link ?? '#' }}" target="_blank" class="hover:underline">
                                            {{ $act->title ?? 'No Title' }}
                                        </a>
                                    </div>
                                    @if(isset($act->download_link))
                                        <div class="flex items-center">
                                            <a href="{{ asset($act->download_link) }}" target="_blank"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-300 transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red"
                                                    class="bi bi-file-earmark-pdf mr-2" viewBox="0 0 16 16">
                                                    <path
                                                        d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                                    <path
                                                        d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.245.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                                                </svg>
                                                Download Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end mt-2">
                        {{ $republics->onEachSide(1)->links() }}
                    </div>
                @else
                    <div class="flex justify-center items-center mt-10">
                        <h1 class="text-gray-600">No Republic Acts available</h1>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

<style scoped>
    @keyframes appear {
        from {
            opacity: 0;
            transform: translateX(-3rem);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .loader {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left: 4px solid #3498db;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }
</style>

<script>
    var timeoutId;

    function searchOnChange() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function () {
            var form = document.getElementById('searchForm');
            form.submit();
        }, 500);
    }
</script>