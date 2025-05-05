
<x-layout title="Create Task">
    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-md">
        <h2 class="text-2xl font-semibold mb-6">Create Task</h2>

        <form action="{{ route('task.create') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="name" id="name" value=""
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-200 focus:ring-opacity-50" required>

                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700 transition">
                    Create
                </button>
            </div>
        </form>
    </div>
</x-layout>
