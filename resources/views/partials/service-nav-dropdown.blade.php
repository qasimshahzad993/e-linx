<li class="nav-item dropdown service-nav-dropdown">
    <button class="nav-link dropdown-toggle {{ request()->routeIs('services', 'service.show') ? 'active' : '' }}" id="nav-{{ $slug }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $category }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="nav-{{ $slug }}">
        @forelse($serviceGroups->get($category, collect())->take(8) as $navService)
            <li><a class="dropdown-item" href="{{ route('service.show', $navService) }}">{{ $navService->title }}</a></li>
        @empty
            <li><span class="dropdown-item-text">Service details are being prepared.</span></li>
        @endforelse
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item nav-category-all" href="{{ route('services') }}#{{ $slug }}">Explore all {{ $category }} services <i class="bi bi-arrow-right" aria-hidden="true"></i></a></li>
    </ul>
</li>
