<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            <nav class="flex items-center justify-end gap-4">
                <span class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] font-bold">
                    Overview
                </span>
            </nav>
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-6 lg:p-20 lg:pb-10 bg-[#f9f9f8] dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
                    <h1 class="mb-1 font-medium text-2xl">NYT Best Sellers - {{ $overview->publishedDate ?: '23.03.2026' }}</h1>
                    <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">Najgorętsze tytuły z listy bestsellerów New York Times.</p>

                    <div class="space-y-12 mt-8">
                        @foreach($overview->lists as $list)
                            <div class="border-b border-gray-200 dark:border-gray-800 pb-8 last:border-0">
                                <h2 class="text-lg font-bold mb-6 text-gray-800 dark:text-gray-200 uppercase tracking-wider border-l-4 border-[#f53003] pl-4 flex items-center justify-between group">
                                    <span class="truncate">{{ $list->displayName }}</span>
                                    <a href="{{ route('list.show', $list->listNameEncoded) }}" class="text-[10px] font-medium bg-[#f53003] dark:bg-[#FF4433] text-white px-2 py-0.5 rounded hover:bg-[#d42a02] transition-colors ml-4 whitespace-nowrap">
                                        View Full List &rarr;
                                    </a>
                                </h2>
                                <div class="grid grid-cols-1 gap-8">
                                    @foreach($list->books as $book)
                                        <div class="p-6 bg-white dark:bg-[#1b1b18] rounded-xl shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                            <h3 class="font-bold text-xl text-[#f53003] dark:text-[#FF4433] mb-3">{{ $book->title }}</h3>
                                            <div class="flex flex-col sm:flex-row gap-6">
                                                @if($book->bookImage)
                                                    <div class="shrink-0 mx-auto sm:mx-0">
                                                        <img src="{{ $book->bookImage }}" alt="{{ $book->title }}" class="w-32 h-48 object-cover rounded-lg shadow-md">
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <p class="text-base italic mb-3">by {{ $book->author }}</p>
                                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] line-clamp-4 mb-4 leading-relaxed">{{ $book->description }}</p>

                                                    <div class="mt-auto pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A] flex flex-col gap-2 text-xs text-gray-600 dark:text-[#A1A09A]">
                                                        <div>
                                                            <span class="flex items-center">
                                                                <strong class="mr-1">Publisher:</strong> {{ $book->publisher }}
                                                            </span>
                                                            <span class="flex items-center">
                                                                <strong class="mr-1">ISBN:</strong> {{ $book->primaryIsbn13 }}
                                                            </span>
                                                            @if($book->rank)
                                                                <span class="flex items-center font-bold text-[#f53003] dark:text-[#FF4433]">
                                                                    Rank: #{{ $book->rank }}
                                                                    @if($book->weeksOnList)
                                                                        <span class="ml-2 font-normal text-gray-500 text-[10px]">({{ $book->weeksOnList }} weeks on list)</span>
                                                                    @endif
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($book->amazonProductUrl)
                                                            <div class="mt-1">
                                                                <a href="{{ $book->amazonProductUrl }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-[#f53003] dark:bg-[#FF4433] text-white rounded hover:bg-[#d42a02] transition-colors font-medium">
                                                                    Buy on Amazon
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($overview->lists->isEmpty())
                        <p class="text-center py-10 text-[#706f6c]">Brak wyników dla wybranej daty.</p>
                    @endif

                    <p class="mt-6 lg:mt-10 text-[#706f6c] dark:text-[#A1A09A]">
                        v{{ app()->version() }}
                    </p>
                </div>
            </main>
        </div>
    </body>
</html>
