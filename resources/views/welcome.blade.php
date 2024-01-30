<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <title>DILG Webiste</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />


    </head>
    <body class="antialiased">
        <div class="h-screen w-screen bg-gray-50 flex items-center justify-center">
            <div class="container flex flex-col md:flex-row items-center justify-between px-5 text-gray-700">
                <div class="w-full lg:w-1/2 mx-3">
                    <div class="text-4xl text-red-500 font-dark font-extrabold mb-5"> Department of the Interior and Local Government- BOHOL</div>
                    <p class="text-xl  font-light leading-normal mb-8 justify-center">
                        The DILG is the executive department of the Philippine government responsible for promoting peace and order, ensuring public safety and strengthening local government capability aimed towards the effective delivery of basic services to the citizenry.
                    </p>

                    <a href="/login" class="px-5 inline py-3 text-sm font-medium leading-5 shadow-2xl text-white transition-all duration-400 border border-transparent rounded-lg focus:outline-none bg-red-600 active:bg-red-700 hover:bg-red-800">Get Started</a>
                </div>
                <div class="w-full lg:flex lg:justify-end lg:w-1/2  my-12">
                    <img src="images/dilg-main.png" class="w-[400px] object-fit" alt="Image">
                </div>
            </div>

        </div>

    </body>


</html>
