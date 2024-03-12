<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visitor Counter') }}
        </h2>
    </x-slot>
    <div class="flex">
        <div class="w-1/2">
            <section class="py-1 bg-blueGray-50">
                <div class="w-full px-4 mx-auto mt-10">
                   <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white ">
                     <div class="rounded-t mb-0 px-4 py-3 border-0">
                       <div class="flex flex-wrap items-center">
                         <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                           <h3 class="font-semibold text-base text-blueGray-700">
                             Visitors Overview
                           </h3>
                         </div>

                       </div>
                     </div>

                     <div class="block w-full overflow-x-auto">
                       <table class="items-center w-full border-collapse text-blueGray-700  ">
                         <thead class="thead-light ">
                           <tr>
                             <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">

                             </th>
                             <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-right">
                               Visitors
                             </th>
                             <th class="px-6 bg-blueGray-50 text-blueGray-700 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left min-w-140-px"></th>
                           </tr>
                         </thead>
                         <tbody>
                           <tr>
                             <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                               Today
                             </th>
                             <td class="border-t-0 px-6 align-middle text-right border-l-0 border-r-0 text-xs whitespace-nowrap p-4 ">
                                {{ $todayCount }}
                             </td>
                           </tr>
                           <tr>
                             <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                              Yesterday
                             </th>
                             <td class="border-t-0 px-6 align-middle text-right border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                                {{ $yesterdayCount }}
                             </td>
                           </tr>
                           <tr>
                             <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                              All
                             </th>
                             <td class="border-t-0 px-6 align-middle text-right border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                              {{$totalVisitorCount}}
                             </td>

                           </tr>

                         </tbody>
                       </table>
                     </div>
                   </div>
                 </div>

               </section>

               <section class="py-1 bg-blueGray-50">
                <div class="w-full mb-12 px-4 mx-auto">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-md font-semibold mb-4">Visitors Last 30 Days</h2>
                        <canvas id="visitorChart" width="400" height="200" class="bg-white"></canvas>
                    </div>
                </div>
            </section>
               <script>
                var dailyCounts = {!! json_encode($dailyCounts) !!};

                var dates = dailyCounts.map(function(item) {
                    return item.date;
                });

                var counts = dailyCounts.map(function(item) {
                    return item.count;
                });

                var ctx = document.getElementById('visitorChart').getContext('2d');
                var visitorChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Visitors',
                            data: counts,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
        <div class="w-1/2">
            <section class="py-1 bg-blueGray-50">
                <div class="w-full mb-12 px-4 mx-auto mt-10">
                  <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded ">
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                      <div class="flex flex-wrap items-center">
                        <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                          <h3 class="font-semibold text-base text-blueGray-700">Visitors Last 30 days</h3>
                        </div>
                      </div>
                    </div>

                    <div class="block w-full overflow-x-auto">
                      <table class="items-center bg-transparent w-full border-collapse ">
                        <thead>
                          <tr>
                            <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Date
                            </th>
                          <th class="px-6 bg-blueGray-50 text-blueGray-500 align-middle border border-solid border-blueGray-100 py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left">
                                Visitors
                            </th>
                        </thead>

                        <tbody>
                            @foreach ($dailyCounts as $dailyCount)
                            <tr>
                                <th class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left text-blueGray-700 ">
                                {{ $dailyCount->date }}
                                </th>
                                <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 ">
                                    {{ $dailyCount->count }}
                                </td>

                              </tr>

                            @endforeach


                        </tbody>

                      </table>
                    </div>
                  </div>
                </div>
                </section>
        </div>
    </div>

</x-app-layout>
