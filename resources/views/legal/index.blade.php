<x-app-layout>

    <div class="w-full max-w-[1190px] px-6 sm:px-8 md:px-16 py-2 md:py-10 rounded-xl min-h-[300px] m-2">
        <div class="flex flex-col md:flex-row items-start md:items-center">
            <div class="md:mr-4"> <!-- Adjust margin based on your design -->
                <h1 class="font-semibold text-2xl mb-2 tracking-wider drop-shadow-[3px_3px_5px_rgba(91,91,91,0.58)]">Legal Opinions Archive</h1>
                {{-- <small class="font-[500]">In the event that we do not have a full 30-days, we extrapolate based on data we have.</small> --}}
            </div>

            <button onclick="document.getElementById('myModal').showModal()" id="btn" class="py-2 px-4 bg-blue-600 text-white rounded text shadow-xl mt-4 md:mt-0 ml-auto">
                <i class="fa-solid fa-plus" style="color: #ffffff; margin-right: 4px;"></i> Add
            </button>

        </div>
        @if(session('success'))
            <div id="flash-message" class="bg-green-200 px-6 py-4 my-4 rounded-md text-lg flex items-center mx-auto max-w-lg absolute top-10 right-0">
                <svg viewBox="0 0 24 24" class="text-green-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                    <path fill="currentColor" d="M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z"></path>
                </svg>
                <span class="text-green-800 text-sm">{{ session('success') }}</span>
            </div>

            <script>
                setTimeout(function(){
                    document.getElementById('flash-message').style.display = 'none';
                }, 2000); // 2000 milliseconds = 2 seconds
            </script>
        @endif
        <div class="flex mt-3">
            <div class="pr-4">
                <div class="relative md:w-full">
                    <form action="{{ route('legal.index') }}" method="GET" class="mb-4" id="filterForm">
                        <select id="category" name="category" autocomplete="category" class="w-full rounded-lg border py-2 px-3">
                            <option value="All" @if(!isset($selectedCategory) || $selectedCategory === 'All') selected @endif>All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @if(isset($selectedCategory) && $selectedCategory === $category) selected @endif>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            {{-- <div class="flex-1 pr-4"> --}}
                <div class=" w-1/2">
                    <form action="{{ route('legal.index') }}" method="GET" class="mb-4" id="searchForm">
                        <input type="text" name="search" value="{{ $search }}"
                               class="w-full pr-4 py-2 rounded-lg shadow focus:outline-none focus:shadow-outline text-gray-600 font-medium"
                               placeholder="Search..." oninput="searchOnChange()">
                        <!-- Include the selected category in the form -->
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        {{-- <div class="absolute inline-flex items-center p-2">
                            <!-- Icon SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" viewBox="0 0 24 24"
                                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                                <circle cx="10" cy="10" r="7"/>
                                <line x1="21" y1="21" x2="15" y2="15"/>
                            </svg>
                        </div> --}}

                        <div class="absolute top-0 left-0 inline-flex items-center p-2">
                            <div id="loadingIndicator" class="hidden loader"></div>
                        </div>
                    </form>
                </div>
            {{-- </div> --}}
        </div>



            <div class=" w-full z-10">
                <div class="flex flex-col">


                    @if(count($legals) === 0 && !empty($search))
                        <div class="text-gray-900 mt-4 justify-center">
                            No data available for your search query "{{ $search }}".
                        </div>
                    @endif

                @if(count($legals) > 0)
                @foreach ($legals as $legal )

                <div class='flex items-center mt-3'>
                    <div class="rounded-xl p-5 shadow-md w-full bg-white border-l-4 border-blue-500">
                    <div class="flex w-full items-center justify-between border-b pb-3">
                      <div class="flex items-center space-x-3">
                        {{-- <div class="h-8 w-8 rounded-full bg-slate-400 bg-[url('https://i.pravatar.cc/32')]"></div> --}}
                        <div class="text-ml font-bold text-slate-700">Reference No: <span class="font-light">{{$legal->issuance->reference_no}}</span></div>
                      </div>
                      <div class="flex items-center space-x-8">
                        <div class="text-xs text-neutral-500">{{ \Carbon\Carbon::parse($legal->issuance->date)->format('F j, Y') }}</div>
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="relative z-10 flex items-center rounded-lg p-2 focus:outline-none bg-white ">

                                {{-- <svg class="h-5 w-5 text-gray-800 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg> --}}
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>


                            <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 w-full h-full z-10"></div>

                            <div x-show="dropdownOpen" class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20">
                                <a  href="{{ url('/legal_opinions/edit', $legal->id) }}" class="block px-4 py-2 text-sm capitalize text-gray-700 hover:bg-blue-500 hover:text-white">
                                   Edit
                                </a>

                                <a onclick="openDeleteModal({{ $legal->id }})" class="block px-4 py-2 text-sm capitalize text-gray-700 hover:bg-red-500 hover:text-white">
                                    Delete
                                </a>
                            </div>
                            <div id="modelConfirm" class="fixed hidden z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 ">
                                <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md">

                                    <div class="flex justify-end p-2">
                                        <button onclick="closeModal('modelConfirm')" type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="p-6 pt-0 text-center">
                                        <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-xl font-normal text-gray-500 mt-5 mb-6">Are you sure you want to delete this legal opinion?</h3>
                                        <div class="flex  justify-center">
                                            <form id="deleteForm" method="POST" action="{{ url('/legal_opinions'. $legal->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" onclick="closeModal('modelConfirm')"
                                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                                                Yes, I'm sure
                                            </button>
                                            </form>
                                            <a href="#" onclick="closeModal('modelConfirm')"
                                                class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-cyan-200 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center"
                                                data-modal-toggle="delete-user-modal">
                                                No, cancel
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>

                      </div>
                    </div>

                    <div class="mt-4 mb-">
                      <div class="mb-3 text-xl font-bold">{{$legal->issuance->title}}</div>
                      <div class="text-sm text-neutral-600 font-bold">
                        @if($legal->responsible_office)
                            Responsible Office: <span class="font-light">{{ $legal->responsible_office }}</span>
                        @endif
                    </div>
                      <div class="text-sm text-neutral-600 font-bold">
                        @if($legal->category)
                            Category: <span class="font-light">{{ $legal->category }}</span>
                        @endif
                    </div>
                      <div class="flex-1 inline-flex items-center">
                        <div class="text-sm text-neutral-600 font-bold">URL link: </div>
                        <a href="{{ $legal->issuance->url_link }}" class="font-bold ml-1 hover:underline" target="_blank">
                         <span class=" text-blue-500 font-light">{{ $legal->issuance->url_link }}</span>
                        </a>
                    </div>
                    </div>

                    <div>
                      <div class="flex items-center justify-between text-slate-500">
                        <div class="flex space-x-4 md:space-x-8">
                          <div class="flex cursor-pointer items-center transition hover:text-slate-600">
                            <div class="text-sm text-neutral-600 font-bold">Keyword/s: </div>
                            <span class="ml-2"> {{$legal->issuance->keyword}}</span>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>

                </div>


                @endforeach
                <div class="text-start" style="color:rgb(83, 82, 82); margin-top: 15px;">
                    {{-- Showing {{ $joints->firstItem() }} to {{ $joints->lastItem() }} of {{ $joints->total() }} entries --}}
                </div>

                <div class="d-flex justify-content-end mt-2">
                    {{ $legals->onEachSide(1)->links() }}
                </div>
                @else
                <div class="flex justify-center items-center">
                    <h1>No Legal opinion available</h1>
                </div>
                @endif
                </div>

        </div>
    </div>

    <dialog id="myModal" class="h-auto w-full md:w-1/2 p-5  bg-white rounded-md">
        <div class="flex flex-col w-full h-auto">
            <!-- Header -->
                <div class="flex w-full h-auto justify-center items-center">
                    <div class="flex w-10/12 h-auto py-3 justify-center items-center text-2xl font-bold ">
                        Create Legal Opinion
                    </div>
                    <div onclick="document.getElementById('myModal').close();" class="flex w-1/12 h-auto justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </div>
                </div>
            <!--Header End-->
            <!-- Modal Content-->
                <div class="flex items-center justify-center p-2">
                    <div class="w-full bg-white">
                        <form method="POST" action="{{route('legal.store')}}">
                            @csrf

                            <div class="mb-5">
                                <label for="date" class="mb-3 block text-base font-medium text-[#07074D]">
                                Date
                                </label>
                                <input type="date" name="date" id="date"
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                            </div>
                            <div class="mb-5">
                                <label for="category" class="mb-3 block text-base font-bold text-[#07074D]">
                                    Category
                                </label>
                                <input type="category" name="category" id="category"
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />

                            </div>

                            <div class="mb-5">
                                <label for="title" class="mb-3 block text-base font-medium text-[#07074D]">
                                Title
                                </label>
                                <textarea type="text" name="title" id="title"
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" /></textarea>
                            </div>
                            <div class="mb-5">
                                <label for="reference_no" class="mb-3 block text-base font-medium text-[#07074D]">
                                Reference No
                                </label>
                                <input type="text" name="reference_no" id="reference_no" placeholder=""
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                            </div>
                            <div class="mb-5">
                                <label for="responsible_office" class="mb-3 block text-base font-medium text-[#07074D]">
                                Responsible Office
                                </label>
                                <input type="text" name="responsible_office" id="responsible_office" placeholder=""
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                            </div>
                            <div class="mb-5">
                                <label for="url_link" class="mb-3 block text-base font-medium text-[#07074D]">
                                Url Link
                                </label>
                                <input type="text" name="url_link" id="url_link" placeholder=""
                                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                            </div>
                            <div id="keyword-container" class="mb-5">
                                <label for="url_link" class="mb-3 block text-base font-medium text-[#07074D]">
                                    Keyword/s:
                                </label>
                                <div class="flex mb-2">
                                    <input type="text" name="keyword[]" placeholder=""
                                           class="flex-1 rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md keyword-input" />
                                    <button type="button" onclick="removeItem(this)"
                                            class="ml-2 text-sm text-red-600 cursor-pointer keyword-remove" style="display: none;">Remove</button>
                                </div>
                            </div>
                            <button type="button" onclick="addItem()" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add keyword</button>
                            {{-- <button type="button" onclick="addItem()" class="mt-2 text-sm text-blue-600 cursor-pointer">Add Item</button> --}}
                            <input type="hidden" name="concatenated_keywords" id="concatenated_keywords">


                                <button
                                    type="submit" class=" mt-3 hover:shadow-form w-full rounded-md bg-blue-400 hover:bg-blue-600 py-3 px-8 text-center text-base font-semibold text-white outline-none">
                                    Save
                                </button>
                            </div>
                    </form>
                </div>
            </div>
            <!-- End of Modal Content-->
        </div>
    </dialog>
</x-app-layout>
<style scoped>
    dialog[open] {
    animation: appear .15s cubic-bezier(0, 1.8, 1, 1.8);
  }

    dialog::backdrop {
      background: linear-gradient(45deg, rgba(0, 0, 0, 0.5), rgba(54, 54, 54, 0.5));
      backdrop-filter: blur(3px);
    }


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
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
   document.addEventListener('DOMContentLoaded', function () {
        addItem();
    });

    function addItem() {
        var container = document.getElementById('keyword-container');
        createInput(container);
    }

    function removeItem(button) {
        var container = document.getElementById('keyword-container');
        var divToRemove = button.parentNode;
        container.removeChild(divToRemove);

        // Display "Remove" button only when there is more than one input
        var inputCount = container.getElementsByClassName('keyword-input').length;
        if (inputCount === 1) {
            container.getElementsByClassName('keyword-remove')[0].style.display = 'none';
        }
    }

    function createInput(container) {
        var input = document.createElement('input');
        input.type = 'text';
        input.name = 'keyword[]';
        input.placeholder = '';
        input.className = 'flex-1 rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md keyword-input';

        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.onclick = function () { removeItem(this); };
        removeButton.className = 'ml-2 text-sm text-red-600 cursor-pointer keyword-remove';
        removeButton.textContent = 'Remove';

        var div = document.createElement('div');
        div.className = 'flex mb-2 keyword-remove';
        div.appendChild(input);

        if (container.getElementsByClassName('keyword-input').length > 0) {
            // Only add "Remove" button for additional inputs
            div.appendChild(removeButton);
        }

        container.appendChild(div);

        // Hide "Remove" button for the initial input
        document.getElementById('initial-input').style.display = 'none';
    }
    window.openModal = function(modalId) {
        document.getElementById(modalId).style.display = 'block'
        document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')
    }

    window.closeModal = function(modalId) {
        document.getElementById(modalId).style.display = 'none'
        document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
    }

    // Close all modals when press ESC
    document.onkeydown = function(event) {
        event = event || window.event;
        if (event.keyCode === 27) {
            document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
            let modals = document.getElementsByClassName('modal');
            Array.prototype.slice.call(modals).forEach(i => {
                i.style.display = 'none'
            })
        }
    };

    function searchOnChange() {
        var form = document.getElementById('searchForm');
        form.submit();
    }
    $(document).ready(function() {
        $('#category').change(function() {
            $('#filterForm').submit();
        });
    });

    function openDeleteModal(id) {
        if (confirm("Are you sure you want to delete this Legal Opinion?")) {
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('/legal_opinions') }}/${id}`;
            form.submit();
        }
    }
</script>



