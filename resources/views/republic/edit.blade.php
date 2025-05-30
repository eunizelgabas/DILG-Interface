<x-app-layout>
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Republic Act Details
            </h3>
            <a href="/republic_acts" type="button" class="text-gray-400 bg-transparent hover:bg-red-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-toggle="product-modal">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </a>
        </div>

        <div class="p-6 space-y-6">
            <form  method="POST" action="{{route('republic.update', $republic->id)}}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-6 gap-6">

                    <div class="col-span-6 sm:col-span-6">
                        <label for="date" class="text-sm font-medium text-gray-900 block mb-2">Date</label>
                        <input type="date" name="date" id="date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" value="{{ old('republic', $republic->issuance->date) }}">
                        @error('date')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="col-span-full">
                        <label for="title" class="text-sm font-medium text-gray-900 block mb-2">Title</label>
                        <textarea id="title" name="title" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-4" required="">{{ old('republic', $republic->issuance->title) }}</textarea>
                        @error('title')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="col-span-6 sm:col-span-6">
                        <label for="reference_no" class="text-sm font-medium text-gray-900 block mb-2">Reference No</label>
                        <input type="text" name="reference_no" id="reference_no" value="{{ old('republic', $republic->issuance->reference_no) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('reference_no')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="col-span-full">
                        <label for="responsible_office" class="text-sm font-medium text-gray-900 block mb-2">Responsible Office</label>
                        <input type="text" name="responsible_office" id="responsible_office" value="{{ old('republic', $republic->responsible_office) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('responsible_office')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="col-span-full">
                        <label for="url_link" class="text-sm font-medium text-gray-900 block mb-2">Url Link</label>
                        <input type="text" name="url_link" id="url_link" value="{{ old('republic', $republic->issuance->url_link) }}" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" >
                        @error('url_link')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <div id="keyword-container" class="col-span-full">
                        <label for="keyword" class="mb-3 block text-base font-medium text-[#07074D]">
                            Keyword/s:
                        </label>
                        <div class="flex mb-2" id="initial-input">
                            <input type="text" name="keyword[]" placeholder="" value="{{ old('republic', $republic->issuance->keyword) }}"
                                class="keyword-input shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"/>
                        </div>
                        @error('keyword')
                        <p class="text-red-500 text-xs mt-2">{{$message}}</p>
                        @enderror
                    </div>
                    <button type="button" onclick="addItem()" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add keyword</button>
                    <input type="hidden" name="concatenated_keywords" id="concatenated_keywords">

                </div>
                <div class="p-6 border-t mt-5 border-gray-200 rounded-b flex justify-end">
                    <a href="/republic_acts" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 mr-3 focus:ring-red-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" >Cancel</a>
                    <button class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Save all</button>
                </div>
            </form>
        </div>



    </div>
</x-app-layout>
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
    div.appendChild(removeButton);

    container.appendChild(div);
}
</script>
