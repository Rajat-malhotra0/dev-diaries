@extends('layouts.app')

@section('content')
@vite('resources/css/index.css')

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Page Title -->
    <h1 class="heading text-3xl font-bold text-center mb-8">Dev Diaries</h1>

    <!-- Main Content Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar - Filters -->
        <aside class="lg:w-1/4">
            <div class="filters card sticky top-6">
                <div class="filters-header card-header">
                    <h2 class="filters-title text-lg font-semibold">Apply Filters</h2>
                </div>
                <div class="filters-content card-content">
                    <form action="{{ route('questions.filter') }}" method="GET">
                        @if (isset($tagsToShow))
                            <div>
                                <h3 class="filters-subtitle text-base font-semibold mb-2">All Tags</h3>
                                @foreach ($tagsToShow as $tag)
                                    <label class="block mb-2">
                                        <input type="checkbox" name="tags[]" value="{{ $tag }}"
                                            @if (is_array(request('tags')) && in_array($tag, request('tags'))) checked @endif>
                                        {{ $tag }}
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <h3 class="filters-subtitle text-base font-semibold mb-2">Most Used Tags</h3>
                                @foreach ($mostUsedTags as $tag)
                                    <label class="block mb-2">
                                        <input type="checkbox" name="tags[]" value="{{ $tag }}"
                                            @if (is_array(request('tags')) && in_array($tag, request('tags'))) checked @endif>
                                        {{ $tag }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        <button type="submit" class="button-primary mt-4 w-full">Filter</button>
                    </form>
                    <a href="{{ route('questions.loadMoreTags') }}" class="button-primary mt-4 inline-block w-full text-center">Load More Tags</a>
                </div>
            </div>
        </aside>

        <!-- Main Content - Questions -->
        <main class="lg:w-3/4">
            <!-- Button: Ask a Question -->
            <div class="text-center mb-6">
                <a href="{{ route('questions.create') }}" class="button-primary">
                    Ask a Question
                </a>
            </div>

            <!-- Admin View: Reported Questions -->
            @if (Auth::user()->role === 'admin')
                <div class="text-center mb-6">
                    <a href="{{ route('admin.reported') }}" class="button-primary">View Reported Questions</a>
                </div>
            @endif

            <!-- Filtered Questions -->
            @if (isset($selectedTags) && !empty($selectedTags))
                <h2 class="text-xl text-blue-500 mb-4">Filtered Questions</h2>
                @forelse($filtered_questions as $question)
                    <div class="question-card">
                        <div class="flex justify-between items-center cursor-pointer"
                            onclick="window.location.href = '{{ route('questions.show', $question->id) }}'">
                            <h3 class="question-card-title">{{ $question->Title }}</h3>
                            <div class="question-card-votes">{{ $question->Upvotes >= 0 ? 'Upvotes: ' . $question->Upvotes : 'Downvotes: ' . -$question->Upvotes }}</div>
                        </div>
                        <p class="question-card-answered mt-2">Answered: {{ $question->Answered ? 'Yes' : 'No' }}</p>
                    </div>
                @empty
                    <p>No questions match the selected tags.</p>
                @endforelse
                {{ $filtered_questions->appends(['tags' => $selectedTags])->links() }}
            @endif

            <!-- Recent Questions -->
            <h2 class="text-xl text-orange-700 mt-8 mb-4">Recent Questions</h2>
            <div class="space-y-4">
                @foreach ($all_questions as $question)
                    <div class="question-card">
                        <div class="flex justify-between items-center cursor-pointer">
                            <a href="{{ route('questions.show', $question->id) }}" class="question-card-title"> 
                                {{ $question->Title }}
                            </a>
                            <div class="question-card-votes">{{ $question->Upvotes >= 0 ? 'Upvotes: ' . $question->Upvotes : 'Downvotes: ' . -$question->Upvotes }}</div>
                        </div>
                        <p class="question-card-answered mt-2">Answered: {{ $question->Answered ? 'Yes' : 'No' }}</p>
                    </div>
                @endforeach
            </div>
            {{ $all_questions->links() }}
        </main>
    </div>
</div>

<!-- Progress Scrollbar -->
<div x-data="scrollHandler(document.getElementById('the-article'))" x-cloak aria-hidden="true" 
    @scroll.window="calculateHeight(window.scrollY)"
    class="fixed h-screen w-1 top-0 left-0 bg-gray-300 dark:bg-gray-600">
    <div :style="`max-height:${height}%`" class="h-full bg-green-400 dark:bg-green-600"></div>
</div>

<!-- Alpine.js Scroll Handler -->
<script>
    function scrollHandler(element = null) {
        return {
            height: 0,
            element: element,
            calculateHeight(position) {
                const distanceFromTop = this.element.offsetTop;
                const contentHeight = this.element.clientHeight;
                const visibleContent = contentHeight - window.innerHeight;
                const start = Math.max(0, position - distanceFromTop);
                const percent = (start / visibleContent) * 100;
                requestAnimationFrame(() => {
                    this.height = percent;
                });
            },
            init() {
                this.element = this.element || document.body;
                this.calculateHeight(window.scrollY);
            }
        };
    }
</script>
@endsection