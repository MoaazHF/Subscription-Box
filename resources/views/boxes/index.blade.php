<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Boxes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased font-sans">
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">My Boxes</h1>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Lock Date</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Theme</th>
                        <th scope="col" class="relative px-6 py-4">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($boxes as $box)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="whitespace-nowrap px-6 py-5 text-sm font-medium text-gray-900">
                                {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $box->status === 'locked' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($box->status) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-500">
                                {{ $box->lock_date ? $box->lock_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-sm text-gray-500">
                                {{ $box->theme ?? 'Standard Theme' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-5 text-right text-sm font-medium">
                                <a href="{{ route('boxes.show', $box->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">View Details &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                No boxes found for your subscription.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
